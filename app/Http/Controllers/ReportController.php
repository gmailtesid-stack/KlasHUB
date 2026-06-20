<?php

namespace App\Http\Controllers;

use App\Models\ClassAttendance;
use App\Models\CashLedger;
use App\Models\Student;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function exportAttendancePdf(Request $request)
    {
        $user = Auth::user();
        $attendances = ClassAttendance::where('class_id', $user->class_id)->with('student')->get();
        $class = $user->academicClass;

        $pdf = Pdf::loadView('reports.attendance_pdf', [
            'attendances' => $attendances,
            'class' => $class,
            'user' => $user,
            'date' => date('d F Y')
        ]);

        return $pdf->download('Laporan_Absensi_' . ($class->code ?? 'Kelas') . '.pdf');
    }

    public function exportAttendanceExcel(Request $request)
    {
        $user = Auth::user();
        $attendances = ClassAttendance::where('class_id', $user->class_id)->with('student')->get();
        $class = $user->academicClass;

        $fileName = 'Laporan-Absensi-' . ($class->code ?? 'Kelas') . '-' . date('Ymd') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($attendances) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'NIM', 'Nama Mahasiswa', 'Mata Kuliah', 'Tanggal', 'Status']);
            foreach ($attendances as $index => $att) {
                fputcsv($file, [
                    $index + 1,
                    $att->student->nim ?? '-',
                    $att->student->name ?? '-',
                    $att->subject_name,
                    $att->attendance_date,
                    $att->status
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportCashPdf(Request $request)
    {
        $user = Auth::user();
        $ledgers = CashLedger::where('class_id', $user->class_id)->with('student')->orderBy('transaction_date', 'desc')->get();
        $class = $user->academicClass;
        $balance = CashLedger::where('class_id', $user->class_id)->where('type', 'income')->sum('amount') - CashLedger::where('class_id', $user->class_id)->where('type', 'expense')->sum('amount');

        $pdf = Pdf::loadView('reports.cash_ledger_pdf', [
            'ledgers' => $ledgers,
            'class' => $class,
            'user' => $user,
            'balance' => $balance,
            'date' => date('d F Y')
        ]);

        return $pdf->download('Laporan_Kas_' . ($class->code ?? 'Kelas') . '.pdf');
    }

    public function exportCashExcel(Request $request)
    {
        $user = Auth::user();
        $ledgers = CashLedger::where('class_id', $user->class_id)->with('student')->orderBy('transaction_date', 'desc')->get();
        $class = $user->academicClass;

        $fileName = 'Laporan-Kas-' . ($class->code ?? 'Kelas') . '-' . date('Ymd') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($ledgers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Tanggal', 'Nama Mahasiswa', 'Tipe', 'Jumlah', 'Keterangan']);
            foreach ($ledgers as $index => $l) {
                fputcsv($file, [
                    $index + 1,
                    $l->transaction_date,
                    $l->student->name ?? 'Admin',
                    $l->type == 'income' ? 'Pemasukan' : 'Pengeluaran',
                    $l->amount,
                    $l->description
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
