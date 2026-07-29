<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fiber_optiks', function (Blueprint $table) {
    $table->id();

    $table->string('nama')->nullable();
    $table->longText('geojson');

    // 1 Backbone
    // 2 FTTX Sidoarjo
    // 3 FTTX Sukodono
    // 4 FTTX Porong
    $table->tinyInteger('jalur');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiber_optiks');
    }
};
