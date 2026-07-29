<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            $table->string('old_id')->nullable();
            $table->string('no_laporan')->nullable();
            $table->dateTime('waktu_lapor')->nullable();

            $table->string('kategori');
            $table->tinyInteger('id_kategori');

            $table->text('deskripsi')->nullable();
            $table->text('lokasi')->nullable();

            $table->string('kecamatan')->nullable();
            $table->string('kelurahan')->nullable();
            $table->text('catatan')->nullable();

            $table->decimal('latitude',10,7)->nullable();
            $table->decimal('longitude',10,7)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
