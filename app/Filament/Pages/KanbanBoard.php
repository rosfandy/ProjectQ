<?php

namespace App\Filament\Pages;

use App\Filament\Resources\TaskResource;
use App\Models\Project;
use App\Models\Status;
use App\Models\Task;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class KanbanBoard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    protected static string $view = 'filament.pages.kanban-board';
    protected static ?string $title = 'Kanban Board';
    protected static ?string $navigationGroup = 'Project Management';

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [];

        try {
            $breadcrumbs[route('dashboard')] = __('Dashboard');
        } catch (\Exception $e) {
            $breadcrumbs[url('/')] = __('Home');
        }

        $breadcrumbs[static::getUrl()] = static::$title;

        return $breadcrumbs;
    }

    #[Url]
    public ?int $selectedProjectId = null;

    #[Computed]
    public function selectedProject()
    {
        return $this->selectedProjectId
            ? Project::find($this->selectedProjectId)
            : null;
    }

    #[Computed]
    public function projects()
    {
        return Project::query()
            ->when(!auth()->user()->hasRole('super_admin'), function ($query) {
                $query->whereHas('members', fn($q) => $q->where('user_id', auth()->id()));
            })
            ->get();
    }

    #[Computed]
    public function taskStatuses()
    {
        try {
            return Status::all();
        } catch (\Throwable $th) {
            return [];
        }
    }

    #[Computed]
    public function tasks()
    {
        try {
            return Task::where('project_id', $this->selectedProjectId)
                ->with('status')
                ->get()
                ->groupBy('status_id');
        } catch (\Throwable $th) {
            return [];
        }
    }

    public function selectProject($projectId): void
    {
        $this->selectedProjectId = $projectId;
    }

    public function showDetailTask($taskId)
    {
        $task = Task::findOrFail($taskId);
        return redirect()->to(
            TaskResource::getUrl('view', ['record' => $taskId])
        );
    }

    public function updateTaskStatus($taskId, $statusId)
    {
        try {
            $task = Task::where('code', $taskId)->first();

            $task->status_id = $statusId;
            $task->save();

            Notification::make()
                ->title('Task Moved Successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
