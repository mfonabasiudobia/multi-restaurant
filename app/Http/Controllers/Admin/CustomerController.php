<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::role(Roles::CUSTOMER->value)
            ->with('notificationPreferences')
            ->paginate(10);

        return view('admin.customer.index', compact('customers'));
    }

    public function toggleWhatsAppStatus(Request $request, $customerId)
    {
        try {
            $user = User::findOrFail($customerId);
            $enabled = $request->boolean('whatsapp_enabled');

            // Create or update notification preferences
            $user->notificationPreferences()->updateOrCreate(
                ['user_id' => $user->id],
                ['whatsapp_enabled' => $enabled]
            );

            return response()->json([
                'success' => true,
                'message' => __('WhatsApp notification status updated successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to update WhatsApp notification status')
            ], 500);
        }
    }
}
