<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oui_vendors', function (Blueprint $table) {
            $table->string('prefix', 6)->primary(); // e.g. '0050C2' — uppercase, no separators
            $table->string('vendor', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oui_vendors');
    }
};
