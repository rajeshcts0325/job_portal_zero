
<footer class="border-t border-gray-200 bg-gray-50">
    <div class="mx-auto grid max-w-7xl gap-8 px-6 py-10 sm:grid-cols-2 md:grid-cols-3">

        <div>
            <h2 class="text-xl font-bold text-green-800">JobPortal</h2>
            <p class="mt-3 text-sm leading-6 text-gray-600">
                Connecting talented people with meaningful career opportunities.
            </p>
        </div>

        <div>
            <h3 class="font-semibold">Explore</h3>
            <ul class="mt-3 space-y-2 text-sm text-gray-600">
                <li><a href="{{ route('home') }}" class="hover:text-green-800">Home</a></li>
                <li><a href="{{ route('about') }}" class="hover:text-green-800">About Us</a></li>
                <!-- <li><a href="{{ route('jobs.index') }}" class="hover:text-green-800">Find Jobs</a></li> -->
            </ul>
        </div>

        <div>
            <h3 class="font-semibold">Support</h3>
            <ul class="mt-3 space-y-2 text-sm text-gray-600">
                <li><a href="{{ route('contact') }}" class="hover:text-green-800">Contact Us</a></li>
            </ul>
        </div>

    </div>

    <div class="border-t border-gray-200 px-6 py-4 text-center text-sm text-gray-500">
        &copy; {{ date('Y') }} JobPortal. All rights reserved.
    </div>
</footer>
