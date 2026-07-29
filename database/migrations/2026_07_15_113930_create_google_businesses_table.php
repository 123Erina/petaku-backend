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
       Schema::create('google_businesses', function (Blueprint $table) {
    $table->id();

    $table->string('nama');
    $table->text('alamat')->nullable();

    $table->decimal('latitude',10,7);
    $table->decimal('longitude',10,7);

    $table->string('range_harga')->nullable();
    $table->string('nomor_telp')->nullable();

    $table->unsignedTinyInteger('kategori');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_businesses');
    }
};
