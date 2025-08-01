<?php

namespace App\Models\Bd\Leads;

use Illuminate\Database\Eloquent\Model;

class Leads extends Model
{
    protected $table='leads';
    public $timestamps=false;
    public $primaryKey= false;
    public $incrementing=false;
    protected $guarded = [];


    public static function generateNextCode()
    {
        $lastLead = self::orderByDesc('code')->first();

        if ($lastLead) {
            // Get the last code value (e.g., L001) and extract the numeric part
            $lastCodeNumber = (int) substr($lastLead->code, 1);
            $nextCodeNumber = $lastCodeNumber + 1;
        } else {
            // If no leads exist, start from 1
            $nextCodeNumber = 1;
        }

        // Format the next code value (e.g., L001, L002, etc.)
        $nextCode = 'L' . str_pad($nextCodeNumber, 3, '0', STR_PAD_LEFT);

        return $nextCode;
    }
}
