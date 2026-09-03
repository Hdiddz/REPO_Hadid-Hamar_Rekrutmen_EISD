<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Auth;

echo "=== STARTING MULTI-ROLE CHAT & GUEST VISIBILITY VERIFICATION ===\n\n";

$passCount = 0;
$failCount = 0;

function assertCondition($name, $condition, $details = '')
{
    global $passCount, $failCount;
    if ($condition) {
        echo '[PASS] '.$name."\n";
        $passCount++;
    } else {
        echo '[FAIL] '.$name.' | '.$details."\n";
        $failCount++;
    }
}

// 1. GUEST TESTS: Floating chat MUST NOT be rendered when user is logged out (guest)
Auth::logout();
$homeGuestView = view('home')->render();
assertCondition(
    'Guest Home: No floating chat popup',
    ! str_contains($homeGuestView, 'id="floatingChatPopup"'),
    'floatingChatPopup found in guest home'
);
assertCondition(
    'Guest Home: No floating chat button',
    ! str_contains($homeGuestView, 'id="floatingChatBtn"'),
    'floatingChatBtn found in guest home'
);

$lowonganGuestView = view('jobs.index')->render();
assertCondition(
    'Guest Lowongan: No floating chat popup',
    ! str_contains($lowonganGuestView, 'id="floatingChatPopup"'),
    'floatingChatPopup found in guest lowongan'
);
assertCondition(
    'Guest Lowongan: No floating chat button',
    ! str_contains($lowonganGuestView, 'id="floatingChatBtn"'),
    'floatingChatBtn found in guest lowongan'
);

// 2. JOBSEEKER TESTS: Floating chat and /chat
$jobseeker = new User(['name' => 'Budi Santoso', 'role' => 'jobseeker', 'email' => 'budi@kerjalokal.id']);
Auth::setUser($jobseeker);

$lowonganJobseekerView = view('jobs.index')->render();
assertCondition(
    'Jobseeker Lowongan: Floating chat popup rendered',
    str_contains($lowonganJobseekerView, 'id="floatingChatPopup"')
);
assertCondition(
    'Jobseeker Lowongan: Contains UMKM contacts (Kedai Kopi Sudut Temu)',
    str_contains($lowonganJobseekerView, 'Kedai Kopi Sudut Temu')
);

$chatJobseekerView = view('chat.index')->render();
assertCondition(
    "Jobseeker /chat: Header says 'Pesan & Obrolan Kerja'",
    str_contains($chatJobseekerView, 'Pesan &amp; Obrolan Kerja')
);

$jobShowView = view('jobs.show', ['id' => 1])->render();
assertCondition(
    "Jobseeker Job Detail: Contains 'Pesan UMKM' action button",
    str_contains($jobShowView, 'Pesan UMKM')
);

// 3. EMPLOYER TESTS: Dashboard, Applicants, Floating Chat, /chat
$employer = new User(['name' => 'Hendra Wijaya', 'role' => 'employer', 'email' => 'mitra@suduttemu.id']);
Auth::setUser($employer);

$employerDashView = view('employer.dashboard')->render();
assertCondition(
    'Employer Dashboard: Has floating chat widget',
    str_contains($employerDashView, 'id="floatingChatPopup"')
);
assertCondition(
    'Employer Dashboard: Floating chat has candidate contact (Budi Santoso)',
    str_contains($employerDashView, 'Budi Santoso')
);
assertCondition(
    "Employer Dashboard: Has 'Pesan Pelamar' trigger button in header",
    str_contains($employerDashView, 'Pesan Pelamar (3)')
);

$employerAppsView = view('employer.applications.index')->render();
assertCondition(
    "Employer Pelamar: Contains 'Pesan Pelamar' interactive action buttons",
    str_contains($employerAppsView, 'Pesan Pelamar')
);

$chatEmployerView = view('chat.index')->render();
assertCondition(
    "Employer /chat: Heading adapted to 'Pesan & Obrolan Pelamar Kerja'",
    str_contains($chatEmployerView, 'Pesan &amp; Obrolan Pelamar Kerja')
);
assertCondition(
    'Employer /chat: Thread list contains applicant Budi Santoso',
    str_contains($chatEmployerView, 'Budi Santoso')
);

// 4. ADMIN TESTS: Dashboard, Floating Chat, /chat
$admin = new User(['name' => 'Super Admin SDG 8', 'role' => 'admin', 'email' => 'admin@kerjalokal.id']);
Auth::setUser($admin);

$adminDashView = view('admin.dashboard')->render();
assertCondition(
    'Admin Dashboard: Has floating chat widget',
    str_contains($adminDashView, 'id="floatingChatPopup"')
);
assertCondition(
    'Admin Dashboard: Floating chat contacts adapted to audit & monitoring',
    str_contains($adminDashView, 'Saluran Pengawasan')
);

$chatAdminView = view('chat.index')->render();
assertCondition(
    "Admin /chat: Heading adapted to 'Saluran Pengawasan & Kepatuhan SDG 8'",
    str_contains($chatAdminView, 'Saluran Pengawasan &amp; Kepatuhan SDG 8')
);

echo "\nVerification Summary: $passCount PASS, $failCount FAIL\n";
