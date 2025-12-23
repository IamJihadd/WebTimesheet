<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id'; // Beri tahu ini adalah Primary Key
    public $incrementing = false;     // Matikan auto-increment
    protected $keyType = 'string';    // Beri tahu tipenya string

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'user_id',
        'email',
        'password',
        'role',
        'is_active',
        'department',
        'level_grade',
        'lokasi_kerja',
        'tanggal_masuk',
        'tanggal_keluar',
        'manager_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'tanggal_masuk' => 'date',
            'tanggal_keluar' => 'date',
        ];
    }

    /**
     * IMPORTANT: Override default username field dari 'email' ke 'user_id'
     */
    public function getAuthIdentifierName()
    {
        return 'user_id';
    }

    /**
     * Helper methods untuk check role
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }
    
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKaryawan(): bool
    {
        return $this->role === 'karyawan';
    }

    // RELATIONSHIPS (HIERARKI & TIMESHEET)
    // =================================================================

    /**
     * Relasi ke ATASAN (Manager)
     * User ini melapor ke siapa?
     */
    public function manager(): BelongsTo
    {
        // belongsTo(Model, Foreign Key, Owner Key)
        return $this->belongsTo(User::class, 'manager_id', 'user_id');
    }

    /**
     * Relasi ke BAWAHAN (Subordinates)
     * Siapa saja yang melapor ke user ini?
     */
    public function subordinates(): HasMany
    {
        // hasMany(Model, Foreign Key, Local Key)
        return $this->hasMany(User::class, 'manager_id', 'user_id');
    }
    
    /**
     * Relasi ke Timesheet
     */
    public function timesheets(): HasMany
    {
        return $this->hasMany(Timesheet::class, 'user_id', 'user_id');
    }

    public static function generateId($role = null)
    {
        // Prefix tunggal untuk semua role
        $prefix = 'DEC';

        // Cari User terakhir yang ID-nya diawali 'DEC'
        $lastUser = self::where('user_id', 'like', $prefix . '%')
            ->orderByRaw('LENGTH(user_id) DESC') // Urutkan panjang dulu (biar 10 > 9)
            ->orderBy('user_id', 'desc')         // Lalu urutkan stringnya
            ->first();

        if (!$lastUser) {
            // Jika belum ada user sama sekali, mulai dari DEC001
            return $prefix . '001';
        }

        // Ambil angkanya saja (Hapus 'DEC')
        // Contoh: DEC005 -> ambil "005" -> jadi integer 5
        $lastNumber = (int) substr($lastUser->user_id, 3); 
        
        // Tambah 1
        $newNumber = $lastNumber + 1;

        // Format ulang jadi 3 digit (misal: 6 -> 006)
        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
