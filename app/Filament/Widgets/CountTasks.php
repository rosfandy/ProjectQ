<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class CountTasks extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            return [
                Stat::make('ToDo Tasks', \App\Models\Task::whereHas('status', function ($query) {
                    $query->where('name', 'todo');
                })->count())
                    ->description('Tasks in todo')
                    ->icon('heroicon-o-clipboard-document-list'),

                Stat::make('In Progress Tasks', \App\Models\Task::whereHas('status', function ($query) {
                    $query->where('name', 'in_progress');
                })->count())
                    ->description('Tasks in progress')
                    ->icon('heroicon-o-clipboard-document-list'),

                Stat::make('Done Tasks', \App\Models\Task::whereHas('status', function ($query) {
                    $query->where('name', 'done');
                })->count())
                    ->description('Tasks in complete')
                    ->icon('heroicon-o-clipboard-document-list'),
            ];
        } else {
            return [
                Stat::make('ToDo Tasks', \App\Models\Task::where('user_id', $user->id)
                    ->whereHas('status', function ($query) {
                        $query->where('name', 'todo');
                    })->count())
                    ->description('Tasks in todo')
                    ->icon('heroicon-o-clipboard-document-list'),

                Stat::make('In Progress Tasks', \App\Models\Task::where('user_id', $user->id)
                    ->whereHas('status', function ($query) {
                        $query->where('name', 'in_progress');
                    })->count())
                    ->description('Tasks in progress')
                    ->icon('heroicon-o-clipboard-document-list'),

                Stat::make('Done Tasks', \App\Models\Task::where('user_id', $user->id)
                    ->whereHas('status', function ($query) {
                        $query->where('name', 'done');
                    })->count())
                    ->description('Tasks in complete')
                    ->icon('heroicon-o-clipboard-document-list'),
            ];
        }
    }
}
