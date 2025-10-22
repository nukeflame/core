<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Exception;

class ErrorNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected Exception $exception;

    /**
     * Create a new message instance.
     */
    public function __construct(Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        try {
            $environment    = app()->environment();
            $url            = request()->fullUrl() ?? 'Unknown URL';
            $exceptionClass = (null !== ($class = get_class($this->exception))) ? $class : 'Unknown Exception';
            $message        =  $this->exception->getMessage();
            $code_number    = 0; //$this->exception->getCode();
            $file           = $this->exception->getFile();
            $line           = $this->exception->getLine();
            $trace          = $this->exception->getTraceAsString();

            $sqlQuery = null;
            $connection = null;
            if (preg_match('/\(Connection: (.*?), SQL: (.*?)\)$/', $this->exception->getMessage(), $matches)) {
                $connection = $matches[1];
                $sqlQuery = $matches[2];
            }

            try {
                $requestData = request()->all();
            } catch (\Exception $e) {
                $requestData = ['error' => 'Unable to retrieve request data: ' . $e->getMessage()];
            }

            try {
                $user = auth()->check() ? [
                    'id' => auth()->user()->id,
                    'email' => auth()->user()->email,
                ] : null;
            } catch (\Exception $e) {
                $user = ['error' => 'Unable to retrieve user data: ' . $e->getMessage()];
            }

            return $this->subject("[{$environment}] Application Error: {$exceptionClass}")
                ->to(config('app.admin_emails', 'pknuek@gmail.com'))
                ->view('emails.error_notification')
                ->with([
                    'environment' => $environment,
                    'url' => $url,
                    'exceptionClass' => $exceptionClass,
                    'exceptionMessage' => $message,
                    'code_number' => $code_number,
                    'file' => $file,
                    'line' => $line,
                    'trace' => $trace,
                    'request' => $requestData,
                    'user' => $user,
                    'connection' => $connection,
                    'sqlQuery' => $sqlQuery,
                ]);
        } catch (\Exception $e) {
            // Fallback to a very basic email if something goes wrong
            // return $this->subject('Application Error (Fallback Notification)')
            //     ->to(config('app.admin_emails', 'pknuek@gmail.com'))
            //     ->html('<p>An error occurred, but there was a problem generating the detailed notification.</p>
            //            <p>Original error: ' . $this->exception->getMessage() . '</p>
            //            <p>Error notification error: ' . $e->getMessage() . '</p>');
            // return $this->subject('Application Error (Fallback Notification)')
            //     ->to(config('app.admin_emails', 'pknuek@gmail.com'))
            //     ->view('emails.error_notification_fallback')
            //     ->with([
            //         'originalErrorClass' => get_class($this->exception),
            //         'originalErrorMessage' => $this->exception->getMessage(),
            //         'originalErrorFile' => $this->exception->getFile(),
            //         'originalErrorLine' => $this->exception->getLine(),
            //         'notificationErrorMessage' => $e->getMessage()
            //     ]);
        }
    }
}
