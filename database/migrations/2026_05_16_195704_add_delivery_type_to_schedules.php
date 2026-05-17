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
            $table->string('delivery_type')->default('offline')->after('class_name'); // offline or online
        });
    }

    public function down(): void
    {
        Schema::table('academic_schedules', function (Blueprint $table) {
            $table->dropColumn('delivery_type');
        });
    }
};
