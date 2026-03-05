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
    Schema::create('siswas', function (Blueprint $table) {
        $table->uuid('id_siswa')->primary(); // Otomatis jadi UUID
        $table->unsignedBigInteger('id_kelas');
        $table->string('namaSiswa');
        $table->string('NIS')->unique();
        $table->timestamps();

        // Hubungkan ke tabel kelas
        $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
