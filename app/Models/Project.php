<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function members()
    {
        return $this->hasMany(ProjectMember::class, 'project_id',);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
