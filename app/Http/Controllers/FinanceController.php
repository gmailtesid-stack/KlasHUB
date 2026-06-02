<?php

namespace App\Http\Controllers;

use App\Models\CashLedger;
use App\Http\Traits\AuthorizesAdmin;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    use AuthorizesAdmin;

    public function storeCashLedger(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric',
            'description' => 'required|string',
            'transaction_date' => 'required|date',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg|max:3048'
        ]);

        if ($request->hasFile('proof_image')) {
            $data['proof_image'] = $request->file('proof_image')->store('proofs', 'public');
        }

        $data['is_validated'] = in_array(Auth::user()->role, ['ketua_kelas', 'super_admin', 'bendahara']);
        $data['class_id'] = Auth::user()->class_id;
        $ledger = CashLedger::create($data);

        $typeLabel = $ledger->type === 'income' ? 'Pemasukan' : 'Pengeluaran';
        NotificationService::notifyClass(Auth::user()->class_id, "💰 Transaksi Kas Baru ($typeLabel): Rp " . number_format($ledger->amount, 0, ',', '.') . " - " . $ledger->description);

        return response()->json(['success' => true, 'ledger' => $ledger]);
    }

    public function validateCash(Request $request)
    {
        $this->authorizeKetuaKelas();
        $request->validate(['id' => 'required|integer']);

        $ledger = CashLedger::findOrFail($request->id);
        if (Auth::user()->role !== 'super_admin' && $ledger->class_id !== Auth::user()->class_id) {
            abort(403, 'Akses Ditolak: Data keuangan ini bukan milik kelas Anda.');
        }

        $ledger->update(['is_validated' => true]);
        return response()->json(['success' => true]);
    }
}
