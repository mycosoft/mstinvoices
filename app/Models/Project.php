<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Project extends Model
{
    use LogsActivity;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'name',
        'description',
        'status',
        'start_date',
        'end_date',
        'budget',
        'currency',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    // Computed attributes
    public function getProgressPercentageAttribute(): float
    {
        // If project is completed, return 100%
        if ($this->status === 'completed') {
            return 100.0;
        }
        
        // If no tasks, return 0%
        if ($this->tasks->isEmpty()) {
            return 0.0;
        }
        
        // Calculate weighted progress based on task hours if available
        $totalEstimatedHours = $this->tasks->sum('estimated_hours');
        if ($totalEstimatedHours > 0) {
            $weightedProgress = 0;
            foreach ($this->tasks as $task) {
                $weight = $task->estimated_hours / $totalEstimatedHours;
                $weightedProgress += ($task->percent_complete / 100) * $weight;
            }
            return round($weightedProgress * 100, 2);
        }
        
        // Fallback to simple average
        return round($this->tasks->avg('percent_complete'), 2);
    }

    public function getTotalInvoicedAttribute(): float
    {
        return $this->invoices->sum('total_amount');
    }

    public function getTotalExpensesAttribute(): float
    {
        return $this->expenses->sum('amount');
    }

    public function getBudgetRemainingAttribute(): float
    {
        if (is_null($this->budget)) {
            return 0;
        }
        return $this->budget - ($this->total_invoiced + $this->total_expenses);
    }

    /**
     * Update project status based on task completion
     */
    public function updateStatusFromTasks(): void
    {
        if ($this->tasks->isEmpty()) {
            return;
        }

        $totalTasks = $this->tasks->count();
        $completedTasks = $this->tasks->where('status', 'completed')->count();
        $cancelledTasks = $this->tasks->where('status', 'cancelled')->count();
        $activeTasks = $this->tasks->whereIn('status', ['pending', 'in_progress', 'on_hold'])->count();

        // Auto-update status based on task completion
        if ($completedTasks === $totalTasks && $completedTasks > 0) {
            $this->update(['status' => 'completed']);
        } elseif ($cancelledTasks === $totalTasks && $cancelledTasks > 0) {
            $this->update(['status' => 'cancelled']);
        } elseif ($activeTasks > 0 && $this->status === 'pending') {
            $this->update(['status' => 'in_progress']);
        }
        
        // Refresh the model to get updated values
        $this->refresh();
    }

    // Legacy attributes for backward compatibility
    public function getProgressPercentAttribute(): int
    {
        return (int) $this->progress_percentage;
    }

    public function getBudgetAmountAttribute(): float
    {
        return $this->budget ?? 0;
    }

    public function getNotesAttribute(): string
    {
        return $this->description ?? '';
    }

    public function getBudgetUsedAttribute(): float
    {
        return $this->total_expenses;
    }

    public function getBudgetVarianceAttribute(): float
    {
        return $this->budget_remaining;
    }

    /**
     * Get total estimated hours for all tasks
     */
    public function getTotalEstimatedHoursAttribute(): float
    {
        return $this->tasks->sum('estimated_hours');
    }

    /**
     * Get total actual hours worked on all tasks
     */
    public function getTotalActualHoursAttribute(): float
    {
        return $this->tasks->sum('actual_hours');
    }

    /**
     * Get project efficiency (based on task completion vs estimated hours)
     */
    public function getEfficiencyAttribute(): float
    {
        if ($this->total_estimated_hours == 0) {
            return 0;
        }
        // Calculate efficiency based on completed tasks vs total estimated hours
        $completedTasks = $this->tasks->where('status', 'completed');
        $completedHours = $completedTasks->sum('estimated_hours');
        return round(($completedHours / $this->total_estimated_hours) * 100, 2);
    }

    /**
     * Get overdue tasks count
     */
    public function getOverdueTasksCountAttribute(): int
    {
        return $this->tasks->where('due_date', '<', now())
                          ->whereNotIn('status', ['completed', 'cancelled'])
                          ->count();
    }

    /**
     * Get tasks due today
     */
    public function getTasksDueTodayCountAttribute(): int
    {
        return $this->tasks->where('due_date', today())
                          ->whereNotIn('status', ['completed', 'cancelled'])
                          ->count();
    }

    /**
     * Get project health score (0-100)
     */
    public function getHealthScoreAttribute(): int
    {
        $score = 0;
        
        // Progress score (40 points)
        $score += min(40, $this->progress_percentage * 0.4);
        
        // Budget health (30 points)
        if ($this->budget && $this->budget_remaining >= 0) {
            $score += 30;
        } elseif ($this->budget) {
            $score += max(0, 30 - (abs($this->budget_remaining) / $this->budget) * 30);
        }
        
        // Timeline health (30 points)
        if ($this->end_date) {
            $daysRemaining = now()->diffInDays($this->end_date, false);
            if ($daysRemaining >= 0) {
                $score += min(30, 30 - ($daysRemaining / 30) * 10);
            } else {
                $score += max(0, 30 + ($daysRemaining / 30) * 10);
            }
        }
        
        return min(100, max(0, round($score)));
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
