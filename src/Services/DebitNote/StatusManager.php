<?php

namespace Nukeflame\Core\Services\DebitNote;

use App\Exceptions\BusinessRuleException;
use App\Models\DebitNote;
use App\Models\TransactionLog;
use Illuminate\Support\Facades\DB;

class StatusManager
{
    public function __construct(
        private readonly TransactionLog $transactionLogger
    ) {}

    public function submit(DebitNote $debitNote): DebitNote
    {
        return DB::transaction(function () use ($debitNote) {
            $debitNote = DebitNote::lockForUpdate()->findOrFail($debitNote->id);

            if (! $debitNote->canSubmit()) {
                throw BusinessRuleException::invalidStatusTransition(
                    $debitNote->status,
                    DebitNote::STATUS_PENDING
                );
            }

            // Ensure has items
            if ($debitNote->items()->count() === 0) {
                throw BusinessRuleException::missingItems();
            }

            $oldValues = $debitNote->toArray();

            $debitNote->update([
                'status' => DebitNote::STATUS_PENDING,
                'submitted_at' => now(),
                'submitted_by' => auth()->id(),
            ]);

            // $this->transactionLogger->log($debitNote, 'SUBMIT', $oldValues);

            return $debitNote->fresh();
        });
    }

    public function approve(DebitNote $debitNote): DebitNote
    {
        return DB::transaction(function () use ($debitNote) {
            $debitNote = DebitNote::lockForUpdate()->findOrFail($debitNote->id);

            if (! $debitNote->canApprove()) {
                throw BusinessRuleException::invalidStatusTransition(
                    $debitNote->status,
                    DebitNote::STATUS_APPROVED
                );
            }

            // Prevent self-approval
            if ($debitNote->submitted_by === auth()->id()) {
                throw BusinessRuleException::selfApproval();
            }

            $oldValues = $debitNote->toArray();

            $debitNote->update([
                'status' => DebitNote::STATUS_APPROVED,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);

            // $this->transactionLogger->log($debitNote, 'APPROVE', $oldValues);

            return $debitNote->fresh();
        });
    }

    /**
     * Reject debit note
     */
    public function reject(DebitNote $debitNote, string $reason): DebitNote
    {
        return DB::transaction(function () use ($debitNote, $reason) {
            $debitNote = DebitNote::lockForUpdate()->findOrFail($debitNote->id);

            if (! $debitNote->canApprove()) {
                throw BusinessRuleException::invalidStatusTransition(
                    $debitNote->status,
                    DebitNote::STATUS_REJECTED
                );
            }

            // Validate rejection reason
            if (empty(trim($reason))) {
                throw new \InvalidArgumentException('Rejection reason is required');
            }

            $oldValues = $debitNote->toArray();

            $debitNote->update([
                'status' => DebitNote::STATUS_REJECTED,
                'rejected_at' => now(),
                'rejected_by' => auth()->id(),
                'rejection_reason' => $reason,
            ]);

            // $this->transactionLogger->log($debitNote, 'REJECT', $oldValues);

            return $debitNote->fresh();
        });
    }

    /**
     * Revert rejected debit note to draft
     */
    public function revertToDraft(DebitNote $debitNote): DebitNote
    {
        return DB::transaction(function () use ($debitNote) {
            $debitNote = DebitNote::lockForUpdate()->findOrFail($debitNote->id);

            if ($debitNote->status !== DebitNote::STATUS_REJECTED) {
                throw BusinessRuleException::invalidStatusTransition(
                    $debitNote->status,
                    DebitNote::STATUS_DRAFT
                );
            }

            $oldValues = $debitNote->toArray();

            $debitNote->update([
                'status' => DebitNote::STATUS_DRAFT,
                'rejected_at' => null,
                'rejected_by' => null,
                'rejection_reason' => null,
            ]);

            // $this->transactionLogger->log($debitNote, 'REVERT_DRAFT', $oldValues);

            return $debitNote->fresh();
        });
    }

    /**
     * Cancel debit note
     */
    public function cancel(DebitNote $debitNote, string $reason): DebitNote
    {
        return DB::transaction(function () use ($debitNote, $reason) {
            $debitNote = DebitNote::lockForUpdate()->findOrFail($debitNote->id);

            if (! $debitNote->canCancel()) {
                throw BusinessRuleException::invalidStatusTransition(
                    $debitNote->status,
                    DebitNote::STATUS_CANCELLED
                );
            }

            // Validate cancellation reason
            if (empty(trim($reason))) {
                throw new \InvalidArgumentException('Cancellation reason is required');
            }

            $oldValues = $debitNote->toArray();

            $debitNote->update([
                'status' => DebitNote::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'cancelled_by' => auth()->id(),
                'cancellation_reason' => $reason,
            ]);

            // $this->transactionLogger->log($debitNote, 'CANCEL', $oldValues);

            return $debitNote->fresh();
        });
    }

    /**
     * Check if status transition is valid
     */
    public function canTransition(DebitNote $debitNote, string $toStatus): bool
    {
        $validTransitions = [
            DebitNote::STATUS_DRAFT => [
                DebitNote::STATUS_PENDING,
            ],
            DebitNote::STATUS_PENDING => [
                DebitNote::STATUS_APPROVED,
                DebitNote::STATUS_REJECTED,
            ],
            DebitNote::STATUS_REJECTED => [
                DebitNote::STATUS_DRAFT,
            ],
            DebitNote::STATUS_APPROVED => [
                DebitNote::STATUS_POSTED,
                DebitNote::STATUS_CANCELLED,
            ],
            DebitNote::STATUS_POSTED => [
                DebitNote::STATUS_CANCELLED,
            ],
        ];

        $currentStatus = $debitNote->status;

        return isset($validTransitions[$currentStatus])
            && in_array($toStatus, $validTransitions[$currentStatus]);
    }

    /**
     * Get available actions for current status
     */
    public function getAvailableActions(DebitNote $debitNote): array
    {
        $actions = [];

        if ($debitNote->canSubmit()) {
            $actions[] = 'submit';
        }

        if ($debitNote->canApprove()) {
            $actions[] = 'approve';
            $actions[] = 'reject';
        }

        if ($debitNote->canPost()) {
            $actions[] = 'post';
        }

        if ($debitNote->canCancel()) {
            $actions[] = 'cancel';
        }

        if ($debitNote->status === DebitNote::STATUS_REJECTED) {
            $actions[] = 'revert_to_draft';
        }

        if ($debitNote->isEditable()) {
            $actions[] = 'edit';
            $actions[] = 'delete';
        }

        return $actions;
    }
}
