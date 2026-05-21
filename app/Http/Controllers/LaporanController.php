<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    /**
     * Ekspor Laporan Kas dalam format PDF (Formal Minimalis).
     */
    public function exportPdf($class_id)
    {
        $class = DB::table('academic_classes')->where('id', $class_id)->first();
        if (!$class)
            return abort(404, 'Kelas tidak ditemukan');

        $ledgers = DB::table('cash_ledgers')
            ->leftJoin('students', 'cash_ledgers.student_id', '=', 'students.id')
            ->where('cash_ledgers.class_id', $class_id)
            ->select('cash_ledgers.*', 'students.name as student_name')
            ->orderBy('transaction_date', 'asc')
            ->get();

        $totalIncome = $ledgers->where('type', 'income')->sum('amount');
        $totalExpense = $ledgers->where('type', 'expense')->sum('amount');
        $currentBalance = $totalIncome - $totalExpense;

        $pdf = Pdf::loadView('reports.pdf', [
            'class' => $class,
            'ledgers' => $ledgers,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'currentBalance' => $currentBalance,
            'date' => now()->format('d F Y')
        ]);

        return $pdf->download('Laporan-Keuangan-' . $class->code . '.pdf');
    }

    /**
     * Ekspor Laporan Kas dalam format CSV (RAM Optimized Stream).
     */
    public function exportExcel($class_id)
    {
        $class = DB::table('academic_classes')->where('id', $class_id)->first();
        if (!$class)
            return abort(404, 'Kelas tidak ditemukan');

        $fileName = 'Laporan-Kas-' . $class->code . '-' . date('Ymd') . '.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($class_id) {
            $file = fopen('php://output', 'w');

            // Header CSV
            fputcsv($file, ['ID', 'Tanggal', 'Tipe', 'Jumlah', 'Nama Mahasiswa', 'Keterangan']);

            // Fetch data per batch untuk hemat RAM (Vercel protection)
            DB::table('cash_ledgers')
                ->leftJoin('students', 'cash_ledgers.student_id', '=', 'students.id')
                ->where('cash_ledgers.class_id', $class_id)
                ->select('cash_ledgers.*', 'students.name as student_name')
                ->orderBy('transaction_date', 'asc')
                ->chunk(200, function ($ledgers) use ($file) {
                    foreach ($ledgers as $row) {
                        fputcsv($file, [
                            $row->id,
                            $row->transaction_date,
                            strtoupper($row->type),
                            $row->amount,
                            $row->student_name ?? 'Umum',
                            $row->description
                        ]);
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
