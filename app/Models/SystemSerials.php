<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSerials extends Model
{
    use HasFactory;

    protected $table = 'system_serials';
    public $timestamps = true;
    public $primaryKey = 'type';
    public $incrementing = false;
    protected $guarded = [];

    public static function nextSerial($type)
    {
        $serial = SystemSerials::where('type', $type)->first();

        $tran_no = (int) $serial->serial_no;
        $serial->update(['serial_no' => $tran_no + 1]);

        return $tran_no;
    }
}
