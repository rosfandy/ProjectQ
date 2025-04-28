<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->label('Title')->columnSpanFull()->placeholder('Task Title'),
                Forms\Components\Select::make('project_id')->required()->label('Project')->relationship('project', 'name')
                    ->placeholder('Select Project')
                    ->columnSpanFull()
                    ->live()
                    ->searchable(),
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tasks')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Title'),
                Tables\Columns\TextColumn::make('code')->label('Task Code'),
                Tables\Columns\TextColumn::make('status.name')->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'todo' => 'info',
                        'in_progress' => 'warning',
                        'done' => 'success',
                    }),
                Tables\Columns\TextColumn::make('user.email')->label('Assigned To'),
                Tables\Columns\TextColumn::make('deadline')->label('Deadline'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
