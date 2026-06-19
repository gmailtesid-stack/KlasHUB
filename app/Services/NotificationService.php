<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Student;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Kirim notifikasi ke seluruh mahasiswa di satu kelas (Internal & Eksternal).
     */
    public static function notifyClass($classId, $message)
    {
        $students = Student::where('class_id', $classId)->get();
        $playerIds = [];

        foreach ($students as $student) {
            Notification::create([
                'class_id' => $classId,
                'student_id' => $student->id,
                'message' => $message,
                'is_read' => false
            ]);

            if ($student->onesignal_id) {
                $playerIds[] = $student->onesignal_id;
            }
        }

        if (!empty($playerIds)) {
            self::sendExternalPush($playerIds, $message);
        }
    }

    /**
     * Kirim notifikasi ke satu mahasiswa tertentu (Internal & Eksternal).
     */
    public static function notifyStudent($studentId, $message)
    {
        $student = Student::find($studentId);
        if ($student) {
            Notification::create([
                'class_id' => $student->class_id,
                'student_id' => $student->id,
                'message' => $message,
                'is_read' => false
            ]);

            if ($student->onesignal_id) {
                self::sendExternalPush([$student->onesignal_id], $message);
            }
        }
    }

    /**
     * Kirim ke Ketua Kelas.
     */
    public static function notifyKetua($classId, $message)
    {
        $ketua = Student::where('class_id', $classId)->where('role', 'ketua_kelas')->first();
        if ($ketua) {
            self::notifyStudent($ketua->id, $message);
        }
    }

    /**
     * Kirim ke Pengurus.
     */
    public static function notifyManagement($classId, $message)
    {
        $admins = Student::where('class_id', $classId)->whereIn('role', ['ketua_kelas', 'sekretaris', 'bendahara'])->get();
        foreach ($admins as $admin) {
            self::notifyStudent($admin->id, $message);
        }
    }

    /**
     * Kirim Push Notification Pop-up via OneSignal REST API.
     */
    private static function sendExternalPush(array $playerIds, $message)
    {
        // PERBAIKAN: Mengambil data dari config/services.php
        $appId = config('services.onesignal.app_id');
        $apiKey = config('services.onesignal.rest_api_key');

        if (!$appId || !$apiKey) {
            Log::warning('OneSignal credentials not set. External push skipped.');
            return;
        }

        try {
            // PERBAIKAN: Tambah withoutVerifying() untuk amankan request keluar dari Vercel
            Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Basic ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://onesignal.com/api/v1/notifications', [
                'app_id' => $appId,
                'include_subscription_ids' => $playerIds,
                'contents' => ['en' => $message, 'id' => $message],
                'headings' => ['en' => 'Berita KelasHUB', 'id' => 'Berita KelasHUB'],
                'small_icon' => 'ic_stat_onesignal_default',
                'android_accent_color' => 'FF18181B' // Zinc 900
            ]);
        } catch (\Exception $e) {
            Log::error('OneSignal Push Error: ' . $e->getMessage());
        }
    }
}