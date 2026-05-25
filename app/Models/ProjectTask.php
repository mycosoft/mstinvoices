<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ProjectTask extends Model
{
    use LogsActivity;
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id', // Creator of the task
        'assigned_to', // User assigned to the task
        'title',
        'description',
        'due_date',
        'percent_complete',
        'status',
        'priority',
        'estimated_hours',
        'actual_hours',
        'start_date',
    ];

    protected $casts = [
        'due_date' => 'date',
        'start_date' => 'date',
        'percent_complete' => 'integer',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Boot the model and register model events
     */
    protected static function boot()
    {
        parent::boot();

        // Before saving, auto-update progress based on status
        static::saving(function ($task) {
            if ($task->isDirty('status')) {
                if ($task->status === 'completed') {
                    $task->percent_complete = 100;
                } elseif ($task->status === 'cancelled') {
                    $task->percent_complete = 0;
                }
            }
        });

        // After a task is updated, update the project status
        static::updated(function ($task) {
            $task->project->updateStatusFromTasks();
        });

        // After a task is created, update the project status
        static::created(function ($task) {
            $task->project->updateStatusFromTasks();
        });

        // After a task is deleted, update the project status
        static::deleted(function ($task) {
            $task->project->updateStatusFromTasks();
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
