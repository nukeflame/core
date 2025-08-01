<?php

namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Model;

class Prospects extends Model
{
    protected $table = 'pipeline_opportunities';

    protected $primaryKey =false;

    protected $guarded = [];

    public $incrementing = false;

    public $timestamps = true;


    public static function generateNextCode()
    {
        $lastLead = self::orderByDesc('opportunity_id')->first();

        if ($lastLead) {
            $lastCodeNumber = (int) substr($lastLead->opportunity_id, 1);
            
            $nextCodeNumber = $lastCodeNumber + 1;
        } else {
            $nextCodeNumber = 1;
        }

        $nextCode = 'L' . str_pad($nextCodeNumber, 3, '0', STR_PAD_LEFT);

        return $nextCode;
    }

}
