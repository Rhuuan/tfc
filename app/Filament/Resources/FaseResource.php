<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaseResource\Pages;
use App\Models\Fase;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->sortable()
                    ->searchable(),

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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

    /**
     * 🔹 Sempre salvar o registro vinculado ao usuário logado
     */
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Filament::auth()->id();
        return $data;
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
            'index' => Pages\ListFases::route('/'),
            'create' => Pages\CreateFase::route('/criar'),
            'edit' => Pages\EditFase::route('/{record}/editar'),
            'view' => Pages\ViewFase::route('/{record}'),
        ];
    }
}
