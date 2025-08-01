<?php

namespace App\Models;

use App\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InsuredDetail extends Model
{
    use HasFactory;
    protected $fillable = ['client_id', 'description', 'attachment'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    
}
