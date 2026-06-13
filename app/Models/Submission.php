<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = ['project_id', 'staff_id', 'submitted', 'submitted_at', 'shift_data', 'notes'];

    protected $casts = [
        'submitted'    => 'boolean',
        'submitted_at' => 'datetime',
        'shift_data'   => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
