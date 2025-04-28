<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class CountData extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            return [
                Stat::make('Total Projects', \App\Models\Project::count())
                    ->description('All projects in system')
                    ->icon('heroicon-o-folder'),

                Stat::make('Total Tasks', \App\Models\Task::count())
                    ->description('All tasks in system')
                    ->icon('heroicon-o-clipboard-document-list'),

            ];
        } else {
            return [
                Stat::make('Your Projects', \App\Models\ProjectMember::where('user_id', $user->id)->count())
                    ->description('Projects assigned to you')
                    ->icon('heroicon-o-folder'),

                Stat::make('Your Tasks', \App\Models\Task::where('user_id', $user->id)->count())
                    ->description('Tasks assigned to you')
                    ->icon('heroicon-o-clipboard-document-list'),

            ];
        }
    }
}
