
@extends('layouts.app')

@section('title', 'About Us | JobPortal')

@section('content')

    <section class="bg-green-50 px-6 py-20 text-center">
        <div class="mx-auto max-w-3xl">
            <h1 class="text-4xl font-bold sm:text-5xl">About JobPortal</h1>

            <p class="mt-6 text-lg leading-8 text-gray-600">
                Our goal is to make discovering career opportunities
                and connecting with employers simpler.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-5xl px-6 py-16">
        <h2 class="text-3xl font-bold">What we do</h2>

        <p class="mt-5 leading-8 text-gray-600">
            JobPortal provides a platform where job seekers can explore
            opportunities and employers can connect with potential candidates.
            We aim to make the job search process clear, accessible, and organized.
        </p>

        <div class="mt-10 grid gap-6 sm:grid-cols-2">
            <article class="rounded-xl border border-gray-200 p-6">
                <h3 class="text-xl font-semibold">Our Mission</h3>
                <p class="mt-3 leading-7 text-gray-600">
                    Help people discover opportunities aligned with their skills
                    and professional goals.
                </p>
            </article>

            <article class="rounded-xl border border-gray-200 p-6">
                <h3 class="text-xl font-semibold">Our Vision</h3>
                <p class="mt-3 leading-7 text-gray-600">
                    Create a straightforward and accessible job-search experience
                    for candidates and employers.
                </p>
            </article>
        </div>
    </section>

@endsection
