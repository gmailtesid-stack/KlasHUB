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

        ClassAttendance::where('id', $request->id)->update(['is_validated' => true]);
        return response()->json(['success' => true]);
    }
}
