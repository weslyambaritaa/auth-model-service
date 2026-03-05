<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index() {
        $siswas = Siswa::join('kelas', 'siswas.id_kelas', '=', 'kelas.id_kelas')
            ->select('siswas.*', 'kelas.nama_kelas')
            ->orderBy('siswas.created_at', 'desc')
            ->get();
        return response()->json(['siswas' => $siswas]);
    }

    public function store(Request $request) {
        $siswa = Siswa::create($request->all());
        return response()->json(['message' => 'Siswa berhasil ditambah', 'siswa' => $siswa]);
    }

    public function show($id) {
        $siswa = Siswa::findOrFail($id);
        return response()->json(['siswa' => $siswa]);
    }

    public function update(Request $request, $id) {
        $siswa = Siswa::findOrFail($id);
        $siswa->update($request->all());
        return response()->json(['message' => 'Siswa berhasil diupdate']);
    }
}