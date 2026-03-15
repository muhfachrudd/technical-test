<?php

namespace App\Exports;

use App\Models\Todo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class TodosExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Todo::query();

        if (!empty($this->filters['title'])) {
            $query->where('title', 'like', '%' . $this->filters['title'] . '%');
        }

        if (!empty($this->filters['assignee'])) {
            $assignees = explode(',', $this->filters['assignee']);
            $query->whereIn('assignee', $assignees);
        }

        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $query->whereBetween('due_date', [$this->filters['start_date'], $this->filters['end_date']]);
        }

        if (isset($this->filters['min_time']) && isset($this->filters['max_time'])) {
            $query->whereBetween('time_tracked', [$this->filters['min_time'], $this->filters['max_time']]);
        }

        if (!empty($this->filters['status'])) {
            $statuses = explode(',', $this->filters['status']);
            $query->whereIn('status', $statuses);
        }

        if (!empty($this->filters['priority'])) {
            $priorities = explode(',', $this->filters['priority']);
            $query->whereIn('priority', $priorities);
        }

        $todos = $query->get();

        $totalTodos = $todos->count();
        $totalTimeTracked = $todos->sum('time_tracked');

        // Map todos to array for easier appending
        $data = $todos->map(function ($todo) {
            return [
                $todo->title,
                $todo->assignee,
                $todo->due_date->format('Y-m-d'),
                $todo->time_tracked,
                $todo->status,
                $todo->priority,
            ];
        });

        // Add empty row before summary
        $data->push(['', '', '', '', '', '']);

        // Add summary row
        $data->push([
            'SUMMARY',
            'Count: ' . $totalTodos,
            '',
            'Total: ' . $totalTimeTracked,
            '',
            ''
        ]);

        return $data;
    }

    public function headings(): array
    {
        return [
            'Title',
            'Assignee',
            'Due Date',
            'Time Tracked',
            'Status',
            'Priority',
        ];
    }

    public function map($row): array
    {
        return $row;
    }
}
