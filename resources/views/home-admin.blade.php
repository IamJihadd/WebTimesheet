@php
    use App\Models\Timesheet;
    use App\Models\User;
    use Carbon\Carbon;

    // 1. Hitung Berapa yang BUTUH APPROVAL (Status 'submitted')
    $pendingApprovals = Timesheet::where('status', 'submitted')->count();

    // 2. Hitung Berapa yang SUDAH DI-APPROVE Minggu Ini
    $startOfWeek = now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
    $approvedThisWeek = Timesheet::where('status', 'approved')->where('week_start', $startOfWeek)->count();

    // 3. Total Karyawan Aktif (Kecuali Admin/Manager sendiri)
    $totalStaff = User::where('is_active', true)->count();
@endphp

<x-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-gray-800 dark:text-gray-200 leading-tight z-50">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>
    <div class="py-7 max-sm:py-1">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-10">

            {{-- 1. STATS CARDS (DATA MANAGER) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Card 1: BUTUH APPROVAL (Penting!) -->
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg max-sm:rounded-md p-6 border-l-4 border-yellow-500 flex justify-between flex-row">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-md font-medium text-gray-500 dark:text-gray-400">Pending Approval</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $pendingApprovals }} <span class="text-sm font-normal text-gray-500">docs</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card 2: APPROVED MINGGU INI -->
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg max-sm:rounded-md p-6 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-md font-medium text-gray-500 dark:text-gray-400">Timesheets had Approved</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $approvedThisWeek }} <span class="text-sm font-normal text-gray-500">docs</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card 3: TOTAL TEAM -->
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg max-sm:rounded-md p-6 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-md font-medium text-gray-500 dark:text-gray-400">Total Staff</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $totalStaff }} <span class="text-sm font-normal text-gray-500">people</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. MAIN CONTENT GRID --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- LEFT COLUMN: MANAGER ACTIONS --}}
                <div class="lg:col-span-2">

                    {{-- QUICK ACTIONS --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg max-sm:rounded-md mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Management Actions</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- Tombol Review Timesheet --}}
                                <a href="{{ route('timesheet.index') }}"
                                    class="flex items-center p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg hover:bg-indigo-100 transition border border-indigo-200 dark:border-indigo-800 group">
                                    <div
                                        class="p-3 bg-indigo-500 rounded-full text-white mr-4 group-hover:scale-110 transition">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 dark:text-white">Review The Timesheet</p>
                                        <p class="text-sm max-sm:text-sm text-gray-500">Check submitted and approved timesheets</p>
                                    </div>
                                </a>

                                {{-- Tombol Report Bulanan --}}
                                <a href="{{ route('report.monthly') }}"
                                    class="flex items-center p-4 bg-teal-50 dark:bg-teal-900/20 rounded-lg hover:bg-teal-100 transition border border-teal-200 dark:border-teal-800 group">
                                    <div
                                        class="p-3 bg-teal-500 rounded-full text-white mr-4 group-hover:scale-110 transition">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 dark:text-white">Monthly Report</p>
                                        <p class="text-sm max-sm:text-sm text-gray-500">View team performance summary</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- WELCOME BANNER --}}
                    <div
                        class="bg-gradient-to-r from-slate-700 to-slate-800 rounded-lg shadow-lg p-6 text-white border border-slate-600">
                        <div class="flex items-start">
                            <div class="p-2 bg-white/10 rounded-lg mr-4">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold mb-1">Good Day, Admin!</h3>
                                <p class="text-slate-300 text-sm">
                                    Anda memiliki <strong>{{ $pendingApprovals }} timesheet</strong> yang dapat anda
                                    review minggu ini. Mohon ditinjau sebelum hari Jumat.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DIGITAL ID CARD (ADMIN VERSION) --}}
                <div
                    class="bg-gradient-to-br from-slate-900 to-black rounded-xl overflow-hidden shadow-2xl text-white h-full border border-slate-700">
                    <div class="p-6 z-10">
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                                <span class="text-xs tracking-widest uppercase font-semibold text-white">Admin
                                    Access</span>
                            </div>
                            <span
                                class="px-2 py-1 bg-blue-500/20 text-blue-400 text-[10px] font-bold rounded border border-yellow-500/30">
                                AUTHORIZED
                            </span>
                        </div>

                        <div class="flex items-center space-x-4 mb-6">
                            <div
                                class="w-16 h-16 rounded-full bg-gradient-to-r from-green-500 to-blue-600 flex items-center justify-center text-xl font-bold text-white shadow-lg border-2 border-slate-700">
                                {{ substr(Auth::user()->name, 0, 2) }}
                            </div>

                            <div>
                                <h3 class="text-lg font-bold leading-tight">{{ Auth::user()->name }}</h3>
                                <p class="text-sm text-slate-400">{{ Auth::user()->email }}</p>
                                <p class="text-xs text-green-500 mt-1 uppercase tracking-wide font-semibold">
                                    {{ Auth::user()->level_grade }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 border-t border-slate-700/50 pt-4">
                            <div>
                                <p class="text-[10px] text-slate-500 uppercase">Employee ID</p>
                                <p class="font-mono text-sm tracking-wider text-slate-200">{{ Auth::user()->user_id }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-500 uppercase">Joined Date</p>
                                {{-- Gunakan created_at sebagai dummy tanggal masuk --}}
                                <p class="font-mono text-sm">{{ Auth::user()->tanggal_masuk->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-500 uppercase">Department</p>
                                <p class="font-mono text-sm text-slate-200">
                                    {{ Auth::user()->department ?? 'General' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-500 uppercase">Work Location</p>
                                {{-- Gunakan created_at sebagai dummy tanggal masuk --}}
                                <p class="font-mono text-sm">{{ Auth::user()->lokasi_kerja }}</p>
                            </div>
                        </div>
                        {{-- <div class="mt-4 pt-2 opacity-40">
                            <div
                                class="h-8 w-full bg-[url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAICAYAAAC3W5OAAAAA20lEQVR42mNgwA2YoZiRAgZ2Yg0kFA/6j65fC4h1gFgbiLVAWAGItYFYG4h1gFgbhI0sD5IP0g/SDzIPpB9kHkg/SD/IPJB+kHkg/SD9IPNA+kHmQQ0D6QeZB9IPMg+kH6QfZB5IP8g8kH6QfpB5IP0g80D6QfpB5oH0g8wD6QfpB5kH0g8yD6QfZB5IP0g/yDyQfpB5IP0g/SDzQPpB5oH0g/SDzAPpB5kH0g/SDzIPpB9kHkg/SD/IPJB+kHkg/SD9IPNA+kHmQQwD6QeZB9IPMg+kH6QfZB7I/wAA804x24B7G8gAAAAASUVORK5CYII=')] bg-repeat-x bg-contain">
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
