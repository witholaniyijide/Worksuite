<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Each progress report can have weekly evaluations (Week 1-5)
        Schema::create('weekly_evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('progress_report_id');
            $table->unsignedTinyInteger('week_number'); // 1-5
            $table->text('topics_covered')->nullable();
            $table->text('evaluation')->nullable();
            $table->enum('performance_rating', ['excellent', 'good', 'satisfactory', 'needs_improvement', 'poor'])->nullable();
            $table->unsignedTinyInteger('attendance_this_week')->default(0);
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->foreign('progress_report_id')->references('id')->on('progress_reports')->onDelete('cascade');
            $table->unique(['progress_report_id', 'week_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_evaluations');
    }
};
