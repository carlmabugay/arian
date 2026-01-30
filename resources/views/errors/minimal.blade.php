<!DOCTYPE html>
<html lang="en" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>
        @vite('resources/css/app.css')
    </head>
    <body class="antialiased dark:bg-slate-800">
    <div class="max-w-7xl mx-auto flex-row items-center justify-center mt-48">

        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-28 mx-auto text-slate-600">
            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
        </svg>


        <div class="text-center">
            <h1 class="text-9xl font-bold text-[#00EB97]">@yield('code')</h1>
            <p class="text-2xl text-slate-400 mt-4 font-semibold "> @yield('message')</p>
            @php
                $redirect = auth()->check() && auth()->user()->isStaff() ? '/admin/assets' : '/';
            @endphp
            @if($withCTA)
                <a href="{{ url($redirect) }}" class="mt-6 inline-block font-semibold bg-slate-700 px-4 py-2 rounded-sm text-white duration-200 hover:bg-[#00EB97] hover:text-slate-800">
                    Back to Home
                </a>
            @endif


        </div>
    </div>
    </body>
</html>
