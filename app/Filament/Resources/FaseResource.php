<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaseResource\Pages;
use App\Models\Fase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FaseResource extends Resource
{
    protected static ?string $model = Fase::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Fases';
    protected static ?string $pluralLabel = 'Fases';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nome')
                ->required()
                ->label('Nome'),

            Forms\Components\DatePicker::make('data')
                ->required()
                ->label('Data'),

            Forms\Components\Select::make('atividades')
                ->relationship('atividades', 'nome')
                ->multiple()
                ->preload()
                ->label('Atividades'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('data')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('atividades_count')
                    ->counts('atividades')
                    ->label('Nº de Atividades'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(), // ✅ Adicionado botão de visualização
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar exclusão')
                    ->modalDescription('Tem certeza que deseja excluir esta fase?'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFases::route('/'),
            'create' => Pages\CreateFase::route('/criar'),
            'edit' => Pages\EditFase::route('/{record}/editar'),
            'view' => Pages\ViewFase::route('/{record}'), 
        ];
    }
}
