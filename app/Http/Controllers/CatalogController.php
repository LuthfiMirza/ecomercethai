<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->where('is_active', true);

        $categoryValues = collect(Arr::wrap($request->input('category')))
            ->filter(fn ($value) => filled($value))
            ->unique()
            ->values();

        if ($categoryValues->isNotEmpty()) {
            $slugValues = $categoryValues
                ->filter(fn ($value) => ! is_numeric($value))
                ->values();
            $idValues = $categoryValues
                ->filter(fn ($value) => is_numeric($value))
                ->map(fn ($value) => (int) $value)
                ->values();

            $matchedCategoryIds = Category::query()
                ->where(function ($builder) use ($slugValues, $idValues) {
                    if ($slugValues->isNotEmpty()) {
                        $builder->whereIn('slug', $slugValues);
                    }

                    if ($idValues->isNotEmpty()) {
                        $slugValues->isNotEmpty()
                            ? $builder->orWhereIn('id', $idValues)
                            : $builder->whereIn('id', $idValues);
                    }
                })
                ->pluck('id')
                ->unique()
                ->filter()
                ->values();

            if ($matchedCategoryIds->isNotEmpty()) {
                $query->whereIn('category_id', $matchedCategoryIds);
            } else {
                $query->whereRaw('0 = 1');
            }
        }

        $brandValues = collect(Arr::wrap($request->input('brand')))
            ->filter(fn ($value) => filled($value))
            ->unique()
            ->values();

        if ($brandValues->isNotEmpty()) {
            $query->whereIn('brand', $brandValues->all());
        }

        $minPrice = $request->input('min_price', $request->input('min'));
        if (is_numeric($minPrice)) {
            $query->where('price', '>=', (float) $minPrice);
        }

        $maxPrice = $request->input('max_price', $request->input('max'));
        if (is_numeric($maxPrice)) {
            $query->where('price', '<=', (float) $maxPrice);
        }

        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        $searchTerm = $request->input('search', $request->input('q'));

        if (filled($searchTerm)) {
            $search = trim((string) $searchTerm);
            if ($search !== '') {
                $query->where(function ($builder) use ($search) {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%");
                });
            }
        }

        $sortBy = $request->get('sort', 'newest');
        switch ($sortBy) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'popularity':
            case 'newest':
            default:
                $query->orderByDesc('created_at');
                break;
        }

        $products = $query
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();
        $brands = Product::query()
            ->where('is_active', true)
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand');

        return view('pages.catalog', compact('products', 'categories', 'brands'));
    }

    public function show(string $locale, $slug)
    {
        $product = Product::with(['category', 'images'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        return view('pages.product', compact('product', 'relatedProducts'));
    }
}
