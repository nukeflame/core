<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SettingsMenu extends Model
{
    use HasFactory;

    protected $table = 'settings_menu';
    public $timestamps = true;
    public $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = ['title', 'route', 'parent_id'];

    public function children()
    {
        return $this->hasMany(SettingsMenu::class, 'parent_id');
    }

    /**
     * Get the parent that owns the SettingsMenu
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(SettingsMenu::class, 'parent_id', 'id');
    }
}
