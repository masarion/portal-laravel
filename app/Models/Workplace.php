<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workplace extends Model
{
    protected $fillable = ['name', 'order'];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
