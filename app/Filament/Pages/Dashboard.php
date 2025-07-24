<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';
    protected static ?string $title = 'Início';
    protected static ?string $navigationLabel = 'Início';

    public function getUserName(): string
    {
        return Auth::user()?->name ?? 'Usuário';
    }
}
