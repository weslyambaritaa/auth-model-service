<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            $user->hakAkses()->delete();
            
            $aksesArray = is_array($request->akses) ? $request->akses : [$request->akses];
            foreach ($aksesArray as $role) {
                $user->hakAkses()->create(['akses' => $role]);
            }
        }

        return response()->json(['message' => 'User berhasil diupdate']);
    }
}