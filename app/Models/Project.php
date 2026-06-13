<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    const AUTH_NAME = 'name';
    const AUTH_CODE = 'code';

    protected $fillable = [
        'name', 'auth_mode', 'start_date', 'end_date', 'deadline',
        'info_message', 'copy_guide_message', 'confirm_message',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'deadline'   => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($project) {
            $project->submit_token = (string) Str::uuid();
        });
    }

    public function staffMembers()
    {
        return $this->hasMany(Staff::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function isNameMode(): bool
    {
        return $this->auth_mode === self::AUTH_NAME;
    }

    public function isPastDeadline(): bool
    {
        return now()->gt($this->deadline);
    }
}
