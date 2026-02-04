<?php

namespace App\Exceptions;

use Exception;

class BusinessRuleException extends Exception
{
    protected string $errorCode;

    protected array $context;

    protected int $httpStatusCode;

    protected string $errorMessage;

    public function __construct(
        string $message,
        string $errorCode = 'BUSINESS_RULE_VIOLATION',
        array $context = [],
        int $httpStatusCode = 422,
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->errorCode = $errorCode;
        $this->context = $context;
        $this->httpStatusCode = $httpStatusCode;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    public function render(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'error_code' => $this->errorCode,
            'context' => $this->context,
        ], $this->httpStatusCode);
    }

    public static function invalidStatusTransition(string $from, string $to): self
    {
        return new self(
            "Cannot transition from '{$from}' to '{$to}' status",
            'INVALID_STATUS_TRANSITION',
            ['from_status' => $from, 'to_status' => $to],
            422
        );
    }

    public static function selfApproval(): self
    {
        return new self(
            'You cannot approve a debit note that you submitted',
            'SELF_APPROVAL_DENIED',
            ['user_id' => auth()->id()],
            403
        );
    }

    public static function missingItems(): self
    {
        return new self(
            'Debit note must have at least one line item',
            'MISSING_LINE_ITEMS',
            [],
            422
        );
    }

    public static function alreadyPosted(string $debitNoteNo): self
    {
        return new self(
            "Cannot modify debit note '{$debitNoteNo}' - already posted",
            'ALREADY_POSTED',
            ['debit_note_no' => $debitNoteNo],
            422
        );
    }

    public static function duplicateDebitNote(string $coverNo, int $installment): self
    {
        return new self(
            "Debit note already exists for cover {$coverNo}, installment {$installment}",
            'DUPLICATE_DEBIT_NOTE',
            ['cover_no' => $coverNo, 'installment' => $installment],
            409
        );
    }

    public static function duplicateDebitNoteNumber(string $debitNoteNo): self
    {
        return new self(
            "Debit note '{$debitNoteNo}' already exists",
            'DUPLICATE_DEBIT_NOTE_NUMBER',
            ['debit_note_no' => $debitNoteNo],
            409
        );
    }

    public static function inactiveCover(string $coverNo, string $status): self
    {
        return new self(
            "Cannot generate debit note for cover with status: {$status}",
            'INACTIVE_COVER',
            ['cover_no' => $coverNo, 'status' => $status],
            422
        );
    }

    public static function coverNotFound(string $coverNo): self
    {
        return new self(
            "Cover '{$coverNo}' not found",
            'COVER_NOT_FOUND',
            ['cover_no' => $coverNo],
            404
        );
    }

    public static function noReinsurers(string $coverNo): self
    {
        return new self(
            "No reinsurers found for cover '{$coverNo}'",
            'NO_REINSURERS',
            ['cover_no' => $coverNo],
            422
        );
    }

    public static function invalidShare(float $share, ?string $reinsurerName = null): self
    {
        $message = $reinsurerName
            ? "Invalid share percentage for {$reinsurerName}: {$share}%. Must be between 0 and 100"
            : "Invalid share percentage: {$share}%. Must be between 0 and 100";

        return new self(
            $message,
            'INVALID_SHARE',
            ['share' => $share, 'reinsurer' => $reinsurerName],
            422
        );
    }

    public static function invalidPostingPeriod(string $date, string $quarter): self
    {
        return new self(
            "Transaction date {$date} does not fall within quarter {$quarter}",
            'INVALID_POSTING_PERIOD',
            ['date' => $date, 'quarter' => $quarter],
            422
        );
    }

    public static function invalidPostingPeriodYearQuarter(int $year, int $quarter): self
    {
        return new self(
            "Invalid posting period: Q{$quarter} {$year}",
            'INVALID_POSTING_PERIOD',
            ['year' => $year, 'quarter' => $quarter],
            422
        );
    }

    public static function invalidDateRange(?string $startDate = null, ?string $endDate = null): self
    {
        return new self(
            'End date must be after start date',
            'INVALID_DATE_RANGE',
            ['start_date' => $startDate, 'end_date' => $endDate],
            422
        );
    }

    public static function invalidAmount(string $field, float $amount, ?string $reason = null): self
    {
        $message = $reason
            ? "Invalid {$field} amount: {$amount}. {$reason}"
            : "Invalid {$field} amount: {$amount}. Must be greater than 0";

        return new self(
            $message,
            'INVALID_AMOUNT',
            ['field' => $field, 'amount' => $amount],
            422
        );
    }

    public static function limitExceeded(string $field, $value, $limit): self
    {
        return new self(
            "{$field} value {$value} exceeds maximum allowed: {$limit}",
            'LIMIT_EXCEEDED',
            ['field' => $field, 'value' => $value, 'limit' => $limit],
            422
        );
    }

    public static function invalidRate(string $rateType, float $rate, float $min = 0, float $max = 100): self
    {
        return new self(
            "{$rateType} rate {$rate}% is invalid. Must be between {$min}% and {$max}%",
            'INVALID_RATE',
            ['rate_type' => $rateType, 'rate' => $rate, 'min' => $min, 'max' => $max],
            422
        );
    }

    public static function requiredFieldMissing(string $field): self
    {
        return new self(
            "Required field '{$field}' is missing",
            'REQUIRED_FIELD_MISSING',
            ['field' => $field],
            422
        );
    }

    public static function insufficientPermissions(string $action, ?string $resource = null): self
    {
        $message = $resource
            ? "You do not have permission to {$action} {$resource}"
            : "You do not have permission to {$action}";

        return new self(
            $message,
            'INSUFFICIENT_PERMISSIONS',
            ['action' => $action, 'resource' => $resource, 'user_id' => auth()->id()],
            403
        );
    }

    public static function unauthorized(string $resource = 'this resource'): self
    {
        return new self(
            "You are not authorized to access {$resource}",
            'UNAUTHORIZED',
            ['resource' => $resource, 'user_id' => auth()->id()],
            403
        );
    }

    public static function calculationError(string $reason, array $context = []): self
    {
        return new self(
            "Calculation error: {$reason}",
            'CALCULATION_ERROR',
            $context,
            422
        );
    }

    public static function totalShareMismatch(float $expected, float $actual): self
    {
        return new self(
            "Total reinsurer shares ({$actual}%) do not match expected ({$expected}%)",
            'TOTAL_SHARE_MISMATCH',
            ['expected' => $expected, 'actual' => $actual, 'difference' => abs($expected - $actual)],
            422
        );
    }

    public static function negativeBalance(string $field, float $balance): self
    {
        return new self(
            "{$field} cannot be negative: {$balance}",
            'NEGATIVE_BALANCE',
            ['field' => $field, 'balance' => $balance],
            422
        );
    }

    public static function treatyNotActive(string $treatyNo, string $status): self
    {
        return new self(
            "Treaty '{$treatyNo}' is not active. Current status: {$status}",
            'TREATY_NOT_ACTIVE',
            ['treaty_no' => $treatyNo, 'status' => $status],
            422
        );
    }

    public static function periodAlreadyClosed(int $year, int $quarter): self
    {
        return new self(
            "Posting period Q{$quarter} {$year} is already closed",
            'PERIOD_CLOSED',
            ['year' => $year, 'quarter' => $quarter],
            422
        );
    }

    public static function invalidInstallmentSequence(int $expected, int $provided): self
    {
        return new self(
            "Invalid installment number. Expected {$expected}, got {$provided}",
            'INVALID_INSTALLMENT_SEQUENCE',
            ['expected' => $expected, 'provided' => $provided],
            422
        );
    }

    public static function concurrentModification(string $entity, string $identifier): self
    {
        return new self(
            "The {$entity} '{$identifier}' was modified by another user. Please refresh and try again",
            'CONCURRENT_MODIFICATION',
            ['entity' => $entity, 'identifier' => $identifier],
            409
        );
    }

    public static function operationNotAllowed(string $operation, string $currentState): self
    {
        return new self(
            "Cannot {$operation} in current state: {$currentState}",
            'OPERATION_NOT_ALLOWED',
            ['operation' => $operation, 'state' => $currentState],
            422
        );
    }

    public static function dataIntegrityViolation(string $reason, array $context = []): self
    {
        return new self(
            "Data integrity violation: {$reason}",
            'DATA_INTEGRITY_VIOLATION',
            $context,
            422
        );
    }

    public static function orphanedRecord(string $recordType, string $identifier, string $parentType): self
    {
        return new self(
            "{$recordType} '{$identifier}' has no associated {$parentType}",
            'ORPHANED_RECORD',
            ['record_type' => $recordType, 'identifier' => $identifier, 'parent_type' => $parentType],
            422
        );
    }

    public static function create(
        string $message,
        string $errorCode = 'BUSINESS_RULE_VIOLATION',
        array $context = [],
        int $httpStatusCode = 422
    ): self {
        return new self($message, $errorCode, $context, $httpStatusCode);
    }

    public static function fromValidationErrors(array $errors): self
    {
        return new self(
            'Validation failed',
            'VALIDATION_FAILED',
            ['errors' => $errors],
            422
        );
    }
}
