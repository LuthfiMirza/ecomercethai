<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

// Optional: Excel/PDF dependencies (installed via composer)
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrdersExport;
use App\Imports\OrdersImport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\CouponService;

class OrderController extends Controller
{
    private const STATUS_OPTIONS = ['pending','processing','shipped','completed','cancelled'];
    private const PAYMENT_STATUS_OPTIONS = ['pending','processing','paid'];

    /**
     * Display a listing of the orders.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = Order::with('user')->latest();

        $q = request('q');
        $status = request('status');
        $from = request('from');
        $to = request('to');

        if ($q) {
            $query->where(function($qb) use ($q) {
                $qb->where('id', $q)
                   ->orWhereHas('user', function($u) use ($q) {
                        $u->where('name', 'like', "%$q%")
                          ->orWhere('email', 'like', "%$q%");
                   });
            });
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $orders = $query->paginate(10)->appends(request()->query());
        $statusOptions = self::STATUS_OPTIONS;
        $paymentStatusOptions = self::PAYMENT_STATUS_OPTIONS;

        return view('admin.orders.index', compact('orders', 'q', 'status', 'from', 'to', 'statusOptions', 'paymentStatusOptions'));
    }

    /**
     * Display the specified order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $locale, $id)
    {
        $order = Order::with('user', 'orderItems.product')->findOrFail($id);
        $statusOptions = self::STATUS_OPTIONS;
        $paymentStatusOptions = self::PAYMENT_STATUS_OPTIONS;

        return view('admin.orders.show', compact('order', 'statusOptions', 'paymentStatusOptions'));
    }

    /**
     * Show the form for editing an order.
     */
    public function edit(string $locale, $id)
    {
        $order = Order::with('user', 'orderItems.product')->findOrFail($id);
        $statusOptions = self::STATUS_OPTIONS;
        $paymentStatusOptions = self::PAYMENT_STATUS_OPTIONS;

        return view('admin.orders.edit', compact('order', 'statusOptions', 'paymentStatusOptions'));
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, string $locale, $id)
    {
        $order = Order::with('orderItems')->findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', Rule::in(self::STATUS_OPTIONS)],
            'payment_status' => ['required', Rule::in(self::PAYMENT_STATUS_OPTIONS)],
            'payment_method' => ['required', 'string', 'max:100'],
            'shipping_address' => ['required', 'string', 'max:2000'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
            'remove_payment_proof' => ['nullable', 'boolean'],
        ]);

        $data = [
            'status' => $validated['status'],
            'payment_status' => $validated['payment_status'],
            'payment_method' => $validated['payment_method'],
            'shipping_address' => $validated['shipping_address'],
            'total_amount' => $validated['total_amount'],
            'discount_amount' => $validated['discount_amount'] ?? 0,
        ];

        if ($data['payment_status'] === 'paid') {
            $data['payment_verified_at'] = $order->payment_verified_at ?? now();
        } else {
            $data['payment_verified_at'] = null;
        }

        if ($request->boolean('remove_payment_proof')) {
            if ($order->payment_proof_path) {
                Storage::disk('public')->delete($order->payment_proof_path);
            }
            $data['payment_proof_path'] = null;
        } elseif ($request->hasFile('payment_proof')) {
            if ($order->payment_proof_path) {
                Storage::disk('public')->delete($order->payment_proof_path);
            }
            $data['payment_proof_path'] = $request->file('payment_proof')->store('payment-proofs', 'public');
        }

        $order->update($data);

        return redirect()
            ->route('admin.orders.show', ['locale' => $locale, 'id' => $order->id])
            ->with('success', 'Order updated successfully.');
    }

    /**
     * Update only the order status.
     */
    public function updateStatus(Request $request, string $locale, $id)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(self::STATUS_OPTIONS)],
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $validated['status']]);

        return back()->with('success', 'Order status updated successfully.');
    }

    /**
     * Update only the payment status for an order.
     */
    public function updatePaymentStatus(Request $request, string $locale, $id)
    {
        $validated = $request->validate([
            'payment_status' => ['required', Rule::in(self::PAYMENT_STATUS_OPTIONS)],
        ]);

        $order = Order::findOrFail($id);
        $data = ['payment_status' => $validated['payment_status']];

        if ($validated['payment_status'] === 'paid') {
            $data['payment_verified_at'] = $order->payment_verified_at ?? now();
        } else {
            $data['payment_verified_at'] = null;
        }

        $order->update($data);

        return back()->with('success', 'Payment status updated successfully.');
    }

    /**
     * Export orders to CSV (no package needed).
     */
    public function exportCsv(): StreamedResponse
    {
        $fileName = 'orders_'.now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Customer', 'Total', 'Status', 'Created At']);
            Order::with('user')->chunk(500, function ($chunk) use ($handle) {
                foreach ($chunk as $order) {
                    fputcsv($handle, [
                        $order->id,
                        optional($order->user)->name,
                        $order->total_amount,
                        $order->status,
                        $order->created_at?->toDateTimeString(),
                    ]);
                }
            });
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export orders to Excel (requires maatwebsite/excel).
     */
    public function exportExcel()
    {
        return Excel::download(new OrdersExport, 'orders_'.now()->format('Ymd_His').'.xlsx');
    }

    /**
     * Export orders list to PDF (requires barryvdh/laravel-dompdf).
     */
    public function exportPdf()
    {
        $orders = Order::with('user')->get();
        $pdf = Pdf::loadView('admin.orders.pdf-list', compact('orders'));
        return $pdf->download('orders_'.now()->format('Ymd_His').'.pdf');
    }

    /**
     * Show printable invoice HTML for an order.
     */
    public function invoice(string $locale, $id)
    {
        $order = Order::with(['user', 'orderItems.product'])->findOrFail($id);
        return view('admin.orders.invoice', compact('order'));
    }

    /**
     * Download invoice as PDF for an order.
     */
    public function invoicePdf(string $locale, $id)
    {
        $order = Order::with(['user', 'orderItems.product'])->findOrFail($id);
        $pdf = Pdf::loadView('admin.orders.pdf-invoice', compact('order'));
        return $pdf->download('invoice_ORD'.$order->id.'.pdf');
    }

    /**
     * Import orders update (status) from CSV/Excel.
     * Accepts file with columns: id,status
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls',
        ]);

        // Use Excel importer if Excel file; fallback to csv manually
        $extension = strtolower($request->file('file')->getClientOriginalExtension());
        if (in_array($extension, ['xlsx', 'xls'])) {
            Excel::import(new OrdersImport, $request->file('file'));
        } else {
            // Simple CSV reader: id,status
            $path = $request->file('file')->getRealPath();
            if (($handle = fopen($path, 'r')) !== false) {
                // Skip header
                fgetcsv($handle);
                while (($row = fgetcsv($handle)) !== false) {
                    [$id, $status] = $row + [null, null];
                    if ($id && $status) {
                        Order::whereKey($id)->update(['status' => $status]);
                    }
                }
                fclose($handle);
            }
        }

        return back()->with('success', 'Orders imported successfully.');
    }

    /** Apply coupon to an order */
    public function applyCoupon(Request $request, string $locale, $id, CouponService $svc)
    {
        $request->validate(['coupon_code' => 'required|string']);
        $order = Order::with('orderItems')->findOrFail($id);
        [$ok, $msg] = $svc->validateAndApply($order, $request->coupon_code);
        return back()->with($ok ? 'success' : 'error', $msg);
    }
}

