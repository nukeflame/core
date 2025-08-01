<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\implementationobjectives;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class implementationChecklist extends Model
{
    use HasFactory;
    protected $table = 'implementation_checklist';
    
     // Define the relationship with ImplementationObjectives
     public function objectives()
     {
         return $this->hasMany(implementationobjectives::class, 'task_slug', 'task_slug');
     }
}
