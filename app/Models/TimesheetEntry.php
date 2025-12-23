<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class TimesheetEntry extends Model
{
    protected $fillable = [
        'timesheet_id',
        'discipline',
        'level_grade',
        'project_code',
        'cost_code',
        'task',
        'monday_regular',
        'monday_overtime',
        'tuesday_regular',
        'tuesday_overtime',
        'wednesday_regular',
        'wednesday_overtime',
        'thursday_regular',
        'thursday_overtime',
        'friday_regular',
        'friday_overtime',
        'saturday_regular',
        'saturday_overtime',
        'sunday_regular',
        'sunday_overtime',
    ];

    protected function casts(): array
    {
        return [
            'monday_regular' => 'decimal:2',
            'monday_overtime' => 'decimal:2',
            'tuesday_regular' => 'decimal:2',
            'tuesday_overtime' => 'decimal:2',
            'wednesday_regular' => 'decimal:2',
            'wednesday_overtime' => 'decimal:2',
            'thursday_regular' => 'decimal:2',
            'thursday_overtime' => 'decimal:2',
            'friday_regular' => 'decimal:2',
            'friday_overtime' => 'decimal:2',
            'saturday_regular' => 'decimal:2',
            'saturday_overtime' => 'decimal:2',
            'sunday_regular' => 'decimal:2',
            'sunday_overtime' => 'decimal:2',
        ];
    }

    // Relasi ke Header Timesheet
    public function timesheet(): BelongsTo
    {
        return $this->belongsTo(Timesheet::class);
    }

    // Calculate totals
    public function getTotalRegularAttribute(): float
    {
        return $this->monday_regular + $this->tuesday_regular +
            $this->wednesday_regular + $this->thursday_regular +
            $this->friday_regular + $this->saturday_regular +
            $this->sunday_regular;
    }

    public function getTotalOvertimeAttribute(): float
    {
        return $this->monday_overtime + $this->tuesday_overtime +
            $this->wednesday_overtime + $this->thursday_overtime +
            $this->friday_overtime + $this->saturday_overtime +
            $this->sunday_overtime;
    }

    public function getTotalHoursAttribute(): float
    {
        return $this->total_regular + $this->total_overtime;
    }

    // Get hours for specific day
    public function getDayHours(string $day): array
    {
        $day = strtolower($day);
        return [
            'regular' => $this->{$day . '_regular'} ?? 0,
            'overtime' => $this->{$day . '_overtime'} ?? 0,
        ];
    }
}
