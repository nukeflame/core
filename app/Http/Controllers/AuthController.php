<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Verify the access code
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyAccessCode(Request $request)
    {
        $request->validate([
            'accessCode' => 'required|string'
        ]);
        $isValid = $this->validateAccessCode($request->accessCode);

        if ($isValid) {
            Cookie::queue('show_report_sidebar', true, 43200);

            return response()->json([
                'success' => true,
                'message' => 'Authentication successful',
                'redirect' => route('dashboard.index')
            ]);
        }

        Cookie::queue(Cookie::forget('show_report_sidebar'));
        return response()->json([
            'success' => false,
            'message' => 'Access Denied. Provided access code/password is incorrect.'
        ], 401);
    }

    /**
     * Validate the access code
     *
     * @param string $accessCode
     * @return bool
     */
    private function validateAccessCode($accessCode)
    {
        $user = Auth::user();
        if ($user && Hash::check($accessCode, $user->password)) {
            return true;
        }

        return false;
    }

    /**
     * Reset sidebar to default view
     *
     * @return \Illuminate\Http\Response
     */
    public function resetSidebar()
    {
        // Remove the cookie to reset to default sidebar
        Cookie::queue(Cookie::forget('show_report_sidebar'));
        return response()->json([
            'success' => true,
            'message' => 'Default sidebar restored'
        ]);
    }
}
