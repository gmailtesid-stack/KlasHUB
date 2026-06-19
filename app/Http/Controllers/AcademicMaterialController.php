<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\LearningModule;
use App\Http\Traits\AuthorizesAdmin;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AcademicMaterialController extends Controller
{
    use AuthorizesAdmin;

    public function storeAssignment(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'subject_name' => 'required|string',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
            'material_link' => 'nullable|string',
            'type' => 'required|in:individual,kelompok',
            'members' => 'nullable|string',
        ]);

        $data['is_validated'] = in_array(Auth::user()->role, ['ketua_kelas', 'super_admin']);
        $assignment = Assignment::create($data);

        NotificationService::notifyClass(Auth::user()->class_id, "📝 Tugas baru tersedia: " . $assignment->title . " (Deadline: " . $assignment->deadline . ")");

        return response()->json(['success' => true, 'assignment' => $assignment]);
    }

    public function storeModule(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'subject_name' => 'required|string',
            'type' => 'required|in:file,link',
            'title' => 'required_if:type,link',
            'link_url' => 'required_if:type,link',
            'file' => 'required_if:type,file|file|mimes:pdf,doc,docx,txt|max:4096',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $data['title'] = $file->getClientOriginalName();
            $data['mime_type'] = $file->getMimeType();
            $data['file_path'] = $file->store('modules', 'public');
            $data['file_content'] = null; // Mencegah payload bengkak di database
            $data['type'] = 'file';
        }

        $data['is_validated'] = in_array(Auth::user()->role, ['ketua_kelas', 'super_admin']);
        $module = LearningModule::create($data);

        NotificationService::notifyClass(Auth::user()->class_id, "📚 Modul pembelajaran baru telah diunggah: " . $module->title);

        return response()->json(['success' => true, 'module' => $module]);
    }

    public function downloadModule(Request $request, $id)
    {
        $module = LearningModule::findOrFail($id);

        if ($module->file_path) {
            $path = storage_path('app/public/' . $module->file_path);
            if (file_exists($path)) {
                return response()->download($path, $module->title);
            }
        }

        if ($module->file_content) {
            $fileContent = base64_decode($module->file_content);
            $mimeType = $module->mime_type ?? 'application/octet-stream';

            $mimeMap = ['application/pdf' => 'pdf', 'application/msword' => 'doc', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx', 'text/plain' => 'txt'];
            $ext = $mimeMap[$mimeType] ?? 'bin';
            $filename = pathinfo($module->title, PATHINFO_FILENAME) . '.' . $ext;

            return response($fileContent, 200, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => strlen($fileContent),
            ]);
        }

        abort(404, 'File tidak tersedia');
    }

    public function validateAssignment(Request $request)
    {
        $this->authorizeKetuaKelas();
        $request->validate(['id' => 'required|integer']);
        Assignment::where('id', $request->id)->update(['is_validated' => true]);
        return response()->json(['success' => true]);
    }

    public function validateModule(Request $request)
    {
        $this->authorizeKetuaKelas();
        $request->validate(['id' => 'required|integer']);
        LearningModule::where('id', $request->id)->update(['is_validated' => true]);
        return response()->json(['success' => true]);
    }
}
