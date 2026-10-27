
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Job Portal')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 font-sans text-gray-900">

    @include('components.navbar')

    <main class="mx-auto max-w-7xl px-6 py-8">
        @yield('content')
    </main>

    @include('components.footer')

</body>
</html>
