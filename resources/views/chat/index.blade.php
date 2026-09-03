@extends('layouts.app')

@php
    $currentRole = Auth::user()->role ?? 'jobseeker';
    $userName = Auth::user()->name ?? 'Budi Santoso';
    $userInitials = strtoupper(substr($userName, 0, 2));
@endphp

@section('title', ($currentRole === 'employer' ? 'Pesan Pelamar Kerja' : ($currentRole === 'admin' ? 'Saluran Pengawasan SDG 8' : 'Pesan & Obrolan Kerja')) . ' - KerjaLokal')

@section('content')
<div class="flex flex-col w-full bg-slate-50 min-h-screen py-6 sm:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        
        <!-- Page Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                        @if($currentRole === 'employer')
                            Pesan &amp; Obrolan Pelamar Kerja
                        @elseif($currentRole === 'admin')
                            Saluran Pengawasan &amp; Kepatuhan SDG 8
                        @else
                            Pesan &amp; Obrolan Kerja
                        @endif
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $currentRole === 'employer' ? 'bg-blue-50 text-blue-800' : ($currentRole === 'admin' ? 'bg-purple-50 text-purple-800' : 'bg-teal-50 text-teal-800') }}">
                        {{ $currentRole === 'employer' ? 'Portal Mitra UMKM' : ($currentRole === 'admin' ? 'Super Admin SDG 8' : 'Pencari Kerja') }}
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    @if($currentRole === 'employer')
                        Koordinasi seleksi, penjadwalan wawancara, dan tanya-jawab berkas bersama kandidat pelamar
                    @elseif($currentRole === 'admin')
                        Saluran komunikasi resmi tim pengawas independen bersama Mitra UMKM dan Pencari Kerja
                    @else
                        Koordinasi langsung dengan pemilik UMKM terkait proses seleksi dan wawancara bebas percaloan
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-800 text-xs font-bold rounded-xl border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>100% Saluran Resmi Etis</span>
                </span>
            </div>
        </div>

        <!-- Chat Split Container -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[620px]">
            
            <!-- Left Pane: Conversation List (4 Cols) -->
            <div class="lg:col-span-4 border-r border-slate-200 flex flex-col bg-slate-50/50">
                <!-- Search bar -->
                <div class="p-3.5 border-b border-slate-200 bg-white">
                    <div class="flex items-center bg-slate-100 rounded-xl px-3 py-2 text-xs focus-within:bg-white focus-within:ring-2 focus-within:ring-teal-600/20 focus-within:border-teal-600 border border-transparent transition-all">
                        <span class="material-symbols-outlined text-slate-400 text-base mr-2">search</span>
                        <input type="text" placeholder="{{ $currentRole === 'employer' ? 'Cari nama pelamar atau posisi...' : ($currentRole === 'admin' ? 'Cari mitra atau aduan pelamar...' : 'Cari percakapan UMKM...') }}" class="w-full bg-transparent border-none outline-none text-slate-900 placeholder:text-slate-400 p-0 text-xs">
                    </div>
                </div>

                <!-- Chat Thread Items -->
                <div class="flex-1 overflow-y-auto divide-y divide-slate-100" id="chatThreadList">
                    @if($currentRole === 'employer')
                        <!-- EMPLOYER THREADS (Job Candidates) -->
                        <!-- Thread 1: Budi Santoso (Active) -->
                        <div class="p-4 bg-white border-l-4 border-teal-600 cursor-pointer transition-all hover:bg-slate-50 flex items-start gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-teal-100 text-teal-800 font-bold flex items-center justify-center shrink-0 text-sm">
                                BS
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1 mb-0.5">
                                    <span class="font-bold text-xs text-slate-900 truncate">Budi Santoso</span>
                                    <span class="text-[10px] text-teal-700 font-semibold shrink-0">10:15</span>
                                </div>
                                <span class="inline-block text-[10px] font-bold text-teal-800 bg-teal-50 px-1.5 py-0.5 rounded mb-1">
                                    Barista &amp; Kasir • 100% Match
                                </span>
                                <p class="text-xs text-slate-600 truncate font-semibold">
                                    "Saya konfirmasi siap hadir tepat waktu besok jam 14.00..."
                                </p>
                            </div>
                            <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0 mt-2"></span>
                        </div>

                        <!-- Thread 2: Siti Rahma -->
                        <div class="p-4 bg-transparent cursor-pointer transition-all hover:bg-white flex items-start gap-3 opacity-80 hover:opacity-100">
                            <div class="w-11 h-11 rounded-2xl bg-blue-100 text-blue-800 font-bold flex items-center justify-center shrink-0 text-sm">
                                SR
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1 mb-0.5">
                                    <span class="font-bold text-xs text-slate-800 truncate">Siti Rahma</span>
                                    <span class="text-[10px] text-slate-400 shrink-0">Kemarin</span>
                                </div>
                                <span class="inline-block text-[10px] font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded mb-1">
                                    Kasir Grosir • 85% Match
                                </span>
                                <p class="text-xs text-slate-500 truncate">
                                    "Terima kasih infonya, saya membawa fotokopi KTP dan CV..."
                                </p>
                            </div>
                        </div>

                        <!-- Thread 3: Ahmad Fauzi -->
                        <div class="p-4 bg-transparent cursor-pointer transition-all hover:bg-white flex items-start gap-3 opacity-80 hover:opacity-100">
                            <div class="w-11 h-11 rounded-2xl bg-amber-100 text-amber-800 font-bold flex items-center justify-center shrink-0 text-sm">
                                AF
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1 mb-0.5">
                                    <span class="font-bold text-xs text-slate-800 truncate">Ahmad Fauzi</span>
                                    <span class="text-[10px] text-slate-400 shrink-0">2 hari</span>
                                </div>
                                <span class="inline-block text-[10px] font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded mb-1">
                                    Kurir Logistik • SIM C
                                </span>
                                <p class="text-xs text-slate-500 truncate">
                                    "Motor dan SIM C siap untuk operasional shift..."
                                </p>
                            </div>
                        </div>

                    @elseif($currentRole === 'admin')
                        <!-- ADMIN THREADS (Monitoring & Audit) -->
                        <!-- Thread 1: Kedai Kopi Sudut Temu (Active) -->
                        <div class="p-4 bg-white border-l-4 border-teal-600 cursor-pointer transition-all hover:bg-slate-50 flex items-start gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-teal-100 text-teal-800 font-bold flex items-center justify-center shrink-0 text-sm">
                                ST
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1 mb-0.5">
                                    <span class="font-bold text-xs text-slate-900 truncate">Kedai Kopi Sudut Temu</span>
                                    <span class="text-[10px] text-teal-700 font-semibold shrink-0">11:30</span>
                                </div>
                                <span class="inline-block text-[10px] font-bold text-teal-800 bg-teal-50 px-1.5 py-0.5 rounded mb-1">
                                    Hendra Wijaya • Audit Gaji
                                </span>
                                <p class="text-xs text-slate-600 truncate font-semibold">
                                    "Laporan transparansi upah bulanan telah diunggah lengkap."
                                </p>
                            </div>
                            <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0 mt-2"></span>
                        </div>

                        <!-- Thread 2: Budi Santoso (Pelamar) -->
                        <div class="p-4 bg-transparent cursor-pointer transition-all hover:bg-white flex items-start gap-3 opacity-80 hover:opacity-100">
                            <div class="w-11 h-11 rounded-2xl bg-purple-100 text-purple-800 font-bold flex items-center justify-center shrink-0 text-sm">
                                BS
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1 mb-0.5">
                                    <span class="font-bold text-xs text-slate-800 truncate">Budi Santoso</span>
                                    <span class="text-[10px] text-slate-400 shrink-0">Kemarin</span>
                                </div>
                                <span class="inline-block text-[10px] font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded mb-1">
                                    Pencari Kerja • Verifikasi Loker
                                </span>
                                <p class="text-xs text-slate-500 truncate">
                                    "Terima kasih tindak lanjut atas verifikasi loker kemarin..."
                                </p>
                            </div>
                        </div>

                        <!-- Thread 3: Toko Berkah Mandiri -->
                        <div class="p-4 bg-transparent cursor-pointer transition-all hover:bg-white flex items-start gap-3 opacity-80 hover:opacity-100">
                            <div class="w-11 h-11 rounded-2xl bg-slate-200 text-slate-700 font-bold flex items-center justify-center shrink-0 text-sm">
                                BM
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1 mb-0.5">
                                    <span class="font-bold text-xs text-slate-800 truncate">Toko Berkah Mandiri</span>
                                    <span class="text-[10px] text-slate-400 shrink-0">3 hari</span>
                                </div>
                                <span class="inline-block text-[10px] font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded mb-1">
                                    Bpk. Subagyo • Standar Jam Kerja
                                </span>
                                <p class="text-xs text-slate-500 truncate">
                                    "Jadwal shift 8 jam sudah diterapkan ketat di toko."
                                </p>
                            </div>
                        </div>

                    @else
                        <!-- JOBSEEKER THREADS (UMKM Partners) -->
                        <!-- Thread 1: Kedai Kopi Sudut Temu (Active) -->
                        <div class="p-4 bg-white border-l-4 border-teal-600 cursor-pointer transition-all hover:bg-slate-50 flex items-start gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-teal-100 text-teal-800 font-bold flex items-center justify-center shrink-0 text-sm">
                                ST
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1 mb-0.5">
                                    <span class="font-bold text-xs text-slate-900 truncate">Kedai Kopi Sudut Temu</span>
                                    <span class="text-[10px] text-teal-700 font-semibold shrink-0">10:15</span>
                                </div>
                                <span class="inline-block text-[10px] font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded mb-1">
                                    Barista &amp; Kasir
                                </span>
                                <p class="text-xs text-slate-600 truncate font-semibold">
                                    Undangan Wawancara: Besok jam 14.00 WIB
                                </p>
                            </div>
                            <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0 mt-2"></span>
                        </div>

                        <!-- Thread 2: Toko Berkah Mandiri -->
                        <div class="p-4 bg-transparent cursor-pointer transition-all hover:bg-white flex items-start gap-3 opacity-80 hover:opacity-100">
                            <div class="w-11 h-11 rounded-2xl bg-slate-200 text-slate-700 font-bold flex items-center justify-center shrink-0 text-sm">
                                BM
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1 mb-0.5">
                                    <span class="font-bold text-xs text-slate-800 truncate">Toko Berkah Mandiri</span>
                                    <span class="text-[10px] text-slate-400 shrink-0">Kemarin</span>
                                </div>
                                <span class="inline-block text-[10px] font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded mb-1">
                                    Staf Gudang
                                </span>
                                <p class="text-xs text-slate-500 truncate">
                                    Berkas lamaran Anda sudah kami verifikasi.
                                </p>
                            </div>
                        </div>

                        <!-- Thread 3: Sentra Distribusi Cepat -->
                        <div class="p-4 bg-transparent cursor-pointer transition-all hover:bg-white flex items-start gap-3 opacity-80 hover:opacity-100">
                            <div class="w-11 h-11 rounded-2xl bg-slate-200 text-slate-700 font-bold flex items-center justify-center shrink-0 text-sm">
                                SC
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1 mb-0.5">
                                    <span class="font-bold text-xs text-slate-800 truncate">Sentra Distribusi Cepat</span>
                                    <span class="text-[10px] text-slate-400 shrink-0">2 hari</span>
                                </div>
                                <span class="inline-block text-[10px] font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded mb-1">
                                    Kurir Logistik
                                </span>
                                <p class="text-xs text-slate-500 truncate">
                                    Apakah Anda memiliki SIM C aktif saat ini?
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Pane: Active Chat Conversation (8 Cols) -->
            <div class="lg:col-span-8 flex flex-col bg-white">
                
                <!-- Chat Window Header -->
                <div class="p-4 border-b border-slate-200 flex items-center justify-between bg-white">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-teal-600 text-white font-bold flex items-center justify-center text-sm">
                            @if($currentRole === 'employer')
                                BS
                            @else
                                ST
                            @endif
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-sm text-slate-900">
                                    @if($currentRole === 'employer')
                                        Budi Santoso (Pelamar)
                                    @elseif($currentRole === 'admin')
                                        Hendra Wijaya (Owner)
                                    @else
                                        Hendra Wijaya (Owner)
                                    @endif
                                </span>
                                <span class="px-2 py-0.5 rounded bg-teal-50 text-teal-800 text-[10px] font-bold border border-teal-200">
                                    @if($currentRole === 'employer')
                                        Posisi: Barista &amp; Kasir
                                    @else
                                        Kedai Kopi Sudut Temu
                                    @endif
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span>
                                    @if($currentRole === 'employer')
                                        Online • Melamar 24 Okt 2024 • CV Terverifikasi
                                    @elseif($currentRole === 'admin')
                                        Online • Kepatuhan Kerja Layak: Terverifikasi
                                    @else
                                        Online • Posisi: Barista &amp; Kasir
                                    @endif
                                </span>
                            </p>
                        </div>
                    </div>

                    @if($currentRole === 'employer')
                        <a href="{{ route('employer.applications.index') }}" class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 transition-all flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">person_search</span>
                            <span>Tinjau Berkas</span>
                        </a>
                    @elseif($currentRole === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 transition-all flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">dashboard</span>
                            <span>Dashboard Audit</span>
                        </a>
                    @else
                        <a href="{{ route('jobs.show', ['id' => 1]) }}" class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 transition-all flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">visibility</span>
                            <span>Lihat Loker</span>
                        </a>
                    @endif
                </div>

                <!-- Anti-Fraud Safe Banner -->
                <div class="px-4 py-2.5 bg-amber-50/80 border-b border-amber-100 text-[11px] text-amber-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-700 text-base shrink-0">info</span>
                    <span>
                        @if($currentRole === 'employer')
                            Kepatuhan Etis: Dilarang memungut biaya administrasi atau menahan ijazah asli calon pekerja.
                        @elseif($currentRole === 'admin')
                            Audit Kepatuhan: Seluruh histori komunikasi tercatat di sistem pemantauan standar SDG 8.
                        @else
                            Peringatan Keamanan: Rekrutmen KerjaLokal bebas biaya calo. Jangan pernah mentransfer uang atau menyerahkan ijazah asli.
                        @endif
                    </span>
                </div>

                <!-- Messages Scroll Area -->
                <div class="flex-1 p-4 sm:p-6 overflow-y-auto space-y-4 bg-slate-50/30" id="messageContainer">
                    
                    <!-- Date Separator -->
                    <div class="flex items-center justify-center">
                        <span class="px-3 py-1 bg-white border border-slate-200 text-slate-500 text-[10px] font-bold rounded-full shadow-2xs">
                            Kemarin
                        </span>
                    </div>

                    @if($currentRole === 'employer')
                        <!-- EMPLOYER POV MESSAGES -->
                        <!-- Outbound (From Employer) -->
                        <div class="flex items-start justify-end gap-2.5 ml-auto max-w-lg">
                            <div class="bg-teal-700 text-white rounded-2xl rounded-tr-sm p-3.5 shadow-2xs text-xs space-y-1 text-left">
                                <p class="font-bold text-[11px] text-teal-200">Hendra Wijaya • Kedai Kopi Sudut Temu</p>
                                <p class="leading-relaxed">
                                    Halo Budi Santoso, salam kenal. Kami sudah melihat profil dan resume Anda yang dikirim melalui KerjaLokal. Pengalaman Anda di mesin espresso Pawoon POS cocok dengan kualifikasi yang kami butuhkan.
                                </p>
                                <span class="text-[9px] text-teal-200 block text-right">16:40</span>
                            </div>
                            <div class="w-7 h-7 rounded-xl bg-teal-800 text-white font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">
                                ST
                            </div>
                        </div>

                        <!-- Inbound (From Candidate Budi) -->
                        <div class="flex items-start gap-2.5 max-w-lg">
                            <div class="w-7 h-7 rounded-xl bg-slate-800 text-white font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">
                                BS
                            </div>
                            <div class="bg-white border border-slate-200 rounded-2xl rounded-tl-sm p-3.5 shadow-2xs text-xs text-slate-800 space-y-1">
                                <p class="font-bold text-[11px] text-teal-800">Budi Santoso</p>
                                <p class="leading-relaxed">
                                    Selamat sore Pak Hendra. Terima kasih banyak atas apresiasinya. Saya sangat tertarik untuk berkontribusi di Kedai Kopi Sudut Temu dan siap bekerja sistem shift.
                                </p>
                                <span class="text-[9px] text-slate-400 block text-right">16:45</span>
                            </div>
                        </div>

                        <!-- Date Separator -->
                        <div class="flex items-center justify-center pt-2">
                            <span class="px-3 py-1 bg-white border border-slate-200 text-slate-500 text-[10px] font-bold rounded-full shadow-2xs">
                                Hari Ini
                            </span>
                        </div>

                        <!-- Outbound (Employer Invitation) -->
                        <div class="flex items-start justify-end gap-2.5 ml-auto max-w-lg">
                            <div class="space-y-2 w-full">
                                <div class="bg-teal-700 text-white rounded-2xl rounded-tr-sm p-3.5 shadow-2xs text-xs space-y-1 text-left">
                                    <p class="leading-relaxed">
                                        Apakah besok Kamis Anda berkenan datang ke kedai kami di Dipatiukur untuk sesi ngobrol santai dan uji kalibrasi mesin kopi?
                                    </p>
                                    <span class="text-[9px] text-teal-200 block text-right">10:00</span>
                                </div>

                                <!-- Mini Ticket Sent -->
                                <div class="p-3.5 bg-teal-50 border border-teal-200 rounded-2xl space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-teal-800">Undangan Wawancara Dikirim</span>
                                        <span class="material-symbols-outlined text-sm text-teal-700">check_circle</span>
                                    </div>
                                    <div class="space-y-0.5 text-xs text-slate-900">
                                        <div class="font-black text-sm">Kamis, 14:00 - 15:00 WIB</div>
                                        <div class="text-slate-600 text-[11px]">Lokasi: Jl. Dipatiukur No. 42, Coblong, Bandung</div>
                                    </div>
                                </div>
                            </div>
                            <div class="w-7 h-7 rounded-xl bg-teal-800 text-white font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">
                                ST
                            </div>
                        </div>

                        <!-- Inbound (Candidate Reply) -->
                        <div class="flex items-start gap-2.5 max-w-lg">
                            <div class="w-7 h-7 rounded-xl bg-slate-800 text-white font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">
                                BS
                            </div>
                            <div class="bg-white border border-slate-200 rounded-2xl rounded-tl-sm p-3.5 shadow-2xs text-xs text-slate-800 space-y-1">
                                <p class="font-bold text-[11px] text-teal-800">Budi Santoso</p>
                                <p class="leading-relaxed">
                                    Saya konfirmasi siap hadir tepat waktu besok jam 14.00 WIB Pak. Saya akan membawa fotokopi CV fisik. Terima kasih atas kesempatannya!
                                </p>
                                <span class="text-[9px] text-slate-400 block text-right">10:15</span>
                            </div>
                        </div>

                    @else
                        <!-- JOBSEEKER & ADMIN POV MESSAGES -->
                        <!-- Inbound Message -->
                        <div class="flex items-start gap-2.5 max-w-lg">
                            <div class="w-7 h-7 rounded-xl bg-teal-600 text-white font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">
                                ST
                            </div>
                            <div class="bg-white border border-slate-200 rounded-2xl rounded-tl-sm p-3.5 shadow-2xs text-xs text-slate-800 space-y-1">
                                <p class="font-bold text-[11px] text-teal-800">Hendra Wijaya • Kedai Kopi Sudut Temu</p>
                                <p class="leading-relaxed">
                                    Halo, salam kenal. Kami sudah melihat berkas dan kualifikasi yang Anda kirimkan melalui KerjaLokal. Pengalaman Anda cocok dengan kualifikasi yang kami butuhkan.
                                </p>
                                <span class="text-[9px] text-slate-400 block text-right">16:40</span>
                            </div>
                        </div>

                        <!-- Outbound Message -->
                        <div class="flex items-start justify-end gap-2.5 ml-auto max-w-lg">
                            <div class="bg-teal-700 text-white rounded-2xl rounded-tr-sm p-3.5 shadow-2xs text-xs space-y-1 text-left">
                                <p class="leading-relaxed">
                                    Selamat sore Pak Hendra. Terima kasih banyak atas apresiasinya. Saya sangat tertarik untuk berkontribusi di Kedai Kopi Sudut Temu.
                                </p>
                                <span class="text-[9px] text-teal-200 block text-right">16:45</span>
                            </div>
                            <div class="w-7 h-7 rounded-xl bg-slate-800 text-white font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">
                                {{ $userInitials }}
                            </div>
                        </div>

                        <!-- Date Separator -->
                        <div class="flex items-center justify-center pt-2">
                            <span class="px-3 py-1 bg-white border border-slate-200 text-slate-500 text-[10px] font-bold rounded-full shadow-2xs">
                                Hari Ini
                            </span>
                        </div>

                        <!-- Inbound Invitation Message & Card -->
                        <div class="flex items-start gap-2.5 max-w-lg">
                            <div class="w-7 h-7 rounded-xl bg-teal-600 text-white font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">
                                ST
                            </div>
                            <div class="space-y-2 w-full">
                                <div class="bg-white border border-slate-200 rounded-2xl rounded-tl-sm p-3.5 shadow-2xs text-xs text-slate-800 space-y-1">
                                    <p class="font-bold text-[11px] text-teal-800">Hendra Wijaya</p>
                                    <p class="leading-relaxed">
                                        Apakah besok Kamis Anda berkenan datang ke kedai kami untuk sesi ngobrol santai dan melihat langsung area kerja bar kopi?
                                    </p>
                                    <span class="text-[9px] text-slate-400 block text-right">10:15</span>
                                </div>

                                <!-- Interview Ticket Card -->
                                <div class="p-3.5 bg-teal-50 border border-teal-200 rounded-2xl space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-teal-800">Undangan Temu Wawancara</span>
                                        <span class="material-symbols-outlined text-sm text-teal-700">event</span>
                                    </div>
                                    <div class="space-y-0.5 text-xs text-slate-900">
                                        <div class="font-black text-sm">Kamis, 14:00 - 15:00 WIB</div>
                                        <div class="text-slate-600 text-[11px]">Lokasi: Jl. Dipatiukur No. 42, Coblong, Bandung</div>
                                    </div>
                                    <div class="pt-1 flex items-center gap-2">
                                        <button type="button" onclick="sendQuickReply('Saya konfirmasi siap hadir tepat waktu besok jam 14.00 WIB.')" class="px-3 py-1.5 bg-teal-700 hover:bg-teal-800 text-white font-bold text-[11px] rounded-xl shadow-xs cursor-pointer">
                                            Konfirmasi Hadir
                                        </button>
                                        <button type="button" onclick="sendQuickReply('Mohon maaf Pak, apakah jam wawancaranya bisa disesuaikan ke jam 16.00 WIB?')" class="px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-[11px] rounded-xl border border-slate-200 cursor-pointer">
                                            Minta Reschedule
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

                <!-- Chat Input Strip -->
                <div class="p-4 border-t border-slate-200 bg-white">
                    <form onsubmit="handleSendMessage(event)" class="flex items-center gap-2">
                        <button type="button" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition-colors" title="Unggah dokumen / gambar">
                            <span class="material-symbols-outlined text-xl">attach_file</span>
                        </button>
                        <input type="text" id="chatInput" placeholder="{{ $currentRole === 'employer' ? 'Tulis pesan balasan ke Budi Santoso...' : ($currentRole === 'admin' ? 'Tulis catatan pengawasan etis...' : 'Tulis pesan ke Mitra UMKM...') }}" class="flex-1 py-2.5 px-4 bg-slate-100 focus:bg-white rounded-xl border border-transparent focus:border-teal-600 focus:ring-2 focus:ring-teal-600/15 text-xs text-slate-900 outline-none transition-all">
                        <button type="submit" class="px-4 py-2.5 bg-teal-700 hover:bg-teal-800 active:scale-95 text-white font-bold text-xs rounded-xl shadow-xs transition-all flex items-center gap-1 cursor-pointer">
                            <span>Kirim</span>
                            <span class="material-symbols-outlined text-sm">send</span>
                        </button>
                    </form>
                </div>

            </div>

        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function sendQuickReply(text) {
        const input = document.getElementById('chatInput');
        if (input) {
            input.value = text;
            input.focus();
        }
    }

    function handleSendMessage(e) {
        e.preventDefault();
        const input = document.getElementById('chatInput');
        const text = input ? input.value.trim() : '';
        if (!text) return;

        const container = document.getElementById('messageContainer');
        const newMsg = document.createElement('div');
        newMsg.className = 'flex items-start justify-end gap-2.5 ml-auto max-w-lg animate-page-enter';
        newMsg.innerHTML = `
            <div class="bg-teal-700 text-white rounded-2xl rounded-tr-sm p-3.5 shadow-2xs text-xs space-y-1 text-left">
                <p class="leading-relaxed">${escapeHtml(text)}</p>
                <span class="text-[9px] text-teal-200 block text-right">Baru saja</span>
            </div>
            <div class="w-7 h-7 rounded-xl bg-slate-800 text-white font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">
                {{ $userInitials }}
            </div>
        `;
        container.appendChild(newMsg);
        input.value = '';
        container.scrollTop = container.scrollHeight;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
@endpush
