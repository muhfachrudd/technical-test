<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\StoreTodoRequest;
use App\Models\Todo;

use App\Exports\TodosExport;
use Maatwebsite\Excel\Facades\Excel;

class TodoController extends Controller
{
    public function index(Request $request)
    {
        $query = Todo::query();

        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->has('assignee')) {
            $assignees = explode(',', $request->assignee);
            $query->whereIn('assignee', $assignees);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('due_date', [$request->start_date, $request->end_date]);
        }

        if ($request->has('status')) {
            $statuses = explode(',', $request->status);
            $query->whereIn('status', $statuses);
        }

        if ($request->has('priority')) {
            $priorities = explode(',', $request->priority);
            $query->whereIn('priority', $priorities);
        }

        return response()->json($query->latest()->get());
    }

    public function store(StoreTodoRequest $request)
    {
        $validated = $request->validated();
        
        $todo = Todo::create($validated);
        
        return response()->json([
            'message' => 'Todo created successfully',
            'data' => $todo
        ], 201);
    }

    public function update(StoreTodoRequest $request, Todo $todo)
    {
        $todo->update($request->validated());
        return response()->json(['message' => 'Todo updated successfully', 'data' => $todo]);
    }

    public function destroy(Todo $todo)
    {
        $todo->delete();
        return response()->json(['message' => 'Todo deleted successfully']);
    }

    public function export(Request $request)
    {
        $filters = $request->only(['title', 'assignee', 'start_date', 'end_date', 'min_time', 'max_time', 'status', 'priority']);
        
        return Excel::download(new TodosExport($filters), 'todos.xlsx');
    }

    public function chart(Request $request)
    {
        $type = $request->query('type');

        if ($type === 'status') {
            $summary = [
                'pending' => Todo::where('status', 'pending')->count(),
                'open' => Todo::where('status', 'open')->count(),
                'in_progress' => Todo::where('status', 'in_progress')->count(),
                'completed' => Todo::where('status', 'completed')->count(),
            ];
            return response()->json(['status_summary' => $summary]);
        }

        if ($type === 'priority') {
            $summary = [
                'low' => Todo::where('priority', 'low')->count(),
                'medium' => Todo::where('priority', 'medium')->count(),
                'high' => Todo::where('priority', 'high')->count(),
            ];
            return response()->json(['priority_summary' => $summary]);
        }

        if ($type === 'assignee') {
            $todos = Todo::all();
            $summary = $todos->groupBy('assignee')->map(function ($group) {
                return [
                    'total_todos' => $group->count(),
                    'total_pending_todos' => $group->where('status', 'pending')->count(),
                    'total_timetracked_completed_todos' => (float) $group->where('status', 'completed')->sum('time_tracked'),
                ];
            });
            return response()->json(['assignee_summary' => $summary]);
        }

        return response()->json(['message' => 'Invalid chart type'], 400);
    }
}
