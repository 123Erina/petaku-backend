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
    Schema::create('call_centers', function (Blueprint $table) {
        $table->id();

        $table->string('no_laporan');
        $table->dateTime('waktu_lapor')->nullable();

        $table->tinyInteger('id_kategori');
        $table->string('kategori')->nullable();

        $table->text('deskripsi')->nullable();
        $table->text('lokasi')->nullable();
        $table->string('kecamatan')->nullable();
        $table->string('kelurahan')->nullable();
        $table->text('catatan')->nullable();

        $table->decimal('latitude',10,7);
        $table->decimal('longitude',10,7);

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_centers');
    }
};
