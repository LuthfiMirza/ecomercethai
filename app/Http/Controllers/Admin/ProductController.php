<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = Product::with('category');
        $q = request('q');
        $category_id = request('category_id');

        if ($q) {
            $query->where('name', 'like', "%$q%");
        }
        if ($category_id) {
            $query->where('category_id', $category_id);
        }

        $products = $query->paginate(10)->appends(request()->query());
        $categories = Category::orderBy('name')->get();
        return view('admin.products.index', compact('products','categories','q','category_id'));
    }

    /**
     * Show the form for creating a new product.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, string $locale)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
                'max:999999999999999999.99',
            ],
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|max:4096',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|max:4096',
            'colors' => 'nullable|string',
            'color_galleries' => 'nullable|array',
            'color_galleries.*.color_key' => 'required_with:color_galleries.*.images|string|max:80',
            'color_galleries.*.images' => 'nullable|array',
            'color_galleries.*.images.*' => 'image|max:4096',
        ]);
        
        $payload = $request->only([
            'name',
            'description',
            'price',
            'stock',
            'category_id',
            'status',
        ]);

        $payload['is_active'] = $request->status === 'active';
        $colors = $this->normalizeColors($request->input('colors'));
        $payload['colors'] = $colors ?: null;

        if ($request->hasFile('image')) {
            $payload['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($payload);
        $this->storeGalleryImages($product, $request->file('gallery_images', []));
        $this->storeColorGalleries($product, $request->input('color_galleries', []), $request->file('color_galleries', []));
        $this->ensurePrimaryForEachColor($product);
        
        return redirect()->route('admin.products.index', ['locale' => $locale])->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product.
     */
    public function show(string $locale, $id)
    {
        $product = Product::with(['category'])->findOrFail($id);
        $totalSold = (int) $product->orderItems()->sum('quantity');

        return view('admin.products.show', compact('product', 'totalSold'));
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(string $locale, $id)
    {
        $product = Product::with('images')->findOrFail($id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $locale, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
                'max:999999999999999999.99',
            ],
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|max:4096',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|max:4096',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'integer',
            'colors' => 'nullable|string',
            'color_galleries' => 'nullable|array',
            'color_galleries.*.color_key' => 'required_with:color_galleries.*.images|string|max:80',
            'color_galleries.*.images' => 'nullable|array',
            'color_galleries.*.images.*' => 'image|max:4096',
            'existing_images' => 'nullable|array',
            'existing_images.*.color_key' => 'nullable|string|max:80',
            'color_primary' => 'nullable|array',
            'color_primary.*' => 'integer',
            'color_primary_lookup' => 'nullable|array',
        ]);
        
        $product = Product::findOrFail($id);
        
        $payload = $request->only([
            'name',
            'description',
            'price',
            'stock',
            'category_id',
            'status',
        ]);

        $payload['is_active'] = $request->status === 'active';
        $colors = $this->normalizeColors($request->input('colors'));
        $payload['colors'] = $colors ?: null;

        if ($request->hasFile('image')) {
            if ($product->image && ! Str::startsWith($product->image, ['http://', 'https://'])) {
                Storage::disk('public')->delete($product->image);
            }

            $payload['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($payload);
        $this->removeGalleryImages($product, $request->input('remove_images', []));
        $this->storeGalleryImages($product, $request->file('gallery_images', []));
        $this->storeColorGalleries($product, $request->input('color_galleries', []), $request->file('color_galleries', []));
        $this->syncExistingImages(
            $product,
            $request->input('existing_images', []),
            $request->input('color_primary', []),
            $request->input('color_primary_lookup', [])
        );
        $this->ensurePrimaryForEachColor($product);
        
        return redirect()->route('admin.products.index', ['locale' => $locale])->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $locale, $id)
    {
        $product = Product::with('images')->findOrFail($id);
        if ($product->image && ! Str::startsWith($product->image, ['http://', 'https://'])) {
            Storage::disk('public')->delete($product->image);
        }

        foreach ($product->images as $image) {
            $filePath = $image->file_path ?: $image->path;
            if ($filePath && ! Str::startsWith($filePath, ['http://', 'https://'])) {
                Storage::disk('public')->delete($filePath);
            }
        }

        $product->delete();
        
        return redirect()->route('admin.products.index', ['locale' => $locale])->with('success', 'Product deleted successfully.');
    }

    private function normalizeColors(?string $input): array
    {
        if (! $input) {
            return [];
        }

        return collect(preg_split('/[,\\n]+/', $input))
            ->map(fn ($color) => trim((string) $color))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function normalizeColorKey(?string $color): ?string
    {
        $value = trim((string) $color);

        return $value === '' ? null : $value;
    }

    private function storeColorGalleries(Product $product, array $colorGalleries, array $filePayload = []): void
    {
        foreach ($colorGalleries as $index => $gallery) {
            $colorKey = $this->normalizeColorKey($gallery['color_key'] ?? null);
            $files = data_get($filePayload, "{$index}.images", []);

            if (! is_array($files)) {
                $files = [$files];
            }

            $this->storeGalleryImages($product, $files, $colorKey);
        }
    }

    private function syncExistingImages(Product $product, array $imagesData, array $primarySelections, array $primaryLookup): void
    {
        if (! empty($imagesData)) {
            foreach ($imagesData as $imageId => $payload) {
                $colorKey = array_key_exists('color_key', (array) $payload)
                    ? $this->normalizeColorKey($payload['color_key'] ?? null)
                    : null;

                if ($colorKey !== null || array_key_exists('color_key', (array) $payload)) {
                    $product->images()->where('id', (int) $imageId)->update(['color_key' => $colorKey]);
                }
            }
        }

        foreach ($primarySelections as $hash => $imageId) {
            $colorKey = $primaryLookup[$hash] ?? null;
            $this->setPrimaryImage($product, (int) $imageId, $colorKey);
        }
    }

    private function storeGalleryImages(Product $product, array $files, ?string $colorKey = null): void
    {
        $files = array_filter($files);

        if (empty($files)) {
            return;
        }

        $sortOrder = (int) ($product->images()->max('sort_order') ?? 0);
        $colorKey = $this->normalizeColorKey($colorKey);
        $hasPrimary = $this->colorHasPrimary($product, $colorKey);

        foreach ($files as $index => $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $path = $file->store('products/gallery', 'public');
            $isPrimary = ! $hasPrimary && $index === 0;

            $attributes = [
                'color_key' => $colorKey,
                'file_path' => $path,
                'is_primary' => $isPrimary,
                'sort_order' => $sortOrder + $index + 1,
            ];

            if (Schema::hasColumn('product_images', 'path')) {
                $attributes['path'] = $path;
            }

            $product->images()->create($attributes);

            if ($isPrimary) {
                $hasPrimary = true;
            }
        }
    }

    private function removeGalleryImages(Product $product, array $imageIds): void
    {
        $ids = collect($imageIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();

        if ($ids->isEmpty()) {
            return;
        }

        $images = $product->images()->whereIn('id', $ids)->get();

        foreach ($images as $image) {
            $filePath = $image->file_path ?: $image->path;
            if ($filePath && ! Str::startsWith($filePath, ['http://', 'https://'])) {
                Storage::disk('public')->delete($filePath);
            }

            $image->delete();
        }
    }

    private function setPrimaryImage(Product $product, int $imageId, ?string $colorKey = null): void
    {
        $image = $product->images()->where('id', $imageId)->first();
        if (! $image) {
            return;
        }

        $colorKey = $this->normalizeColorKey($colorKey ?? $image->color_key);

        $product->images()
            ->when(
                $colorKey === null,
                fn ($query) => $query->whereNull('color_key'),
                fn ($query) => $query->where('color_key', $colorKey)
            )
            ->update(['is_primary' => false]);

        $image->update([
            'color_key' => $colorKey,
            'is_primary' => true,
        ]);
    }

    private function colorHasPrimary(Product $product, ?string $colorKey): bool
    {
        return $product->images()
            ->when(
                $colorKey === null,
                fn ($query) => $query->whereNull('color_key'),
                fn ($query) => $query->where('color_key', $colorKey)
            )
            ->where('is_primary', true)
            ->exists();
    }

    private function ensurePrimaryForEachColor(Product $product): void
    {
        $colors = $product->images()->select('color_key')->distinct()->pluck('color_key');

        foreach ($colors as $color) {
            $this->ensurePrimaryForColor($product, $color);
        }
    }

    private function ensurePrimaryForColor(Product $product, ?string $colorKey): void
    {
        $colorKey = $this->normalizeColorKey($colorKey);

        $query = $product->images()
            ->when(
                $colorKey === null,
                fn ($builder) => $builder->whereNull('color_key'),
                fn ($builder) => $builder->where('color_key', $colorKey)
            );

        $checkQuery = clone $query;

        if ($checkQuery->where('is_primary', true)->exists()) {
            return;
        }

        $firstImage = $query->orderBy('sort_order')->orderBy('id')->first();

        if ($firstImage) {
            $firstImage->update(['is_primary' => true]);
        }
    }

    /**
     * Export products to CSV.
     */
    public function exportCsv(): StreamedResponse
    {
        $fileName = 'products_'.now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Category', 'Price', 'Stock', 'Status']);
            Product::with('category')->chunk(500, function ($chunk) use ($handle) {
                foreach ($chunk as $p) {
                    fputcsv($handle, [
                        $p->id,
                        $p->name,
                        optional($p->category)->name,
                        $p->price,
                        $p->stock,
                        $p->status,
                    ]);
                }
            });
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export products to Excel (requires maatwebsite/excel).
     */
    public function exportExcel()
    {
        return Excel::download(new ProductsExport, 'products_'.now()->format('Ymd_His').'.xlsx');
    }

    /**
     * Export products list to PDF (brand-styled)
     */
    public function exportPdf()
    {
        $products = Product::with('category')->get();
        $pdf = Pdf::loadView('admin.products.pdf-list', compact('products'));
        return $pdf->download('products_'.now()->format('Ymd_His').'.pdf');
    }

    /**
     * Import products (basic upsert) from Excel/CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls',
        ]);

        $extension = strtolower($request->file('file')->getClientOriginalExtension());
        if (in_array($extension, ['xlsx', 'xls'])) {
            Excel::import(new ProductsImport, $request->file('file'));
        } else {
            $path = $request->file('file')->getRealPath();
            if (($handle = fopen($path, 'r')) !== false) {
                // Expect header: name,description,price,stock,category_id,status
                fgetcsv($handle);
                while (($row = fgetcsv($handle)) !== false) {
                    [$name,$description,$price,$stock,$category_id,$status] = $row + [null,null,null,null,null,null];
                    if ($name) {
                        Product::updateOrCreate(
                            ['name' => $name],
                            compact('description','price','stock','category_id','status')
                        );
                    }
                }
                fclose($handle);
            }
        }

        return back()->with('success', 'Products imported successfully.');
    }
}
