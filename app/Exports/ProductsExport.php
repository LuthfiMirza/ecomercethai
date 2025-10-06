<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Product::with('category')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Category', 'Price', 'Stock', 'Status'];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            optional($product->category)->name,
            $product->price,
            $product->stock,
            $product->status,
        ];
    }
}

