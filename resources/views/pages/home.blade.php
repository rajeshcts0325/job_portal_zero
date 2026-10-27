
@extends('layouts.app')

@section('title', 'Home | JobPortal')

@section('description', 'Find your next career opportunity with JobPortal.')

@section('content')

    <!-- Hero section -->
    <section class="bg-green-50">
        <div class="mx-auto grid max-w-7xl items-center gap-12 px-6 py-20 md:grid-cols-2 md:py-28">

            <div>
                <span class="inline-block rounded-full bg-green-100 px-4 py-2 text-sm font-semibold text-green-800">
                    Your next opportunity starts here
                </span>

                <h1 class="mt-6 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
                    Find the right job for your
                    <span class="text-green-800">future.</span>
                </h1>

                <p class="mt-6 max-w-xl text-lg leading-8 text-gray-600">
                    Explore career opportunities, discover great companies,
                    and take the next step in your professional journey.
                </p>

                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('jobs.index') }}"
                       class="rounded-lg bg-green-800 px-6 py-3 font-semibold text-white hover:bg-green-900">
                        Explore Jobs
                    </a>

                    <a href="{{ route('about') }}"
                       class="rounded-lg border border-gray-300 bg-white px-6 py-3 font-semibold hover:bg-gray-50">
                        Learn More
                    </a>
                </div>
            </div>

            <div class="rounded-2xl bg-white p-8 shadow-xl ring-1 ring-gray-100">
                <h2 class="text-2xl font-bold">Your career journey</h2>

                <p class="mt-2 text-gray-600">
                    Three simple steps to get started.
                </p>

                <div class="mt-8 space-y-6">
                    <div class="flex gap-4">
                        <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-green-100 font-bold text-green-800">1</div>
                        <div>
                            <h3 class="font-semibold">Create your profile</h3>
                            <p class="mt-1 text-sm text-gray-600">Showcase your skills and experience.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-green-100 font-bold text-green-800">2</div>
                        <div>
                            <h3 class="font-semibold">Explore opportunities</h3>
                            <p class="mt-1 text-sm text-gray-600">Discover jobs that match your goals.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-green-100 font-bold text-green-800">3</div>
                        <div>
                            <h3 class="font-semibold">Apply for jobs</h3>
                            <p class="mt-1 text-sm text-gray-600">Take the next step in your career.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Features section -->
    <section class="mx-auto max-w-7xl px-6 py-20">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-3xl font-bold">Everything you need to move forward</h2>
            <p class="mt-4 text-gray-600">
                Tools to help you find opportunities and build your career.
            </p>
        </div>

        <div class="mt-12 grid gap-6 md:grid-cols-3">
            <article class="rounded-xl border border-gray-200 p-6">
                <h3 class="text-xl font-semibold">Discover Jobs</h3>
                <p class="mt-3 leading-7 text-gray-600">
                    Browse opportunities across different roles and industries.
                </p>
            </article>

            <article class="rounded-xl border border-gray-200 p-6">
                <h3 class="text-xl font-semibold">Build Your Profile</h3>
                <p class="mt-3 leading-7 text-gray-600">
                    Present your skills, qualifications, and work experience.
                </p>
            </article>

            <article class="rounded-xl border border-gray-200 p-6">
                <h3 class="text-xl font-semibold">Manage Applications</h3>
                <p class="mt-3 leading-7 text-gray-600">
                    Keep your career search and applications organized.
                </p>
            </article>
        </div>
    </section>

    <!-- Call to action -->
    <section class="mx-auto max-w-7xl px-6 pb-20">
        <div class="rounded-2xl bg-green-800 px-8 py-12 text-center text-white md:px-16">
            <h2 class="text-3xl font-bold">Ready for your next opportunity?</h2>

            <p class="mt-4 text-green-100">
                Start exploring career possibilities today.
            </p>

            <a href="{{ route('jobs.index') }}"
               class="mt-8 inline-block rounded-lg bg-white px-6 py-3 font-semibold text-green-900 hover:bg-green-50">
                Browse Jobs
            </a>
        </div>
    </section>

@endsection
