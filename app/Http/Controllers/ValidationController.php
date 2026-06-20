<?php

namespace App\Http\Controllers;

use App\Models\AcademicSchedule;
use App\Models\Assignment;
use App\Models\LearningModule;
use App\Models\CashLedger;
use App\Models\ClassAttendance;
use App\Http\Traits\AuthorizesAdmin;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ValidationController extends Controller
{
    use AuthorizesAdmin;

    public function validateData(Request $request)
    {
        $this->authorizeKetuaKelas();

        $request->validate([
            'id' => 'required|integer',
            'type' => 'required|in:schedule,assignment,module,cash,attendance',
        ]);

        $id = $request->id;
        $type = $request->type;

        switch ($type) {
            case 'schedule':
                $item = AcademicSchedule::findOrFail($id);
                if (Auth::user()->role !== 'super_admin' && $item->class_id !== Auth::user()->class_id)
                    abort(403);
                $item->update(['is_validated' => true]);
                break;
            case 'assignment':
                $item = Assignment::findOrFail($id);
                if (Auth::user()->role !== 'super_admin' && $item->class_id !== Auth::user()->class_id)
                    abort(403);
                $item->update(['is_validated' => true]);
                break;
            case 'module':
                $item = LearningModule::findOrFail($id);
                if (Auth::user()->role !== 'super_admin' && $item->class_id !== Auth::user()->class_id)
                    abort(403);
                $item->update(['is_validated' => true]);
                break;
            case 'cash':
                $item = CashLedger::findOrFail($id);
                if (Auth::user()->role !== 'super_admin' && $item->class_id !== Auth::user()->class_id)
                    abort(403);
                $item->update(['is_validated' => true]);
                break;
            case 'attendance':
                $att = ClassAttendance::findOrFail($id);
                if (Auth::user()->role !== 'super_admin' && $att->class_id !== Auth::user()->class_id)
                    abort(403);
                $att->update(['is_validated' => true]);
                NotificationService::notifyStudent($att->student_id, "✅ Pengajuan Izin/Sakit Anda untuk matkul " . $att->subject_name . " telah DISETUJUI oleh Ketua Kelas.");
                break;
        }

        return response()->json(['success' => true]);
    }

    public function getPendingValidations()
    {
        $this->authorizeKetuaKelas();

        $pending = collect([]);
        $classId = Auth::user()->class_id;
        $isSuper = Auth::user()->role === 'super_admin';

        foreach (CashLedger::where('is_validated', false)->when(!$isSuper, function ($q) use ($classId) {
            return $q->where('class_id', $classId);
        })->get() as $item) {
            $proof = $item->proof_image ? (str_starts_with($item->proof_image, 'data:image') ? $item->proof_image : asset('storage/' . $item->proof_image)) : null;
            $pending->push(['id' => $item->id, 'type' => 'cash', 'title' => 'Uang Kas: Rp. ' . $item->amount, 'description' => $item->description, 'proof_image' => $proof]);
        }

        foreach (ClassAttendance::with('student')->where('is_validated', false)->when(!$isSuper, function ($q) use ($classId) {
            return $q->where('class_id', $classId);
        })->get() as $item) {
            $studentName = $item->student ? $item->student->name : 'Unknown';
            $pending->push(['id' => $item->id, 'type' => 'attendance', 'title' => 'Absen: ' . $studentName, 'description' => $item->status . ' - ' . $item->subject_name]);
        }

        foreach (AcademicSchedule::where('is_validated', false)->when(!$isSuper, function ($q) use ($classId) {
            return $q->where('class_id', $classId);
        })->get() as $item) {
            $pending->push(['id' => $item->id, 'type' => 'schedule', 'title' => 'Jadwal: ' . $item->subject_name, 'description' => $item->day . ' (' . $item->time_start . '-' . $item->time_end . ')']);
        }

        foreach (Assignment::where('is_validated', false)->when(!$isSuper, function ($q) use ($classId) {
            return $q->where('class_id', $classId);
        })->get() as $item) {
            $pending->push(['id' => $item->id, 'type' => 'assignment', 'title' => 'Tugas: ' . $item->title, 'description' => 'Deadline: ' . $item->deadline]);
        }

        foreach (LearningModule::where('is_validated', false)->when(!$isSuper, function ($q) use ($classId) {
            return $q->where('class_id', $classId);
        })->get() as $item) {
            $pending->push(['id' => $item->id, 'type' => 'module', 'title' => 'Modul: ' . $item->title, 'description' => $item->type]);
        }

        return response()->json(['pending' => $pending]);
    }
}
