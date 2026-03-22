<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_attendances', function (Blueprint $table) {
            // Stand-in attendance fields
            $table->boolean('is_stand_in')->default(false)->after('status');
            $table->string('stand_in_reason')->nullable()->after('is_stand_in');

            // Late submission tracking
            $table->boolean('is_late')->default(false)->after('stand_in_reason');
            $table->boolean('is_late_submission')->default(false)->after('is_late');

            // Rescheduled class tracking
            $table->boolean('is_rescheduled')->default(false)->after('is_late_submission');
            $table->time('original_scheduled_time')->nullable()->after('is_rescheduled');
            $table->string('reschedule_reason')->nullable()->after('original_scheduled_time');
            $table->string('reschedule_notes')->nullable()->after('reschedule_reason');

            // Rejection reason
            $table->text('rejection_reason')->nullable()->after('reschedule_notes');
        });
    }

    public function down(): void
    {
        Schema::table('class_attendances', function (Blueprint $table) {
            $table->dropColumn([
                'is_stand_in',
                'stand_in_reason',
                'is_late',
                'is_late_submission',
                'is_rescheduled',
                'original_scheduled_time',
                'reschedule_reason',
                'reschedule_notes',
                'rejection_reason',
            ]);
        });
    }
};
