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
            if (
                static::$sendingErrorNotification ||
                $e instanceof \Exception &&
                strpos($e->getFile(), 'ErrorNotification.php') !== false
            ) {
                return;
            }

            // Skip email for certain exception types if needed
            if (
                $e instanceof \Illuminate\Validation\ValidationException ||
                // $e instanceof \Illuminate\Session\TokenMismatchException ||
                $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException ||
                $e instanceof \Spatie\Permission\Exceptions\PermissionDoesNotExist
            ) {
                return;
            }


            try {
                static::$sendingErrorNotification = true;

                if (env("MAIL_ERROR_NOTIFICATIONS", false)) {
                    if ($this->hasInternetConnection()) {
                        Mail::queue(new ErrorNotification($e));
                    }
                }

                static::$sendingErrorNotification = false;
            } catch (\Exception $mailException) {
                static::$sendingErrorNotification = false;
            }
        });

        $this->renderable(function (Exception $e, $request) {
            logger($e);

            if ($e instanceof AuthenticationException) {
                return $request->expectsJson()
                    ? response()->json(['message' => 'Unauthenticated'], 401)
                    : redirect()->guest(route('login'));
            }

            if ($e instanceof TokenMismatchException) {
                $request->session()->flush();

                return $request->expectsJson()
                    ? response()->json(['message' => 'CSRF token mismatch'], 419)
                    : redirect()->route('login')->with('message', 'Your session has expired. Please log in again.');
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
     * Check if there is an internet connection
     * @return bool
     */
    private function hasInternetConnection()
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
