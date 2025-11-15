<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'path',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getUrlAttribute(): ?string
    {
        if (! $this->path) {
            return null;
        }

        if (filter_var($this->path, FILTER_VALIDATE_URL)) {
            return $this->path;
        }

        if (Storage::disk('public')->exists($this->path)) {
            return Storage::disk('public')->url($this->path);
        }

        return null;
    }
}
