<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TarefaResource\Pages;
use App\Models\Tarefa;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TagsColumn;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;

class TarefaResource extends Resource
{
    protected static ?string $model = Tarefa::class;
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $navigationLabel = 'Tarefas';
    protected static ?string $pluralLabel = 'Tarefas';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nome')
                ->required()
                ->label('Nome da Tarefa'),

            Forms\Components\Textarea::make('descricao')
                ->label('Descrição'),

            Forms\Components\MultiSelect::make('metodos_ferramentas')
                ->relationship('metodosFerramentas', 'nome', function (Builder $query) {
                    return $query->where('user_id', Filament::auth()->id());
                })
                ->label('Métodos e Ferramentas')
                ->preload()
                ->searchable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->label('Nome')
                    ->sortable()
                    ->searchable(),

                TagsColumn::make('metodosFerramentas.nome')
                    ->label('Métodos e Ferramentas')
                    ->separator(', '),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar exclusão')
                    ->modalDescription('Tem certeza que deseja excluir esta tarefa?'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    /**
     * 🔹 Só listar registros do usuário logado
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Filament::auth()->id());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTarefas::route('/'),
            'create' => Pages\CreateTarefa::route('/criar'),
            'edit' => Pages\EditTarefa::route('/{record}/editar'),
            'view' => Pages\ViewTarefa::route('/{record}'),
        ];
    }
}
