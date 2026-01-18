<?php

namespace App\Http\Responses;

use Filament\Auth\Http\Responses\LoginResponse as BaseLoginResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse extends BaseLoginResponse
{
    public function toResponse($request): Redirector|RedirectResponse
    {
        $user = auth()->user();

        // Staff redirect
        if ($user->isStaff()) {
            return redirect()->route('filament.admin.resources.assets.index');
        }

        // SuperAdmin
        if ($user->isSuperAdmin()) {
            return redirect()->route('filament.admin.pages.super-admin-dashboard');
        }

        // CompanyAdmin
        if ($user->isCompanyAdmin()) {
            return redirect()->route('filament.admin.pages.company-dashboard');
        }

        // Default fallback
        return parent::toResponse($request);
    }
}
