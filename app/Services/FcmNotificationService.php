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
                ->pluck('fcm_token')
                ->toArray();
        } elseif ($targetType === 'store_managers') {
            $managerPhones = DB::table('users')->where('role', 'store_manager')->pluck('phone')->toArray();
            if (!empty($managerPhones)) {
                $phoneVariations = [];
                foreach ($managerPhones as $mPhone) {
                    $digits = preg_replace('/[^0-9]/', '', $mPhone);
                    $phoneVariations[] = $mPhone;
                    $phoneVariations[] = $digits;
                    $phoneVariations[] = (strlen($digits) === 10) ? '+91' . $digits : '+' . $digits;
                }
                $tokens = DB::table('fcm_tokens')
                    ->whereIn('user_phone', array_unique($phoneVariations))
                    ->pluck('fcm_token')
                    ->toArray();
            }
        } else {
            $tokens = DB::table('fcm_tokens')
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

    /**
     * Dedicated Separate API method for Store Manager App Order Push Notifications
     */
    public static function sendManagerOrderNotification($orderId, $storeId, $title, $body)
    {
        // Manager App (SB Mart Ops) specific Firebase Service Account
        $managerServiceAccount = [
            "type" => "service_account",
            "project_id" => "sbmartops",
            "private_key_id" => "95934a17f6e2879738bd5eb798d28435d47a59fa",
            "private_key" => "-----BEGIN PRIVATE KEY-----\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDJ2y+WckftCE4H\nVlyAu2/qVzotyGeLLflUCyiy0xp12+EDYi3cxZAr27/f1u1/0lOhdsiR8PI446zl\nOyTJDkZS3TIoVzAPSrFClZG8udrJWQU72NGnudpOUeK3llXNPak8rhzZT8pJKO4T\nQE/HiH2EQ6aq/BiN+pIye3iQw8Ni+6nLHy7NiIVZy/ghLD9KYqn7Vfvd5j2PSMYX\nc/gHi1GX86gcgzfWyUuKS9GflZjACwjeqS3Enb0cpDzKfPmuMGdaF+u0yW9X/Mku\n+qSffFVEL6CqZ5o9KyZ9OErvwrjTphqQZo1cv4NStwpFWTj1HJjr1n4pUQNsfzaI\nyZoAxmAhAgMBAAECggEABsf9Rkbv5vpOHML9oo7UHgGbgRQ9c5iHdW0BNgNk3SwW\nLWtBR9cUln9FvTsBiYzC+yamwE225XWzRj7WvTEPPb4T7vUBi/yiDdmWfB+bm64I\n609yY6RuaA5/g6liQoiIGfNuIVgj3zynxYUgk6nLMFlRd7xhBXazLfel6j8ZXkba\nFvssKnQF13CmHTL1RjtvaL2s8wUv97dovd2iaRuPG/+I/zcofIP0KAepkyG4Js9X\n9qtKIsMPx32tSi3QOTsV/QO+6GajwaLfhV9inezARnLLXAbxddneVDD+Uc0f6ri9\nOiyEiXm62vIBNk6MAq32rKw+V1D0iAlw/+IfHEtmrQKBgQD43zoG2f0XJ00upfVL\ndgbIWqT/YoK5Pi+JkwIPmdgzYwdJ/vCsl3n8X0gpbEieylF0A+6LIKAObNVNbZcL\nWO65I2HcEFMTO4AxfRl2MoAxxEjpnQIA2wmii5W7IMdcqw9Y59mt36ShNPyf7bcy\ngHqw65JFS15ft5hgI0tat9piAwKBgQDPozsu0P0nBYRhoExUtCVGB9FiuTs/iBeE\nOjepFpziVmnKb5FCxKBuI1KJrsBJ70WuWyU0hYrk7FW5T7898BmAT9uMsvjNfJLI\nbKr1m8Mt3Rt825iCiitC+keJPlJ7r+VnSJ0lfMOPhU6l/oQu2rmzmmFAKoAMWwYc\ngvlsKFgOCwKBgQCG18RD9BaaVfQOw2LNuSxhoCNoMELuBZCSNPHX7B5lcVGAuT5D\n9Wrl3+Zkc0RBrRNHDd5QyaOPTU6hPjCpuEzYSSB7sOiiMgn6RnLmROSKknSDB0wP\nlJ560LCXDGKYhiKxpCWgfN1hbyk1qgIpvc08UNcW7og6ymooJNduVtFfawKBgDRN\nLY8xXVMC9MGSmyeK6Qim13tCpUXvhdzsvTB+Xa41jhhL2g8zcCXOB/BecFkvSCIP\nG2QLb10SmtU+3TFA1WuYsfjS7BD2nBKYLMgJIDThSRc+SUA4hYUtfe94H1bAi8xk\nYhEbSDdSoOj3H1yeA8DV1kFPc0mpc/SimSlBUEzZAoGBAOjdNicXA8PHFvMzoojW\nd3PgByBMtR+Lpglbf+mcbEglNVrEgg2P/7/IUPc2pTN30OxeKEcp6zNfymXO/uWT\nqcpOPuAkuwUWDVN6vFQfR60JsTgDse9bDFZpAYFTWgJKKg+4fSEJxOtQI2ruhoiP\nW/gOOvdGsecsyu9vjBjDtWda\n-----END PRIVATE KEY-----\n",
            "client_email" => "firebase-adminsdk-fbsvc@sbmartops.iam.gserviceaccount.com",
            "client_id" => "114809931680734876454",
            "auth_uri" => "https://accounts.google.com/o/oauth2/auth",
            "token_uri" => "https://oauth2.googleapis.com/token",
            "auth_provider_x509_cert_url" => "https://www.googleapis.com/oauth2/v1/certs",
            "client_x509_cert_url" => "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-fbsvc%40sbmartops.iam.gserviceaccount.com",
            "universe_domain" => "googleapis.com"
        ];

        $serviceAccount = $managerServiceAccount;

        $managers = DB::table('users')
            ->whereIn('role', ['store_manager', 'admin'])
            ->where(function($q) use ($storeId) {
                $q->where('store_id', $storeId)
                  ->orWhereNull('store_id');
            })
            ->get();

        $targets = [];
        foreach ($managers as $m) {
            if (!empty($m->phone)) {
                $digits = preg_replace('/[^0-9]/', '', $m->phone);
                $targets[] = $m->phone;
                $targets[] = $digits;
                $targets[] = (strlen($digits) === 10) ? '+91' . $digits : '+' . $digits;
            }
            if (!empty($m->email)) {
                $targets[] = $m->email;
            }
            $targets[] = (string)$m->id;
        }

        $tokens = DB::table('fcm_tokens')
            ->where(function($q) use ($targets) {
                if (!empty($targets)) {
                    $q->whereIn('user_phone', array_unique($targets));
                }
                $q->orWhere('user_phone', 'LIKE', '%manager%')
                  ->orWhere('user_phone', 'LIKE', '%@%');
            })
            ->pluck('fcm_token')
            ->toArray();

        // STRICT SECURITY: Remove any fallback to ALL user tokens so customers NEVER receive manager alerts!
        $tokens = array_values(array_unique(array_filter($tokens)));

        $payloadData = [
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'screen' => 'manager_orders',
            'order_id' => (string)$orderId,
            'store_id' => (string)$storeId,
        ];

        $sentCount = 0;
        $responseData = [];

        if ($serviceAccount && isset($serviceAccount['client_email'], $serviceAccount['private_key'], $serviceAccount['project_id'])) {
            $accessToken = self::getOAuthToken($serviceAccount);
            $pId = $serviceAccount['project_id'];

            if ($accessToken) {
                $endpoint = "https://fcm.googleapis.com/v1/projects/{$pId}/messages:send";

                // 1. Send to individual manager tokens if available
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
                                    'sound' => 'swiggy_new_order',
                                    'channel_id' => 'high_importance_channel',
                                    'notification_priority' => 'PRIORITY_MAX',
                                    'visibility' => 'PUBLIC',
                                    'default_sound' => false,
                                    'default_vibrate_timings' => true,
                                ],
                            ],
                        ]
                    ];

                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Content-Type' => 'application/json',
                    ])->post($endpoint, $message);

                    if ($response->successful()) {
                        $sentCount++;
                    }
                    $responseData[] = $response->json();
                }

                // 2. ALSO Broadcast to FCM Topic 'store_managers' and 'store_manager_{storeId}'
                $managerTopics = ['store_managers', "store_manager_{$storeId}"];
                foreach ($managerTopics as $topic) {
                    $topicMessage = [
                        'message' => [
                            'topic' => $topic,
                            'notification' => [
                                'title' => $title,
                                'body' => $body,
                            ],
                            'data' => $payloadData,
                            'android' => [
                                'priority' => 'high',
                                'notification' => [
                                    'sound' => 'swiggy_new_order',
                                    'channel_id' => 'high_importance_channel',
                                    'notification_priority' => 'PRIORITY_MAX',
                                    'visibility' => 'PUBLIC',
                                    'default_sound' => false,
                                    'default_vibrate_timings' => true,
                                ],
                            ],
                        ]
                    ];

                    $topicResp = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Content-Type' => 'application/json',
                    ])->post($endpoint, $topicMessage);

                    if ($topicResp->successful()) {
                        $sentCount++;
                    }
                    $responseData[] = $topicResp->json();
                }
            }
        }

        DB::table('notification_logs')->insert([
            'title' => $title,
            'body' => $body,
            'image_url' => null,
            'target_type' => 'manager_app_order_alert',
            'target_phone' => 'store_id_' . $storeId,
            'order_id' => (string)$orderId,
            'status' => ($sentCount > 0) ? 'sent' : 'queued',
            'response_data' => json_encode($responseData),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'status' => 'success',
            'sent_count' => $sentCount,
            'total_tokens' => count($tokens),
        ];
    }
}
