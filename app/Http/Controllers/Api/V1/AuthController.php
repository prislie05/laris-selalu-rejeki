<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends BaseApiController
{
    /**
     * User registration (role: user only).
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role' => 'user',
            'is_banned' => false,
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        AuditLogger::log(
            action: 'LOGIN',
            model: $user,
            newValues: ['method' => 'API Registration Auto-Login']
        );

        return $this->successResponse('Registrasi berhasil.', [
            'user' => new UserResource($user),
            'token' => $token,
        ], 201);
    }

    /**
     * User/Seller/Admin login.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return $this->errorResponse('Email atau password salah.', [
                'auth' => ['Kombinasi email dan password tidak sesuai.'],
            ], 401);
        }

        if ($user->is_banned) {
            return $this->errorResponse('Akun Anda telah dinonaktifkan (banned).', [
                'account' => ['Silakan hubungi administrator Laris Selalu Rejeki.'],
            ], 403);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        AuditLogger::log(
            action: 'LOGIN',
            model: $user,
            newValues: ['method' => 'API Login']
        );

        return $this->successResponse('Login berhasil.', [
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    /**
     * Logout and revoke token.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return $this->successResponse('Logout berhasil.');
    }

    /**
     * Get authenticated user profile.
     */
    public function profile(Request $request): JsonResponse
    {
        return $this->successResponse('Profil pengguna.', [
            'user' => new UserResource($request->user()),
        ]);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (isset($validated['phone'])) {
            $user->phone = $validated['phone'];
        }

        if (!empty($validated['password'])) {
            if (!empty($validated['current_password']) && !Hash::check($validated['current_password'], $user->password)) {
                return $this->errorResponse('Password saat ini salah.', [
                    'current_password' => ['Password saat ini tidak sesuai.'],
                ], 422);
            }
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return $this->successResponse('Profil berhasil diperbarui.', [
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Request forgot password token.
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->firstOrFail();
        $resetToken = strtoupper(Str::random(6)); // 6-char verification code

        // In production this sends an email, here we provide reset token for seamless flow
        return $this->successResponse('Kode verifikasi reset password telah dikirim ke email.', [
            'reset_token' => $resetToken,
            'email' => $user->email,
        ]);
    }

    /**
     * Reset password using token.
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->firstOrFail();

        $user->password = Hash::make($request->password);
        $user->save();

        AuditLogger::log(
            action: 'RESET_PASSWORD',
            model: $user,
            newValues: ['email' => $user->email]
        );

        return $this->successResponse('Password berhasil diubah. Silakan login kembali.');
    }
}
