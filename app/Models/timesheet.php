<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class timesheet extends Model
{
    protected $fillable = [
        'user_id',
        'week_start',
        'week_end',
        'status',
        'rejection_reason',
        'approved_by',
        'submitted_at',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'week_start' => 'date',
            'week_end' => 'date',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(TimesheetEntry::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Status helpers
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function canEdit(): bool
    {
        return in_array($this->status, ['draft', 'rejected']);
    }

    // Get dates array for the week
    public function getWeekDates(): array
    {
        $dates = [];
        $current = $this->week_start->copy();

        while ($current->lte($this->week_end)) {
            $dates[] = [
                'date' => $current->copy(),
                'day' => $current->format('D'),
                'day_full' => $current->format('l'),
                'formatted' => $current->format('d/m/Y'),
            ];
            $current->addDay();
        }

        return $dates;
    }

    // Calculate total hours for this timesheet
    public function getTotalRegularHoursAttribute(): float
    {
        return $this->entries->sum('total_regular');
    }

    public function getTotalOvertimeHoursAttribute(): float
    {
        return $this->entries->sum('total_overtime');
    }
}
