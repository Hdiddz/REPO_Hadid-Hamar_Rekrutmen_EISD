<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ChatMessage;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmployerJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_can_create_job_and_sync_skill_pivot(): void
    {
        $employer = User::factory()->employer()->create();
        $category = Category::factory()->create();
        $skills = Skill::factory()->count(2)->create();

        $response = $this->actingAs($employer)->post(route('employer.jobs.store'), [
            'category_id' => $category->id,
            'title' => 'Barista dan Kasir',
            'description' => 'Melayani pelanggan, meracik minuman, dan menjaga kebersihan area kerja setiap hari.',
            'location' => 'Coblong, Kota Bandung',
            'salary_type' => 'monthly',
            'salary_amount' => 3200000,
            'work_hours_per_day' => 7,
            'skills' => $skills->modelKeys(),
        ]);

        $response->assertRedirect(route('employer.dashboard'))
            ->assertSessionHas('success');
        $job = Job::query()->sole();
        $this->assertSame($employer->id, $job->employer_id);
        $this->assertEqualsCanonicalizing($skills->modelKeys(), $job->skills()->pluck('skills.id')->all());
    }

    public function test_job_hours_cannot_exceed_eight_and_error_is_visible_below_field(): void
    {
        $employer = User::factory()->employer()->create();
        $category = Category::factory()->create();
        $skill = Skill::factory()->create();

        $response = $this->actingAs($employer)->followingRedirects()->from(route('employer.jobs.create'))->post(route('employer.jobs.store'), [
            'category_id' => $category->id,
            'title' => 'Kasir',
            'description' => 'Melayani transaksi pelanggan dengan ramah dan membuat pencatatan penjualan harian.',
            'location' => 'Bandung',
            'salary_type' => 'daily',
            'salary_amount' => 150000,
            'work_hours_per_day' => 10,
            'skills' => [$skill->id],
        ]);

        $response->assertOk()
            ->assertSee('Jam kerja harus berada di antara 1 sampai 8 jam per hari.');
    }

    public function test_employer_cannot_edit_another_employers_job(): void
    {
        $owner = User::factory()->employer()->create();
        $intruder = User::factory()->employer()->create();
        $job = Job::factory()->for($owner, 'employer')->create();

        $this->actingAs($intruder)->get(route('employer.jobs.edit', $job))->assertForbidden();
    }

    public function test_employer_can_add_custom_new_skills_when_creating_job(): void
    {
        $employer = User::factory()->employer()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($employer)->post(route('employer.jobs.store'), [
            'category_id' => $category->id,
            'title' => 'Motion Designer',
            'description' => 'Mendesain animasi grafis gerak untuk promosi media sosial UMKM secara kreatif dan terstruktur.',
            'location' => 'Bandung',
            'salary_type' => 'monthly',
            'salary_amount' => 4000000,
            'work_hours_per_day' => 7,
            'new_skills' => ['After Effects', 'Cinema 4D'],
        ]);

        $response->assertRedirect(route('employer.dashboard'))
            ->assertSessionHas('success');
        $job = Job::query()->where('title', 'Motion Designer')->firstOrFail();
        $this->assertCount(2, $job->skills);
        $this->assertTrue(Skill::where('name', 'After Effects')->exists());
        $this->assertTrue(Skill::where('name', 'Cinema 4D')->exists());
    }

    public function test_job_detail_page_shows_edit_button_for_owner_and_last_updated_time(): void
    {
        $employer = User::factory()->employer()->create();
        $job = Job::factory()->for($employer, 'employer')->create();

        $response = $this->actingAs($employer)->get(route('jobs.show', $job));

        $response->assertOk()
            ->assertSee('Edit Lowongan Ini')
            ->assertSee(route('employer.jobs.edit', $job))
            ->assertSee('Status Pembaruan Mitra:');
    }

    public function test_employer_cannot_destroy_global_skill(): void
    {
        $employer = User::factory()->employer()->create();
        $skill = Skill::factory()->create(['name' => 'Keahlian Bersama']);

        $response = $this->actingAs($employer)->deleteJson("/mitra/keterampilan/{$skill->id}");

        $response->assertNotFound();
        $this->assertModelExists($skill);
    }

    public function test_employer_can_close_and_reopen_job_status(): void
    {
        $employer = User::factory()->employer()->create();
        $job = Job::factory()->for($employer, 'employer')->create(['status' => 'open']);

        // Detail page shows status badge, but close button is only in edit mode
        $this->actingAs($employer)->get(route('jobs.show', $job))
            ->assertOk()
            ->assertDontSee('Tutup Lowongan Ini')
            ->assertSee('Sedang Dibuka');

        // Employer closes job via status endpoint or edit form
        $this->actingAs($employer)->patch(route('employer.jobs.status', $job), ['status' => 'closed'])
            ->assertSessionHas('success');

        $this->assertSame('closed', $job->fresh()->status);

        // Shows Sedang Ditutup status on detail page without inline toggle button
        $this->actingAs($employer)->get(route('jobs.show', $job))
            ->assertOk()
            ->assertDontSee('Buka Kembali Lowongan Ini')
            ->assertSee('Sedang Ditutup');
    }

    public function test_accepted_applicants_are_recorded_and_displayed_on_employer_dashboard(): void
    {
        $employer = User::factory()->employer()->create(['business_name' => 'Kedai Kopi Nusantara']);
        $job = Job::factory()->for($employer, 'employer')->create(['title' => 'Barista Senior']);
        $jobseeker = User::factory()->jobseeker()->create(['name' => 'Budi Santoso']);
        $application = JobApplication::factory()->create([
            'job_id' => $job->id,
            'user_id' => $jobseeker->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($employer)->patch(route('employer.applications.update', $application), [
            'status' => 'accepted',
        ]);

        $response->assertSessionHas('success');
        $this->assertSame('accepted', $application->fresh()->status);

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $employer->id,
            'receiver_id' => $jobseeker->id,
        ]);

        $dashboardResponse = $this->actingAs($employer)->get(route('employer.dashboard'));
        $dashboardResponse->assertOk()
            ->assertSee('Peserta &amp; Tenaga Kerja Diterima', false)
            ->assertSee('Budi Santoso')
            ->assertSee('Barista Senior')
            ->assertSee('Diterima Bekerja')
            ->assertSee('Chat Peserta');
    }

    public function test_employer_can_schedule_interview_with_custom_modal_fields(): void
    {
        $employer = User::factory()->employer()->create(['business_name' => 'Studio Foto Kreatif']);
        $job = Job::factory()->for($employer, 'employer')->create(['title' => 'Videografer']);
        $jobseeker = User::factory()->jobseeker()->create(['name' => 'Ahmad Fauzi']);
        $application = JobApplication::factory()->create([
            'job_id' => $job->id,
            'user_id' => $jobseeker->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($employer)->patch(route('employer.applications.update', $application), [
            'status' => 'interview',
            'interview_date' => '2026-09-10',
            'interview_time' => '14:00',
            'interview_type' => 'Online via Google Meet',
            'interview_location' => 'https://meet.google.com/abc-defg-hij',
            'interview_notes' => 'Mohon siapkan laptop dan contoh karya video terbaru Anda.',
        ]);

        $response->assertSessionHas('success');
        $this->assertSame('interview', $application->fresh()->status);

        // Verify candidate received chat invitation with schedule
        $chat = ChatMessage::where('receiver_id', $jobseeker->id)->latest('id')->first();
        $this->assertNotNull($chat);
        $this->assertStringContainsString('Undangan Wawancara Kerja', $chat->message);
        $this->assertStringContainsString('14:00 WIB', $chat->message);
        $this->assertStringContainsString('meet.google.com', $chat->message);

        // Verify notification was sent
        $notif = $jobseeker->notifications()->first();
        $this->assertNotNull($notif);
        $this->assertSame('interview', $notif->data['status']);
        $this->assertStringContainsString('wawancara', $notif->data['message']);
    }

    public function test_employer_applications_page_renders_specialized_status_modals(): void
    {
        $employer = User::factory()->employer()->create();
        $job = Job::factory()->for($employer, 'employer')->create();
        $jobseeker = User::factory()->jobseeker()->create();
        JobApplication::factory()->create([
            'job_id' => $job->id,
            'user_id' => $jobseeker->id,
        ]);

        $response = $this->actingAs($employer)->get(route('employer.applications.index'));

        $response->assertOk()
            ->assertSee('modalStatusInterview')
            ->assertSee('modalStatusAccepted')
            ->assertSee('modalStatusRejected')
            ->assertSee('modalStatusPending')
            ->assertSee('Atur & Perbarui Status', false);
    }

    public function test_employer_can_create_job_with_cover_image_and_workplace_photos(): void
    {
        Storage::fake('public');

        $employer = User::factory()->employer()->create();
        $category = Category::factory()->create();
        $skill = Skill::factory()->create();

        $coverFile = UploadedFile::fake()->create('coffee_shop_cover.jpg', 500, 'image/jpeg');
        $photo1 = UploadedFile::fake()->create('barista_corner.jpg', 400, 'image/jpeg');
        $photo2 = UploadedFile::fake()->create('kitchen_area.png', 400, 'image/png');

        $response = $this->actingAs($employer)->post(route('employer.jobs.store'), [
            'category_id' => $category->id,
            'title' => 'Barista Kedai Kopi',
            'description' => 'Meracik kopi spesialti dan menjaga keramahan kedai kopi lokal setiap shift.',
            'location' => 'Sumur Bandung, Kota Bandung',
            'salary_type' => 'monthly',
            'salary_amount' => 3500000,
            'work_hours_per_day' => 8,
            'skills' => [$skill->id],
            'cover_image' => $coverFile,
            'workplace_photos' => [$photo1, $photo2],
        ]);

        $response->assertRedirect(route('employer.dashboard'))
            ->assertSessionHas('success');

        $job = Job::query()->where('title', 'Barista Kedai Kopi')->firstOrFail();
        $this->assertNotNull($job->cover_image);
        Storage::disk('public')->assertExists($job->cover_image);

        $this->assertCount(2, $job->workplacePhotos);
        foreach ($job->workplacePhotos as $workplacePhoto) {
            Storage::disk('public')->assertExists($workplacePhoto->photo_path);
        }
    }

    public function test_employer_can_update_job_and_replace_or_remove_cover_image(): void
    {
        Storage::fake('public');

        $employer = User::factory()->employer()->create();
        $category = Category::factory()->create();
        $skill = Skill::factory()->create();

        $initialCover = UploadedFile::fake()->create('initial_cover.jpg', 300, 'image/jpeg');
        $initialCoverPath = $initialCover->store('jobs/covers', 'public');

        $job = Job::factory()->for($employer, 'employer')->create([
            'category_id' => $category->id,
            'cover_image' => $initialCoverPath,
        ]);

        Storage::disk('public')->assertExists($initialCoverPath);

        // Test replacing cover image
        $newCover = UploadedFile::fake()->create('new_cover.jpg', 300, 'image/jpeg');
        $response = $this->actingAs($employer)->put(route('employer.jobs.update', $job), [
            'category_id' => $category->id,
            'title' => 'Barista Baru',
            'status' => 'open',
            'description' => 'Deskripsi pekerjaan baru yang mencakup operasional harian kedai kopi.',
            'location' => 'Bandung Wetan',
            'salary_type' => 'monthly',
            'salary_amount' => 3600000,
            'work_hours_per_day' => 8,
            'skills' => [$skill->id],
            'cover_image' => $newCover,
        ]);

        $response->assertRedirect(route('jobs.show', $job));
        $job->refresh();
        Storage::disk('public')->assertMissing($initialCoverPath);
        Storage::disk('public')->assertExists($job->cover_image);

        // Test removing cover image
        $currentCoverPath = $job->cover_image;
        $responseRemove = $this->actingAs($employer)->put(route('employer.jobs.update', $job), [
            'category_id' => $category->id,
            'title' => 'Barista Tanpa Cover',
            'status' => 'open',
            'description' => 'Deskripsi pekerjaan baru yang mencakup operasional harian kedai kopi.',
            'location' => 'Bandung Wetan',
            'salary_type' => 'monthly',
            'salary_amount' => 3600000,
            'work_hours_per_day' => 8,
            'skills' => [$skill->id],
            'remove_cover_image' => '1',
        ]);

        $responseRemove->assertRedirect(route('jobs.show', $job));
        $job->refresh();
        $this->assertNull($job->cover_image);
        Storage::disk('public')->assertMissing($currentCoverPath);
    }

    public function test_employer_can_delete_specific_workplace_photos(): void
    {
        Storage::fake('public');

        $employer = User::factory()->employer()->create();
        $skill = Skill::factory()->create();
        $job = Job::factory()->for($employer, 'employer')->create();

        $photo1 = $job->workplacePhotos()->create([
            'photo_path' => 'jobs/workplace/photo1.jpg',
            'sort_order' => 1,
        ]);
        Storage::disk('public')->put('jobs/workplace/photo1.jpg', 'content1');

        $photo2 = $job->workplacePhotos()->create([
            'photo_path' => 'jobs/workplace/photo2.jpg',
            'sort_order' => 2,
        ]);
        Storage::disk('public')->put('jobs/workplace/photo2.jpg', 'content2');

        $response = $this->actingAs($employer)->put(route('employer.jobs.update', $job), [
            'category_id' => $job->category_id,
            'title' => $job->title,
            'status' => 'open',
            'description' => $job->description,
            'location' => $job->location,
            'salary_type' => $job->salary_type,
            'salary_amount' => $job->salary_amount,
            'work_hours_per_day' => $job->work_hours_per_day,
            'skills' => [$skill->id],
            'delete_workplace_photo_ids' => [$photo1->id],
        ]);

        $response->assertRedirect(route('jobs.show', $job));
        $this->assertDatabaseMissing('job_workplace_photos', ['id' => $photo1->id]);
        $this->assertDatabaseHas('job_workplace_photos', ['id' => $photo2->id]);
        Storage::disk('public')->assertMissing('jobs/workplace/photo1.jpg');
        Storage::disk('public')->assertExists('jobs/workplace/photo2.jpg');
    }

    public function test_employer_cannot_exceed_six_total_workplace_photos_when_updating_job(): void
    {
        Storage::fake('public');

        $employer = User::factory()->employer()->create();
        $skill = Skill::factory()->create();
        $job = Job::factory()->for($employer, 'employer')->create();

        foreach (range(1, 5) as $index) {
            $job->workplacePhotos()->create([
                'photo_path' => "jobs/workplace/existing-{$index}.jpg",
                'sort_order' => $index,
            ]);
        }

        $response = $this->actingAs($employer)
            ->from(route('employer.jobs.edit', $job))
            ->put(route('employer.jobs.update', $job), [
                'category_id' => $job->category_id,
                'title' => $job->title,
                'status' => 'open',
                'description' => $job->description,
                'location' => $job->location,
                'salary_type' => $job->salary_type,
                'salary_amount' => $job->salary_amount,
                'work_hours_per_day' => $job->work_hours_per_day,
                'skills' => [$skill->id],
                'workplace_photos' => [
                    UploadedFile::fake()->create('new-1.jpg', 100, 'image/jpeg'),
                    UploadedFile::fake()->create('new-2.jpg', 100, 'image/jpeg'),
                ],
            ]);

        $response->assertRedirect(route('employer.jobs.edit', $job))
            ->assertSessionHasErrors([
                'workplace_photos' => 'Total foto lingkungan kerja maksimal 6 foto, termasuk foto yang sudah tersimpan.',
            ]);
        $this->assertCount(5, $job->workplacePhotos()->get());
    }

    public function test_job_public_views_render_cover_and_workplace_photos_carousel(): void
    {
        Storage::fake('public');

        $employer = User::factory()->employer()->create();
        $job = Job::factory()->for($employer, 'employer')->create([
            'cover_image' => 'jobs/covers/test_cover.jpg',
            'status' => 'open',
        ]);
        Storage::disk('public')->put('jobs/covers/test_cover.jpg', 'fake-image');

        $job->workplacePhotos()->create([
            'photo_path' => 'jobs/workplace/wp1.jpg',
            'caption' => 'Ruang Barista Modern',
            'sort_order' => 1,
        ]);
        $job->workplacePhotos()->create([
            'photo_path' => 'jobs/workplace/wp2.jpg',
            'caption' => 'Area Kasir & Display',
            'sort_order' => 2,
        ]);
        Storage::disk('public')->put('jobs/workplace/wp1.jpg', 'fake-image');
        Storage::disk('public')->put('jobs/workplace/wp2.jpg', 'fake-image');

        // Test Job Details view
        $showResponse = $this->get(route('jobs.show', $job));
        $showResponse->assertOk()
            ->assertDontSee('Foto Lowongan Resmi')
            ->assertSee('Perbesar Sampul')
            ->assertSee('Foto')
            ->assertSee('Ruang Barista Modern')
            ->assertSee('Area Kasir & Display')
            ->assertSee('workplaceCarousel')
            ->assertSee('workplaceLightboxModal');

        // Test Jobs Index view
        $indexResponse = $this->get(route('jobs.index'));
        $indexResponse->assertOk()
            ->assertSee('2 Foto');
    }

    public function test_job_photo_upload_validates_mimes_and_max_size(): void
    {
        Storage::fake('public');

        $employer = User::factory()->employer()->create();
        $category = Category::factory()->create();

        // Non-image file
        $pdfFile = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($employer)->post(route('employer.jobs.store'), [
            'category_id' => $category->id,
            'title' => 'Barista',
            'description' => 'Deskripsi pekerjaan valid dengan panjang teks yang cukup.',
            'location' => 'Bandung',
            'salary_type' => 'monthly',
            'salary_amount' => 3000000,
            'work_hours_per_day' => 8,
            'cover_image' => $pdfFile,
        ]);

        $response->assertSessionHasErrors(['cover_image']);
    }

    public function test_job_data_update_persists_to_database_and_reflects_in_detail_page(): void
    {
        $employer = User::factory()->employer()->create();
        $oldCategory = Category::factory()->create(['name' => 'Kategori Lama']);
        $newCategory = Category::factory()->create(['name' => 'Kategori Baru']);
        $skill1 = Skill::factory()->create(['name' => 'Keahlian Satu']);
        $skill2 = Skill::factory()->create(['name' => 'Keahlian Dua']);

        $job = Job::factory()->for($employer, 'employer')->create([
            'category_id' => $oldCategory->id,
            'title' => 'Judul Awal Sebelum Diubah',
            'description' => 'Deskripsi lama pekerjaan sebelum diperbarui oleh mitra UMKM.',
            'location' => 'Kota Awal',
            'salary_type' => 'monthly',
            'salary_amount' => 3000000,
            'work_hours_per_day' => 7,
            'status' => 'open',
            'created_at' => now()->subDays(3),
            'updated_at' => now()->subDays(2),
        ]);
        $job->skills()->attach($skill1);

        $this->travel(1)->hours();

        $response = $this->actingAs($employer)->put(route('employer.jobs.update', $job), [
            'category_id' => $newCategory->id,
            'title' => 'Judul Baru Setelah Diubah Mitra',
            'description' => 'Deskripsi pekerjaan terbaru yang jauh lebih jelas, profesional, dan detail.',
            'location' => 'Kota Baru Bandung',
            'salary_type' => 'daily',
            'salary_amount' => 175000,
            'work_hours_per_day' => 8,
            'status' => 'open',
            'skills' => [$skill2->id],
            'new_skills' => ['Skill Kustom Tambahan'],
        ]);

        $response->assertRedirect(route('jobs.show', $job))
            ->assertSessionHas('success');

        // Verify Database Persistence
        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'category_id' => $newCategory->id,
            'title' => 'Judul Baru Setelah Diubah Mitra',
            'location' => 'Kota Baru Bandung',
            'salary_type' => 'daily',
            'salary_amount' => 175000,
            'work_hours_per_day' => 8,
            'status' => 'open',
        ]);

        $job->refresh();
        $this->assertDatabaseMissing('job_skill', [
            'job_id' => $job->id,
            'skill_id' => $skill1->id,
        ]);
        $this->assertDatabaseHas('job_skill', [
            'job_id' => $job->id,
            'skill_id' => $skill2->id,
        ]);
        $this->assertTrue($job->skills()->where('name', 'Skill Kustom Tambahan')->exists());

        // Verify updated_at is touched
        $this->assertTrue($job->updated_at->isAfter(now()->subMinutes(5)));

        // Verify Job Detail view displays updated information and subtle updated timestamp
        $detailResponse = $this->get(route('jobs.show', $job));
        $detailResponse->assertOk()
            ->assertSee('Judul Baru Setelah Diubah Mitra')
            ->assertSee('Kategori Baru')
            ->assertSee('Kota Baru Bandung')
            ->assertSee('Keahlian Dua')
            ->assertSee('Skill Kustom Tambahan')
            ->assertSee('Terakhir diperbarui')
            ->assertDontSee('Judul Awal Sebelum Diubah');
    }

    public function test_post_too_large_exception_redirects_back_with_friendly_error(): void
    {
        $employer = User::factory()->employer()->create();
        $job = Job::factory()->for($employer, 'employer')->create();

        $request = Request::create(route('employer.jobs.update', $job), 'POST');
        $request->headers->set('referer', route('employer.jobs.edit', $job));
        $exception = new PostTooLargeException;

        $response = app(ExceptionHandler::class)->render($request, $exception);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue(session()->has('error'));
    }
}
