<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class FirstLoginPasswordController extends Controller
{

    /**
     * Show the form for changing password on first login.
     *
     * @param  string  $token
     * @return \Illuminate\View\View
     */
    public function showForm(Request $request, $token)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $email = $request->email ?? '';
        // $storedToken = $request->cookie('reset_token');
        // if (!$storedToken || !Hash::check($email . $token, $storedToken)) {
        //     return redirect()->route('login')
        //         ->withErrors(['email' => 'Invalid password reset link.']);
        // }

        return view('auth.first-login-password-change', [
            'token' => $token,
            'email' => $email
        ]);
    }

    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',       // at least one uppercase letter
                'regex:/[a-z]/',       // at least one lowercase letter
                'regex:/[0-9]/',       // at least one number
                'regex:/[^A-Za-z0-9]/' // at least one special character
            ],
        ], [
            'password.regex' => 'Your password must include uppercase and lowercase letters, numbers, and special characters.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where(['email' => $request->email, 'requires_password_reset' => true])->first();
        if (!$user) {
            return response()->json([
                'errors' => ['email' => 'User not found.'],
                'message' => 'User not found'
            ], 404);
        }

        try {
            $user->password = Hash::make($request->password);
            $user->password_changed_at = now();
            $user->requires_password_reset = false;
            $user->save();
        } catch (\Exception $e) {
            return response()->json([
                'errors' => ['general' => 'Failed to update password.'],
                'message' => 'Failed to update password.'
            ], 500);
        }

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'message' => 'Your password has been changed successfully. You can now login with your new password.',
            'redirect' => route('login')
        ], 200);
    }

    /**
     * Generate reset token for first login.
     *
     * @param  \App\Models\User  $user
     * @return string
     */
    public function generateFirstLoginToken(User $user)
    {
        DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->delete();

        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now()
        ]);

        return $token;
    }
}
