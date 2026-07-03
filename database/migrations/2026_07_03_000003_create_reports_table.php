<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id('report_id');

            $table->date('date');

            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('address', 255);

            $table->uuid('zone_id');
            $table->foreign('zone_id', 'fk_reports_zone')->references('id')->on('zones')
                ->restrictOnDelete()->cascadeOnUpdate();

            $table->string('color', 50);
            $table->integer('number_of_people');
            $table->text('description')->nullable();
            $table->string('photo', 255)->nullable();

            $table->string('name', 100);
            $table->string('phone', 20);

            $table->foreignId('urgency_id')->constrained('urgencies', 'urgency_id', 'fk_reports_urgency')
                ->restrictOnDelete()->cascadeOnUpdate();

            $table->timestamps();

            $table->index('date');
            $table->index('zone_id');
            $table->index('urgency_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
