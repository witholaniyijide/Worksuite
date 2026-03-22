<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->unique()->nullable(); // e.g. EV-2024-001
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->unsignedBigInteger('region_id');
            $table->unsignedBigInteger('guardian_id');
            $table->enum('status', ['active', 'inactive', 'graduated', 'withdrawn'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('region_id')->references('id')->on('regions')->onDelete('restrict');
            $table->foreign('guardian_id')->references('id')->on('guardians')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
