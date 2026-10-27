
@extends('layouts.app')

@section('title', 'Contact Us | JobPortal')

@section('content')

    <section class="mx-auto max-w-5xl px-6 py-16">
        <div class="mx-auto max-w-2xl text-center">
            <h1 class="text-4xl font-bold">Contact Us</h1>

            <p class="mt-4 leading-7 text-gray-600">
                Have a question or need help? Send us a message.
            </p>
        </div>

        <div class="mx-auto mt-10 max-w-2xl rounded-2xl border border-gray-200 bg-white p-8 shadow-sm">


            @if (session('success'))
                <div class="mb-6 rounded-lg bg-green-50 p-4 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('contact.submit') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="mb-2 block text-sm font-medium">
                        Full name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        maxlength="100"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-green-700 focus:ring-2 focus:ring-green-100"
                    >

                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="mb-2 block text-sm font-medium">
                        Email address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        maxlength="255"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-green-700 focus:ring-2 focus:ring-green-100"
                    >

                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="message" class="mb-2 block text-sm font-medium">
                        Message
                    </label>

                    <textarea
                        id="message"
                        name="message"
                        rows="5"
                        required
                        maxlength="5000"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-green-700 focus:ring-2 focus:ring-green-100"
                    >{{ old('message') }}</textarea>

                    @error('message')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full rounded-lg bg-green-800 px-6 py-3 font-semibold text-white hover:bg-green-900"
                >
                    Send Message
                </button>
            </form>

        </div>
    </section>

@endsection
