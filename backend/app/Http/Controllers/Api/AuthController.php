<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(StoreUserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => $user,
            'token' => $token,
            'message' => 'Registration successful'
        ], 201);
    }

    public function login(LoginUserRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => $user,
            'token' => $token,
            'message' => 'Login successful'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function googleRedirect()
    {
        return response()->json([
            'url' => \Laravel\Socialite\Facades\Socialite::driver('google')->stateless()->redirect()->getTargetUrl()
        ]);
    }

    public function googleCallback()
    {
        try {
            $driver = \Laravel\Socialite\Facades\Socialite::driver('google')->stateless();
            if (config('app.env') === 'local' || config('app.env') === 'testing') {
                $driver->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));
            }
            $googleUser = $driver->user();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google OAuth callback error: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            return redirect($frontendUrl . '/login?error=Google authentication failed.');
        }

        // Find or create user
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            // Update google_id and avatar_url if not set
            $user->update([
                'google_id' => $user->google_id ?: $googleUser->getId(),
                'avatar_url' => $user->avatar_url ?: $googleUser->getAvatar(),
            ]);
        } else {
            $user = User::create([
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Google User',
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar_url' => $googleUser->getAvatar(),
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');

        return redirect($frontendUrl . '/auth/google/callback?token=' . $token);
    }
}
