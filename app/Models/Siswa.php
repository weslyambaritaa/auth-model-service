<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // PENTING

class Siswa extends Model
{
    use HasUuids; // Mengaktifkan UUID otomatis

    protected $primaryKey = 'id_siswa';
    protected $fillable = ['id_kelas', 'namaSiswa', 'NIS'];
}