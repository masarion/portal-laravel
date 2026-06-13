<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $fillable = ['project_id', 'name', 'code', 'password'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function submission()
    {
        return $this->hasOne(Submission::class);
    }

    public static function normalizeName(string $name): string
    {
        $name = str_replace('　', ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        return trim($name);
    }
}
