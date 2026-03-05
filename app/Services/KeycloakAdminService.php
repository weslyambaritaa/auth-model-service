<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class KeycloakAdminService
{
    private $baseUrl;
    private $realm;
    private $adminUser;
    private $adminPassword;

    public function __construct()
    {
        $this->baseUrl = env('KEYCLOAK_BASE_URL');
        $this->realm = env('KEYCLOAK_REALM');
        $this->adminUser = env('KEYCLOAK_ADMIN_USER');
        $this->adminPassword = env('KEYCLOAK_ADMIN_PASSWORD');
    }

    // 1. Meminta Tiket (Token) Khusus Admin ke Keycloak
    private function getAdminToken()
    {
        $response = Http::asForm()->post("{$this->baseUrl}/realms/master/protocol/openid-connect/token", [
            'client_id' => 'admin-cli',
            'username' => $this->adminUser,
            'password' => $this->adminPassword,
            'grant_type' => 'password',
        ]);

        if ($response->failed()) {
            throw new Exception("Gagal mendapatkan token admin Keycloak.");
        }

        return $response->json('access_token');
    }

    // 2. Fungsi Utama Membuat User
    public function createUser($name, $email, $password, $roles = [])
    {
        $token = $this->getAdminToken();

        // A. Buat Akun di Keycloak
        $response = Http::withToken($token)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$this->baseUrl}/admin/realms/{$this->realm}/users", [
                'username' => $email, // Jadikan email sebagai username login
                'email' => $email,
                'firstName' => $name,
                'enabled' => true,
                'emailVerified' => true,
                'credentials' => [
                    [
                        'type' => 'password',
                        'value' => $password,
                        'temporary' => false,
                    ]
                ]
            ]);

        if ($response->status() !== 201) {
            throw new Exception("Keycloak menolak pembuatan user: " . $response->body());
        }

        // B. Cari ID User yang baru saja dibuat
        $userResponse = Http::withToken($token)->get("{$this->baseUrl}/admin/realms/{$this->realm}/users", [
            'email' => $email,
            'exact' => true,
        ]);
        $kcUserId = $userResponse->json()[0]['id'];

        // C. Tembakkan Role (Jabatan) ke User tersebut
        foreach ($roles as $roleName) {
            // Cari ID dari Role tersebut di Keycloak
            $roleResp = Http::withToken($token)->get("{$this->baseUrl}/admin/realms/{$this->realm}/roles/{$roleName}");
            if ($roleResp->successful()) {
                $roleData = $roleResp->json();
                
                // Pasangkan Role ke User
                Http::withToken($token)->post("{$this->baseUrl}/admin/realms/{$this->realm}/users/{$kcUserId}/role-mappings/realm", [
                    [
                        'id' => $roleData['id'],
                        'name' => $roleData['name']
                    ]
                ]);
            }
        }

        return true;
    }
}