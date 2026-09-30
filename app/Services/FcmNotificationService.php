<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Exception;

class FcmNotificationService
{
    /**
     * Send Push Notification via Firebase Cloud Messaging HTTP v1 / legacy payload API
     */
    public static function sendNotification($title, $body, $targetType = 'all', $targetPhone = null, $imageUrl = null, $orderId = null)
    {
        $settings = DB::table('fcm_settings')->first();
        
        $projectId = $settings->project_id ?? null;
        $serviceAccountRaw = $settings->service_account_json ?? null;

        $serviceAccount = null;
        if (!empty($serviceAccountRaw)) {
            $serviceAccount = json_decode($serviceAccountRaw, true);
        }

        // Fetch target FCM tokens cleanly without duplicates or dummy tokens
        $tokens = [];
        if ($targetType === 'specific_user' && !empty($targetPhone)) {
            $phoneDigits = preg_replace('/[^0-9]/', '', $targetPhone);
            $cleanPhone = (strlen($phoneDigits) === 10) ? '+91' . $phoneDigits : '+' . $phoneDigits;

            $tokens = DB::table('fcm_tokens')
                ->where(function($q) use ($targetPhone, $cleanPhone, $phoneDigits) {
                    $q->where('user_phone', $targetPhone)
                      ->orWhere('user_phone', $cleanPhone)
                      ->orWhere('user_phone', $phoneDigits);
                })
                ->where('fcm_token', 'NOT LIKE', 'fcm_%')
                ->pluck('fcm_token')
                ->toArray();
        } elseif ($targetType === 'store_managers') {
            $managerPhones = DB::table('users')->where('role', 'store_manager')->pluck('phone')->toArray();
            if (!empty($managerPhones)) {
                $tokens = DB::table('fcm_tokens')
                    ->whereIn('user_phone', $managerPhones)
                    ->where('fcm_token', 'NOT LIKE', 'fcm_%')
                    ->pluck('fcm_token')
                    ->toArray();
            }
        } else {
            $tokens = DB::table('fcm_tokens')
                ->where('fcm_token', 'NOT LIKE', 'fcm_%')
                ->pluck('fcm_token')
                ->toArray();
        }

        $tokens = array_values(array_unique(array_filter($tokens)));


        $sentCount = 0;
        $responseData = [];

        // Build Payload
        $payloadData = [
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'screen' => 'order_details',
            'order_id' => $orderId ? (string)$orderId : '',
        ];

        $errorNote = "";
        // Attempt HTTP v1 or Fallback OAuth Dispatch
        if ($serviceAccount && isset($serviceAccount['client_email'], $serviceAccount['private_key'], $serviceAccount['project_id'])) {
            $accessToken = self::getOAuthToken($serviceAccount);
            $pId = $serviceAccount['project_id'];

            if ($accessToken) {
                $endpoint = "https://fcm.googleapis.com/v1/projects/{$pId}/messages:send";

                foreach ($tokens as $token) {
                    $message = [
                        'message' => [
                            'token' => $token,
                            'notification' => [
                                'title' => $title,
                                'body' => $body,
                            ],
                            'data' => $payloadData,
                            'android' => [
                                'priority' => 'high',
                                'notification' => [
                                    'sound' => 'default',
                                    'channel_id' => 'high_importance_channel',
                                    'notification_priority' => 'PRIORITY_MAX',
                                    'visibility' => 'PUBLIC',
                                    'default_sound' => true,
                                    'default_vibrate_timings' => true,
                                ],
                            ],
                        ]
                    ];


                    if (!empty($imageUrl)) {
                        $message['message']['notification']['image'] = $imageUrl;
                        $message['message']['data']['image_url'] = $imageUrl;
                    }

                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Content-Type' => 'application/json',
                    ])->post($endpoint, $message);

                    if ($response->successful()) {
                        $sentCount++;
                    } else {
                        $errorNote = " (API Error: " . ($response->json()['error']['message'] ?? $response->status()) . ")";
                    }
                    $responseData[] = $response->json();
                }
            } else {
                $errorNote = " (OAuth Token Generation Failed - Check Private Key)";
            }
        } else {
            $errorNote = " (Firebase Service Account JSON missing. Please upload your JSON in the 'Firebase JSON Config' tab above)";
        }

        // Record log in notification_logs
        DB::table('notification_logs')->insert([
            'title' => $title,
            'body' => $body,
            'image_url' => $imageUrl,
            'target_type' => $targetType,
            'target_phone' => $targetPhone,
            'order_id' => $orderId ? (string)$orderId : null,
            'status' => ($sentCount > 0) ? 'sent' : 'queued',
            'response_data' => json_encode($responseData),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($sentCount > 0) {
            return [
                'status' => 'success',
                'message' => "Successfully sent $sentCount push notification(s) out of " . count($tokens) . " target device(s)!",
                'sent_count' => $sentCount,
                'total_tokens' => count($tokens),
            ];
        }

        return [
            'status' => 'warning',
            'message' => "Notification queued for " . count($tokens) . " device(s), but Sent count is 0" . $errorNote,
            'sent_count' => $sentCount,
            'total_tokens' => count($tokens),
        ];
    }


    /**
     * Generate OAuth2 Access Token using Service Account Private Key without external composer dependencies
     */
    private static function getOAuthToken(array $sa)
    {
        try {
            $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
            $now = time();
            $claim = json_encode([
                'iss' => $sa['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => $now + 3600,
                'iat' => $now,
            ]);

            $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
            $base64UrlClaim = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($claim));

            $signatureInput = $base64UrlHeader . "." . $base64UrlClaim;
            $privateKey = str_replace('\n', "\n", $sa['private_key']);

            $binarySignature = '';

            openssl_sign($signatureInput, $binarySignature, $privateKey, 'SHA256');
            $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($binarySignature));

            $jwt = $signatureInput . "." . $base64UrlSignature;

            $res = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if ($res->successful()) {
                return $res->json()['access_token'] ?? null;
            }
        } catch (Exception $e) {
            error_log("FCM OAuth Error: " . $e->getMessage());
        }
        return null;
    }
}
