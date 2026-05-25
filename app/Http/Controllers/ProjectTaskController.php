<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectTaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'percent_complete' => 'required|integer|min:0|max:100',
            'status' => 'required|in:pending,in_progress,completed,on_hold,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_hours' => 'nullable|numeric|min:0|max:9999.99',
        ]);
        
        $validated['user_id'] = Auth::id(); // Creator of the task
        $validated['project_id'] = $project->id;
        
        $task = $project->tasks()->create($validated);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Task created successfully!',
                'task' => $task
            ]);
        }
        
        return back()->with('success', 'Task created successfully!');
    }

    public function update(Request $request, Project $project, ProjectTask $task)
    {
        if ($project->user_id !== Auth::id() || $task->project_id !== $project->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'percent_complete' => 'required|integer|min:0|max:100',
            'status' => 'required|in:pending,in_progress,completed,on_hold,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_hours' => 'nullable|numeric|min:0|max:9999.99',
        ]);
        
        $task->update($validated);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully!',
                'task' => $task->fresh()
            ]);
        }
        
        return back()->with('success', 'Task updated successfully!');
    }

    public function destroy(Project $project, ProjectTask $task)
    {
        if ($project->user_id !== Auth::id() || $task->project_id !== $project->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $task->delete();
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Task deleted successfully!'
            ]);
        }
        
        return back()->with('success', 'Task deleted successfully!');
    }
}
