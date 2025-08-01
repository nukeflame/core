<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission as ModelsPermission;

class Permission extends ModelsPermission
{
    use HasFactory;

    public function systemProcesses()
    {
        return $this->hasMany(SystemProcess::class);
    }

    public function systemActions()
    {
        return $this->hasMany(SystemProcessAction::class);
    }
}
