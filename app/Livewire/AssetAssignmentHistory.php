<?php

namespace App\Livewire;

use App\Models\Asset;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class AssetAssignmentHistory extends Component
{
    public Asset $asset;

    public function render(): View|Application|\Illuminate\View\View
    {
        return view('livewire.asset-assignment-history', [
            'assignments' => $this->asset
                ->assignments()
                ->latest('assigned_at')
                ->get(),
        ]);
    }
}
