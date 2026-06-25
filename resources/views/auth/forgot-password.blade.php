<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        <p class="mb-2"><strong>Lupa kata sandi Anda?</strong></p>
        <ul class="list-disc pl-5 space-y-1">
            <li>Bagi <strong>Admin</strong> dan <strong>Kasir</strong>: Silakan hubungi <strong>Owner</strong> toko Anda untuk mereset kata sandi.</li>
            <li>Bagi <strong>Owner</strong>: Masukkan email Anda di bawah ini untuk menerima tautan reset kata sandi.</li>
        </ul>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
