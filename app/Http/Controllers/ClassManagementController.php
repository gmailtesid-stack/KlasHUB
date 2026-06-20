<?php

namespace App\Http\Controllers;

use App\Models\AcademicClass;
use App\Models\Student;
use App\Http\Traits\AuthorizesAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassManagementController extends Controller
{
    use AuthorizesAdmin;

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
        $class = AcademicClass::create([
            'name' => $data['department'] . ' - ' . $data['class_code'],
            'code' => $data['class_code'],
            'department' => $data['department'],
            'contact' => $data['contact'],
            'academic_year' => null,
        ]);

        // 2. Create Ketua Kelas
        $password = \Illuminate\Support\Str::random(8);
        Student::create([
            'name' => $data['ketua_name'],
            'nim' => $data['ketua_nim'],
            'role' => 'ketua_kelas',
            'class_id' => $class->id,
            'password' => bcrypt($password),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kelas ' . $class->name . ' dan Ketua Kelas (' . $data['ketua_name'] . ') berhasil didaftarkan. Password Ketua: ' . $password,
                'default_password' => $password
            ]);
        }

        return back()->with('success', 'Kelas dan Ketua Kelas berhasil didaftarkan: ' . $data['ketua_name'] . '. Password Awal: ' . $password);
    }

    public function nextSemester()
    {
        $this->authorizeKetuaKelas();
        $class = AcademicClass::findOrFail(Auth::user()->class_id);

        $class->semester_ke++;
        $class->save();

        return response()->json([
            'success' => true,
            'new_semester' => $class->semester_ke
        ]);
    }

    public function uploadQris(Request $request)
    {
        $this->authorizeAdmin(); // Pengurus kelas

        $request->validate([
            'qris_image' => 'required|image|mimes:jpeg,png,jpg|max:3048'
        ]);

        $class = AcademicClass::findOrFail(Auth::user()->class_id);

        if ($request->hasFile('qris_image')) {
            $file = $request->file('qris_image');
            $fileData = file_get_contents($file->getRealPath());
            $base64 = 'data:image/' . $file->extension() . ';base64,' . base64_encode($fileData);

            $class->qris_image = $base64;
            $class->save();
            return response()->json(['success' => true, 'image_url' => $base64]);
        }

        return response()->json(['success' => false, 'message' => 'Gagal mengunggah file.']);
    }

    public function deleteQris()
    {
        $this->authorizeAdmin();
        $class = AcademicClass::findOrFail(Auth::user()->class_id);
        $class->qris_image = null;
        $class->save();
        return response()->json(['success' => true]);
    }

    public function deleteClass($id)
    {
        if (Auth::user()->role !== 'super_admin') {
            abort(403, 'Akses Ditolak: Hanya Super Admin');
        }

        $class = AcademicClass::findOrFail($id);

        // Hapus paksa isi database milik class_id ini (apabila tidak ada constraint On Delete Cascade)
        \App\Models\Student::where('class_id', $class->id)->delete();
        \App\Models\Assignment::where('class_id', $class->id)->delete();
        \App\Models\AcademicSchedule::where('class_id', $class->id)->delete();
        \App\Models\LearningModule::where('class_id', $class->id)->delete();
        \App\Models\CashLedger::where('class_id', $class->id)->delete();
        \App\Models\ClassAttendance::where('class_id', $class->id)->delete();

        $class->delete();

        return response()->json(['success' => true]);
    }
}
