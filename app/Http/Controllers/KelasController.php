<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index() {
        $kelas = Kelas::leftJoin('users', 'kelas.wali_kelas_id', '=', 'users.id')
            ->select('kelas.*', 'users.name as nama_wali_kelas')
            ->orderBy('kelas.nama_kelas', 'asc')
            ->get();
        return response()->json(['kelas' => $kelas]);
    }

    public function store(Request $request) {
        $kelas = Kelas::create($request->all());
        return response()->json(['message' => 'Kelas berhasil ditambah', 'kelas' => $kelas]);
    }

    public function show($id) {
        $kelas = Kelas::findOrFail($id);
        return response()->json(['kelas' => $kelas]);
    }

    public function update(Request $request, $id) {
        $kelas = Kelas::findOrFail($id);
        $kelas->update($request->all());
        return response()->json(['message' => 'Kelas berhasil diupdate']);
    }

    // Mengambil daftar akun yang memiliki role WALI KELAS
    public function listWaliKelas() {
        $waliKelas = User::whereHas('hakAkses', function ($query) {
            $query->where('akses', 'WALI KELAS');
        })->get();
        
        return response()->json(['wali_kelas' => $waliKelas]);
    }
}