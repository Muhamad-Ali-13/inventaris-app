<x-app-layout>
    <div class="py-12">
        <!-- Header -->
        <div class="max-w-6xl mx-auto mb-10 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold text-green-700 mb-2 animate-fadeIn">Profil Pengguna</h2>
            <p class="text-gray-600 text-sm sm:text-base">Kelola informasi akun, ubah password, atau hapus akun Anda.</p>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
