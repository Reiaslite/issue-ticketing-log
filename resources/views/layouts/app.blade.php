<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Issue Ticketing Log')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell" data-layout-shell data-requires-auth>
        <x-layout.sidebar />

        <div class="app-overlay" data-sidebar-overlay aria-hidden="true"></div>

        <div class="app-body">
            <x-layout.top-navbar />

            <x-layout.main-content>
                @yield('content')
            </x-layout.main-content>
        </div>
    </div>
</body>
</html>
