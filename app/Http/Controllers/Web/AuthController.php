<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle user/seller/admin login.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'Email atau kata sandi tidak cocok.',
            ])->withInput($request->only('email'));
        }

        if ($user->is_banned) {
            return back()->withErrors([
                'email' => 'Akun Anda dinonaktifkan (banned). Silakan hubungi admin Laris Selalu Rejeki.',
            ])->withInput($request->only('email'));
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        AuditLogger::log(
            action: 'LOGIN',
            model: $user,
            newValues: ['method' => 'Web Session Login']
        );

        return $this->redirectBasedOnRole($user)->with('success', "Selamat datang kembali, {$user->name}!");
    }

    /**
     * Show user registration form.
     */
    public function showRegisterForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.register');
    }

    /**
     * Handle customer registration and auto-login.
     */
    public function register(RegisterRequest $request): RedirectResponse
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

        Auth::login($user);
        $request->session()->regenerate();

        AuditLogger::log(
            action: 'LOGIN',
            model: $user,
            newValues: ['method' => 'Web Registration Auto-Login']
        );

        return redirect()->route('user.shop')->with('success', "Pendaftaran berhasil! Selamat berbelanja di Toko Lily.");
    }

    /**
     * Show forgot password form.
     */
    public function showForgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle forgot password submission.
     */
    public function forgotPassword(ForgotPasswordRequest $request): RedirectResponse
    {
        $user = User::where('email', $request->email)->firstOrFail();
        $dummyToken = strtoupper(Str::random(6));

        // For convenience in dev/preview, pass the token directly to the reset view
        return redirect()->route('password.reset.form', [
            'email' => $user->email,
            'token' => $dummyToken,
        ])->with('success', "Token reset password berhasil dibuat: {$dummyToken}. Silakan buat password baru.");
    }

    /**
     * Show reset password form.
     */
    public function showResetPasswordForm(Request $request): View
    {
        return view('auth.reset-password', [
            'email' => $request->query('email'),
            'token' => $request->query('token'),
        ]);
    }

    /**
     * Handle password reset.
     */
    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $user = User::where('email', $request->email)->firstOrFail();

        $user->password = Hash::make($request->password);
        $user->save();

        AuditLogger::log(
            action: 'RESET_PASSWORD',
            model: $user,
            newValues: ['email' => $user->email]
        );

        return redirect()->route('login')->with('success', 'Password berhasil diubah. Silakan login.');
    }

    /**
     * Update user profile (Name, Email, Password).
     */
    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
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
                return back()->withErrors(['current_password' => 'Password saat ini salah.']);
            }
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Profil akun Anda berhasil diperbarui!');
    }

    /**
     * Logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Helper to route user based on role.
     */
    protected function redirectBasedOnRole(User $user): RedirectResponse
    {
        return match ($user->role) {
            'admin' => redirect()->route('admin.revenue'),
            'seller' => redirect()->route('seller.shop'),
            default => redirect()->route('user.shop'),
        };
    }
}
