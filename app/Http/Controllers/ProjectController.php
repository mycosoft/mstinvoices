<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Project::query()
            ->where('user_id', Auth::id())
            ->with(['client', 'tasks']); // Eager load client and tasks for progress calculation

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', (int) $request->client_id);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where('name', 'like', "%{$q}%");
        }

        $projects = $query->orderByDesc('created_at')->paginate(15);
        $settings = Setting::forUser();
        $clients = Client::where('user_id', Auth::id())->orderBy('name')->get(['id','name']);

        return view('projects.index', compact('projects', 'settings', 'clients'));
    }

    public function dashboard()
    {
        $projects = Project::where('user_id', Auth::id())
            ->with(['client', 'tasks.assignee'])
            ->get();

        // Calculate statistics
        $totalProjects = $projects->count();
        $activeProjects = $projects->where('status', 'in_progress')->count();
        $overdueTasks = $projects->sum('overdue_tasks_count');
        $totalBudget = $projects->sum('budget');

        // Get recent tasks (last 10)
        $recentTasks = \App\Models\ProjectTask::whereHas('project', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->with(['project', 'assignee'])
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        // Get upcoming deadlines (next 7 days)
        $upcomingDeadlines = \App\Models\ProjectTask::whereHas('project', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(7))
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->with(['project', 'assignee'])
            ->orderBy('due_date')
            ->get();

        $settings = Setting::forUser();

        return view('projects.dashboard', compact(
            'projects', 'totalProjects', 'activeProjects', 'overdueTasks', 
            'totalBudget', 'recentTasks', 'upcomingDeadlines', 'settings'
        ));
    }

    public function create()
    {
        $settings = Setting::forUser();
        $clients = Client::where('user_id', Auth::id())->orderBy('name')->get(['id','name']);
        return view('projects.create', compact('settings', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0|max:999999999999.99',
            'currency' => 'nullable|string|size:3',
        ]);

        $validated['user_id'] = Auth::id();
        
        // Set default currency if not provided
        if (empty($validated['currency'])) {
            $settings = Setting::forUser();
            $validated['currency'] = $settings->default_currency ?? 'UGX';
        }

        $project = Project::create($validated);
        
        // Check if request is AJAX (for modal form submission)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Project created successfully!',
                'project' => $project
            ]);
        }
        
        return redirect()->route('projects.show', $project)->with('success', 'Project created successfully!');
    }

    public function show(Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $project->load(['client', 'tasks.assignee', 'invoices', 'expenses']);
        $settings = Setting::forUser();
        
        return view('projects.show', compact('project', 'settings'));
    }

    public function edit(Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $settings = Setting::forUser();
        $clients = Client::where('user_id', Auth::id())->orderBy('name')->get(['id','name']);
        $users = User::where('id', '!=', Auth::id())->orderBy('name')->get(['id','name']);
        
        return view('projects.edit', compact('project', 'settings', 'clients', 'users'));
    }

    public function update(Request $request, Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0|max:999999999999.99',
            'currency' => 'nullable|string|size:3',
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project)->with('success', 'Project updated successfully!');
    }

    public function destroy(Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $project->delete();
        
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully!');
    }
}
