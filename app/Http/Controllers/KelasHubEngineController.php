<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ClassAttendance;
use App\Models\CashLedger;
use App\Models\AcademicSchedule;
use App\Models\Assignment;
use App\Models\LearningModule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KelasHubEngineController extends Controller
{
    public function getStudentDashboard()
    {
        $student = Auth::user();

        $masterSubjects = \App\Models\MasterSubject::orderBy('name')->get();

        // Optimized Attendance Calculation
        $attendances = ClassAttendance::where('student_id', $student->id)
            ->where('status', 'Alfa')
            ->where('is_validated', true)
            ->get()
            ->groupBy('subject_name');

        $absensi = $masterSubjects->map(function ($ms) use ($attendances) {
            $total_alfa = isset($attendances[$ms->name]) ? $attendances[$ms->name]->count() : 0;
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
        $saldoKasSaatIni = CashLedger::when(!$isAdmin, function ($q) {
            return $q->where('is_validated', true);
        })->where('type', 'income')->sum('amount') - CashLedger::when(!$isAdmin, function ($q) {
            return $q->where('is_validated', true);
        })->where('type', 'expense')->sum('amount');

        $pemasukanMingguan = CashLedger::when(!$isAdmin, function ($q) {
            return $q->where('is_validated', true);
        })->where('type', 'income')->where('transaction_date', '>=', $startOfWeek)->sum('amount');

        $pengeluaranMingguan = CashLedger::when(!$isAdmin, function ($q) {
            return $q->where('is_validated', true);
        })->where('type', 'expense')->where('transaction_date', '>=', $startOfWeek)->sum('amount');

        // Other heavy lists will be loaded via AJAX
        $schedules = AcademicSchedule::when(!$isAdmin, function ($q) {
            return $q->where('is_validated', true);
        })->get();

        $pendingCount = 0;
        if (in_array($student->role, ['ketua_kelas', 'super_admin'])) {
            $pendingCount += CashLedger::where('is_validated', false)->count();
            $pendingCount += Assignment::where('is_validated', false)->count();
            $pendingCount += LearningModule::where('is_validated', false)->count();
            $pendingCount += ClassAttendance::where('is_validated', false)->count();
            $pendingCount += AcademicSchedule::where('is_validated', false)->count();
        }

        return view('dashboard.main_mobile', [
            'student' => $student,
            'absensi' => $absensi,
            'saldo_kas' => $saldoKasSaatIni,
            'pemasukan_mingguan' => $pemasukanMingguan,
            'pengeluaran_mingguan' => $pengeluaranMingguan,
            'master_subjects' => $masterSubjects,
            'jadwal_harian' => $schedules,
            'pending_count' => $pendingCount,
            'academic_classes' => $student->role === 'super_admin' ? \App\Models\AcademicClass::withCount('students')->with('ketuaKelas')->get() : []
        ]);
    }

    public function getDashboardData()
    {
        $student = Auth::user();
        $isAdmin = in_array($student->role, ['ketua_kelas', 'sekretaris', 'bendahara', 'super_admin']);

        return response()->json([
            'semua_mahasiswa' => Student::orderBy('name', 'asc')->get(),
            'semua_tugas' => Assignment::when(!$isAdmin, function ($q) {
                return $q->where('is_validated', true);
            })->orderBy('deadline', 'asc')->get(),
            'semua_modul' => LearningModule::when(!$isAdmin, function ($q) {
                return $q->where('is_validated', true);
            })->latest()->get(),
            'transaksi_kas' => CashLedger::with('student')->latest()->get()
        ]);
    }

    public function storeMasterSubject(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'name' => 'required|string|unique:master_subjects,name',
            'sks' => 'required|integer',
            'code' => 'required|string',
            'default_lecturer' => 'nullable|string'
        ]);

        $subject = \App\Models\MasterSubject::create($data);
        return response()->json(['success' => true, 'subject' => $subject]);
    }

    public function deleteSubject($id)
    {
        $this->authorizeAdmin();
        \App\Models\MasterSubject::destroy($id);
        return response()->json(['success' => true]);
    }

    public function deleteStudent($id)
    {
        $this->authorizeAdmin();
        $user = Auth::user();
        if ($user->id == $id)
            return response()->json(['success' => false, 'message' => 'Anda tidak bisa menghapus diri sendiri!'], 400);
        Student::destroy($id);
        return response()->json(['success' => true]);
    }

    public function toggleDeliveryType(Request $request)
    {
        $this->authorizeAdmin();
        $request->validate([
            'subject_name' => 'required|string',
            'delivery_type' => 'required|string|in:offline,online'
        ]);

        $schedule = AcademicSchedule::firstOrCreate(
            ['subject_name' => $request->subject_name],
            [
                'day' => 'Sabtu', // Default to Saturday as per user request
                'lecturer_name' => 'Belum Diatur',
                'time_start' => '07:30',
                'time_end' => '10:00',
                'room' => 'V.706',
                'is_validated' => true
            ]
        );

        $schedule->delivery_type = $request->delivery_type;
        $schedule->save();

        return response()->json(['success' => true, 'schedule' => $schedule]);
    }

    public function storeSchedule(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'subject_name' => 'required|string',
            'subject_code' => 'nullable|string',
            'lecturer_name' => 'required|string',
            'day' => 'required|string',
            'time_start' => 'required',
            'time_end' => 'required',
            'room' => 'required|string',
            'class_name' => 'nullable|string',
            'delivery_type' => 'nullable|string',
        ]);

        $data['is_validated'] = in_array(Auth::user()->role, ['ketua_kelas', 'super_admin']);
        $schedule = AcademicSchedule::create($data);
        return response()->json(['success' => true, 'schedule' => $schedule]);
    }

    public function storeAssignment(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'subject_name' => 'required|string',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
            'material_link' => 'nullable|string',
            'type' => 'required|in:individual,kelompok',
            'members' => 'nullable|string',
        ]);

        $data['is_validated'] = in_array(Auth::user()->role, ['ketua_kelas', 'super_admin']);
        $assignment = Assignment::create($data);
        return response()->json(['success' => true, 'assignment' => $assignment]);
    }

    public function storeModule(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'subject_name' => 'required|string',
            'type' => 'required|in:file,link',
            'title' => 'required_if:type,link',
            'link_url' => 'required_if:type,link',
            'file' => 'required_if:type,file|file|mimes:pdf,doc,docx,txt|max:4096',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // Store file content as base64 directly in TiDB - no filesystem needed
            $data['title'] = $file->getClientOriginalName();
            $data['mime_type'] = $file->getMimeType();
            $data['file_content'] = base64_encode(file_get_contents($file->getRealPath()));
            $data['file_path'] = null;
            $data['type'] = 'file';
        }

        $data['is_validated'] = in_array(Auth::user()->role, ['ketua_kelas', 'super_admin']);
        $module = LearningModule::create($data);
        return response()->json(['success' => true, 'module' => $module]);
    }

    public function downloadModule(Request $request, $id)
    {
        $module = LearningModule::findOrFail($id);
        if (!$module->file_content) {
            abort(404, 'File tidak tersedia');
        }
        $fileContent = base64_decode($module->file_content);
        $mimeType = $module->mime_type ?? 'application/octet-stream';
        // Guess extension from mime type
        $ext = 'bin';
        $mimeMap = ['application/pdf' => 'pdf', 'application/msword' => 'doc', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx', 'text/plain' => 'txt'];
        if (isset($mimeMap[$mimeType]))
            $ext = $mimeMap[$mimeType];
        $filename = pathinfo($module->title, PATHINFO_FILENAME) . '.' . $ext;
        return response($fileContent, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => strlen($fileContent),
        ]);
    }

    public function storeCashLedger(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric',
            'description' => 'required|string',
            'transaction_date' => 'required|date',
        ]);

        $data['is_validated'] = in_array(Auth::user()->role, ['ketua_kelas', 'super_admin', 'bendahara']);
        $ledger = CashLedger::create($data);
        return response()->json(['success' => true, 'ledger' => $ledger]);
    }

    protected function authorizeAdmin()
    {
        $role = Auth::user()->role;
        if (!in_array($role, ['ketua_kelas', 'sekretaris', 'bendahara', 'super_admin'])) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function storeStudent(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'nim' => 'required|string|unique:students,nim',
            'name' => 'required|string',
            'role' => 'required|string|in:mahasiswa,ketua_kelas,sekretaris,bendahara,super_admin',
            'class_id' => 'nullable|exists:academic_classes,id'
        ]);

        // Default to current user's class if not specified
        if (!isset($data['class_id'])) {
            $data['class_id'] = Auth::user()->class_id;
        }

        // Password convention: NIM + Code (KK, SK, BD)
        $code = '';
        if ($data['role'] === 'ketua_kelas')
            $code = 'KK';
        elseif ($data['role'] === 'sekretaris')
            $code = 'SK';
        elseif ($data['role'] === 'bendahara')
            $code = 'BD';

        $password = $data['nim'] . $code;
        $data['password'] = bcrypt($password);

        $student = Student::create($data);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'student' => $student]);
        }

        return back()->with('success', 'Mahasiswa berhasil didaftarkan: ' . $student->name);
    }

    public function validateData(Request $request)
    {
        if (!in_array(Auth::user()->role, ['ketua_kelas', 'super_admin'])) {
            abort(403, 'Hanya Ketua Kelas yang bisa memvalidasi data!');
        }

        $request->validate([
            'id' => 'required|integer',
            'type' => 'required|in:schedule,assignment,module,cash,attendance',
        ]);

        $id = $request->id;
        $type = $request->type;

        if ($type === 'schedule')
            AcademicSchedule::where('id', $id)->update(['is_validated' => true]);
        elseif ($type === 'assignment')
            Assignment::where('id', $id)->update(['is_validated' => true]);
        elseif ($type === 'module')
            LearningModule::where('id', $id)->update(['is_validated' => true]);
        elseif ($type === 'cash')
            CashLedger::where('id', $id)->update(['is_validated' => true]);
        elseif ($type === 'attendance')
            ClassAttendance::where('id', $id)->update(['is_validated' => true]);

        return response()->json(['success' => true]);
    }

    public function storeAttendance(Request $request)
    {
        $request->validate([
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|string',
            'subject_name' => 'required|string',
            'date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        $user = Auth::user();
        $isAdmin = in_array($user->role, ['ketua_kelas', 'sekretaris', 'bendahara', 'super_admin']);

        // If not admin, they can only record their OWN attendance (Rekap Mandiri)
        if (!$isAdmin) {
            foreach ($request->attendances as $att) {
                if ($att['student_id'] != $user->id) {
                    abort(403, 'Anda hanya bisa melakukan Rekap Mandiri untuk diri sendiri!');
                }
            }
        }

        // Only Ketua Kelas can auto-validate
        $isValidated = in_array($user->role, ['ketua_kelas', 'super_admin']);

        foreach ($request->attendances as $att) {
            ClassAttendance::create([
                'student_id' => $att['student_id'],
                'subject_name' => $request->subject_name,
                'attendance_date' => $request->date,
                'status' => $att['status'],
                'notes' => $request->notes,
                'is_validated' => $isValidated,
            ]);
        }

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

    public function storeUnifiedClass(Request $request)
    {
        $this->authorizeAdmin();
        if (Auth::user()->role !== 'super_admin') {
            abort(403, 'Hanya Super Admin yang bisa mendaftarkan kelas baru.');
        }

        $data = $request->validate([
            'class_code' => 'required|string|unique:academic_classes,code',
            'department' => 'required|string',
            'ketua_name' => 'required|string',
            'ketua_nim' => 'required|string|unique:students,nim',
            'contact' => 'nullable|string',
        ]);

        // 1. Create Class
        $class = \App\Models\AcademicClass::create([
            'name' => $data['department'] . ' - ' . $data['class_code'],
            'code' => $data['class_code'],
            'department' => $data['department'],
            'contact' => $data['contact'],
            'academic_year' => null,
        ]);

        // 2. Create Ketua Kelas
        $password = $data['ketua_nim'] . 'KK';
        Student::create([
            'name' => $data['ketua_name'],
            'nim' => $data['ketua_nim'],
            'role' => 'ketua_kelas',
            'class_id' => $class->id,
            'password' => bcrypt($password),
        ]);

        return back()->with('success', 'Kelas dan Ketua Kelas berhasil didaftarkan: ' . $data['ketua_name']);
    }

    public function updateStudentRole(Request $request, $id)
    {
        $currentUser = Auth::user();
        if (!in_array($currentUser->role, ['ketua_kelas', 'super_admin'])) {
            abort(403, 'Anda tidak memiliki hak untuk mengubah peran.');
        }

        $request->validate([
            'role' => 'required|in:mahasiswa,ketua_kelas,sekretaris,bendahara'
        ]);

        $targetStudent = Student::findOrFail($id);

        if ($currentUser->role !== 'super_admin' && $targetStudent->class_id !== $currentUser->class_id) {
            abort(403, 'Anda hanya bisa mengatur anggota kelas sendiri.');
        }

        $targetStudent->update(['role' => $request->role]);

        return response()->json(['success' => true]);
    }
}
