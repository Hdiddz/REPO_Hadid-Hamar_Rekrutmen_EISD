<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Kategori Pekerjaan
        collect([
            'Kreatif dan Media' => 'kreatif-dan-media',
            'Kuliner dan Kedai Kopi' => 'kuliner-kedai-kopi',
            'Ritel dan Toko' => 'ritel-toko',
            'Logistik dan Gudang' => 'logistik-gudang',
            'Jasa dan Pelayanan' => 'jasa-pelayanan',
            'Administrasi dan Keuangan' => 'administrasi-keuangan',
        ])->each(function (string $slug, string $name): void {
            Category::updateOrCreate(['slug' => $slug], ['name' => $name]);
        });

        // 2. Keterampilan / Skills
        collect([
            'Video Editing',
            'Color Grading',
            'Storytelling',
            'Premiere Pro / DaVinci',
            'CapCut Kreatif',
            'Audio Mixing',
            'Espresso Machine',
            'Kasir POS',
            'Customer Service',
            'Manajemen Stok',
        ])->each(function (string $name): void {
            Skill::updateOrCreate(['name' => $name]);
        });

        // 3. Akun Admin Tunggal
        $admin = User::updateOrCreate(
            ['email' => 'Hadid@adm.id'],
            [
                'name' => 'Hadid@adm.id',
                'username' => 'Hadid',
                'role' => 'admin',
                'phone' => '081234567890',
                'business_name' => null,
                'password' => Hash::make('REMOVED_CREDENTIAL'),
            ]
        );

        // 4. Bersihkan data demo/user lama, hanya tinggalkan akun admin
        JobApplication::query()->delete();
        Job::query()->delete();
        User::where('id', '!=', $admin->id)->delete();
    }
}
