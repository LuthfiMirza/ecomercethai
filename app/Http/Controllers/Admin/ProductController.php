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
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:active,inactive',
        ]);
        
        $product = new Product($request->all());
        $product->save();
        
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
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
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:active,inactive',
        ]);
        
        $product = Product::findOrFail($id);
        $product->update($request->all());
        
        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
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
