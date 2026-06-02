<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('students', function (Blueprint $row) {
            $row->string('onesignal_id')->nullable()->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $row) {
            $row->dropColumn('onesignal_id');
        });
    }
};
