<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sensors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->uuid('zone_id')->nullable();
            $table->foreign('zone_id')->references('id')->on('zones')->nullOnDelete();
            $table->enum('type', ['motion', 'thermal', 'camera', 'vibration', 'gas', 'smoke']);
            $table->enum('status', ['online', 'offline', 'alert', 'maintenance'])->default('online');
            $table->integer('battery_level')->default(100);
            $table->timestamp('last_ping')->useCurrent();
            $table->decimal('x_percent', 8, 4)->nullable();
            $table->decimal('y_percent', 8, 4)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('zone_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensors');
    }
};
