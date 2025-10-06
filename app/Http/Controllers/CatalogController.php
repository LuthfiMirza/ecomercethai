<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_active', true);

        // Filter by category
        if ($request->filled('category')) {
            $categoryParam = $request->category;
            if (is_numeric($categoryParam)) {
                $query->where('category_id', $categoryParam);
            } else {
                $categoryId = Category::where('slug', $categoryParam)->value('id');
                if ($categoryId) {
                    $query->where('category_id', $categoryId);
                }
            }
        }

        // Filter by brand
        if ($request->has('brand') && $request->brand) {
            $query->where('brand', $request->brand);
        }

        // Filter by price range
        if ($request->has('min_price') && $request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // Sort
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
            case 'newest':
            default:
                $query->orderByDesc('created_at');
                break;
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::all();
        $brands = Product::where('is_active', true)
            ->distinct()
            ->pluck('brand')
            ->filter();

        return view('pages.catalog', compact('products', 'categories', 'brands'));
    }

    public function show($slug)
    {
        $product = Product::with('category')
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
