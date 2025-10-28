<?php

namespace App\Filament\Resources\ProjetoResource\Widgets;

use Filament\Widgets\Widget;

class ProjetoIntro extends Widget
{
    protected static string $view = 'filament.resources.projeto-resource.widgets.projeto-intro';

    protected int|string|array $columnSpan = 'full';
}
