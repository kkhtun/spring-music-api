<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function saveDeviceToken(Request $request)
    {

        try {
            $request->validate([
                'device_id' => 'required',
                'fcm_token' => 'required',
            ]);
            $user = User::where("device_id", $request->device_id)->first();
            if (!$user) $user = new User;
            $user->device_id = $request->device_id;
            $user->fcm_token = $request->fcm_token;
            $user->save();
            return response()->json([
                "data" => [
                    "fcm_token" => $user->fcm_token,
                    "device_id" => $user->device_id,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function sendNotification(Request $request)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fcmTokens = User::whereNotNull('fcm_token')->pluck('fcm_token')->all();

        $fcmServerKey = env('FCM_SERVER_KEY');

        $data = [
            "registration_ids" => $fcmTokens,
            "notification" => [
                "title" => $request->title,
                "body" => $request->body,
            ]
        ];

        $RESPONSE = json_encode($data);

        $headers = [
            'Authorization:key=' . $fcmServerKey,
            'Content-Type: application/json',
        ];

        // CURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $RESPONSE);

        $output = curl_exec($ch);
        if ($output === FALSE) {
            curl_error($ch);
            curl_close($ch);
            return response()->send([
                "error" => "Something went wrong"
            ], 500);
        }
        curl_close($ch);
        return response()->json([
            "message" => json_encode($output),
            "status" => "success"
        ]);
    }
}
