<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications for the authenticated customer.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Ensure the user is authenticated and has a customer profile
        if (!$user || !$user->customer) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated or not a customer.',
            ], 401);
        }

        // Retrieve notifications for the customer
        $notifications = $user->notifications()->latest()->get();
        \Log::info('Retrieved notifications for user', ['user' => $user->id, 'notifications' => $notifications]);

        return response()->json([
            'success' => true,
            'notifications' => NotificationResource::collection($notifications),
        ]);
    }
}