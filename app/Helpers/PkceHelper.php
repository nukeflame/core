<?php

namespace App\Helpers;

class PkceHelper
{
    /**
     * Generate a random code verifier.
     *
     * @param int $length
     * @return string
     */
    public static function generateCodeVerifier(int $length = 64): string
    {
        return self::base64UrlEncode(random_bytes($length));
    }

    /**
     * Generate code challenge from code verifier.
     *
     * @param string $codeVerifier
     * @return string
     */
    public static function generateCodeChallenge(string $codeVerifier): string
    {
        return self::base64UrlEncode(hash('sha256', $codeVerifier, true));
    }

    /**
     * Encode to Base64 URL-safe (RFC 7636).
     *
     * @param string $data
     * @return string
     */
    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
