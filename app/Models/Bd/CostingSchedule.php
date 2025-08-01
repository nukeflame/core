<?php

namespace App\Models;

use App\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CostingSchedule extends Model
{
    use HasFactory;
    protected $fillable = ['client_id', 'document'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
