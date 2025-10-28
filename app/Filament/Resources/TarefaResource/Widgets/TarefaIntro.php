<?php

namespace App\Filament\Resources\TarefaResource\Widgets;

use Filament\Widgets\Widget;

class TarefaIntro extends Widget
{
    protected static string $view = 'filament.resources.tarefa-resource.widgets.tarefa-intro';

    protected int|string|array $columnSpan = 'full';
}
