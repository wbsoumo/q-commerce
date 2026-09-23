<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\FcmNotificationService;

class NotificationController extends Controller
{
    public function index()
    {
        $settings = DB::table('fcm_settings')->first();
        $logs = DB::table('notification_logs')->orderBy('created_at', 'desc')->paginate(15);
        $totalTokens = DB::table('fcm_tokens')->count();

        return view('admin.notifications.index', compact('settings', 'logs', 'totalTokens'));
    }

    public function saveSettings(Request $request)
    {
        $projectId = trim($request->input('project_id', ''));
        $jsonInput = trim($request->input('service_account_json', ''));

        // Handle uploaded file if provided
        if ($request->hasFile('service_account_file')) {
            $jsonInput = file_get_contents($request->file('service_account_file')->getRealPath());
        }

        // Validate JSON format if provided
        if (!empty($jsonInput)) {
            $parsed = json_decode($jsonInput, true);
            if (!$parsed || !isset($parsed['project_id'])) {
                return redirect()->back()->with('error', 'Invalid Firebase Service Account JSON structure.');
            }
            if (empty($projectId) && isset($parsed['project_id'])) {
                $projectId = $parsed['project_id'];
            }
        }

        $existing = DB::table('fcm_settings')->first();

        if ($existing) {
            DB::table('fcm_settings')->where('id', $existing->id)->update([
                'project_id' => $projectId,
                'service_account_json' => $jsonInput,
                'is_active' => true,
                'updated_at' => now(),
            ]);
        } else {
            DB::table('fcm_settings')->insert([
                'project_id' => $projectId,
                'service_account_json' => $jsonInput,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Firebase FCM settings saved successfully!');
    }

    public function sendManual(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'target_type' => 'required|in:all,specific_user',
        ]);

        $title = $request->input('title');
        $body = $request->input('body');
        $targetType = $request->input('target_type');
        $targetPhone = $request->input('target_phone');
        $imageUrl = $request->input('image_url');
        $orderId = $request->input('order_id');

        if ($targetType === 'specific_user' && empty($targetPhone)) {
            return redirect()->back()->with('error', 'Please enter a target phone number for specific user push.');
        }

        $result = FcmNotificationService::sendNotification(
            $title,
            $body,
            $targetType,
            $targetPhone,
            $imageUrl,
            $orderId
        );

        return redirect()->back()->with('success', $result['message']);
    }
}
