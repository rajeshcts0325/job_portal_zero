
<header class="border-b border-gray-200 bg-white">
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">

        <a href="{{ route('home') }}"
           class="text-2xl font-bold tracking-tight text-green-800">
            JobPortal
        </a>

        <div class="hidden items-center gap-8 md:flex">
            <a href="{{ route('home') }}"
               class="text-gray-700 hover:text-green-800">
                Home
            </a>

            <a href="{{ route('about') }}"
               class="text-gray-700 hover:text-green-800">
                About
            </a>

            <!-- <a href="{{ route('jobs.index') }}"
               class="text-gray-700 hover:text-green-800">
                Jobs
            </a> -->

            <a href="{{ route('contact') }}"
               class="text-gray-700 hover:text-green-800">
                Contact
            </a>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('login') }}"
               class="hidden text-sm font-semibold text-gray-700 sm:inline">
                Login
            </a>

            <a href="{{ route('register') }}"
               class="rounded-lg bg-green-800 px-4 py-2 text-sm font-semibold text-white hover:bg-green-900">
                Get Started
            </a>
        </div>

    </nav>
</header>
