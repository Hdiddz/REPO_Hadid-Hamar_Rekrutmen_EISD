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
        $categories = collect([
            'Kreatif dan Media' => 'kreatif-dan-media',
            'Kuliner dan Kedai Kopi' => 'kuliner-kedai-kopi',
            'Ritel dan Toko' => 'ritel-toko',
            'Logistik dan Gudang' => 'logistik-gudang',
            'Jasa dan Pelayanan' => 'jasa-pelayanan',
            'Administrasi dan Keuangan' => 'administrasi-keuangan',
        ])->mapWithKeys(function (string $slug, string $name): array {
            $category = Category::updateOrCreate(['slug' => $slug], ['name' => $name]);

            return [$slug => $category];
        });

        // 2. Keterampilan / Skills
        $skills = collect([
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
        ])->mapWithKeys(function (string $name): array {
            $skill = Skill::updateOrCreate(['name' => $name]);

            return [$name => $skill];
        });

        // 3. Akun Admin (Tetap disediakan untuk pengujian role & tata kelola sistem)
        $admin = User::updateOrCreate(
            ['email' => 'admin@kerjalokal.id'],
            [
                'name' => 'Hafiz',
                'username' => 'Hafiz',
                'role' => 'admin',
                'phone' => '085712349988',
                'business_name' => null,
                'password' => Hash::make('REMOVED_CREDENTIAL'),
            ]
        );

        // 4. Akun Mitra Utama (Hadid Hamar)
        $hadid = User::updateOrCreate(
            ['email' => 'hadids@kerjalokal.id'],
            [
                'name' => 'hadids',
                'username' => 'hadids',
                'role' => 'employer',
                'phone' => '081234567890',
                'business_name' => 'Hadids Creative Studio',
                'password' => Hash::make('REMOVED_CREDENTIAL'),
            ]
        );

        // 5. Bersihkan data demo lama jika belum ada lowongan Video Editor
        if (Job::where('title', 'Video Editor')->doesntExist()) {
            JobApplication::query()->delete();
            Job::query()->delete();
            User::whereNotIn('id', [$admin->id, $hadid->id])->delete();

            // 6. Buat 1 lowongan kerja: Video Editor di Bandung
            $videoJob = Job::create([
                'employer_id' => $hadid->id,
                'category_id' => $categories['kreatif-dan-media']->id,
                'title' => 'Video Editor',
                'description' => 'Dibutuhkan Video Editor kreatif untuk memproduksi video konten promosi UMKM lokal Bandung, reels, tiktok, dan dokumentasi visual. Menguasai software editing (Premiere Pro/DaVinci/CapCut), memiliki ritme storytelling visual yang baik, dan mampu bekerja dalam jam kerja terukur serta upah yang layak.',
                'location' => 'Bandung',
                'salary_type' => 'monthly',
                'salary_amount' => 3500000,
                'work_hours_per_day' => 7,
                'status' => 'open',
            ]);

            $videoJob->skills()->sync(
                $skills->only(['Video Editing', 'Color Grading', 'Storytelling', 'Premiere Pro / DaVinci'])->pluck('id')
            );
        }
    }
}
