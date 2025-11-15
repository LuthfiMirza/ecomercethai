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
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|max:4096',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|max:4096',
            'colors' => 'nullable|string',
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
        $this->ensurePrimaryGalleryImage($product);
        
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
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|max:4096',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|max:4096',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'integer',
            'colors' => 'nullable|string',
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
        $this->ensurePrimaryGalleryImage($product);
        
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
            if ($image->path && ! Str::startsWith($image->path, ['http://', 'https://'])) {
                Storage::disk('public')->delete($image->path);
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

    private function storeGalleryImages(Product $product, array $files): void
    {
        $files = array_filter($files);

        if (empty($files)) {
            return;
        }

        $sortOrder = (int) ($product->images()->max('sort_order') ?? 0);
        $hasPrimary = $product->images()->where('is_primary', true)->exists();

        foreach ($files as $index => $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $path = $file->store('products/gallery', 'public');
            $isPrimary = ! $hasPrimary && $index === 0 && ! $product->image;

            $product->images()->create([
                'path' => $path,
                'is_primary' => $isPrimary,
                'sort_order' => $sortOrder + $index + 1,
            ]);

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
            if ($image->path && ! Str::startsWith($image->path, ['http://', 'https://'])) {
                Storage::disk('public')->delete($image->path);
            }

            $image->delete();
        }
    }

    private function ensurePrimaryGalleryImage(Product $product): void
    {
        $hasPrimary = $product->images()->where('is_primary', true)->exists();

        if ($hasPrimary) {
            return;
        }

        $firstImage = $product->images()->orderBy('sort_order')->orderBy('id')->first();

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
