<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserNotificationPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationPreferenceController extends Controller
{
    public function getPreferences()
    {
        try {
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $preferences = UserNotificationPreference::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'whatsapp_enabled' => false,
                    'firebase_token' => null
                ]
            );

            return response()->json([
                'success' => true,
                'preferences' => $preferences,
                'phone_number' => $user->phone
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching notification preferences: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching notification preferences'
            ], 500);
        }
    }

    public function updatePreferences(Request $request)
    {
        try {
            $request->validate([
                'whatsapp_enabled' => 'required|boolean'
            ]);

            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            if (!$user->phone) {
                return response()->json([
                    'success' => false,
                    'message' => 'You need to add a phone number to your profile first'
                ], 422);
            }

            $preferences = UserNotificationPreference::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'whatsapp_enabled' => $request->whatsapp_enabled,
                    'firebase_token' => $request->firebase_token ?? null
                ]
            );

            return response()->json([
                'success' => true,
                'preferences' => $preferences,
                'message' => 'WhatsApp notifications ' . ($request->whatsapp_enabled ? 'enabled' : 'disabled') . ' successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating notification preferences: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating notification preferences'
            ], 500);
        }
    }
}