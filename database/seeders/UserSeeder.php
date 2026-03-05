<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::create([
    'name' => 'Admin TU',
    'email' => 'tu@sigumpar.sch.id',
    'password' => bcrypt('password123'),
]);
$user->hakAkses()->create(['akses' => 'Tata Usaha']);
    }
}
