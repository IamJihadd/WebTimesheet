<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use App\Models\TimesheetEntry;
use App\Models\Discipline;
use App\Models\LevelGrade;
use App\Models\ProjectCode;
use App\Models\CostCode;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class TimesheetController extends Controller
{
    /**
     * Display list of timesheets
     * Manager: See all timesheets
     * Karyawan: See own timesheets only
     */
    public function index()
    {
        $user = auth()->user();

        // Query dasar untuk sorting status (Digunakan oleh Admin & Manager)
        $statusOrder = "CASE 
                        WHEN status = 'submitted' THEN 1 
                        WHEN status = 'rejected' THEN 2 
                        WHEN status = 'approved' THEN 3 
                        ELSE 4 
                        END";

        // ================================================================
        // 1. LOGIKA ADMIN (GLOBAL VIEW)
        // Admin melihat SEMUA timesheet dari seluruh perusahaan
        // ================================================================
        if ($user->isAdmin()) {
            $timesheets = Timesheet::with(['user', 'entries'])
                ->whereIn('status', ['submitted', 'approved', 'rejected'])
                ->orderByRaw($statusOrder)
                ->orderBy('week_start', 'desc')
                ->orderBy('submitted_at', 'desc')
                ->paginate(15);

            return view('timesheet.manager-index', compact('timesheets'));
        }

        // ================================================================
        // 2. LOGIKA MANAGER (HIERARCHY VIEW + SELF VIEW)
        // Manager melihat timesheet BAWAHANNYA + timesheet DIRINYA SENDIRI
        // ================================================================
        if ($user->isManager()) {
            // 1. Ambil ID semua bawahan langsung
            $subordinateIds = $user->subordinates()->pluck('user_id')->toArray();

            // 2. TAMBAHKAN ID SAYA SENDIRI KE DAFTAR
            // Agar timesheet saya juga muncul di dashboard ini
            $viewableIds = array_merge($subordinateIds, [$user->user_id]);

            $timesheets = Timesheet::with(['user', 'entries'])
                ->whereIn('user_id', $viewableIds) // <--- Gunakan daftar gabungan ini
                ->whereIn('status', ['submitted', 'approved', 'rejected'])
                ->orderByRaw($statusOrder)
                ->orderBy('week_start', 'desc')
                ->orderBy('submitted_at', 'desc')
                ->paginate(15);

            return view('timesheet.manager-index', compact('timesheets'));
        }

        // ================================================================
        // 3. LOGIKA KARYAWAN (PERSONAL VIEW)
        // Karyawan hanya melihat timesheet miliknya sendiri
        // ================================================================
        $timesheets = Timesheet::with('entries')
            ->where('user_id', $user->user_id)
            ->orderBy('week_start', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('timesheet.index', compact('timesheets'));
    }

    /**
     * Show form to create/edit timesheet
     */
    public function create()
    {
        // 1. Tentukan Minggu Ini (PAKSA HARI SENIN)
        $today = Carbon::today();
        // Tambahkan parameter Carbon::MONDAY agar server tidak bingung (kadang defaultnya Minggu)
        $weekStart = $today->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $today->copy()->endOfWeek(Carbon::SUNDAY);

        // 2. CEK DATABASE: Apakah user ini SUDAH PUNYA data di tanggal minggu ini?
        $existingTimesheet = Timesheet::where('user_id', auth()->user()->user_id) // String ID
            ->whereDate('week_start', $weekStart) // Pakai whereDate agar lebih akurat
            ->first();

        // 3. LOGIKA PENGALIHAN (REDIRECT)
        if ($existingTimesheet) {
            if (in_array($existingTimesheet->status, ['submitted', 'approved'])) {
                // Kalau sudah submit/approve -> Lempar ke halaman Detail (Read Only)
                return redirect()->route('timesheet.show', $existingTimesheet->id)
                    ->with('info', 'Timesheet untuk minggu ini sudah disubmit/approved. Anda tidak bisa membuat baru.');
            } else {
                // Kalau masih draft/reject -> Lempar ke halaman Edit
                return redirect()->route('timesheet.edit', $existingTimesheet->id)
                    ->with('info', 'Anda sudah memiliki draft timesheet minggu ini. Silakan lanjutkan.');
            }
        }

        // 4. JIKA BENAR-BENAR BELUM ADA -> Tampilkan Form Baru
        $timesheet = new Timesheet([
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
            'user_id' => auth()->user()->user_id,
            'status' => 'draft'
        ]);

        // Get master data for dropdowns
        $disciplines = Discipline::active()->pluck('name', 'name');
        $projectCodes = ProjectCode::active()->get()->mapWithKeys(function ($item) {
            return [$item->code => $item->code . ' - ' . $item->name];
        });
        $levelGrades = LevelGrade::active()->pluck('name', 'name');
        $costCodes = CostCode::all();
        $tasks = Task::all();

        // $levelGrades = LevelGrade::all();
        // $disciplines = Discipline::all();
        // $projectCodes = ProjectCode::all();
        return view('timesheet.create', compact(
            'timesheet',
            'disciplines',
            'levelGrades',
            'projectCodes',
            'costCodes',
            'tasks'
        ));
    }

    // Function storeNew untuk menangkap route dari Create Form
    public function storeNew(Request $request)
    {
        $emptyTimesheet = new Timesheet();
        return $this->store($request, $emptyTimesheet);
    }

    /**
     * Show edit form
     */
    public function edit(Timesheet $timesheet)
    {
        // Authorization check
        if (!auth()->user()->isManager() && $timesheet->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // CEK STATUS: Jika sudah Submitted/Approved, user tidak boleh Edit
        if (in_array($timesheet->status, ['submitted', 'approved'])) {
            return redirect()->route('timesheet.show', $timesheet->id)
                ->with('error', 'Timesheet ini sudah disubmit dan tidak dapat diedit.');
        }

        // Load entries
        $timesheet->load('entries');

        // Get master data for dropdowns
        $disciplines = Discipline::active()->pluck('name', 'name');
        $levelGrades = LevelGrade::active()->pluck('name', 'name');
        $projectCodes = ProjectCode::active()->get()->mapWithKeys(function ($item) {
            return [$item->code => $item->code . ' - ' . $item->name];
        });
        // $disciplines = Discipline::all();
        // $levelGrades = LevelGrade::all();
        // $projectCodes = ProjectCode::all();
        $costCodes = CostCode::all();
        $tasks = Task::all();

        return view('timesheet.edit', compact('timesheet', 'disciplines', 'levelGrades', 'projectCodes', 'costCodes', 'tasks'));
    }
    /**
     * Store/Update timesheet entries
     */
    public function store(Request $request, Timesheet $timesheet)
    {
        $validated = $request->validate([
            'entries' => 'required|array',
            'entries.*.discipline' => 'required|string|max:255',
            'entries.*.level_grade' => 'required|string|max:255',
            'entries.*.project_code' => 'required|string|max:255',
            'entries.*.cost_code' => 'required|string|max:255',
            'entries.*.task' => 'required|string|max:255',
            'entries.*.monday_regular' => 'nullable|numeric|min:0|max:8',
            'entries.*.monday_overtime' => 'nullable|numeric|min:0|max:4',
            'entries.*.tuesday_regular' => 'nullable|numeric|min:0|max:8',
            'entries.*.tuesday_overtime' => 'nullable|numeric|min:0|max:4',
            'entries.*.wednesday_regular' => 'nullable|numeric|min:0|max:8',
            'entries.*.wednesday_overtime' => 'nullable|numeric|min:0|max:4',
            'entries.*.thursday_regular' => 'nullable|numeric|min:0|max:8',
            'entries.*.thursday_overtime' => 'nullable|numeric|min:0|max:4',
            'entries.*.friday_regular' => 'nullable|numeric|min:0|max:8',
            'entries.*.friday_overtime' => 'nullable|numeric|min:0|max:4',
            'entries.*.saturday_regular' => 'nullable|numeric|min:0|max:8',
            'entries.*.saturday_overtime' => 'nullable|numeric|min:0|max:4',
            'entries.*.sunday_regular' => 'nullable|numeric|min:0|max:8',
            'entries.*.sunday_overtime' => 'nullable|numeric|min:0|max:4',
        ]);

        DB::beginTransaction();
        try {
            // === SECURITY CHECK: CEK DUPLIKAT DI DATABASE ===
            if (!$timesheet || !$timesheet->exists) {
                // Cek lagi di DB, barangkali user buka 2 tab atau memanipulasi form
                $existing = Timesheet::where('user_id', auth()->user()->user_id)
                    ->where('week_start', $request->week_start)
                    ->first();

                if ($existing) {
                    // Jika ternyata sudah ada, batalkan Create Baru!
                    // Gunakan yang sudah ada saja.
                    $timesheet = $existing;

                    // Kalau statusnya sudah submit, tolak edit.
                    if (in_array($timesheet->status, ['submitted', 'approved'])) {
                        return redirect()->route('timesheet.show', $timesheet->id)
                            ->with('error', 'Security: Timesheet sudah ada dan disubmit. Tidak bisa ditimpa.');
                    }
                } else {
                    // Benar-benar belum ada, baru create
                    $timesheet = Timesheet::create([
                        'user_id' => auth()->user()->user_id,
                        'week_start' => $request->week_start,
                        'week_end' => $request->week_end,
                        'status' => 'draft',
                    ]);
                }
            }
            // ===============================================

            // Reset entries lama (timpa dengan yang baru dari form)
            $timesheet->entries()->delete();

            // Create new entries
            foreach ($validated['entries'] as $entryData) {
                $timesheet->entries()->create($entryData);
            }

            // Check action
            if ($request->input('action') === 'submit') {
                $timesheet->update([
                    'status' => 'submitted',
                    'submitted_at' => now(),
                ]);

                DB::commit();
                return redirect()->route('timesheet.index')
                    ->with('success', 'Timesheet submitted for approval!');
            }

            DB::commit();

            return redirect()->route('timesheet.edit', $timesheet->id)
                ->with('success', 'Timesheet saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to save timesheet: ' . $e->getMessage()]);
        }
    }


    public function approve(Timesheet $timesheet)
    {   
        $user = auth()->user();

        //Cek role manager atau bukan
        if (!auth()->user()->isManager()) {
            abort(403, 'Only Managers can approve timesheets.');
        }

        // Cek pemilik timesheet (ANTI SELF-APPROVAL)
        // Jika pemilik timesheet adalah saya sendiri -> TOLAK
        if ($timesheet->user_id === $user->user_id) {
            return back()->with('error', 'Violation: Anda tidak dapat menyetujui (Approve) timesheet Anda sendiri. Tunggu atasan Anda.');
        }

        $timesheet->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Timesheet approved!');
    }

    /**
     * Reject timesheet (Manager only)
     */
    public function reject(Request $request, Timesheet $timesheet)
    {
        $user = auth()->user();

        // Cek role user
        if (!auth()->user()->isManager()) {
            abort(403, 'Only Managers can approve timesheets.');
        }

        // Cek pemilik timesheet (ANTI SELF-APPROVAL)
        // Jika pemilik timesheet adalah saya sendiri -> TOLAK
        if ($timesheet->user_id === $user->user_id) {
            return back()->with('error', 'Violation: Anda tidak dapat menyetujui (Approve) timesheet Anda sendiri. Tunggu atasan Anda.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $timesheet->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Timesheet rejected.');
    }

    /**
     * View timesheet details (read-only)
     */
    public function show(Timesheet $timesheet)
    {
        // Authorization
        if (!auth()->user()->isManager() && !auth()->user()->isAdmin() && $timesheet->user_id !== auth()->id()) {
            abort(403);
        }

        $timesheet->load('entries', 'user', 'approver');

        return view('timesheet.show', compact('timesheet'));
    }

    /**
     * Export timesheet to PDF
     */
    public function exportPdf(Timesheet $timesheet)
    {
        // Authorization check
        if (!auth()->user()->isManager() && $timesheet->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $timesheet->load('entries', 'user', 'approver');

        // Generate PDF
        $pdf = Pdf::loadView('timesheet.pdf', compact('timesheet'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif'
            ]);

        $filename = 'Timesheet_' . $timesheet->user->name . '_WEEK_' . $timesheet->week_start->format('Y-W') . '.pdf';

        return $pdf->download($filename);
    }
}
