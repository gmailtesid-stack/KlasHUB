<?php

namespace App\Http\Controllers;

use App\Models\ClassAttendance;
use App\Http\Traits\AuthorizesAdmin;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    use AuthorizesAdmin;

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
                    abort(403, 'Akses Ditolak: Anda hanya bisa melakukan Rekap Mandiri untuk diri sendiri!');
                }
            }
        } else {
            // Admin can record for others, BUT only within their own class
            if ($user->role !== 'super_admin') {
                foreach ($request->attendances as $att) {
                    $targetStudent = \App\Models\Student::find($att['student_id']);
                    if (!$targetStudent || $targetStudent->class_id !== $user->class_id) {
                        abort(403, 'Akses Ditolak: Mahasiswa bukan dari kelas Anda.');
                    }
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

            // Notifikasi ke Ketua Kelas jika Izin/Sakit (Rekap Mandiri)
            if (!$isValidated && in_array($att['status'], ['Izin', 'Sakit'])) {
                NotificationService::notifyKetua($user->class_id, "🔴 Mahasiswa " . $user->name . " mengajukan " . $att['status'] . " untuk " . $request->subject_name);
            }
        }

        return response()->json(['success' => true]);
    }

    public function validateAttendance(Request $request)
    {
        $this->authorizeKetuaKelas();
        $request->validate(['id' => 'required|integer']);

        $attendance = ClassAttendance::findOrFail($request->id);

        if (Auth::user()->role !== 'super_admin') {
            $student = \App\Models\Student::find($attendance->student_id);
            if (!$student || $student->class_id !== Auth::user()->class_id) {
                abort(403, 'Akses Ditolak: Absensi tersebut bukan dari anggota kelas Anda.');
            }
        }

        $attendance->update(['is_validated' => true]);
        return response()->json(['success' => true]);
    }

    public function getAttendance()
    {
        $student = Auth::user();
        $masterSubjects = \App\Models\MasterSubject::orderBy('name')->get();

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

        $isAdmin = in_array($student->role, ['ketua_kelas', 'super_admin']);
        $pendingAttendances = [];
        if ($isAdmin) {
            if ($student->role === 'super_admin') {
                $pendingAttendances = ClassAttendance::where('is_validated', false)->get();
            } else {
                $pendingAttendances = ClassAttendance::where('is_validated', false)
                    ->whereHas('student', function ($query) use ($student) {
                        $query->where('class_id', $student->class_id);
                    })->get();
            }
        }

        return response()->json([
            'absensi_saya' => $absensi,
            'pending_attendances' => $pendingAttendances
        ]);
    }
}
