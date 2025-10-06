<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductsImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $header = true;
        foreach ($rows as $row) {
            if ($header) { $header = false; continue; }
            $name = $row[0] ?? null;
            $description = $row[1] ?? null;
            $price = $row[2] ?? null;
            $stock = $row[3] ?? null;
            $category_id = $row[4] ?? null;
            $status = $row[5] ?? 'active';

            if ($name) {
                Product::updateOrCreate(
                    ['name' => $name],
                    compact('description','price','stock','category_id','status')
                );
            }
        }
    }
}

