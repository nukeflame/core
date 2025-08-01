<?php

namespace App\Observers\Bd;

use App\Models\Bd\PipelineOpportunity;
use App\Models\Bd\StageTransition;
use Illuminate\Support\Carbon;

class PipelineOpportunityObserver
{
    /**
     * Handle the PipelineOpportunity "created" event.
     */
    public function updating(PipelineOpportunity $opportunity)
    {
        if ($opportunity->isDirty('stage') || $opportunity->stage === $opportunity->getOriginal('stage')) {
            $oldStage = $opportunity->getOriginal('stage');
            $stageUpdatedAt = $opportunity->stage_updated_at;



            if ($oldStage && $stageUpdatedAt) {
                $duration = Carbon::now()->diffInSeconds($stageUpdatedAt);
                StageTransition::updateOrCreate(
                    [
                        'opportunity_id' => $opportunity->opportunity_id,
                        'stage' => $oldStage,
                    ],
                    [
                        'started_at' => $stageUpdatedAt,
                        'ended_at' => Carbon::now(),
                        'duration' => $duration,
                    ]
                );
            }
            $opportunity->stage_updated_at = Carbon::now();
        }
    }

    public function created(PipelineOpportunity $opportunity)
    {
        $opportunity->stage_updated_at = $opportunity->created_at;
        $opportunity->save();
    }
}
