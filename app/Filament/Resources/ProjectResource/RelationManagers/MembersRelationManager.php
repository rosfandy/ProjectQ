<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Models\ProjectMember;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Member')
                    ->placeholder('Select Member')
                    ->options(fn() => \App\Models\User::whereNotIn('id', function ($query) {
                        $query->select('user_id')
                            ->from('project_members')
                            ->where('project_id', $this->ownerRecord->id);
                    })->pluck('email', 'id'))
                    ->required()
                    ->searchable(),
            ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Name'),
                Tables\Columns\TextColumn::make('user.email')->label('Email'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Member')
                    ->modalHeading('Add Member'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['project_id'] = $this->ownerRecord->id;

        return $data;
    }
}
