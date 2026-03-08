<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\KeycloakAdminService;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('hakAkses')->orderBy('created_at', 'desc')->get();
        return response()->json(['users' => $users]);
    }

    public function show($id)
    {
        $user = User::with('hakAkses')->findOrFail($id);
        return response()->json(['user' => $user]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password)
            ]);
        }

        if ($request->has('akses')) {
            // 1. Update di Database Lokal
            $user->hakAkses()->delete();
            
            $aksesArray = is_array($request->akses) ? $request->akses : [$request->akses];
            foreach ($aksesArray as $role) {
                $user->hakAkses()->create(['akses' => $role]);
            }

            // 2. Sinkronisasikan Perubahan Role ke Keycloak
            try {
                $keycloakService = new KeycloakAdminService();
                $keycloakService->syncUserRoles($user->email, $aksesArray);
            } catch (\Exception $e) {
                // Return respon error jika gagal menembus Keycloak
                return response()->json(['message' => 'User lokal terupdate, tapi gagal sinkron ke Keycloak: ' . $e->getMessage()], 500);
            }
        }

        return response()->json(['message' => 'User berhasil diupdate di Lokal dan Keycloak']);
    }
}