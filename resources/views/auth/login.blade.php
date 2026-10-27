
@extends('layouts.app')

@section('title', 'Sign In | Government Job Portal')

@section('content')
<div class="mx-auto max-w-xl rounded-2xl bg-white p-6 shadow-sm sm:p-10">
    <h1 class="text-3xl font-bold text-gray-900">Sign in</h1>
    <p class="mt-2 text-sm text-gray-600">
        Sign in to manage your job applications and profile.
    </p>

    <div id="auth-message" role="status" aria-live="polite"
         class="mt-5 hidden rounded-lg p-4 text-sm"></div>

    <section id="login-panel" class="mt-8 space-y-8">
        <div>
            <h2 class="font-semibold text-gray-900">User ID and password</h2>

            <form id="password-form" class="mt-4 space-y-4">
                <div>
                    <label for="login-user-id" class="mb-1 block text-sm font-medium">User ID</label>
                    <input id="login-user-id" name="user_id" required maxlength="30"
                        autocomplete="username"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3">
                </div>

                <div>
                    <label for="login-password" class="mb-1 block text-sm font-medium">Password</label>
                    <input id="login-password" name="password" type="password"
                        required autocomplete="current-password"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3">
                </div>

                <button type="submit"
                    class="w-full rounded-lg bg-indigo-700 px-4 py-3 font-semibold text-white hover:bg-indigo-800">
                    Sign in with password
                </button>
            </form>
        </div>

        <div class="border-t border-gray-200 pt-6">
            <h2 class="font-semibold text-gray-900">Sign in with a verification code</h2>

            <form id="otp-request-form" class="mt-4 space-y-4">
                <div>
                    <label for="otp-channel" class="mb-1 block text-sm font-medium">Send code to</label>
                    <select id="otp-channel" name="channel"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3">
                        <option value="email">Email address</option>
                        <option value="sms">Mobile number</option>
                    </select>
                </div>

                <div>
                    <label for="otp-identifier" class="mb-1 block text-sm font-medium">
                        Registered email or mobile number
                    </label>
                    <input id="otp-identifier" name="identifier" required maxlength="255"
                        autocomplete="username"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3"
                        placeholder="Email or +919876543210">
                </div>

                <button type="submit"
                    class="w-full rounded-lg border border-indigo-700 px-4 py-3 font-semibold text-indigo-700 hover:bg-indigo-50">
                    Request verification code
                </button>
            </form>

            <form id="otp-verify-form" class="mt-4 hidden space-y-4">
                <div>
                    <label for="login-otp" class="mb-1 block text-sm font-medium">6-digit code</label>
                    <input id="login-otp" name="otp" required inputmode="numeric"
                        autocomplete="one-time-code" pattern="[0-9]{6}"
                        minlength="6" maxlength="6"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3">
                </div>

                <button type="submit"
                    class="w-full rounded-lg bg-indigo-700 px-4 py-3 font-semibold text-white hover:bg-indigo-800">
                    Verify code and sign in
                </button>
            </form>
        </div>
    </section>

    <section id="authenticated-panel" class="mt-8 hidden">
        <h2 class="text-xl font-semibold text-gray-900">You are signed in</h2>
        <p id="signed-in-user" class="mt-2 text-sm text-gray-600"></p>

        <button id="logout-button" type="button"
            class="mt-5 w-full rounded-lg bg-gray-900 px-4 py-3 font-semibold text-white hover:bg-gray-700">
            Sign out
        </button>
    </section>

    <p class="mt-8 text-center text-sm text-gray-600">
        Don't have an account?
        <a href="{{ route('register') }}" class="font-semibold text-indigo-700 hover:underline">
            Register
        </a>
    </p>
</div>

<script>
(() => {
    // Intentionally held only in memory. Never store this token in browser storage.
    let accessToken = null;
    let currentOtp = null;

    const message = document.getElementById('auth-message');
    const loginPanel = document.getElementById('login-panel');
    const authenticatedPanel = document.getElementById('authenticated-panel');
    const signedInUser = document.getElementById('signed-in-user');
    const otpVerifyForm = document.getElementById('otp-verify-form');

    function showMessage(text, isError = false) {
        message.textContent = text;
        message.className = 'mt-5 rounded-lg p-4 text-sm ' +
            (isError ? 'bg-red-50 text-red-800' : 'bg-green-50 text-green-800');
    }

    async function api(url, payload = null, authenticated = false) {
        const headers = { 'Accept': 'application/json' };

        if (payload !== null) {
            headers['Content-Type'] = 'application/json';
        }

        if (authenticated && accessToken) {
            headers['Authorization'] = 'Bearer ' + accessToken;
        }

        const response = await fetch(url, {
            method: payload === null ? 'GET' : 'POST',
            headers,
            body: payload === null ? undefined : JSON.stringify(payload),
            credentials: 'omit'
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            const validationMessages = data.errors
                ? Object.values(data.errors).flat().join(' ')
                : null;

            throw new Error(validationMessages || data.message || 'Request failed.');
        }

        return data;
    }

    function signIn(data) {
        accessToken = data.token.access_token;
        currentOtp = null;

        loginPanel.classList.add('hidden');
        authenticatedPanel.classList.remove('hidden');

        // Use textContent, not innerHTML, for server-provided values.
        signedInUser.textContent = 'Signed in as ' + data.user.name +
            ' (' + data.user.user_id + ').';

        showMessage('Sign-in successful. This page holds your access token in memory only.');
    }

    document.getElementById('password-form').addEventListener('submit', async event => {
        event.preventDefault();

        const form = new FormData(event.currentTarget);

        try {
            const data = await api('/api/v1/public/login', {
                user_id: form.get('user_id'),
                password: form.get('password')
            });

            signIn(data);
        } catch (error) {
            showMessage(error.message, true);
        }
    });

    document.getElementById('otp-request-form').addEventListener('submit', async event => {
        event.preventDefault();

        const form = new FormData(event.currentTarget);
        currentOtp = {
            channel: form.get('channel'),
            identifier: form.get('identifier')
        };

        try {
            const data = await api('/api/v1/public/login/otp/request', currentOtp);
            otpVerifyForm.classList.remove('hidden');
            showMessage(data.message);
        } catch (error) {
            showMessage(error.message, true);
        }
    });

    otpVerifyForm.addEventListener('submit', async event => {
        event.preventDefault();

        if (!currentOtp) {
            showMessage('Request a verification code first.', true);
            return;
        }

        try {
            const form = new FormData(event.currentTarget);
            const data = await api('/api/v1/public/login/otp/verify', {
                ...currentOtp,
                otp: form.get('otp')
            });

            signIn(data);
        } catch (error) {
            showMessage(error.message, true);
        }
    });

    document.getElementById('logout-button').addEventListener('click', async () => {
        try {
            await api('/api/v1/public/logout', {}, true);
            showMessage('You have signed out.');
        } catch (error) {
            showMessage('The server could not confirm logout. The local token has been cleared.', true);
        } finally {
            accessToken = null;
            currentOtp = null;
            loginPanel.classList.remove('hidden');
            authenticatedPanel.classList.add('hidden');
            otpVerifyForm.classList.add('hidden');
            document.getElementById('password-form').reset();
            document.getElementById('otp-request-form').reset();
            otpVerifyForm.reset();
        }
    });
})();
</script>
@endsection
