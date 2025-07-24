<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjetoResource\Pages;
use App\Models\Projeto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TagsColumn;

class ProjetoResource extends Resource
{
    protected static ?string $model = Projeto::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = 'Projetos';

    protected static ?string $pluralLabel = 'Projetos';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nome')
                ->required()
                ->label('Nome'),

            Forms\Components\Textarea::make('descricao')
                ->label('Descrição'),

            Forms\Components\MultiSelect::make('fases')
                ->relationship('fases', 'nome')
                ->label('Fases')
                ->required()
                ->preload()
                ->searchable(),

            Forms\Components\MultiSelect::make('atividades')
                ->relationship('atividades', 'nome')
                ->label('Atividades')
                ->required()
                ->preload()
                ->searchable(),

            Forms\Components\MultiSelect::make('tarefas')
                ->relationship('tarefas', 'nome')
                ->label('Tarefas')
                ->required()
                ->preload()
                ->searchable(),

            Forms\Components\MultiSelect::make('metodoFerramentas')
                ->relationship('metodoFerramentas', 'nome')
                ->label('Métodos e Ferramentas')
                ->required()
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

                Tables\Columns\TextColumn::make('descricao')
                    ->label('Descrição')
                    ->limit(50),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar exclusão')
                    ->modalDescription('Tem certeza que deseja excluir este projeto?'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjetos::route('/'),
            'create' => Pages\CreateProjeto::route('/criar'),
            'edit' => Pages\EditProjeto::route('/{record}/editar'),
            'view' => Pages\ViewProjeto::route('/{record}'),
        ];
    }
}
