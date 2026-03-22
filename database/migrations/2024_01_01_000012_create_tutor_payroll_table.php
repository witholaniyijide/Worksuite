<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutor_payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tutor_id');
            $table->unsignedInteger('pay_month');
            $table->unsignedInteger('pay_year');
            $table->decimal('total_hours', 8, 2)->default(0);
            $table->decimal('total_classes', 8, 0)->default(0);
            $table->decimal('gross_amount', 12, 2)->default(0); // sum of all class_attendances.amount_earned
            $table->decimal('adjustments', 12, 2)->default(0); // manual adjustments
            $table->text('adjustment_notes')->nullable();
            $table->decimal('net_amount', 12, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->enum('status', ['draft', 'calculated', 'reviewed', 'approved', 'paid'])->default('draft');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('tutor_id')->references('id')->on('tutors')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');

            $table->unique(['tutor_id', 'pay_month', 'pay_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutor_payrolls');
    }
};
