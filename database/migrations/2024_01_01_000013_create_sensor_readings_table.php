<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sensor_id');
            $table->foreign('sensor_id')->references('id')->on('sensors')->cascadeOnDelete();
            $table->decimal('value', 15, 6);
            $table->string('unit', 50);
            $table->boolean('triggered')->default(false);
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();

            $table->index('sensor_id');
            $table->index('recorded_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensor_readings');
    }
};
