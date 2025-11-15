<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'is_active',
        'category_id',
        'image',
        'status',
        'brand',
        'colors',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'stock' => 'integer',
        'colors' => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                return $this->image;
            }

            if (Storage::disk('public')->exists($this->image)) {
                return Storage::disk('public')->url($this->image);
            }
        }

        $primary = $this->relationLoaded('images')
            ? $this->images->first(fn ($image) => $image->is_primary) ?? $this->images->first()
            : $this->images()->orderByDesc('is_primary')->orderBy('sort_order')->orderBy('id')->first();

        return $primary?->url;
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true)->orderBy('sort_order')->orderBy('id');
    }

    public function getGalleryImageUrlsAttribute(): array
    {
        $images = $this->relationLoaded('images') ? $this->images : $this->images()->get();

        return $images
            ->map(fn (ProductImage $image) => $image->url)
            ->filter()
            ->values()
            ->all();
    }

    public function getAvailableColorsAttribute(): array
    {
        return collect($this->colors ?? [])
            ->map(fn ($color) => trim((string) $color))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
