<?php

namespace Database\Seeders;

use App\Models\Category;
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
            'Persiapan Makanan',
            'Packing Barang',
            'Perawatan Kendaraan',
            'Ketelitian',
            'Kreativitas',
            'Desain Grafis',
            'Microsoft Office',
        ])->each(function (string $name): void {
            Skill::updateOrCreate(['name' => $name]);
        });

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

        $this->call(DemoEmployerSeeder::class);

        // Pertahankan hanya akun admin dan akun dummy mitra johan1..johan10
        $allowedUsernames = collect(range(1, 10))->map(fn (int $i): string => 'johan'.$i)->push('Hadid');
        User::whereNotIn('username', $allowedUsernames)->delete();
    }
}
