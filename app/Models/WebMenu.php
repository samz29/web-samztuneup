<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebMenu extends Model
{
    protected $fillable = [
        'title',
        'url',
        'icon',
        'parent_id',
        'sort_order',
        'is_active',
        'target',
        'location',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(WebMenu::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(WebMenu::class, 'parent_id')->ordered();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }
}
