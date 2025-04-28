<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $attributes = [
        'status_id' => 1
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            if (empty($task->code)) {
                $task->code = $task->generateTaskId();
            }
            $task->status_id = $task->status_id ?: 1;
        });
    }

    protected function generateTaskId(): string
    {
        $projectCode = $this->project->code ?? 'TASK';
        $randomNumber = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);

        return Str::upper($projectCode) . '-' . $randomNumber;
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
