@php
    use App\Models\Timesheet;
    use App\Models\User;
    use Carbon\Carbon;

    //setting waktu untuk minggu ini
    $startOfWeek = now()->setTimezone('Asia/Jakarta')->startOfWeek(Carbon::MONDAY)->format('Y-m-d');

    //Ambil Status Timesheet Minggu Ini
    $userLogin = Auth::user();
    $currentTimesheet = Timesheet::where('user_id', $userLogin->user_id)
        ->whereDate('week_start', $startOfWeek)
        ->first(); // Ambil satu data saja

    //Ambil Statusnya (Default 'No Data' jika belum ada)
    $currentStatus = $currentTimesheet ? ucfirst($currentTimesheet->status) : 'Not Created';

    //Total Karyawan Aktif (Kecuali Admin/Manager sendiri)
    $totalStaff = User::where('role', 'karyawan')->where('is_active', true)->count();

    //hours Logged in month
    // Ambil semua timesheet di bulan ini
    $monthlyTimesheets = Timesheet::with('entries')
        ->where('user_id', $userLogin->user_id)
        ->whereMonth('week_start', now()->month)
        ->whereYear('week_start', now()->year)
        ->get();

    // Hitung total jam (Looping manual agar akurat karena kolom total tidak ada di DB)
    $loggedHours = $monthlyTimesheets->sum(function ($timesheet) {
        return $timesheet->entries->sum(function ($entry) {
            return (float) $entry->monday_regular +
                (float) $entry->tuesday_regular +
                (float) $entry->wednesday_regular +
                (float) $entry->thursday_regular +
                (float) $entry->friday_regular +
                (float) $entry->saturday_regular +
                (float) $entry->sunday_regular;
        });
    });

    // Hitung Berapa Docs yang Belum di-Approve Minggu Ini
    $startOfWeek = now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
    $docsHaventApproved = Timesheet::where('user_id', $userLogin->user_id)
        ->where('status', 'submitted')
        ->whereDate('week_start', $startOfWeek)
        ->count();
@endphp

<x-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Welcome back, ') }} {{ Auth::user()->name }}! 👋
        </h2>
    </x-slot>

    <div class="py-8 max-sm:py-1">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-10">

            {{-- 1. STATS CARDS --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Card 1: Timesheet Status -->
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Week Status</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{-- Logika dummy, nanti bisa diganti data asli --}}
                                {{ $currentStatus }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Total Hours -->
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Hours Logged (This Month)
                            </p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $loggedHours }} <span class="text-sm font-normal text-gray-500">hrs</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Pending Approval -->
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-500 mr-4">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Approvals</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $docsHaventApproved }} <span class="text-sm font-normal text-gray-500">doc</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. MAIN CONTENT GRID --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- LEFT COLUMN: QUICK ACTIONS --}}
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <a href="{{ route('timesheet.create') }}"
                                    class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-200 transition border border-blue-200 dark:border-blue-800">
                                    <div class="p-3 bg-blue-500 rounded-full text-white mr-4">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 dark:text-white">Create Timesheet</p>
                                        <p class="text-xs text-gray-500">Log your work hours for this week</p>
                                    </div>
                                </a>

                                <a href="{{ route('timesheet.index') }}"
                                    class="flex items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-200 transition border border-purple-200 dark:border-purple-800">
                                    <div class="p-3 bg-purple-500 rounded-full text-white mr-4">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 dark:text-white">My History</p>
                                        <p class="text-xs text-gray-500">View past submitted timesheets</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- ANNOUNCEMENT --}}
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                        <div class="flex items-start">
                            <div class="p-2 bg-white/20 rounded-lg mr-4">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold mb-1">Reminder: Submit on Time!</h3>
                                <p class="text-indigo-100 text-sm">
                                    Please ensure all timesheets for this week are submitted by <strong>Friday, 17:00
                                        PM</strong>. Late submissions may delay approval process.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: DIGITAL ID CARD --}}
                <div
                    class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl overflow-hidden shadow-xl text-white relative h-full border border-slate-700">

                    <!-- Background Pattern (Hiasan) -->
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-white opacity-5 rounded-full -mr-16 -mt-16 blur-2xl">
                    </div>
                    <div
                        class="absolute bottom-0 left-0 w-24 h-24 bg-blue-500 opacity-10 rounded-full -ml-10 -mb-10 blur-xl">
                    </div>

                    <div class="p-6 relative z-10">
                        <div class="flex justify-between items-start mb-6">
                            <!-- Logo Perusahaan Kecil -->
                            <div class="flex items-center space-x-2 opacity-80">
                                <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                                <span class="text-xs tracking-widest uppercase font-semibold">Employee ID</span>
                            </div>
                            <!-- Status Badge -->
                            <span
                                class="px-2 py-1 bg-green-500/20 text-green-400 text-[10px] font-bold rounded border border-green-500/30">
                                ACTIVE
                            </span>
                        </div>

                        <div class="flex items-center space-x-4 mb-9">
                            <!-- Foto Profil (Inisial Nama) -->
                            <div
                                class="w-16 h-16 rounded-full bg-gradient-to-r from-blue-400 to-indigo-500 flex items-center justify-center text-xl font-bold text-white shadow-lg border-2 border-slate-700">
                                {{ substr(Auth::user()->name, 0, 2) }}
                            </div>

                            <div>
                                <h3 class="text-lg font-bold leading-tight">{{ Auth::user()->name }}</h3>
                                <p class="text-sm text-slate-400">{{ Auth::user()->email }}</p>
                                <p class="text-xs text-blue-400 mt-1 uppercase tracking-wide font-semibold">
                                    {{ Auth::user()->isManager() ? 'Manager Team' : 'Staff Member' }}
                                </p>
                            </div>
                        </div>

                        <!-- Detail Grid -->
                        <div class="grid grid-cols-2 gap-4 border-t border-slate-700/50 pt-8">
                            <div>
                                <p class="text-[10px] text-slate-500 uppercase">Employee ID</p>
                                <p class="font-mono text-sm tracking-wider">{{ Auth::user()->user_id }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-500 uppercase">Joined Date</p>
                                {{-- Gunakan created_at sebagai dummy tanggal masuk --}}
                                <p class="font-mono text-sm">{{ Auth::user()->tanggal_masuk->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-500 uppercase">Department</p>
                                {{-- Gunakan created_at sebagai dummy tanggal masuk --}}
                                <p class="font-mono text-sm">{{ Auth::user()->department }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-500 uppercase">Work Location</p>
                                {{-- Gunakan created_at sebagai dummy tanggal masuk --}}
                                <p class="font-mono text-sm">{{ Auth::user()->lokasi_kerja }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
