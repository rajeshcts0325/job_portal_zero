 @extends('layouts.app')

@section('title', 'Create Account | Government Job Portal')

@section('content')
<div class="mx-auto max-w-2xl rounded-2xl bg-white p-6 shadow-sm sm:p-10">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Create your account</h1>
        <p class="mt-2 text-sm text-gray-600">
            Register to access government job notifications and application services.
        </p>
    </div>

    @if (session('status'))
        <div class="mb-5 rounded-lg bg-green-50 p-4 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-lg bg-red-50 p-4 text-sm text-red-800">
            <p class="font-semibold">Please correct the following:</p>
            <ul class="mt-2 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
        @csrf

        <div>
            <label for="name" class="mb-1 block text-sm font-medium">Full name</label>
            <input id="name" name="name" value="{{ old('name') }}"
                autocomplete="name" required maxlength="100"
                class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-600 focus:outline-none">
        </div>

        <div>
            <label for="email" class="mb-1 block text-sm font-medium">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}"
                autocomplete="email" required maxlength="255"
                class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-600 focus:outline-none">
        </div>

        <div>
            <label for="mobile" class="mb-1 block text-sm font-medium">Mobile number</label>
            <input id="mobile" name="mobile" type="tel" value="{{ old('mobile') }}"
                placeholder="+919876543210" autocomplete="tel" required
                pattern="^\+[1-9][0-9]{7,14}$"
                class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-600 focus:outline-none">
            <p class="mt-1 text-xs text-gray-500">Include the country code, such as +91 for India.</p>
        </div>

        <div>
            <label for="user_id" class="mb-1 block text-sm font-medium">User ID</label>
            <input id="user_id" name="user_id" value="{{ old('user_id') }}"
                autocomplete="username" required minlength="4" maxlength="30"
                pattern="[A-Za-z0-9._-]+"
                class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-600 focus:outline-none">
            <p class="mt-1 text-xs text-gray-500">Use 4–30 letters, numbers, dots, underscores, or hyphens.</p>
        </div>

        <div>
            <label for="address" class="mb-1 block text-sm font-medium">Address (optional)</label>
            <textarea id="address" name="address" rows="3" maxlength="1000"
                autocomplete="street-address"
                class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-600 focus:outline-none">{{ old('address') }}</textarea>
        </div>

        <div>
            <label for="password" class="mb-1 block text-sm font-medium">Password</label>
            <input id="password" name="password" type="password"
                autocomplete="new-password" required minlength="12"
                class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-600 focus:outline-none">
            <p class="mt-1 text-xs text-gray-500">Use at least 12 characters. Never share your password or OTP.</p>
        </div>

        <div>
            <label for="password_confirmation" class="mb-1 block text-sm font-medium">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password"
                autocomplete="new-password" required minlength="12"
                class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-600 focus:outline-none">
        </div>

        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
            <p class="text-sm font-medium text-gray-800">Verification required</p>
            <p class="mt-1 text-sm text-gray-600">
                We will send one verification code to your email and another to your mobile number.
                Both must be verified before your account is activated.
            </p>
        </div>

                <div>
            <div class="cf-turnstile"
                data-sitekey="{{ config('services.turnstile.site_key') }}">
            </div>
        </div>
    
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

        <button type="submit"
            class="w-full rounded-lg bg-indigo-700 px-5 py-3 font-semibold text-white hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Create account
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        Already registered?
        <a href="{{ route('login') }}" class="font-semibold text-indigo-700 hover:underline">Sign in</a>
    </p>
</div>
@endsection