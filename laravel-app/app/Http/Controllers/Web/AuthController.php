<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // Update last login
            Auth::user()->update(['last_login' => now()]);

            return redirect()->intended('/');
        }

        return back()->with('error', 'Invalid credentials. Please try again.');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        Auth::login($user);

        return redirect('/')->with('success', 'Account created successfully!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // Google OAuth Login
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes([
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/userinfo.profile',
                'https://www.googleapis.com/auth/calendar',
                'https://www.googleapis.com/auth/gmail.send',
            ])
            ->with(['access_type' => 'offline', 'prompt' => 'consent'])
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            \Log::info('Google OAuth Callback received', [
                'request_data' => request()->all()
            ]);

            $googleUser = Socialite::driver('google')->user();
            
            \Log::info('Google user data received', [
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName()
            ]);
            
            // Find or create user
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                    'password' => Hash::make(Str::random(16)), // Random password for SSO users
                    'role' => 'user',
                ]);
                \Log::info('New user created via Google OAuth', ['user_id' => $user->id]);
            } else {
                // Update Google ID and avatar if not set
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
                \Log::info('Existing user updated via Google OAuth', ['user_id' => $user->id]);
            }

            // Store Google tokens
            $user->update([
                'google_access_token' => json_encode([
                    'access_token' => $googleUser->token,
                    'refresh_token' => $googleUser->refreshToken,
                    'expires_in' => $googleUser->expiresIn,
                    'created' => time(),
                ]),
                'google_refresh_token' => $googleUser->refreshToken,
                'last_login' => now(),
            ]);

            Auth::login($user);

            \Log::info('User logged in successfully via Google OAuth', ['user_id' => $user->id]);

            return redirect()->route('dashboard')->with('success', 'Successfully logged in with Google!');
            
        } catch (\Exception $e) {
            \Log::error('Google OAuth Error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('login')->with('error', 'Failed to login with Google. Error: ' . $e->getMessage());
        }
    }
}
