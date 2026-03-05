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
    Schema::create('kelas', function (Blueprint $table) {
        $table->id('id_kelas');
        $table->string('nama_kelas');
        // Tambahkan kolom penghubung ke User (Wali Kelas)
        $table->unsignedBigInteger('wali_kelas_id')->nullable(); 
        $table->timestamps();

        // Jadikan Foreign Key
        $table->foreign('wali_kelas_id')->references('id')->on('users')->onDelete('set null');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
