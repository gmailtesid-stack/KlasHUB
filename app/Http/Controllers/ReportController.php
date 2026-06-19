<?php

namespace App\Http\Controllers;

use App\Models\ClassAttendance;
use App\Models\CashLedger;
use App\Models\Student;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
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

        return Excel::create('Laporan_Absensi_' . ($class->code ?? 'Kelas'), function ($excel) use ($attendances, $class) {
            $excel->sheet('Absensi', function ($sheet) use ($attendances, $class) {
                $data = [];
                $data[] = ['LAPORAN ABSENSI KELAS ' . ($class->name ?? '')];
                $data[] = ['Kode Kelas: ' . ($class->code ?? '')];
                $data[] = ['Tanggal Cetak: ' . date('d-m-Y')];
                $data[] = [];
                $data[] = ['No', 'NIM', 'Nama Mahasiswa', 'Mata Kuliah', 'Tanggal', 'Status'];

                foreach ($attendances as $index => $att) {
                    $data[] = [
                        $index + 1,
                        $att->student->nim ?? '-',
                        $att->student->name ?? '-',
                        $att->subject_name,
                        $att->attendance_date,
                        $att->status
                    ];
                }

                $sheet->fromArray($data, null, 'A1', false, false);
            });
        })->download('xlsx');
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

        return Excel::create('Laporan_Kas_' . ($class->code ?? 'Kelas'), function ($excel) use ($ledgers, $class) {
            $excel->sheet('Kas Kelas', function ($sheet) use ($ledgers, $class) {
                $data = [];
                $data[] = ['LAPORAN KAS KELAS ' . ($class->name ?? '')];
                $data[] = ['Kode Kelas: ' . ($class->code ?? '')];
                $data[] = ['Tanggal Cetak: ' . date('d-m-Y')];
                $data[] = [];
                $data[] = ['No', 'Tanggal', 'Nama Mahasiswa', 'Tipe', 'Jumlah', 'Keterangan'];

                foreach ($ledgers as $index => $l) {
                    $data[] = [
                        $index + 1,
                        $l->transaction_date,
                        $l->student->name ?? 'Admin',
                        $l->type == 'income' ? 'Pemasukan' : 'Pengeluaran',
                        $l->amount,
                        $l->description
                    ];
                }

                $sheet->fromArray($data, null, 'A1', false, false);
            });
        })->download('xlsx');
    }
}
