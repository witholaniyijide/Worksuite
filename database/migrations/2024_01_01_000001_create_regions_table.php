<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // UK, US, Canada
            $table->string('code', 10)->unique(); // uk, us, ca
            $table->string('timezone')->nullable();
            $table->string('currency', 10)->default('USD');
            $table->timestamps();
        });

        // Seed the three regions
        DB::table('regions')->insert([
            ['name' => 'United Kingdom', 'code' => 'uk', 'timezone' => 'Europe/London', 'currency' => 'GBP', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'United States', 'code' => 'us', 'timezone' => 'America/New_York', 'currency' => 'USD', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Canada', 'code' => 'ca', 'timezone' => 'America/Toronto', 'currency' => 'CAD', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
