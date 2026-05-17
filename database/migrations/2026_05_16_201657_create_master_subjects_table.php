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
        Schema::create('master_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('sks')->default(2);
            $table->string('code')->default('06TPLE013');
            $table->string('default_lecturer')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_subjects');
    }
};
