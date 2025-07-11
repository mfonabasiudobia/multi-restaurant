<?php

namespace App\Http\Controllers\API\Auth;

use App\Enums\Roles;
use App\Events\SendOTPMail;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\VerifyManage;
use App\Repositories\CustomerRepository;
use App\Repositories\DeviceKeyRepository;
use App\Repositories\UserRepository;
use App\Repositories\VerificationCodeRepository;
use App\Repositories\WalletRepository;
use App\Services\SmsGatewayService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user and return the registration result.
     *
     * @param  RegistrationRequest  $request  The registration request data
     * @return Some_Return_Value The registration result data
     */
    public function register(RegistrationRequest $request)
    {
        // Create a new user
        \Log::info('Registering new user', $request->all());
        $user = UserRepository::registerNewUser($request);

        if ($request->device_key) {
            DeviceKeyRepository::storeByRequest($user, $request);
        }

        // Create a new customer
        CustomerRepository::storeByRequest($user);

        //create wallet
        WalletRepository::storeByRequest($user);

        $user->assignRole(Roles::CUSTOMER->value);

        $verifyManage = Cache::rememberForever('verify_manage', function () {
            return VerifyManage::first();
        });

        $OTP = null;

        // Always create verification code
        $verificationCode = VerificationCodeRepository::findOrCreateByContact($user->phone);
        $OTP = app()->environment('local') ? $verificationCode->otp : null;
        $message = 'Codul dumneavoastră de verificare OTP pentru secondhub.ro este '.$verificationCode->otp;

        // User starts with null phone_verified_at
        $user->phone_verified_at = null;
        $user->save();

        if ($verifyManage?->register_otp_type == 'phone') {
            try {
                (new SmsGatewayService)->sendSMS($user->phone_code, $user->phone, $message);
            } catch (\Throwable $th) {
            }
        } elseif ($user->email) {
            try {
                SendOTPMail::dispatch($user->email, $message);
            } catch (\Throwable $th) {
            }
        }

        // Don't return token since user isn't verified yet
        return $this->json('Registration successfully complete', [
            'user' => new UserResource($user),
            'otp' => $OTP,
            'access' => [
                'token' => UserRepository::getAccessToken($user)
            ]
        ]);
    }

    /**
     * Login a user.
     *
     * @param  LoginRequest  $request  The login request data
     */
    public function login(LoginRequest $request)
    {
        // Authenticate the user
        $user = $this->authenticate($request);
        if ($user?->customer) {
            // Check if phone is verified
            if (!$user->phone_verified_at) {
                // If phone not verified, send new OTP and return appropriate response
                $verificationCode = VerificationCodeRepository::findOrCreateByContact($user->phone);
                $message = 'Codul dumneavoastră de verificare OTP pentru secondhub.ro este '.$verificationCode->otp;
                
                try {
                    (new SmsGatewayService)->sendSMS($user->phone_code, $user->phone, $message);
                } catch (\Throwable $th) {
                }

                return $this->json('Please verify your phone number first', [
                    'requires_verification' => true,
                    'user' => new UserResource($user)
                ], Response::HTTP_FORBIDDEN);
            }

            if ($request->device_key) {
                DeviceKeyRepository::storeByRequest($user, $request);
            }

            return $this->json('Login successfully', [
                'user' => new UserResource($user),
                'access' => [
                    'token' => UserRepository::getAccessToken($user)
                ]
            ]);
        }

        return $this->json('Credential is invalid!', [], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Authenticate the user and return the user.
     *
     * @param  LoginRequest  $request  The login request
     * @return User|null
     */
    private function authenticate(LoginRequest $request)
    {
        $user = UserRepository::findByPhone($request->phone);
        if (! is_null($user) && Hash::check($request->password, $user->password)) {
            return $user;
        }

        return null;
    }

    /**
     * Logout the user and revoke the token.
     *
     * @model User $user
     *
     * @return string
     */
    public function logout()
    {
        /** @var \User $user */
        $user = auth()->user();

        if ($user) {
            $user->currentAccessToken()->delete();

            return $this->json('Logged out successfully!');
        }

        return $this->json('User not found!', [], Response::HTTP_NOT_FOUND);
    }

    public function callback(Request $request) {}

    // Add this new method to verify OTP
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required'
        ]);

        $user = UserRepository::findByPhone($request->phone);
        if (!$user) {
            return $this->json('User not found', [], Response::HTTP_NOT_FOUND);
        }

        $verificationCode = VerificationCodeRepository::checkOTP($request->phone, $request->otp);

        if (!$verificationCode) {
            return $this->json('Invalid OTP', [], Response::HTTP_BAD_REQUEST);
        }

        // Set phone_verified_at timestamp
        $user->phone_verified_at = now();
        $user->save();

        // Delete the verification code
        $verificationCode->delete();

        // Return token and user data for frontend authentication
        return $this->json('Phone number verified successfully', [
            'user' => new UserResource($user),
            'access' => [
                'token' => UserRepository::getAccessToken($user)
            ]
        ]);
    }
}
