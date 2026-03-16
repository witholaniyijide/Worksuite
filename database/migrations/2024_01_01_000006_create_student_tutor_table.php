<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pivot table: which tutor teaches which student which subject
        Schema::create('student_tutor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('tutor_id');
            $table->unsignedBigInteger('subject_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('tutor_id')->references('id')->on('tutors')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');

            $table->unique(['student_id', 'tutor_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_tutor');
    }
};
