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
                AcademicSchedule::where('id', $id)->update(['is_validated' => true]);
                break;
            case 'assignment':
                Assignment::where('id', $id)->update(['is_validated' => true]);
                break;
            case 'module':
                LearningModule::where('id', $id)->update(['is_validated' => true]);
                break;
            case 'cash':
                CashLedger::where('id', $id)->update(['is_validated' => true]);
                break;
            case 'attendance':
                $att = ClassAttendance::find($id);
                if ($att) {
                    $att->update(['is_validated' => true]);
                    NotificationService::notifyStudent($att->student_id, "✅ Pengajuan Izin/Sakit Anda untuk matkul " . $att->subject_name . " telah DISETUJUI oleh Ketua Kelas.");
                }
                break;
        }

        return response()->json(['success' => true]);
    }
}
