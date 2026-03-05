<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HakAkses extends Model
{
    use HasFactory;

    protected $table = 'hak_akses';
    protected $fillable = ['user_id', 'akses'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}