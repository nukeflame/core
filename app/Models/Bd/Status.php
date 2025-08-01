<?php

namespace App\Models\Bd;

use App\Models\User;
use App\Models\Bd\Client;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table='status';

    protected $primaryKey = 'status_code';

    public $incrementing = false;

    protected $keyType = 'string';
        
    protected $fillable = [ 'status_code', 'status_description','created_by','changed_by'];

    public function users()
    {
        return $this -> hasMany(User::class);
    }

    public function clients()
    {
        return $this -> hasMany(Client::class);
    }
}
  