<?php

namespace App\Filament\Resources\AtividadeResource\Widgets;

use Filament\Widgets\Widget;

class AtividadeIntro extends Widget
{
    protected static string $view = 'filament.resources.atividade-resource.widgets.atividade-intro';

    protected int|string|array $columnSpan = 'full';
}
