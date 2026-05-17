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
        Schema::table('assignments', function (Blueprint $table) {
            $table->string('subject_name')->nullable()->after('id');
            $table->string('members')->nullable()->after('type');
        });

        Schema::create('learning_modules', function (Blueprint $table) {
            $table->id();
            $table->string('subject_name');
            $table->string('title');
            $table->string('file_path')->nullable();
            $table->string('link_url')->nullable();
            $table->enum('type', ['file', 'link'])->default('file');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_modules');
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn(['subject_name', 'members']);
        });
    }
};
