<?php

namespace App\Models;

use App\Models\implementationChecklist;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class implementationobjectives extends Model
{
    use HasFactory;

    protected $table = 'implementation_objectives'; 


    public function checklist()
    {
        return $this->belongsTo(implementationChecklist::class, 'task_slug', 'task_slug');
    }

    

}
