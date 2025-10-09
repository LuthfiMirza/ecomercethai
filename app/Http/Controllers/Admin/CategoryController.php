<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $q = request('q');
        $query = Category::query()->latest();
        if ($q) {
            $query->where('name', 'like', "%$q%");
        }
        $categories = $query->paginate(10)->appends(request()->query());
        return view('admin.categories.index', compact('categories','q'));
    }

    /**
     * Show the form for creating a new category.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, string $locale)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $category = new Category($request->all());
        $category->save();
        
        return redirect()->route('admin.categories.index', ['locale' => $locale])->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified category.
     */
    public function show(string $locale, $id)
    {
        $category = Category::withCount('products')
            ->with(['products' => function ($query) {
                $query->select('id', 'name', 'price', 'stock', 'category_id')
                    ->latest()
                    ->take(10);
            }])
            ->findOrFail($id);

        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(string $locale, $id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
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
        ]);
        
        $category = Category::findOrFail($id);
        $category->update($request->all());
        
        return redirect()->route('admin.categories.index', ['locale' => $locale])->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $locale, $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        
        return redirect()->route('admin.categories.index', ['locale' => $locale])->with('success', 'Category deleted successfully.');
    }
}
