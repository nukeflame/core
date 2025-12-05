<?php

namespace App\Exceptions;

use Exception;

class BusinessRuleException extends Exception
{
    /**
     * The error code for categorization
     */
    protected string $errorCode;

    /**
     * Additional context data
     */
    protected array $context;

    /**
     * Create a new business rule exception
     */
    public function __construct(
        string $message,
        string $errorCode = 'BUSINESS_RULE_VIOLATION',
        array $context = [],
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->errorCode = $errorCode;
        $this->context = $context;
    }

    /**
     * Get the error code
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * Get context data
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Create exception for duplicate debit note
     */
    public static function duplicateDebitNote(string $coverNo, int $installment): self
    {
        return new self(
            "Debit note already exists for cover {$coverNo}, installment {$installment}",
            'DUPLICATE_DEBIT_NOTE',
            ['cover_no' => $coverNo, 'installment' => $installment]
        );
    }

    /**
     * Create exception for inactive cover
     */
    public static function inactiveCover(string $coverNo, string $status): self
    {
        return new self(
            "Cannot generate debit note for cover with status: Inactive",
            'INACTIVE_COVER',
            ['cover_no' => $coverNo, 'status' => $status]
        );
    }

    /**
     * Create exception for invalid posting period
     */
    public static function invalidPostingPeriod(string $date, string $quarter): self
    {
        return new self(
            "Transaction date {$date} does not fall within quarter {$quarter}",
            'INVALID_POSTING_PERIOD',
            ['date' => $date, 'quarter' => $quarter]
        );
    }

    /**
     * Create exception for exceeded limit
     */
    public static function limitExceeded(string $field, $value, $limit): self
    {
        return new self(
            "{$field} value {$value} exceeds maximum allowed: {$limit}",
            'LIMIT_EXCEEDED',
            ['field' => $field, 'value' => $value, 'limit' => $limit]
        );
    }

    /**
     * Render the exception as JSON response
     */
    public function render(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'error_code' => $this->errorCode,
            'context' => $this->context
        ], 422);
    }
}
