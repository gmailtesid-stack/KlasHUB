<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRoleKelasHub {
    public function handle(Request $request, Closure $next, ...$roles) {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $student = Auth::user();
        if (in_array($student->role, $roles)) {
            return $next($request);
        }

        abort(403, 'Wave Project.ID Identity Protection: Anda tidak memiliki otoritas hak akses untuk modul ini.');
    }
}
