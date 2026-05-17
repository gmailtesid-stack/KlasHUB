<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('academic_schedules', function (Blueprint $table) {
            $table->string('subject_code')->nullable()->after('subject_name');
            $table->string('class_name')->nullable()->after('room');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_schedules', function (Blueprint $table) {
            $table->dropColumn(['subject_code', 'class_name']);
        });
    }
};
