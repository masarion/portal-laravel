<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Employee extends Model
{
    protected $fillable = ['name', 'employee_number', 'workplace_id', 'is_active'];

    protected static function booted(): void
    {
        static::creating(fn($e) => $e->qr_token = (string) Str::uuid());
    }

    public function workplace()
    {
        return $this->belongsTo(Workplace::class);
    }

    public function records()
    {
        return $this->hasMany(AttendanceRecord::class);
    }
}
