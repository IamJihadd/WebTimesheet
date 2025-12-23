<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TimesheetEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Filter Bulan & Tahun
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // 2. Tentukan Siapa Saja yang Boleh Dilihat (User IDs)
        $allowedUserIds = [];

        if ($user->isAdmin()) {
            // ADMIN: Lihat Semua User aktif
            // Kita ambil ID semua user dari tabel users (opsional: filter is_active=true)
            $allowedUserIds = User::pluck('user_id')->toArray();
        } elseif ($user->isManager()) {
            // MANAGER: Lihat Bawahan + Diri Sendiri
            $subordinates = $user->subordinates()->pluck('user_id')->toArray();
            $allowedUserIds = array_merge($subordinates, [$user->user_id]);
        } else {
            // KARYAWAN: Hanya Lihat Diri Sendiri
            $allowedUserIds = [$user->user_id];
        }

        // 3. Ambil Data Mentah (Tanpa Grouping SQL)
        // Kita ambil semua entries dulu, baru diolah di bawah
        $query = TimesheetEntry::with(['timesheet.user']) 
            ->whereHas('timesheet', function($q) use ($user, $month, $year, $allowedUserIds) {
                $q->whereMonth('week_start', $month)
                  ->whereYear('week_start', $year)
                  ->whereIn('status', ['submitted', 'approved']) // Hanya yang valid
                  ->whereIn('user_id', $allowedUserIds);         // Filter Hak Akses
            });

        $entries = $query->get();

        // 4. Grouping & Hitung di PHP (SOLUSI ANGKA KOSONG)
        $summary = $entries->groupBy(function($entry) {
            return $entry->timesheet->user->user_id ?? 'UNKNOWN';
        })->map(function($group) {
            
            // Ambil data profil karyawan dari entry pertama
            $userData = $group->first()->timesheet->user;

            // RUMUS PHP: Konversi string ke float lalu jumlahkan
            $totalReg = $group->sum(function($e) {
                return (float)$e->monday_regular + 
                       (float)$e->tuesday_regular + 
                       (float)$e->wednesday_regular + 
                       (float)$e->thursday_regular + 
                       (float)$e->friday_regular + 
                       (float)$e->saturday_regular + 
                       (float)$e->sunday_regular;
            });

            $totalOvt = $group->sum(function($e) {
                return (float)$e->monday_overtime + 
                       (float)$e->tuesday_overtime + 
                       (float)$e->wednesday_overtime + 
                       (float)$e->thursday_overtime + 
                       (float)$e->friday_overtime + 
                       (float)$e->saturday_overtime + 
                       (float)$e->sunday_overtime;
            });

            // Format data agar sesuai dengan View yang sudah ada
            return (object) [
                'employee_name'   => $userData ? $userData->name : 'Unknown User',
                'employee_id'     => $userData ? $userData->user_id : '-',
                'employee_divisi' => $userData ? ($userData->department ?? '-') : '-', 
                'total_regular'   => $totalReg,
                'total_overtime'  => $totalOvt
            ];
        })->sortBy('employee_name')->values(); // Urutkan nama A-Z

        // 5. Hitung Grand Total
        $grandTotalRegular = $summary->sum('total_regular');
        $grandTotalOvertime = $summary->sum('total_overtime');

        return view('report.monthly', compact(
            'summary',
            'grandTotalRegular',
            'grandTotalOvertime',
            'month',
            'year'
        ));
    }
}
