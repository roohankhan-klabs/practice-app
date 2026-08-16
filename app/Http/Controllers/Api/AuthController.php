<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return $this->formatError('Invalid credentials', 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->formatResponse('Login successful', [
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => UserStatus::ACTIVE,
            'role_id' => Role::CUSTOMER,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->formatResponse('User created successfully', [
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->formatResponse('Logout successful');
    }

    public function profile(Request $request)
    {
        return $this->formatResponse('Profile fetched successfully', $request->user());
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);

        $user = $request->user();
        $user->update($validated);

        return $this->formatResponse('Profile updated successfully', $user);
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);

        $user = $request->user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            return $this->formatError('Invalid current password', 401);
        }

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return $this->formatResponse('Password changed successfully', $user);
    }

    public function sendOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return $this->formatError('User not found', 404);
        }
        $verification = Verification::where('user_id', $user->id)->where('created_at', '>=', now()->subMinutes(1))->first();

        if ($verification) {
            return $this->formatError('OTP already sent. Wait for 1 minute to resend OTP.', 400);
        }

        if (app()->environment('local') || app()->environment('staging')) {
            $otp = 123456;
        } else {
            $otp = rand(100000, 999999);
        }

        Verification::create([
            'user_id' => $user->id,
            'phone' => $user->phone,
            'email' => $user->email,
            'otp' => $otp,
            'status' => Verification::PENDING,
        ]);

        return $this->formatResponse('OTP sent successfully');
    }

    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return $this->formatError('User not found', 404);
        }

        $verification = Verification::where('user_id', $user->id)
            ->where('otp', $validated['otp'])
            ->where('status', Verification::PENDING)
            ->first();

        if (! $verification) {
            return $this->formatError('Invalid OTP', 401);
        }
        if ($verification->created_at < now()->subMinutes(5)) {
            $verification->update([
                'status' => Verification::EXPIRED,
            ]);

            return $this->formatError('OTP expired', 401);
        }

        $verification->update([
            'status' => Verification::VERIFIED,
        ]);

        return $this->formatResponse('OTP verified successfully', [
            'token' => $user->createToken('auth_token')->plainTextToken,
            'user' => $user,
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return $this->formatError('User not found', 404);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return $this->formatResponse('Password changed successfully');
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed',
            'otp' => 'required',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return $this->formatError('User not found', 404);
        }

        $verification = Verification::where('user_id', $user->id)
            ->where('otp', $validated['otp'])
            ->where('status', Verification::VERIFIED)
            ->first();

        if (! $verification) {
            return $this->formatError('Invalid OTP', 401);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return $this->formatResponse('Password reset successfully');
    }
}
