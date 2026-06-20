<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ClassAttendance;
use App\Models\CashLedger;
use App\Models\AcademicSchedule;
use App\Models\Assignment;
use App\Models\LearningModule;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function getStudentDashboard()
    {
        $student = Auth::user();

        $masterSubjects = \App\Models\MasterSubject::orderBy('name')->get(); // Reverted Cache due to Vercel Read-Only System

        // Optimized Attendance Calculation
        $attendances = \Illuminate\Support\Facades\DB::table('class_attendances')
            ->selectRaw('subject_name, count(*) as total_alfa')
            ->where('student_id', $student->id)
            ->where('status', 'Alfa')
            ->where('is_validated', true)
            ->groupBy('subject_name')
            ->get()
            ->keyBy('subject_name');

        $absensi = $masterSubjects->map(function ($ms) use ($attendances) {
            $total_alfa = isset($attendances[$ms->name]) ? $attendances[$ms->name]->total_alfa : 0;
            $sisa_nyawa = 3 - $total_alfa;
            return [
                'subject' => $ms->name,
                'total_alfa' => $total_alfa,
                'nyawa' => $sisa_nyawa < 0 ? 0 : $sisa_nyawa,
                'status_nilai' => $sisa_nyawa <= 0 ? 'DICEKAL (Nilai E)' : 'AMAN',
                'is_banned' => $sisa_nyawa <= 0
            ];
        });

        $startOfWeek = Carbon::now()->startOfWeek();
        $isAdmin = in_array($student->role, ['ketua_kelas', 'sekretaris', 'bendahara', 'super_admin']);

        // Quick sums remain on first load
        $saldoKasSaatIni = CashLedger::where('class_id', $student->class_id)
            ->where('is_validated', true)
            ->where('type', 'income')->sum('amount')
            - CashLedger::where('class_id', $student->class_id)
                ->where('is_validated', true)
                ->where('type', 'expense')->sum('amount');

        $pemasukanMingguan = CashLedger::where('class_id', $student->class_id)
            ->where('is_validated', true)
            ->where('type', 'income')->where('transaction_date', '>=', $startOfWeek)->sum('amount');

        $pengeluaranMingguan = CashLedger::where('class_id', $student->class_id)
            ->where('is_validated', true)
            ->where('type', 'expense')->where('transaction_date', '>=', $startOfWeek)->sum('amount');

        // Other heavy lists will be loaded via AJAX
        $schedules = AcademicSchedule::where('class_id', $student->class_id)
            ->when(!$isAdmin, function ($q) {
                return $q->where('is_validated', true);
            })->get();

        $pendingCount = 0;
        if (in_array($student->role, ['ketua_kelas', 'super_admin'])) {
            $pendingCount += CashLedger::where('class_id', $student->class_id)->where('is_validated', false)->count();
            $pendingCount += Assignment::where('class_id', $student->class_id)->where('is_validated', false)->count();
            $pendingCount += LearningModule::where('class_id', $student->class_id)->where('is_validated', false)->count();
            $pendingCount += ClassAttendance::where('is_validated', false)
                ->whereHas('student', function ($query) use ($student) {
                    $query->where('class_id', $student->class_id);
                })->count();
            $pendingCount += AcademicSchedule::where('class_id', $student->class_id)->where('is_validated', false)->count();
        }

        $academicClasses = $student->role === 'super_admin' ? \App\Models\AcademicClass::withCount('students')->with('ketuaKelas')->get() : [];

        $class = \Illuminate\Support\Facades\DB::table('academic_classes')
            ->where('id', $student->class_id)
            ->first();

        $notifications = Notification::where('student_id', $student->id)
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.main_mobile', [
            'student' => $student,
            'class_semester' => $class ? ((int) $class->semester_ke) : 1,
            'absensi' => $absensi,
            'saldo_kas' => $saldoKasSaatIni,
            'pemasukan_mingguan' => $pemasukanMingguan,
            'pengeluaran_mingguan' => $pengeluaranMingguan,
            'master_subjects' => $masterSubjects,
            'jadwal_harian' => $schedules,
            'pending_count' => $pendingCount,
            'academic_classes' => $academicClasses,
            'notifications' => $notifications,
        ]);
    }

    public function getDashboardData()
    {
        $student = Auth::user();
        $isAdmin = in_array($student->role, ['ketua_kelas', 'sekretaris', 'bendahara', 'super_admin']);

        $class = \Illuminate\Support\Facades\DB::table('academic_classes')
            ->where('id', $student->class_id)
            ->first();

        return response()->json([
            'student' => $student,
            'class_semester' => $class ? ((int) $class->semester_ke) : 1,
            'qris_image' => $class && $class->qris_image ? (str_starts_with($class->qris_image, 'data:image') ? $class->qris_image : asset('storage/' . $class->qris_image)) : null,
            'semua_mahasiswa' => Student::where('class_id', $student->class_id)->orderBy('name', 'asc')->get(),
            'semua_tugas' => Assignment::where('class_id', $student->class_id)
                ->when(!$isAdmin, function ($q) {
                    return $q->where('is_validated', true);
                })->orderBy('deadline', 'asc')->get(),
            'semua_modul' => LearningModule::where('class_id', $student->class_id)
                ->when(!$isAdmin, function ($q) {
                    return $q->where('is_validated', true);
                })->latest()->get(),
            'transaksi_kas' => CashLedger::with('student')
                ->where('class_id', $student->class_id)
                ->latest()->get()->map(function ($ledger) {
                    if ($ledger->proof_image && !str_starts_with($ledger->proof_image, 'data:image')) {
                        $ledger->proof_image = asset('storage/' . $ledger->proof_image);
                    }
                    return $ledger;
                }),
            'notifikasi' => Notification::where('student_id', $student->id)->latest()->take(20)->get()
        ]);
    }

    public function markNotificationAsRead(Request $request)
    {
        Notification::where('id', $request->id)
            ->where('student_id', Auth::id())
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6'
        ]);

        $student = Auth::user();

        if (!password_verify($request->old_password, $student->password)) {
            return response()->json(['success' => false, 'message' => 'Password lama salah!'], 400);
        }

        $student->update([
            'password' => bcrypt($request->new_password)
        ]);

        return response()->json(['success' => true]);
    }

    public function updateDeviceToken(Request $request)
    {
        $request->validate([
            'player_id' => 'required|string'
        ]);

        Auth::user()->update([
            'onesignal_id' => $request->player_id
        ]);

        return response()->json(['success' => true]);
    }
}
