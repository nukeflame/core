<?php

namespace App\Models\Bd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationChecklist extends Model
{
    use HasFactory;
    protected $table = 'treaty_operation_checklists';
    protected $fillable = ['name', 'created_by','created_by', 'updated_by'];

    /**
     * Get the user that created the checklist.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }
}
