<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\EditProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomEditProfile extends EditProfile
{
    protected function getFormActions(): array
    {
        return [
            $this->getCancelFormAction(),
            $this->getSaveFormAction(),
            $this->getDeleteAccountAction(),
        ];
    }

    protected function getDeleteAccountAction(): Action
    {
        return Action::make('deleteAccount')
            ->label('Excluir Conta')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Excluir sua conta permanentemente?')
            ->modalDescription('Esta ação é irreversível. Sua conta e todos os dados serão excluídos permanentemente. Digite sua senha para confirmar.')
            ->modalIcon('heroicon-o-exclamation-triangle')
            ->form([
                TextInput::make('password')
                    ->label('Senha')
                    ->password()
                    ->required()
                    ->currentPassword(), // Usa o validador nativo do Filament
            ])
            ->action(function (array $data) {
                /** @var \App\Models\User $user */
                $user = Auth::user();
                
                // Fazer logout antes de deletar
                Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();
                
                // Deletar o usuário do banco de dados
                $user->delete();
                
                // Redirecionar para página de confirmação
                return redirect()->route('account.deleted');
            });
    }
}
