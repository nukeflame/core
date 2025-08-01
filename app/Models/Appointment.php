<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'appointment_date',
        'appointment_time',
        'name',
        'email',
        'purpose',
        'confirmation_code',
        'status',
    ];

    /**
     * Get the formatted date for the appointment.
     *
     * @return string
     */
    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->appointment_date)->format('F j, Y');
    }

    /**
     * Scope a query to only include confirmed appointments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope a query to only include upcoming appointments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now()->format('Y-m-d'));
    }
}
