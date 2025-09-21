<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\Projeto;  // Supondo que você queira mostrar projetos no dashboard, se necessário
use App\Models\Fase;  // Supondo que você queira mostrar fases no dashboard

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

    // Você pode criar métodos para buscar projetos ou fases do usuário logado
    public function getProjetos(): \Illuminate\Database\Eloquent\Collection
    {
        return Projeto::where('user_id', Auth::id())->get();  // Filtra projetos apenas do usuário logado
    }

    public function getFases(): \Illuminate\Database\Eloquent\Collection
    {
        return Fase::where('user_id', Auth::id())->get();  // Filtra fases apenas do usuário logado
    }

    // Se você estiver usando widgets no painel, pode filtrar também
    public function getCustomData()
    {
        // Exemplo de retorno com base em dados filtrados
        return [
            'projetos' => $this->getProjetos(),
            'fases' => $this->getFases(),
        ];
    }
}
