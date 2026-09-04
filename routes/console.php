<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('make:admin {name=Hadid@adm.id} {email=Hadid@adm.id} {password=REMOVED_CREDENTIAL}', function (string $name, string $email, string $password) {
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
    $this->line("Password      : {$password}");
})->purpose('Buat atau perbarui akun Administrator');
