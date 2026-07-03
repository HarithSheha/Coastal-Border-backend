<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('reports', 'live_reports');
    }

    public function down(): void
    {
        Schema::rename('live_reports', 'reports');
    }
};
