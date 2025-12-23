<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized. Admin access only!');
        }

        return $next($request);
    }
}
// update model user karna nambah role admin
// daftarkan atau masukkan middleware admin ke bootstrap/app.php
// update TimesheetController (update logika view), bikin agar role admin dapat melihat semua timesheet tetapi tidak bisa approve dan reject (read only). update pada function index() karna fungsi view semua timesheet ada di sana.
// update function approve dan reject, karna role admin tidak bisa approve dan reject timesheet (update message aja karna sedari awal hanya role manager saja yang bisa approve dan reject timesheet).
// update function show karna role admin bisa melihat semua timesheet.
// update reportController, karna role admin dapat melihat semua monthly report.
// update view manager-index untuk menghide button approve dan reject dari role admin.
// update button view detail


