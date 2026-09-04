<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use App\Notifications\ApplicationStatusUpdatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_updating_application_status_sends_notification_to_candidate(): void
    {
        $employer = User::factory()->employer()->create();
        $jobseeker = User::factory()->jobseeker()->create();
        $category = Category::factory()->create();

        $job = Job::factory()->create([
            'employer_id' => $employer->id,
            'category_id' => $category->id,
            'status' => 'open',
        ]);

        $application = JobApplication::create([
            'job_id' => $job->id,
            'user_id' => $jobseeker->id,
            'resume_file' => 'resumes/fake.pdf',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($employer)->patch(route('employer.applications.update', $application), [
            'status' => 'accepted',
        ]);

        $response->assertRedirect();

        // Verify notification exists in database for jobseeker
        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $jobseeker->id,
        ]);

        $notification = $jobseeker->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertSame('accepted', $notification->data['status']);
        $this->assertStringContainsString('DITERIMA', $notification->data['message']);
    }

    public function test_candidate_applying_for_job_sends_notification_to_employer(): void
    {
        Storage::fake('local');

        $employer = User::factory()->employer()->create();
        $jobseeker = User::factory()->jobseeker()->create();
        $category = Category::factory()->create();

        $job = Job::factory()->create([
            'employer_id' => $employer->id,
            'category_id' => $category->id,
            'status' => 'open',
        ]);

        $file = UploadedFile::fake()->create('resume.pdf', 120, 'application/pdf');

        $response = $this->actingAs($jobseeker)->post(route('applications.store', $job), [
            'resume' => $file,
            'note' => 'Saya sangat tertarik dengan posisi ini.',
        ]);

        $response->assertRedirect(route('applications.index'));

        // Verify employer received notification
        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $employer->id,
        ]);

        $notification = $employer->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertSame('new_applicant', $notification->data['status']);

        // Verify candidate received personal activity notification with day and time
        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $jobseeker->id,
        ]);

        $candidateNotif = $jobseeker->notifications()->first();
        $this->assertNotNull($candidateNotif);
        $this->assertSame('application_submitted', $candidateNotif->data['status']);
        $this->assertStringContainsString('Anda telah melamar pada lowongan', $candidateNotif->data['message']);
        $this->assertStringContainsString('pukul', $candidateNotif->data['message']);
    }

    public function test_user_can_fetch_notifications_via_json(): void
    {
        $user = User::factory()->jobseeker()->create();
        $employer = User::factory()->employer()->create();
        $category = Category::factory()->create();
        $job = Job::factory()->create([
            'employer_id' => $employer->id,
            'category_id' => $category->id,
        ]);

        $application = JobApplication::create([
            'job_id' => $job->id,
            'user_id' => $user->id,
            'resume_file' => 'resumes/fake.pdf',
            'status' => 'interview',
        ]);

        $user->notify(new ApplicationStatusUpdatedNotification($application, 'interview'));

        $response = $this->actingAs($user)->getJson(route('notifications.index'));

        $response->assertOk()
            ->assertJsonStructure([
                'unread_count',
                'notifications' => [
                    '*' => ['id', 'data', 'read_at', 'is_read', 'created_at'],
                ],
            ]);

        $this->assertSame(1, $response->json('unread_count'));
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        $user = User::factory()->jobseeker()->create();
        $employer = User::factory()->employer()->create();
        $category = Category::factory()->create();
        $job = Job::factory()->create([
            'employer_id' => $employer->id,
            'category_id' => $category->id,
        ]);

        $application = JobApplication::create([
            'job_id' => $job->id,
            'user_id' => $user->id,
            'resume_file' => 'resumes/fake.pdf',
            'status' => 'interview',
        ]);

        $user->notify(new ApplicationStatusUpdatedNotification($application, 'interview'));
        $notification = $user->unreadNotifications()->first();

        $response = $this->actingAs($user)->postJson(route('notifications.read', $notification->id));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'unread_count' => 0,
            ]);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $user = User::factory()->jobseeker()->create();
        $employer = User::factory()->employer()->create();
        $category = Category::factory()->create();
        $job = Job::factory()->create([
            'employer_id' => $employer->id,
            'category_id' => $category->id,
        ]);

        $application = JobApplication::create([
            'job_id' => $job->id,
            'user_id' => $user->id,
            'resume_file' => 'resumes/fake.pdf',
            'status' => 'pending',
        ]);

        $user->notify(new ApplicationStatusUpdatedNotification($application, 'reviewed'));
        $user->notify(new ApplicationStatusUpdatedNotification($application, 'accepted'));

        $this->assertSame(2, $user->unreadNotifications()->count());

        $response = $this->actingAs($user)->postJson(route('notifications.readAll'));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'unread_count' => 0,
            ]);

        $this->assertSame(0, $user->fresh()->unreadNotifications()->count());
    }

    public function test_admin_updating_application_status_sends_notification_to_candidate(): void
    {
        $admin = User::factory()->admin()->create();
        $employer = User::factory()->employer()->create();
        $jobseeker = User::factory()->jobseeker()->create();
        $category = Category::factory()->create();

        $job = Job::factory()->create([
            'employer_id' => $employer->id,
            'category_id' => $category->id,
        ]);

        $application = JobApplication::create([
            'job_id' => $job->id,
            'user_id' => $jobseeker->id,
            'resume_file' => 'resumes/fake.pdf',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.applications.status', $application), [
            'status' => 'accepted',
        ]);

        $response->assertRedirect();

        $notification = $jobseeker->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertSame('accepted', $notification->data['status']);
    }

    public function test_jobseeker_reporting_job_sends_notification_to_all_admins(): void
    {
        $admin1 = User::factory()->admin()->create();
        $admin2 = User::factory()->admin()->create();
        $employer = User::factory()->employer()->create();
        $jobseeker = User::factory()->jobseeker()->create();
        $category = Category::factory()->create();

        $job = Job::factory()->create([
            'employer_id' => $employer->id,
            'category_id' => $category->id,
            'title' => 'Staf Administrasi Bodong',
        ]);

        $response = $this->actingAs($jobseeker)->post(route('jobs.report', $job), [
            'reason' => 'Indikasi penipuan atau calo',
            'details' => 'Meminta uang registrasi sebesar 500 ribu sebelum interview.',
        ]);

        $response->assertRedirect();

        // Both admins should receive notifications
        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $admin1->id,
        ]);
        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $admin2->id,
        ]);

        $notif1 = $admin1->notifications()->first();
        $this->assertSame('report_pending', $notif1->data['status']);
        $this->assertStringContainsString('Indikasi penipuan', $notif1->data['message']);
    }

    public function test_chat_messages_do_not_trigger_notifications_across_all_three_roles(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Satgas']);
        $employer = User::factory()->employer()->create(['name' => 'Mitra Kopi Jaya', 'business_name' => 'Kopi Jaya Studio']);
        $jobseeker = User::factory()->jobseeker()->create(['name' => 'Kandidat Hebat']);

        // 1. Mitra UMKM kirim pesan ke Pencari Kerja
        $res1 = $this->actingAs($employer)->postJson(route('chat.send', $jobseeker), [
            'message' => 'Halo apakah Anda bersedia wawancara besok pagi?',
        ]);
        $res1->assertCreated();
        $this->assertSame(0, $jobseeker->notifications()->count());

        // 2. Admin kirim pesan ke Mitra UMKM
        $res2 = $this->actingAs($admin)->postJson(route('chat.send', $employer), [
            'message' => 'Tolong lengkapi profil izin usaha Anda.',
        ]);
        $res2->assertCreated();
        $this->assertSame(0, $employer->notifications()->count());

        // 3. Pencari Kerja kirim pesan ke Admin
        $res3 = $this->actingAs($jobseeker)->postJson(route('chat.send', $admin), [
            'message' => 'Halo admin saya ingin bertanya seputar verifikasi.',
        ]);
        $res3->assertCreated();
        $this->assertSame(0, $admin->notifications()->count());
    }

    public function test_user_can_delete_notification(): void
    {
        $user = User::factory()->jobseeker()->create();
        $employer = User::factory()->employer()->create();
        $category = Category::factory()->create();
        $job = Job::factory()->create([
            'employer_id' => $employer->id,
            'category_id' => $category->id,
        ]);

        $application = JobApplication::create([
            'job_id' => $job->id,
            'user_id' => $user->id,
            'resume_file' => 'resumes/fake.pdf',
            'status' => 'pending',
        ]);

        $user->notify(new ApplicationStatusUpdatedNotification($application, 'accepted'));
        $notification = $user->notifications()->first();
        $this->assertNotNull($notification);

        $response = $this->actingAs($user)->deleteJson(route('notifications.destroy', $notification->id));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'unread_count' => 0,
            ]);

        $this->assertDatabaseMissing('notifications', [
            'id' => $notification->id,
        ]);
    }
}
