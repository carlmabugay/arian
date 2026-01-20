<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;

class Topbar extends \Filament\Livewire\Topbar
{
    public function render(): View
    {
        return view('livewire.topbar');
    }
}
