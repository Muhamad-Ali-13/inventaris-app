<x-guest-layout>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <div
        class="w-full flex flex-col md:flex-row items-center justify-center bg-gradient-to-br from-green-50 via-white to-green-100 px-4 sm:px-6 py-10">
        <!-- Form Login -->
        <div
            class="w-full md:w-[480px] lg:w-[460px] xl:w-[480px] bg-white rounded-2xl shadow-2xl border border-green-100 p-8 sm:p-10 animate-fadeIn">
            <!-- Logo & Header -->
            <div class="flex flex-col items-center text-center mb-6">
                <!-- Logo -->
                <img src="{{ asset('image/logo.png') }}" alt="Logo BPR AJM" class="w-20 h-20 object-contain mb-3">

                <!-- Header -->
                <h1 class="text-2xl font-bold text-green-700 leading-tight">BPR Artha Jaya Mandiri</h1>
                <p class="text-gray-500 text-sm mt-1">Sistem Informasi Manajemen Inventaris</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Email')" class="text-green-700 font-semibold" />
                    <x-text-input id="email"
                        class="block mt-2 w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                        type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                        placeholder="Masukkan email Anda" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Password')" class="text-green-700 font-semibold" />
                    <x-text-input id="password"
                        class="block mt-2 w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                        type="password" name="password" required autocomplete="current-password"
                        placeholder="Masukkan password Anda" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember + Forgot -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox"
                            class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500"
                            name="remember">
                        <span class="ms-2 text-sm text-gray-600">{{ __('Ingat saya') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-sm text-green-600 hover:text-green-700 transition font-medium">
                            Lupa password?
                        </a>
                    @endif
                </div>

                <!-- Button -->
                <div>
                    <x-primary-button
                        class="w-full justify-center bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg py-3 transition duration-200 shadow-md hover:shadow-lg">
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>
            </form>

            <!-- Info Hubungi Admin -->
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Belum punya akun?
                    <a href="https://wa.me/6281234567890" target="_blank"
                        class="text-green-600 font-semibold hover:underline">
                        Hubungi admin untuk pendaftaran akun
                    </a>
                </p>
            </div>

            <!-- Divider -->

            {{-- <div class="flex items-center justify-center mt-6">
                <div class="border-t border-gray-300 w-1/4"></div>
                <span class="mx-3 text-gray-500 text-sm">atau</span>
                <div class="border-t border-gray-300 w-1/4"></div>
            </div>

            <!-- Info Hubungi Admin -->
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Belum punya akun?
                    <span class="text-green-600 font-semibold">
                        Silakan hubungi admin untuk pendaftaran akun.
                    </span>
                </p>
            </div> --}}


        </div>
    </div>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(40px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.8s ease-out;
        }

        .animate-slideIn {
            animation: slideIn 1s ease-out;
        }
    </style>
</x-guest-layout>
