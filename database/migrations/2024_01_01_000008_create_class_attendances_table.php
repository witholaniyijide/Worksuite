<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tutor submits attendance for each class session
        Schema::create('class_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tutor_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('subject_id');
            $table->date('class_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('duration_hours', 5, 2); // calculated from start/end
            $table->enum('student_status', ['present', 'absent', 'late', 'excused'])->default('present');
            $table->decimal('rate_applied', 10, 2); // rate used for this class
            $table->decimal('amount_earned', 10, 2); // duration * rate
            $table->string('currency', 10)->default('USD');
            $table->text('class_notes')->nullable();
            $table->text('topics_covered')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'disputed'])->default('submitted');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('tutor_id')->references('id')->on('tutors')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_attendances');
    }
};
