<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->query('q', ''));

        $query = Payment::with('user')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('reference', 'like', "%$q%")
                        ->orWhere('status', 'like', "%$q%")
                        ->orWhere('method', 'like', "%$q%")
                        ->orWhereHas('user', function ($uq) use ($q) {
                            $uq->where('name', 'like', "%$q%")
                               ->orWhere('email', 'like', "%$q%");
                        });
                });
            })
            ->orderByDesc('created_at');

        if ($request->query('export') === 'csv') {
            $filename = 'payments-export-'.now()->format('Ymd_His').'.csv';
            $rows = $query->get(['id','user_id','amount','status','method','reference','created_at']);

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = static function () use ($rows) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['ID','User ID','Amount','Status','Method','Reference','Created At']);
                foreach ($rows as $row) {
                    fputcsv($out, [$row->id, $row->user_id, $row->amount, $row->status, $row->method, $row->reference, $row->created_at]);
                }
                fclose($out);
            };

            return response()->stream($callback, 200, $headers);
        }

        $payments = $query->paginate(15)->appends(['q' => $q]);

        return view('admin.payments.index', compact('payments', 'q'));
    }

    public function create()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        abort(404);
    }

    public function show(string $locale, string $id)
    {
        $payment = Payment::with('user')->findOrFail($id);

        return view('admin.payments.show', compact('payment'));
    }

    public function edit(string $id)
    {
        abort(404);
    }

    public function update(Request $request, string $id)
    {
        abort(404);
    }

    public function destroy(string $locale, string $id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return back()->with('status', 'Payment deleted');
    }
}
