<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        \Log::info('Admin profile index');
        return view('admin.profile.index');
    }

    /**
     * edit profile
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request)
    {
        $user = auth()->user();

        \Log::warning('View Edit Profile');

        return view('admin.profile.edit', compact('user'));
    }

    /**
     * update profile
     */
    public function update(ProfileUpdateRequest $request)
    {
        // Ensure the request is properly handling file uploads
        if ($request->hasFile('profile_photo')) {
            // Log for debugging
            \Log::info('Admin profile photo upload', [
                'file' => $request->file('profile_photo')->getClientOriginalName(),
                'size' => $request->file('profile_photo')->getSize(),
                'mime' => $request->file('profile_photo')->getMimeType()
            ]);
        }

        $user = auth()->user();
        $result = UserRepository::updateByRequest($request, $user);

        // Log the result for debugging
        \Log::info('Admin profile updated', [
            'user_id' => $user->id,
            'media_id' => $user->media_id,
            'has_media' => $user->media ? 'yes' : 'no',
            'media_path' => $user->media ? $user->media->src : null,
            'storage_disk' => 's3'
        ]);

        return to_route('admin.profile.index')->withSuccess(__('Profile updated successfully'));
    }

    /**
     * show change password form
     */
    public function changePassword()
    {
        \Log::warning('View Change Profile');

        return view('admin.profile.change-password');
    }

    /**
     * change password
     *
     * @model User $user
     */
    public function updatePassword(ChangePasswordRequest $request)
    {
        $user = auth()->user();
        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withError(__('You have entered wrong password'));
        }
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        \Log::warning('Update Password');

        return back()->withSuccess(__('password change successfully'));
    }
}
