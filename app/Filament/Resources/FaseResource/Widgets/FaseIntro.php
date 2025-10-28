<?php

namespace App\Filament\Resources\FaseResource\Widgets;

use Filament\Widgets\Widget;

class FaseIntro extends Widget
{
    protected static string $view = 'filament.resources.fase-resource.widgets.fase-intro';

    protected int|string|array $columnSpan = 'full';
}
