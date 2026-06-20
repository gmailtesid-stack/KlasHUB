<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('academic_classes', function (Blueprint $table) {
            $table->longText('qris_image')->nullable()->change();
        });
        Schema::table('cash_ledgers', function (Blueprint $table) {
            $table->longText('proof_image')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('academic_classes', function (Blueprint $table) {
            $table->string('qris_image')->nullable()->change();
        });
        Schema::table('cash_ledgers', function (Blueprint $table) {
            $table->string('proof_image')->nullable()->change();
        });
    }
};
