<?php

namespace App\Imports;

use App\Models\Order;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class OrdersImport implements ToCollection
{
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        // Expect header row: id | status
        $isHeader = true;
        foreach ($rows as $row) {
            if ($isHeader) { $isHeader = false; continue; }
            $id = $row[0] ?? null;
            $status = $row[1] ?? null;
            if ($id && $status) {
                Order::whereKey($id)->update(['status' => $status]);
            }
        }
    }
}

