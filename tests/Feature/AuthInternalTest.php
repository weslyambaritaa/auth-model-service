<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

// TAMBAHKAN Tests\TestCase::class di dalam uses()
uses(Tests\TestCase::class, RefreshDatabase::class);

test('bisa melakukan pendaftaran user baru beserta hak aksesnya', function () {
    $response = $this->postJson('/api/internal/register', [
        'name' => 'Guru Testing',
        'email' => 'guru@sigumpar.sch.id',
        'password' => 'password123',
        'akses' => 'Guru'
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('users', ['email' => 'guru@sigumpar.sch.id']);
    $this->assertDatabaseHas('hak_akses', ['akses' => 'Guru']);
});

test('bisa login dan mendapatkan token serta data relasi hak akses', function () {
    // 1. Persiapan data user palsu (factory)
    $user = User::factory()->create([
        'email' => 'admin@sigumpar.sch.id',
        'password' => bcrypt('rahasia123'),
    ]);
    $user->hakAkses()->create(['akses' => 'Admin']);

    // 2. Simulasi hit API Login
    $response = $this->postJson('/api/internal/login', [
        'email' => 'admin@sigumpar.sch.id',
        'password' => 'rahasia123',
    ]);

    // 3. Pastikan API merespons dengan struktur token dan hak akses yang benar
    $response->assertStatus(200)
             ->assertJsonStructure([
                 'message',
                 'access_token',
                 'user' => [
                     'id',
                     'name',
                     'email',
                     'hak_akses' => [
                         '*' => ['id', 'akses']
                     ]
                 ]
             ]);
});

test('gagal login jika password salah', function () {
    User::factory()->create([
        'email' => 'salah@sigumpar.sch.id',
        'password' => bcrypt('passwordbenar'),
    ]);

    $response = $this->postJson('/api/internal/login', [
        'email' => 'salah@sigumpar.sch.id',
        'password' => 'passwordsalah',
    ]);

    $response->assertStatus(401)
             ->assertJson(['message' => 'Email atau Password salah']);
});