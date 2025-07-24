<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AtividadeResource\Pages;
use App\Models\Atividade;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;

class AtividadeResource extends Resource
{
    protected static ?string $model = Atividade::class;

    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    protected static ?string $navigationLabel = 'Atividades';
    protected static ?string $pluralLabel = 'Atividades';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nome')
                ->required()
                ->label('Nome'),

            Forms\Components\Textarea::make('descricao')
                ->label('Descrição'),

            Forms\Components\Select::make('fase_id')
                ->relationship('fase', 'nome')
                ->required()
                ->label('Fase'),

            Forms\Components\Select::make('tarefa_id')
                ->relationship('tarefa', 'nome')
                ->label('Tarefa')
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('fase.nome')->label('Fase')->sortable(),
                Tables\Columns\TextColumn::make('tarefa.nome')->label('Tarefa')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Criado em'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar exclusão')
                    ->modalDescription('Tem certeza que deseja excluir esta atividade?'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAtividades::route('/'),
            'create' => Pages\CreateAtividade::route('/criar'),
            'edit' => Pages\EditAtividade::route('/{record}/editar'),
            'view' => Pages\ViewAtividade::route('/{record}'),
        ];
    }
}
