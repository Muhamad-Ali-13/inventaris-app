<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center sm:text-left">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-xl">
                <div class="p-6 sm:p-10 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <p class="text-lg sm:text-xl font-semibold">
                            {{ __("You're logged in!") }}
                        </p>
                        <button
                            class="px-4 py-2 text-sm sm:text-base bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow-md transition">
                            Get Started
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tambahan Card Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
                    <h3 class="text-lg font-semibold mb-2">📊 Statistik</h3>
                    <p class="text-gray-600 dark:text-gray-300">Ringkasan data inventaris terbaru.</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
                    <h3 class="text-lg font-semibold mb-2">👨‍💻 User</h3>
                    <p class="text-gray-600 dark:text-gray-300">Kelola akun dan hak akses.</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
                    <h3 class="text-lg font-semibold mb-2">⚙️ Pengaturan</h3>
                    <p class="text-gray-600 dark:text-gray-300">Sesuaikan sistem sesuai kebutuhan.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
