<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

/**
 * @group User management
 *
 * APIs for managing users
 */
class UserController extends Controller
{
    /**
     * Returns the user profile.
     *
     * @return mixed
     */
    public function index()
    {
        $user = auth()->user();

        return $this->json('profile details', [
            'user' => UserResource::make($user),
        ]);
    }

    /**
     * Updates the user profile.
     *
     * @param  UserRequest  $request  The request object containing the updated user data.
     */
    public function update(UserRequest $request)
    {
        if (app()->environment() == 'local') {
            return $this->json('You can not update your profile in demo mode', [
                'user' => UserResource::make(auth()->user()),
            ]);
        }

        // Log the request for debugging
        \Log::info('API profile update request', [
            'user_id' => auth()->id(),
            'has_profile_photo' => $request->hasFile('profile_photo'),
            'storage_disk' => 's3'
        ]);

        $user = UserRepository::updateByRequest($request, auth()->user());
        $user->refresh();

        // Log the result for debugging
        \Log::info('API profile updated', [
            'user_id' => $user->id,
            'media_id' => $user->media_id,
            'has_media' => $user->media ? 'yes' : 'no',
            'media_path' => $user->media ? $user->media->src : null
        ]);

        return $this->json('Profile updated successfully', [
            'user' => UserResource::make($user),
        ]);
    }

    /**
     * Change the user's password.
     *
     * @param  ChangePasswordRequest  $request  The request object containing the new password.
     * @return string The success message.
     *
     * @throws Some_Exception_Class If the current password does not match.
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        /** @var User $user */
        $user = auth()->user();

        if (app()->environment() == 'local') {
            return $this->json('You can not change your password in demo mode', [], 200);
        }

        if (! Hash::check($request->current_password, $user->password)) {
            return $this->json('Current password does not match', [], 422);
        }
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return $this->json('Password changed successfully');
    }
/**
 * Change the password of a user by their ID.
 *
 * @param  ChangePasswordRequest  $request  The request object containing the new password.
 * @param  int  $id  The ID of the user whose password is to be changed.
 * @return string The success message.
 *
 * @throws Some_Exception_Class If the current password does not match.
 */
public function changePasswordById(Request $request, $id)
{
    try {
        /** @var User $user */
        \Log::info('changePasswordById');
        // \Log::info($request);
        $user = User::findOrFail($id);

        if (strlen($request->password) < 6) {
            return $this->json('Password must be at least 6 characters.', [], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return $this->json('Password changed successfully');
        } catch (\Exception $e) {
        \Log::error('Error changing password: ' . $e->getMessage());
        return $this->json('Failed to change password.', [], 500);
        }
}
/**
 * Update the phone number of a user by their ID.
 *
 * @param  Request  $request  The request object containing the new phone number.
 * @param  int  $id  The ID of the user whose phone number is to be updated.
 * @return string The success message.
 */
public function updatePhoneNumberById(Request $request, $id)
{
    try {
        \Log::info('updatePhoneNumberById');
        /** @var User $user */
        $user = User::findOrFail($id);

        $request->validate([
            'phone_number' => 'required|string|max:15',
        ]);

        $user->update([
            'phone' => $request->phone_number,
        ]);

        return $this->json('Phone number updated successfully');
    } catch (\Exception $e) {
        \Log::error('Error updating phone number: ' . $e->getMessage());
        return $this->json('Failed to update phone number.', [], 500);
    }
}
/**
 * Block or delete a user by their ID.
 *
 * @param  Request  $request  The request object containing the action (block or delete).
 * @param  int  $id  The ID of the user to be blocked or deleted.
 * @return string The success message.
 */
public function blockOrDeleteUserById(Request $request, $id)
{
    try {
        \Log::info('blockOrDeleteUserById');
        /** @var User $user */
        $user = User::findOrFail($id);

        $action = $request->input('action');

        if ($action === 'block') {
            $user->update(['is_active' => false]);
            return $this->json('User blocked successfully');
        } elseif ($action === 'unblock') {
            $user->update(['is_active' => true]);
            return $this->json('User unblocked successfully');
        } elseif ($action === 'delete') {
            $user->delete();
            return $this->json('User deleted successfully');
        } else {
            return $this->json('Invalid action', [], 422);
        }
    } catch (\Exception $e) {
        \Log::error('Error blocking, unblocking, or deleting user: ' . $e->getMessage());
        return $this->json('Failed to block, unblock, or delete user.', [], 500);
    }
}
}
