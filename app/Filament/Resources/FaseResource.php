<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaseResource\Pages;
use App\Models\Fase;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form; // <-- CORRIGIDO
use Filament\Tables\Table; // <-- CORRIGIDO
use Filament\Resources\Resource;

class FaseResource extends Resource
{
    protected static ?string $model = Fase::class;

    protected static ?int $navigationSort = 2;

    // Ícone corrigido para a versão v2 do Heroicons
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list'; // <-- CORRIGIDO
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

            // Este campo requer que a relação "atividades" esteja definida no seu Model
            Forms\Components\Select::make('atividades')
                ->relationship('atividades', 'nome')
                ->multiple() // Use multiple() para MultiSelect
                ->preload()  // Preload melhora a performance
                ->label('Atividades'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('data')->date()->sortable(),
                Tables\Columns\TextColumn::make('atividades_count')
                    ->counts('atividades')
                    ->label('Nº de Atividades'),
            ])
            ->actions([
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
        ];
    }
}