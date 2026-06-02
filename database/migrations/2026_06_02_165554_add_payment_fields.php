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
            $table->string('qris_image')->nullable()->after('academic_year');
        });
        Schema::table('cash_ledgers', function (Blueprint $table) {
            $table->string('proof_image')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_classes', function (Blueprint $table) {
            $table->dropColumn('qris_image');
        });
        Schema::table('cash_ledgers', function (Blueprint $table) {
            $table->dropColumn('proof_image');
        });
    }
};
