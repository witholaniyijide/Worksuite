<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pay rates can be per-tutor, per-subject, or per student-tutor combo
        Schema::create('tutor_pay_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tutor_id');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('student_id')->nullable(); // null = default rate for this tutor/subject
            $table->decimal('rate_per_hour', 10, 2);
            $table->string('currency', 10)->default('USD');
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tutor_id')->references('id')->on('tutors')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('set null');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutor_pay_rates');
    }
};
