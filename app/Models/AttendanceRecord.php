<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AttendanceRecord extends Model
{
    protected $fillable = ['employee_id', 'date', 'check_in', 'check_out'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getWorkDurationAttribute(): string
    {
        if ($this->check_in && $this->check_out) {
            $minutes = Carbon::parse($this->check_out)->diffInMinutes(Carbon::parse($this->check_in));
            return intdiv($minutes, 60) . '時間' . str_pad($minutes % 60, 2, '0', STR_PAD_LEFT) . '分';
        }
        return '-';
    }
}
