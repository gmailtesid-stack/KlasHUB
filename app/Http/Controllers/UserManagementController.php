<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Http\Traits\AuthorizesAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    use AuthorizesAdmin;

    public function storeStudent(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'nim' => 'required|string|unique:students,nim',
            'name' => 'required|string',
            'role' => 'required|string|in:mahasiswa,ketua_kelas,sekretaris,bendahara,super_admin',
            'class_id' => 'nullable|exists:academic_classes,id'
        ]);

        if (!isset($data['class_id'])) {
            $data['class_id'] = Auth::user()->class_id;
        }

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

    public function deleteStudent($id)
    {
        $this->authorizeAdmin();
        $user = Auth::user();
        if ($user->id == $id) {
            return response()->json(['success' => false, 'message' => 'Anda tidak bisa menghapus diri sendiri!'], 400);
        }
        Student::destroy($id);
        return response()->json(['success' => true]);
    }

    public function updateStudentRole(Request $request, $id)
    {
        $this->authorizeKetuaKelas();
        $currentUser = Auth::user();

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
