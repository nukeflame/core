<?php

namespace Nukeflame\Core\Services;

use Illuminate\Support\Str;

/**
 * PasswordService handles password generation and validation
 *
 * @package App\Services
 */
class PasswordService
{
    /**
     * Generate a secure temporary password
     *
     * @param int $length
     * @return string
     */
    public function generateTemporaryPassword(int $length = 12): string
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $specialChars = '@#$%&*!?';

        $password = '';
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $specialChars[random_int(0, strlen($specialChars) - 1)];

        $allChars = $lowercase . $uppercase . $numbers . $specialChars;
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        $password = str_shuffle($password);

        return $password;
    }

    /**
     * Validate password strength
     *
     * @param string $password
     * @return array
     */
    public function validatePasswordStrength(string $password): array
    {
        $errors = [];
        $score = 0;

        // Check minimum length
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long.';
        } else {
            $score += 1;
        }

        // Check for lowercase letters
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter.';
        } else {
            $score += 1;
        }

        // Check for uppercase letters
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter.';
        } else {
            $score += 1;
        }

        // Check for numbers
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number.';
        } else {
            $score += 1;
        }

        // Check for special characters
        if (!preg_match('/[@$!%*?&]/', $password)) {
            $errors[] = 'Password must contain at least one special character (@$!%*?&).';
        } else {
            $score += 1;
        }

        // Check for common patterns
        if (preg_match('/(.)\1{2,}/', $password)) {
            $errors[] = 'Password should not contain repeated characters.';
            $score -= 1;
        }

        // Check for sequential patterns
        if (preg_match('/(?:abc|bcd|cde|def|efg|fgh|ghi|hij|ijk|jkl|klm|lmn|mno|nop|opq|pqr|qrs|rst|stu|tuv|uvw|vwx|wxy|xyz|123|234|345|456|567|678|789)/i', $password)) {
            $errors[] = 'Password should not contain sequential characters.';
            $score -= 1;
        }

        // Determine strength level
        $strength = 'weak';
        if ($score >= 4 && empty($errors)) {
            $strength = 'strong';
        } elseif ($score >= 3) {
            $strength = 'medium';
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
            'score' => max(0, $score),
            'strength' => $strength
        ];
    }

    /**
     * Generate a secure random token
     *
     * @param int $length
     * @return string
     */
    public function generateSecureToken(int $length = 32): string
    {
        return Str::random($length);
    }

    /**
     * Check if password is in common password list
     *
     * @param string $password
     * @return bool
     */
    public function isCommonPassword(string $password): bool
    {
        $commonPasswords = [
            'password',
            '123456',
            '123456789',
            'qwerty',
            'abc123',
            'password123',
            'admin',
            '12345678',
            '1234567890',
            'welcome',
            'login',
            'guest',
            'hello',
            'admin123',
            'root',
            'toor',
            'pass',
            '1234',
            '12345',
            'password1',
            'letmein',
            'monkey'
        ];

        return in_array(strtolower($password), $commonPasswords);
    }

    /**
     * Generate password reset token
     *
     * @return array
     */
    public function generatePasswordResetToken(): array
    {
        $token = $this->generateSecureToken(64);
        $expires = now()->addHours(2); // Token expires in 2 hours

        return [
            'token' => $token,
            'expires_at' => $expires,
            'hashed_token' => hash('sha256', $token)
        ];
    }

    /**
     * Verify password reset token
     *
     * @param string $token
     * @param string $hashedToken
     * @param string $expiresAt
     * @return bool
     */
    public function verifyPasswordResetToken(string $token, string $hashedToken, string $expiresAt): bool
    {
        if (now()->isAfter($expiresAt)) {
            return false;
        }

        return hash_equals($hashedToken, hash('sha256', $token));
    }

    /**
     * Hash password using Laravel's default hasher
     *
     * @param string $password
     * @return string
     */
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verify password against hash
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Generate multiple temporary passwords for batch user creation
     *
     * @param int $count
     * @param int $length
     * @return array
     */
    public function generateMultipleTemporaryPasswords(int $count, int $length = 12): array
    {
        $passwords = [];

        for ($i = 0; $i < $count; $i++) {
            $passwords[] = $this->generateTemporaryPassword($length);
        }

        $passwords = array_unique($passwords);

        while (count($passwords) < $count) {
            $newPassword = $this->generateTemporaryPassword($length);
            if (!in_array($newPassword, $passwords)) {
                $passwords[] = $newPassword;
            }
        }

        return array_values($passwords);
    }

    /**
     * Check password age and determine if it needs to be changed
     *
     * @param \Carbon\Carbon|null $passwordChangedAt
     * @param int $maxAgeDays
     * @return array
     */
    public function checkPasswordAge($passwordChangedAt, int $maxAgeDays = 90): array
    {
        if (!$passwordChangedAt) {
            return [
                'needs_change' => true,
                'is_expired' => true,
                'days_old' => null,
                'days_until_expiry' => 0,
                'message' => 'Password has never been changed and must be updated.'
            ];
        }

        $daysOld = now()->diffInDays($passwordChangedAt);
        $daysUntilExpiry = $maxAgeDays - $daysOld;

        return [
            'needs_change' => $daysOld >= $maxAgeDays,
            'is_expired' => $daysOld >= $maxAgeDays,
            'days_old' => $daysOld,
            'days_until_expiry' => max(0, $daysUntilExpiry),
            'message' => $daysOld >= $maxAgeDays
                ? 'Password has expired and must be changed.'
                : ($daysUntilExpiry <= 7
                    ? "Password will expire in {$daysUntilExpiry} days."
                    : "Password is {$daysOld} days old.")
        ];
    }
}
