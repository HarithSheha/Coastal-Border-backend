<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('urgencies', function (Blueprint $table) {
            $table->id('urgency_id');
            $table->string('urgency_level', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('urgencies');
    }
};
