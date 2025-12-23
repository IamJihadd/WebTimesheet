<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\LevelGrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserManagementController extends Controller
{
    /**
     * Display list of users (Manager only)
     */
    public function index()
    {
        $users = User::orderBy('is_active', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('users.index', compact('users'));
    }

    /**
     * Show create user form
     */
    public function create()
    {
        // Ambil daftar user yang bisa dijadikan atasan (Manager/Director/Admin)
        // Kita exclude 'karyawan' biasa agar list tidak kepanjangan
        $managers = User::whereIn('role', ['manager', 'admin'])
            ->orderBy('name')
            ->get();

        $levelGrades = LevelGrade::all(); 
        $departments = Department::all(); 

        return view('users.create', compact('managers','levelGrades','departments'));
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // 'user_id' => ['required', 'string', 'max:255', 'unique:users,user_id'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'password' => ['required', Rules\Password::defaults()],
            'role' => ['required', 'in:manager,karyawan,admin'],
            'department' => ['nullable', 'string', 'max:255'],
            'level_grade' => ['nullable', 'string', 'max:255'],
            'lokasi_kerja' => ['nullable', 'string', 'max:255'],
            'tanggal_masuk' => ['required', 'date'],
            'level_grade' => ['nullable', 'string', 'exists:level_grades,name'],

            // VALIDASI MANAGER (Boleh kosong jika dia Bos Tertinggi)
            'manager_id' => ['nullable', 'string', 'exists:users,user_id'], 
        ]);

        $newId = User::generateId();
        $validated['user_id'] = $newId; // Masukkan ke array validasi
        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = true;

        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dibuat! ID Karyawan Baru: ' . $newId);
    }

    /**
     * Show edit user form
     */
    public function edit(User $user)
    {
        // Ambil list manager, TAPI exclude user itu sendiri (masa atasan diri sendiri?)
        $managers = User::whereIn('role', ['manager', 'admin'])
            ->where('user_id', '!=', $user->user_id) // Cegah loop (atasan diri sendiri)
            ->orderBy('name')
            ->get();

        $levelGrades = LevelGrade::all();
        $departments = Department::all();

        return view('users.edit', compact('user', 'managers','levelGrades','departments'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // 'user_id' => ['required','string','max:255',Rule::unique('users', 'user_id')->ignore($user->user_id, 'user_id')
            // // "Cek apakah user_id unik di tabel users, KECUALI punya user yang sedang diedit ini"
            // ],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'role' => ['required', 'in:manager,karyawan,admin'],
            'department' => ['nullable', 'string', 'max:255'],
            'level_grade' => ['nullable', 'string', 'max:255'],
            'lokasi_kerja' => ['nullable', 'string', 'max:255'],
            'tanggal_masuk' => ['required', 'date'],
            'tanggal_keluar' => ['nullable', 'date'],
            'level_grade' => ['nullable', 'string', 'exists:level_grades,name'],
            // validasi manager_id
            'manager_id' => ['nullable', 'string', 'exists:users,user_id'],
        ]);

        // Only update password if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => [Rules\Password::defaults()],
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully!');
    }

    /**
     * Deactivate user (soft delete)
     */
    public function deactivate(User $user)
    {
        // Prevent deactivating self
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot deactivate yourself!']);
        }

        $user->update([
            'is_active' => false,
            'tanggal_keluar' => now(),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User deactivated successfully!');
    }

    /**
     * Reactivate user
     */
    public function activate(User $user)
    {
        $user->update([
            'is_active' => true,
            'tanggal_keluar' => null,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User activated successfully!');
    }

    /**
     * Delete user permanently (optional - be careful!)
     */
    public function destroy(User $user)
    {
        // Prevent deleting self
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete yourself!']);
        }

        // Check if user has timesheets
        if ($user->timesheets()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete user with existing timesheets. Deactivate instead.']);
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted permanently!');
    }
}
