<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;

class Sidebar extends \Filament\Livewire\Sidebar
{
    public function render(): View
    {
        return view('livewire.sidebar');
    }
}
