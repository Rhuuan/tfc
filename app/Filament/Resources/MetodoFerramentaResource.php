<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MetodoFerramentaResource\Pages;
use App\Models\MetodoFerramenta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;

class MetodoFerramentaResource extends Resource
{
    protected static ?string $model = MetodoFerramenta::class;

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'Métodos e Ferramentas';
    protected static ?string $pluralLabel = 'Métodos e Ferramentas';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nome')
                ->required()
                ->label('Nome do Método ou Ferramenta')
                ->placeholder('Ex: Scrum, Kanban, 5W2H'),

            Forms\Components\Select::make('tipo')
                ->required()
                ->label('Tipo')
                ->options([
                    'agil' => 'Ágil',
                    'tradicional' => 'Tradicional',
                    'hibrido' => 'Híbrido',
                    'tecnica' => 'Técnica',
                    'ferramenta' => 'Ferramenta',
                    'outra' => 'Outra',
                ])
                ->searchable()
                ->placeholder('Selecione o tipo'),

            Forms\Components\Textarea::make('descricao')
                ->label('Descrição')
                ->rows(4)
                ->placeholder('Descreva brevemente a utilidade ou funcionamento'),
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

                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->sortable(),

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
                    ->modalDescription('Tem certeza que deseja excluir este método/ferramenta?'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMetodoFerramentas::route('/'),
            'create' => Pages\CreateMetodoFerramenta::route('/criar'),
            'edit' => Pages\EditMetodoFerramenta::route('/{record}/editar'),
            'view' => Pages\ViewMetodoFerramenta::route('/{record}'),
        ];
    }
}
