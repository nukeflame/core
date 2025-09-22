<?php

namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Prospects extends Model
{
    protected $table = 'pipeline_opportunities';

    protected $primaryKey = false;

    protected $guarded = [];

    public $incrementing = false;

    public $timestamps = true;

    public static function generateNextCode($lead_year = null)
    {
        $pipeYear = DB::table('pipelines')->where('id', $lead_year)->first();

        $currentYear = $pipeYear->year ?? date('Y');
        $lastOpportunity = self::where('opportunity_id', 'LIKE', "FAC-{$currentYear}-%")
            ->orderByDesc('opportunity_id')
            ->first();

        if ($lastOpportunity) {
            $lastNumber = (int) substr($lastOpportunity->opportunity_id, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $nextOpportunityId = 'FAC-' . $currentYear . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return $nextOpportunityId;
    }
}
