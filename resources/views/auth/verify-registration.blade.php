 @extends('layouts.app')

@section('title', 'Verify Account | Government Job Portal')

@section('content')
<div class="mx-auto max-w-xl rounded-2xl bg-white p-6 shadow-sm sm:p-10">
    <h1 class="text-2xl font-bold text-gray-900">Verify your account</h1>
    <p class="mt-2 text-sm text-gray-600">
        User ID: <strong>{{ $userId }}</strong>
    </p>

    @if (session('status'))
        <div class="mt-5 rounded-lg bg-green-50 p-4 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mt-5 rounded-lg bg-red-50 p-4 text-sm text-red-800">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="mt-8 space-y-8">
        @if (!$emailVerified)
            <section class="rounded-xl border border-gray-200 p-5">
                <h2 class="font-semibold text-gray-900">Email verification</h2>
                <form method="POST" action="{{ route('register.verify') }}" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $userId }}">
                    <input type="hidden" name="channel" value="email">

                    <label for="email_otp" class="block text-sm font-medium">Email code</label>
                    <input id="email_otp" name="otp" inputmode="numeric" autocomplete="one-time-code"
                        pattern="[0-9]{6}" maxlength="6" minlength="6" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-3">

                    <button type="submit"
                        class="w-full rounded-lg bg-indigo-700 px-4 py-3 font-semibold text-white hover:bg-indigo-800">
                        Verify email
                    </button>
                </form>

                <form method="POST" action="{{ route('register.resend') }}" class="mt-3">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $userId }}">
                    <input type="hidden" name="channel" value="email">
                    <button class="text-sm font-medium text-indigo-700 hover:underline" type="submit">
                        Resend email code
                    </button>
                </form>
            </section>
        @else
            <p class="rounded-lg bg-green-50 p-4 text-sm text-green-800">Email verified.</p>
        @endif

        @if (!$phoneVerified)
            <section class="rounded-xl border border-gray-200 p-5">
                <h2 class="font-semibold text-gray-900">Mobile verification</h2>
                <form method="POST" action="{{ route('register.verify') }}" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $userId }}">
                    <input type="hidden" name="channel" value="sms">

                    <label for="sms_otp" class="block text-sm font-medium">SMS code</label>
                    <input id="sms_otp" name="otp" inputmode="numeric" autocomplete="one-time-code"
                        pattern="[0-9]{6}" maxlength="6" minlength="6" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-3">

                    <button type="submit"
                        class="w-full rounded-lg bg-indigo-700 px-4 py-3 font-semibold text-white hover:bg-indigo-800">
                        Verify mobile
                    </button>
                </form>

                <form method="POST" action="{{ route('register.resend') }}" class="mt-3">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $userId }}">
                    <input type="hidden" name="channel" value="sms">
                    <button class="text-sm font-medium text-indigo-700 hover:underline" type="submit">
                        Resend SMS code
                    </button>
                </form>
            </section>
        @else
            <p class="rounded-lg bg-green-50 p-4 text-sm text-green-800">Mobile number verified.</p>
        @endif
    </div>

    <p class="mt-6 text-xs text-gray-500">
        Codes expire after the configured time. Never share a verification code with anyone.
    </p>
</div>
@endsection