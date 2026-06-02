<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Auth;

trait AuthorizesAdmin
{
    protected function authorizeAdmin()
    {
        $role = Auth::user()->role;
        if (!in_array($role, ['ketua_kelas', 'sekretaris', 'bendahara', 'super_admin'])) {
            abort(403, 'Unauthorized action.');
        }
    }

    protected function authorizeKetuaKelas()
    {
        if (!in_array(Auth::user()->role, ['ketua_kelas', 'super_admin'])) {
            abort(403, 'Hanya Ketua Kelas yang bisa melakukan tindakan ini!');
        }
    }
}
