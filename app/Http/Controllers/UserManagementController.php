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

        if (Auth::user()->role !== 'super_admin') {
            $data['class_id'] = Auth::user()->class_id;
            if ($data['role'] === 'super_admin') {
                abort(403, 'Akses Ditolak: Anda bukan Super Admin.');
            }
        } elseif (!isset($data['class_id'])) {
            $data['class_id'] = Auth::user()->class_id;
        }

        $password = $data['nim'];
        $data['password'] = bcrypt($password);

        $student = Student::create($data);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'student' => $student, 'default_password' => $password]);
        }

        return back()->with('success', 'Mahasiswa berhasil didaftarkan: ' . $student->name . '. Password Awal: ' . $password);
    }

    public function updateStudent(Request $request, $id)
    {
        $this->authorizeAdmin();
        $student = Student::findOrFail($id);

        $data = $request->validate([
            'nim' => 'required|string|unique:students,nim,' . $id,
            'name' => 'required|string'
        ]);

        $student->update($data);
        return response()->json(['success' => true, 'student' => $student]);
    }

    public function deleteStudent($id)
    {
        $this->authorizeAdmin();
        $user = Auth::user();
        if ($user->id == $id) {
            return response()->json(['success' => false, 'message' => 'Anda tidak bisa menghapus diri sendiri!'], 400);
        }

        $target = Student::findOrFail($id);

        if ($user->role !== 'super_admin') {
            if ($target->class_id !== $user->class_id) {
                abort(403, 'Akses Ditolak: Mahasiswa ini bukan anggota kelas Anda.');
            }
            if ($target->role === 'super_admin' || $target->role === 'ketua_kelas') {
                abort(403, 'Akses Ditolak: Anda tidak dapat menghapus Super Admin atau Ketua Kelas.');
            }
        }

        $target->delete();
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

        $role = $request->role;
        $newPassword = $targetStudent->nim;

        $targetStudent->update([
            'role' => $role,
            'password' => bcrypt($newPassword)
        ]);

        return response()->json(['success' => true, 'default_password' => $newPassword]);
    }

    public function getProfile()
    {
        return response()->json([
            'student' => Auth::user()
        ]);
    }

    public function getAllStudents()
    {
        $this->authorizeAdmin();
        $user = Auth::user();

        $query = Student::orderBy('name', 'asc');

        if ($user->role !== 'super_admin') {
            $query->where('class_id', $user->class_id);
        }

        $students = $query->get();
        return response()->json(['students' => $students]);
    }
}
