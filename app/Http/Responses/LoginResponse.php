<?php

namespace App\Http\Responses;

use Filament\Auth\Http\Responses\LoginResponse as BaseLoginResponse;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse extends BaseLoginResponse
{
    public function toResponse($request): Redirector|RedirectResponse
    {
        if (auth()->user()->isStaff()) {
            return redirect()->to('/admin/assets');
        }

        return parent::toResponse($request);
    }
}
