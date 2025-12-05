<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class TreatyDocument extends Model
{
    use HasFactory;

    protected $table = 'treaty_documents';

    protected $fillable = [
        'endorsement_no',
        'cover_no',
        'document_type',
        'reference',
        'description',
        'file_path',
        'generated_by',
        'status',
        'generated_date',
    ];

    protected $casts = [
        'generated_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by', 'user_name');
    }

    public function cover(): BelongsTo
    {
        return $this->belongsTo(CoverRegister::class, 'endorsment_no', 'endorsment_no');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('document_type', $type);
    }

    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    public function fileExists(): bool
    {
        return $this->file_path && Storage::disk('public')->exists($this->file_path);
    }
}
