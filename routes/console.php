<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('make:admin {name} {email}', function (string $name, string $email): int {
    $password = $this->secret('Masukkan password Administrator');

    if (! is_string($password) || $password === '') {
        $this->error('Password wajib diisi.');

        return 1;
    }

    $user = User::updateOrCreate(
        ['email' => $email],
        [
            'name' => $name,
            'username' => 'Hadid',
            'role' => 'admin',
            'phone' => '081234567890',
            'password' => Hash::make($password),
        ]
    );

    $this->info('Akun Administrator berhasil dibuat / diperbarui!');
    $this->line("Nama/Username : {$user->name} / {$user->username}");
    $this->line("Email         : {$user->email}");

    return 0;
})->purpose('Buat atau perbarui akun Administrator');
