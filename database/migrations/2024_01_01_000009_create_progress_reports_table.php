<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progress_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tutor_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedInteger('report_month'); // 1-12
            $table->unsignedInteger('report_year');
            $table->text('overall_performance')->nullable();
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('tutor_comments')->nullable();
            $table->unsignedInteger('attendance_count')->default(0); // auto-calculated
            $table->unsignedInteger('total_classes')->default(0);
            $table->enum('status', ['draft', 'submitted', 'pending_review', 'adjustment_requested', 'approved', 'delivered'])->default('draft');
            $table->unsignedBigInteger('reviewed_by')->nullable(); // regional manager
            $table->text('manager_comments')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->foreign('tutor_id')->references('id')->on('tutors')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');

            $table->unique(['student_id', 'subject_id', 'report_month', 'report_year'], 'unique_monthly_report');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress_reports');
    }
};
