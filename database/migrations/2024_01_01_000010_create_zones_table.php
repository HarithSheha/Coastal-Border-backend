<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['restricted', 'danger', 'caution', 'checkpoint']);
            $table->enum('status', ['active', 'inactive', 'breach'])->default('active');
            $table->string('color', 20)->default('#ef4444');
            $table->decimal('x_percent', 8, 4)->nullable();
            $table->decimal('y_percent', 8, 4)->nullable();
            $table->decimal('width_percent', 8, 4)->nullable();
            $table->decimal('height_percent', 8, 4)->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};
