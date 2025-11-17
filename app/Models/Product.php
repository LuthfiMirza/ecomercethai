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

        static::creating(function (self $product) {
            $product->slug = static::generateUniqueSlug($product, $product->slug ?: $product->name);
        });

        static::updating(function (self $product) {
            if ($product->isDirty('slug') || ($product->isDirty('name') && empty($product->slug))) {
                $product->slug = static::generateUniqueSlug($product, $product->slug ?: $product->name);
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

    public function getColorImageMapAttribute(): array
    {
        $images = $this->relationLoaded('images') ? $this->images : $this->images()->get();

        return $images
            ->groupBy(fn (ProductImage $image) => $image->color_key ?: '__default')
            ->map(fn ($group) => $group
                ->sortBy([
                    ['is_primary', 'desc'],
                    ['sort_order', 'asc'],
                    ['id', 'asc'],
                ])
                ->values())
            ->all();
    }

    public function resolveImageForColor(?string $colorKey): ?ProductImage
    {
        $colorKey = $this->normalizeColorKey($colorKey);
        $images = $this->relationLoaded('images') ? $this->images : $this->images()->get();

        $filtered = $images->filter(function (ProductImage $image) use ($colorKey) {
            return $colorKey === null ? $image->color_key === null : $image->color_key === $colorKey;
        });

        if ($filtered->isEmpty()) {
            $filtered = $images;
        }

        return $filtered
            ->sortBy([
                ['is_primary', 'desc'],
                ['sort_order', 'asc'],
                ['id', 'asc'],
            ])
            ->first();
    }

    public function getAvailableColorsAttribute(): array
    {
        $imageColors = $this->relationLoaded('images')
            ? $this->images->pluck('color_key')
            : $this->images()->select('color_key')->pluck('color_key');

        return collect($this->colors ?? [])
            ->merge($imageColors)
            ->map(fn ($color) => trim((string) $color))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected function normalizeColorKey(?string $color): ?string
    {
        $value = trim((string) $color);

        return $value === '' ? null : $value;
    }

    protected static function generateUniqueSlug(self $product, ?string $value): string
    {
        $base = Str::slug((string) $value) ?: 'product';
        $query = static::query()->where('slug', 'like', $base . '%');

        if ($product->exists) {
            $query->where('id', '!=', $product->id);
        }

        $existing = $query->pluck('slug')->filter()->values();

        if (! $existing->contains($base)) {
            return $base;
        }

        $suffix = 1;
        while ($existing->contains($base . '-' . $suffix)) {
            $suffix++;
        }

        return $base . '-' . $suffix;
    }
}
