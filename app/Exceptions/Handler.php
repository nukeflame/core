<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use App\Mail\ErrorNotification;
use Illuminate\Support\Facades\Mail;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Flag to prevent recursive error reporting
     */
    protected static $sendingErrorNotification = false;

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Exception $e) {
            // Skip session/auth related exceptions from error notifications
            if ($this->isSessionOrAuthException($e)) {
                return false;
            }

            if (
                static::$sendingErrorNotification ||
                $e instanceof \Exception &&
                strpos($e->getFile(), 'ErrorNotification.php') !== false
            ) {
                return false;
            }

            if (
                $e instanceof \Illuminate\Validation\ValidationException ||
                $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException ||
                $e instanceof \Spatie\Permission\Exceptions\PermissionDoesNotExist
            ) {
                return false;
            }

            try {
                static::$sendingErrorNotification = true;

                if (env("MAIL_ERROR_NOTIFICATIONS", false)) {
                    if ($this->hasInternetConnection()) {
                        Mail::queue(new ErrorNotification($e));
                    }
                }
            } catch (\Exception $mailException) {
                // Silently fail
            } finally {
                static::$sendingErrorNotification = false;
            }
        });

        $this->renderable(function (Exception $e, $request) {
            // Handle session/auth exceptions first - these should always redirect to login
            if ($this->isSessionOrAuthException($e)) {
                return $this->handleSessionExpired($request);
            }

            if ($e instanceof AuthenticationException) {
                return $this->handleSessionExpired($request);
            }

            if ($e instanceof TokenMismatchException) {
                return $this->handleSessionExpired($request, 'Your session has expired. Please log in again.');
            }

            if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                return $request->expectsJson()
                    ? response()->json(['message' => 'Not Found'], 404)
                    : response()->view('errors.404', [], 404);
            }

            if ($e instanceof \Spatie\Permission\Exceptions\PermissionDoesNotExist) {
                return redirect()->back()->withErrors(['error' => 'Permission does not exist.']);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Server Error',
                    'error' => app()->environment('production') ? 'An unexpected error occurred' : $e->getMessage(),
                    'code' => $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return;
            }

            return response()->view('errors.500', ['exception' => $e], Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    }

    /**
     * Check if exception is related to session or authentication
     */
    private function isSessionOrAuthException(Exception $e): bool
    {
        $sessionExceptions = [
            'Session store not set on request',
            'Session store not started',
            'Unauthenticated',
            'Your session has expired',
            'Session has expired',
            'Token has expired',
        ];

        $message = $e->getMessage();
        foreach ($sessionExceptions as $sessionMessage) {
            if (stripos($message, $sessionMessage) !== false) {
                return true;
            }
        }

        // Check for session-related exception types
        if (
            $e instanceof \RuntimeException &&
            stripos($e->getMessage(), 'session') !== false
        ) {
            return true;
        }

        return false;
    }

    /**
     * Handle expired session - redirect to login
     */
    private function handleSessionExpired($request, ?string $message = null)
    {
        // Safely try to flush session
        try {
            if ($request->hasSession() && $request->session()->isStarted()) {
                $request->session()->flush();
            }
        } catch (\Exception $e) {
            // Session already gone, ignore
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message ?? 'Your session has expired. Please log in again.',
                'redirect' => route('login')
            ], 401);
        }

        return redirect()->guest(route('login'))
            ->with('message', $message ?? 'Your session has expired. Please log in again.');
    }

    /**
     * Check if there is an internet connection
     */
    private function hasInternetConnection(): bool
    {
        $connected = @fsockopen("www.google.com", 80, $errno, $errstr, 3);
        if ($connected) {
            fclose($connected);
            return true;
        }

        $connected = @fsockopen("8.8.8.8", 53, $errno, $errstr, 3);
        if ($connected) {
            fclose($connected);
            return true;
        }

        return false;
    }
}
