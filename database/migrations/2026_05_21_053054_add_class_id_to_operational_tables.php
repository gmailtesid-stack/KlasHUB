<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'students',
            'academic_schedules',
            'assignments',
            'cash_ledgers',
            'class_attendances',
            'learning_modules',
            'master_subjects'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('class_id')->nullable()->after('id')->constrained('academic_classes')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'learning_modules',
            'class_attendances',
            'cash_ledgers',
            'assignments',
            'academic_schedules',
            'students',
            'master_subjects'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->dropForeign([$tableName . '_class_id_foreign']);
                $table->dropColumn('class_id');
            });
        }
    }
};
