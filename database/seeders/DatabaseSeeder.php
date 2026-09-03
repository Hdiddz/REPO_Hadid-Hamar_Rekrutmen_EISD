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
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@kerjalokal.id'],
            ['name' => 'Dewi Kartika', 'role' => 'admin', 'phone' => '085712349988', 'business_name' => null, 'password' => Hash::make('REMOVED_CREDENTIAL')],
        );

        $employer = User::updateOrCreate(
            ['email' => 'mitra@suduttemu.id'],
            ['name' => 'Hendra Wijaya', 'role' => 'employer', 'phone' => '082198765432', 'business_name' => 'Kedai Kopi Sudut Temu', 'password' => Hash::make('REMOVED_CREDENTIAL')],
        );

        $secondEmployer = User::updateOrCreate(
            ['email' => 'mitra@berkahgrosir.id'],
            ['name' => 'Nur Aisyah', 'role' => 'employer', 'phone' => '081322334455', 'business_name' => 'Grosir Berkah Mandiri', 'password' => Hash::make('REMOVED_CREDENTIAL')],
        );

        $jobseekers = collect([
            ['name' => 'Budi Santoso', 'email' => 'budi@kerjalokal.id', 'phone' => '081234567890'],
            ['name' => 'Siti Rahma', 'email' => 'siti@kerjalokal.id', 'phone' => '081298765431'],
            ['name' => 'Dimas Pratama', 'email' => 'dimas@kerjalokal.id', 'phone' => '085612340987'],
        ])->map(fn (array $data): User => User::updateOrCreate(
            ['email' => $data['email']],
            $data + ['role' => 'jobseeker', 'business_name' => null, 'password' => Hash::make('REMOVED_CREDENTIAL')],
        ));

        $categories = collect([
            'Kuliner dan Kedai Kopi' => 'kuliner-kedai-kopi',
            'Ritel dan Toko' => 'ritel-toko',
            'Logistik dan Gudang' => 'logistik-gudang',
            'Jasa dan Pelayanan' => 'jasa-pelayanan',
            'Administrasi dan Keuangan' => 'administrasi-keuangan',
        ])->mapWithKeys(function (string $slug, string $name): array {
            $category = Category::updateOrCreate(['slug' => $slug], ['name' => $name]);

            return [$slug => $category];
        });

        $skills = collect([
            'Espresso Machine',
            'Kasir POS',
            'Customer Service',
            'Manajemen Stok',
            'Packing Barang',
            'Excel Dasar',
            'SIM C Aktif',
        ])->mapWithKeys(function (string $name): array {
            $skill = Skill::updateOrCreate(['name' => $name]);

            return [$name => $skill];
        });

        $barista = Job::updateOrCreate(
            ['employer_id' => $employer->id, 'title' => 'Barista dan Kasir Kedai Kopi'],
            [
                'category_id' => $categories['kuliner-kedai-kopi']->id,
                'description' => 'Melayani pelanggan, menyiapkan minuman kopi, mengoperasikan mesin kasir, dan menjaga kebersihan area kerja. Jadwal dibagi dalam shift pagi atau siang dengan lingkungan kerja yang aman.',
                'location' => 'Jl. Dipatiukur No. 42, Bandung',
                'salary_type' => 'monthly',
                'salary_amount' => 3200000,
                'work_hours_per_day' => 7,
                'status' => 'open',
            ],
        );
        $barista->skills()->sync($skills->only(['Espresso Machine', 'Kasir POS', 'Customer Service'])->pluck('id'));

        $warehouse = Job::updateOrCreate(
            ['employer_id' => $employer->id, 'title' => 'Staf Gudang Bahan dan Roastery'],
            [
                'category_id' => $categories['logistik-gudang']->id,
                'description' => 'Menerima bahan baku, mencatat stok masuk dan keluar, menata produk, serta membantu proses pengepakan. Pekerjaan dilakukan maksimal delapan jam setiap hari.',
                'location' => 'Coblong, Bandung',
                'salary_type' => 'monthly',
                'salary_amount' => 2800000,
                'work_hours_per_day' => 8,
                'status' => 'open',
            ],
        );
        $warehouse->skills()->sync($skills->only(['Manajemen Stok', 'Packing Barang', 'Excel Dasar'])->pluck('id'));

        $cashier = Job::updateOrCreate(
            ['employer_id' => $secondEmployer->id, 'title' => 'Kasir Toko Grosir'],
            [
                'category_id' => $categories['ritel-toko']->id,
                'description' => 'Melayani transaksi pelanggan, mencatat pembayaran, memeriksa kesesuaian harga, dan menyusun laporan kas harian secara tertib dan transparan.',
                'location' => 'Antapani, Bandung',
                'salary_type' => 'daily',
                'salary_amount' => 135000,
                'work_hours_per_day' => 8,
                'status' => 'open',
            ],
        );
        $cashier->skills()->sync($skills->only(['Kasir POS', 'Customer Service', 'Excel Dasar'])->pluck('id'));

        $closedJob = Job::updateOrCreate(
            ['employer_id' => $employer->id, 'title' => 'Petugas Layanan Akhir Pekan'],
            [
                'category_id' => $categories['jasa-pelayanan']->id,
                'description' => 'Mendukung pelayanan pelanggan pada akhir pekan dan memastikan area pelayanan tetap rapi selama operasional berlangsung.',
                'location' => 'Dago, Bandung',
                'salary_type' => 'daily',
                'salary_amount' => 125000,
                'work_hours_per_day' => 6,
                'status' => 'closed',
            ],
        );
        $closedJob->skills()->sync($skills->only(['Customer Service'])->pluck('id'));

        $resumePaths = [
            'budi' => 'resumes/demo-budi-santoso.pdf',
            'siti' => 'resumes/demo-siti-rahma.pdf',
            'dimas' => 'resumes/demo-dimas-pratama.pdf',
        ];

        Storage::disk('local')->put($resumePaths['budi'], $this->makeDemoPdf('Budi Santoso'));
        Storage::disk('local')->put($resumePaths['siti'], $this->makeDemoPdf('Siti Rahma'));
        Storage::disk('local')->put($resumePaths['dimas'], $this->makeDemoPdf('Dimas Pratama'));

        JobApplication::updateOrCreate(
            ['job_id' => $barista->id, 'user_id' => $jobseekers[0]->id],
            ['resume_file' => $resumePaths['budi'], 'note' => 'Berpengalaman sebagai barista dan terbiasa menggunakan mesin kasir POS.', 'status' => 'accepted'],
        );
        JobApplication::updateOrCreate(
            ['job_id' => $barista->id, 'user_id' => $jobseekers[1]->id],
            ['resume_file' => $resumePaths['siti'], 'note' => 'Lulusan SMK Tata Boga dan siap bekerja dalam sistem shift.', 'status' => 'interview'],
        );
        JobApplication::updateOrCreate(
            ['job_id' => $warehouse->id, 'user_id' => $jobseekers[2]->id],
            ['resume_file' => $resumePaths['dimas'], 'note' => 'Terbiasa menangani stok dan melakukan pengepakan barang.', 'status' => 'pending'],
        );

        $admin->touch();
    }

    private function makeDemoPdf(string $name): string
    {
        $stream = "BT /F1 16 Tf 72 720 Td (Resume Demo KerjaLokal - {$name}) Tj ET";
        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>',
            '<< /Length '.strlen($stream)." >>\nstream\n{$stream}\nendstream",
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $objectNumber = $index + 1;
            $pdf .= "{$objectNumber} 0 obj\n{$object}\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 6\n0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $pdf .= sprintf('%010d 00000 n ', $offset)."\n";
        }

        return $pdf."trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF\n";
    }
}
