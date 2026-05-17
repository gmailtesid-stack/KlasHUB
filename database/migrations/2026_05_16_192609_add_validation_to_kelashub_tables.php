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
            $table->boolean('is_validated')->default(true)->after('class_name');
        });
        Schema::table('assignments', function (Blueprint $table) {
            $table->boolean('is_validated')->default(true)->after('type');
        });
        Schema::table('learning_modules', function (Blueprint $table) {
            $table->boolean('is_validated')->default(true)->after('link_url');
        });
        Schema::table('cash_ledgers', function (Blueprint $table) {
            $table->boolean('is_validated')->default(true)->after('transaction_date');
        });
    }

    public function down(): void
    {
        Schema::table('academic_schedules', function (Blueprint $table) { $table->dropColumn('is_validated'); });
        Schema::table('assignments', function (Blueprint $table) { $table->dropColumn('is_validated'); });
        Schema::table('learning_modules', function (Blueprint $table) { $table->dropColumn('is_validated'); });
        Schema::table('cash_ledgers', function (Blueprint $table) { $table->dropColumn('is_validated'); });
    }
};
