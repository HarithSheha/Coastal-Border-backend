<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['intrusion', 'vandalism', 'suspicious', 'environmental', 'sensor_alert', 'other']);
            $table->enum('severity', ['low', 'medium', 'high', 'critical']);
            $table->enum('status', ['open', 'investigating', 'resolved', 'dismissed'])->default('open');
            $table->enum('source', ['mobile', 'sensor', 'manual']);
            $table->uuid('zone_id')->nullable();
            $table->foreign('zone_id')->references('id')->on('zones')->nullOnDelete();
            $table->uuid('sensor_id')->nullable();
            $table->foreign('sensor_id')->references('id')->on('sensors')->nullOnDelete();
            $table->string('reporter_name');
            $table->string('reporter_contact')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('severity');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
