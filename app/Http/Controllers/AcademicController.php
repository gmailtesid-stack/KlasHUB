<?php

namespace App\Http\Controllers;

use App\Models\AcademicSchedule;
use App\Models\MasterSubject;
use App\Http\Traits\AuthorizesAdmin;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AcademicController extends Controller
{
    use AuthorizesAdmin;

    public function storeMasterSubject(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'name' => 'required|string|unique:master_subjects,name',
            'sks' => 'required|integer',
            'code' => 'required|string',
            'default_lecturer' => 'nullable|string'
        ]);

        $subject = MasterSubject::create($data);

        NotificationService::notifyClass(Auth::user()->class_id, "📖 Mata kuliah baru telah ditambahkan: " . $subject->name);

        return response()->json(['success' => true, 'subject' => $subject]);
    }

    public function deleteSubject($id)
    {
        $this->authorizeAdmin();
        MasterSubject::destroy($id);
        return response()->json(['success' => true]);
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

        NotificationService::notifyClass(Auth::user()->class_id, "📅 Jadwal kuliah baru telah diterbitkan: " . $schedule->subject_name . " hari " . $schedule->day);

        return response()->json(['success' => true, 'schedule' => $schedule]);
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
                'day' => 'Sabtu',
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

    public function validateSchedule(Request $request)
    {
        $this->authorizeKetuaKelas();
        $request->validate(['id' => 'required|integer']);

        AcademicSchedule::where('id', $request->id)->update(['is_validated' => true]);
        return response()->json(['success' => true]);
    }

    public function getSchedule()
    {
        $student = Auth::user();
        $isAdmin = in_array($student->role, ['ketua_kelas', 'super_admin']);

        $schedules = AcademicSchedule::when(!$isAdmin, function ($q) {
            return $q->where('is_validated', true);
        })->get();

        return response()->json([
            'schedules' => $schedules
        ]);
    }
}
