<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoEmployerSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::query()->pluck('id', 'slug');
        $skills = Skill::query()->pluck('id', 'name');
        $password = Hash::make('REMOVED_CREDENTIAL');

        $employers = [
            [
                'business_name' => 'Kedai Kopi Pagi Sore',
                'phone' => '081290000001',
                'job' => [
                    'title' => 'Barista',
                    'description' => 'Melayani pelanggan dan menyiapkan minuman kopi sesuai standar kedai. Pengalaman dasar menggunakan mesin espresso menjadi nilai tambah.',
                    'category' => 'kuliner-kedai-kopi',
                    'location' => 'Bandung',
                    'salary_type' => 'monthly',
                    'salary_amount' => 3200000,
                    'work_hours_per_day' => 8,
                    'skills' => ['Espresso Machine', 'Customer Service'],
                ],
            ],
            [
                'business_name' => 'Studio Cerita Visual',
                'phone' => '081290000002',
                'job' => [
                    'title' => 'Video Editor',
                    'description' => 'Mengolah video pendek untuk media sosial UMKM dan menyusun cerita visual yang menarik. Portofolio editing sederhana dapat dilampirkan saat melamar.',
                    'category' => 'kreatif-dan-media',
                    'location' => 'Jakarta Selatan',
                    'salary_type' => 'monthly',
                    'salary_amount' => 4800000,
                    'work_hours_per_day' => 8,
                    'skills' => ['Video Editing', 'Storytelling'],
                ],
            ],
            [
                'business_name' => 'Toko Harian Sejahtera',
                'phone' => '081290000003',
                'job' => [
                    'title' => 'Kasir Toko',
                    'description' => 'Melayani transaksi pelanggan, mencatat pemasukan, dan menjaga kerapian area kasir. Dibutuhkan pribadi yang ramah dan teliti.',
                    'category' => 'ritel-toko',
                    'location' => 'Cimahi',
                    'salary_type' => 'monthly',
                    'salary_amount' => 3100000,
                    'work_hours_per_day' => 7,
                    'skills' => ['Kasir POS', 'Customer Service'],
                ],
            ],
            [
                'business_name' => 'Dapur Rasa Ibu',
                'phone' => '081290000004',
                'job' => [
                    'title' => 'Staf Dapur',
                    'description' => 'Membantu persiapan bahan, menjaga kebersihan dapur, dan menyiapkan pesanan harian. Pengalaman kerja di bidang kuliner lebih disukai.',
                    'category' => 'kuliner-kedai-kopi',
                    'location' => 'Bogor',
                    'salary_type' => 'monthly',
                    'salary_amount' => 3300000,
                    'work_hours_per_day' => 8,
                    'skills' => ['Persiapan Makanan', 'Ketelitian'],
                ],
            ],
            [
                'business_name' => 'Gudang Gerak Cepat',
                'phone' => '081290000005',
                'job' => [
                    'title' => 'Staf Gudang',
                    'description' => 'Menerima barang, menata stok, dan menyiapkan pesanan untuk pengiriman. Pekerjaan membutuhkan ketelitian dan kesiapan bekerja dalam tim.',
                    'category' => 'logistik-gudang',
                    'location' => 'Bekasi',
                    'salary_type' => 'monthly',
                    'salary_amount' => 3900000,
                    'work_hours_per_day' => 8,
                    'skills' => ['Manajemen Stok', 'Packing Barang'],
                ],
            ],
            [
                'business_name' => 'Bengkel Roda Jaya',
                'phone' => '081290000006',
                'job' => [
                    'title' => 'Mekanik Junior',
                    'description' => 'Membantu pemeriksaan dan perawatan kendaraan pelanggan di bawah arahan mekanik senior. Terbuka untuk lulusan baru bidang otomotif.',
                    'category' => 'jasa-pelayanan',
                    'location' => 'Tangerang',
                    'salary_type' => 'monthly',
                    'salary_amount' => 4100000,
                    'work_hours_per_day' => 8,
                    'skills' => ['Perawatan Kendaraan', 'Customer Service'],
                ],
            ],
            [
                'business_name' => 'Laundry Bersih Kita',
                'phone' => '081290000007',
                'job' => [
                    'title' => 'Petugas Laundry',
                    'description' => 'Menangani pencucian, penyetrikaan, dan pengepakan pakaian pelanggan. Jadwal kerja tersusun dan pelatihan alat disediakan.',
                    'category' => 'jasa-pelayanan',
                    'location' => 'Depok',
                    'salary_type' => 'monthly',
                    'salary_amount' => 3000000,
                    'work_hours_per_day' => 7,
                    'skills' => ['Ketelitian', 'Customer Service'],
                ],
            ],
            [
                'business_name' => 'Toko Bunga Mekar',
                'phone' => '081290000008',
                'job' => [
                    'title' => 'Perangkai Bunga',
                    'description' => 'Membuat rangkaian bunga sesuai pesanan dan membantu pelanggan memilih desain. Kreativitas dan perhatian pada detail menjadi nilai utama.',
                    'category' => 'ritel-toko',
                    'location' => 'Yogyakarta',
                    'salary_type' => 'monthly',
                    'salary_amount' => 2900000,
                    'work_hours_per_day' => 7,
                    'skills' => ['Kreativitas', 'Customer Service'],
                ],
            ],
            [
                'business_name' => 'Percetakan Titik Warna',
                'phone' => '081290000009',
                'job' => [
                    'title' => 'Operator Percetakan',
                    'description' => 'Menyiapkan file cetak, mengoperasikan mesin, dan memastikan hasil produksi sesuai pesanan. Pemahaman desain dasar akan membantu pekerjaan.',
                    'category' => 'kreatif-dan-media',
                    'location' => 'Surabaya',
                    'salary_type' => 'monthly',
                    'salary_amount' => 3700000,
                    'work_hours_per_day' => 8,
                    'skills' => ['Desain Grafis', 'Ketelitian'],
                ],
            ],
            [
                'business_name' => 'Rumah Niaga Nusantara',
                'phone' => '081290000010',
                'job' => [
                    'title' => 'Admin Penjualan',
                    'description' => 'Mencatat pesanan, memperbarui data penjualan, dan menjawab pertanyaan pelanggan. Kandidat diharapkan rapi dalam mengelola dokumen.',
                    'category' => 'administrasi-keuangan',
                    'location' => 'Semarang',
                    'salary_type' => 'monthly',
                    'salary_amount' => 3500000,
                    'work_hours_per_day' => 8,
                    'skills' => ['Microsoft Office', 'Customer Service'],
                ],
            ],
        ];

        foreach ($employers as $index => $employerData) {
            $sequence = $index + 1;
            $username = 'johan'.$sequence;
            $jobData = $employerData['job'];

            $employer = User::updateOrCreate(
                ['username' => $username],
                [
                    'name' => $username,
                    'email' => $username.'@kerjalokal.test',
                    'role' => 'employer',
                    'phone' => $employerData['phone'],
                    'business_name' => $employerData['business_name'],
                    'password' => $password,
                ]
            );

            $job = $employer->jobs()->updateOrCreate(
                ['title' => $jobData['title']],
                [
                    'category_id' => $categories[$jobData['category']],
                    'description' => $jobData['description'],
                    'location' => $jobData['location'],
                    'salary_type' => $jobData['salary_type'],
                    'salary_amount' => $jobData['salary_amount'],
                    'work_hours_per_day' => $jobData['work_hours_per_day'],
                    'status' => 'open',
                ]
            );

            $job->skills()->sync(
                collect($jobData['skills'])
                    ->map(fn (string $skillName): int => $skills[$skillName])
                    ->all()
            );
        }
    }
}
