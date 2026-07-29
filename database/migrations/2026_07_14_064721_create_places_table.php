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
    Schema::create('places', function (Blueprint $table) {

        $table->id();

        $table->foreignId('category_id')
              ->constrained('categories')
              ->cascadeOnDelete();

        $table->string('nama');

        $table->text('alamat')->nullable();

        $table->decimal('latitude',10,7);

        $table->decimal('longitude',10,7);

        $table->text('deskripsi')->nullable();

        $table->string('gambar')->nullable();

        $table->string('website')->nullable();

        $table->string('telepon')->nullable();

        $table->boolean('status')->default(true);

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('places');
    }
};
