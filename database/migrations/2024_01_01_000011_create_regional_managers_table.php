<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regional_managers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->unsignedBigInteger('region_id');
            $table->string('staff_id')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->string('sender_email')->nullable(); // email used as sender for parent comms
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regional_managers');
    }
};
