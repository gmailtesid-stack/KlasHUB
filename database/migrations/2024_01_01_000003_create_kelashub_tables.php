<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nim', 20)->unique();
            $table->string('name');
            $table->string('password');
            $table->enum('role', ['ketua_kelas', 'sekretaris', 'bendahara', 'mahasiswa'])->default('mahasiswa');
            $table->string('device_id')->nullable();
            $table->timestamps();
        });

        Schema::create('academic_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('subject_name');
            $table->string('lecturer_name');
            $table->string('day');
            $table->time('time_start');
            $table->time('time_end');
            $table->string('room');
            $table->timestamps();
        });

        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('deadline');
            $table->string('material_link')->nullable();
            $table->enum('type', ['individual', 'group'])->default('individual');
            $table->timestamps();
        });

        Schema::create('cash_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['income', 'expense']);
            $table->integer('amount');
            $table->string('description');
            $table->date('transaction_date');
            $table->timestamps();
        });

        Schema::create('class_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('subject_name');
            $table->date('attendance_date');
            $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alfa'])->default('Hadir');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('class_attendances');
        Schema::dropIfExists('cash_ledgers');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('academic_schedules');
        Schema::dropIfExists('students');
    }
};
