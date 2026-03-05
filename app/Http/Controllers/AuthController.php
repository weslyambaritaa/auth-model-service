<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\KeycloakAdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request, KeycloakAdminService $keycloak)
    {
        $aksesArray = is_array($request->akses) ? $request->akses : [$request->akses];

        // ==========================================
        // 1. TEMBAK KEYCLOAK LEWAT API
        // ==========================================
        try {
            $keycloak->createUser($request->name, $request->email, $request->password, $aksesArray);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal membuat akun di Pusat: ' . $e->getMessage()], 500);
        }

        // ==========================================
        // 2. JIKA SUKSES, SIMPAN KE DATABASE LOKAL
        // ==========================================
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Password disimpan sbg backup
        ]);

        foreach ($aksesArray as $role) {
            $user->hakAkses()->create(['akses' => $role]);
        }

        $user->load('hakAkses');
        return response()->json(['message' => 'User berhasil dibuat di Keycloak & Lokal', 'user' => $user], 201);
    }

    public function login(Request $request)
    {
        $user = User::with('hakAkses')->where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau Password salah'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login sukses',
            'access_token' => $token,
            'user' => $user
        ]);
    }
}