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
        Schema::table('academic_classes', function (Blueprint $table) {
            $table->integer('semester_ke')->default(1);
        });

        $operationalTables = [
            'academic_schedules',
            'assignments',
            'cash_ledgers',
            'class_attendances',
            'learning_modules',
            'master_subjects'
        ];

        foreach ($operationalTables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->integer('semester')->default(1);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_classes', function (Blueprint $table) {
            $table->dropColumn('semester_ke');
        });

        $operationalTables = [
            'academic_schedules',
            'assignments',
            'cash_ledgers',
            'class_attendances',
            'learning_modules',
            'master_subjects'
        ];

        foreach ($operationalTables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('semester');
            });
        }
    }
};
