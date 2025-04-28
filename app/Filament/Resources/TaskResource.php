<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->when(!auth()->user()->hasRole('super_admin'), function (Builder $query) {
                $query->whereHas('user', function (Builder $query) {
                    $query->where('user_id', auth()->id());
                });
            })
            ->withCount(['user']);
    }


    public static function getNavigationBadge(): ?string
    {
        $query = static::getModel()::query();

        if (!auth()->user()->hasRole('super_admin')) {
            $query->whereHas('user', function (Builder $query) {
                $query->where('user_id', auth()->id());
            });
        }

        return (string) $query->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->label('Title')->columnSpanFull()->placeholder('Task Title'),
                Forms\Components\Select::make('project_id')->required()->label('Project')->relationship('project', 'name')
                    ->searchable()
                    ->placeholder('Select Project')
                    ->columnSpanFull()
                    ->live(),
                Forms\Components\Select::make('user_id')->required()->label('Assigned to')->relationship('user', 'name')
                    ->options(function (Forms\Get $get) {
                        $projectId = $get('project_id');
                        if (!$projectId) {
                            return [];
                        }
                        return \App\Models\ProjectMember::find($projectId)
                            ->user()
                            ->pluck('users.name', 'users.id');
                    })
                    ->placeholder('Select Project Member')
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('description')->required()->label('Description')->columnSpanFull(),
                Forms\Components\DatePicker::make('deadline')->required()->label('Deadline')->columnSpanFull(),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('code')->label('Task Code'),
                TextColumn::make('name'),
                TextColumn::make('status.name')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'todo' => 'info',
                        'in_progress' => 'warning',
                        'done' => 'success',
                    }),
                TextColumn::make('deadline')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
            'view' => Pages\ViewTask::route('/{record}/view'),
        ];
    }
}
