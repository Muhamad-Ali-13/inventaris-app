<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIAJM - Sistem Inventaris Artha Jaya Mandiri</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white fixed w-full z-50 shadow-sm">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-4">
            <!-- Logo -->
            <div class="flex items-center gap-2">
                <img src="{{ asset('image/logo.png') }}" alt="Logo" class="w-10 h-10">
                <h1 class="text-2xl font-bold text-emerald-700">SIAJM</h1>
            </div>

            <!-- Menu -->
            <div class="hidden md:flex items-center gap-8 font-medium text-gray-700">
                <a href="#fitur" class="hover:text-emerald-600 transition">Fitur</a>
                <a href="#tentang" class="hover:text-emerald-600 transition">Tentang</a>
                <a href="#kontak" class="hover:text-emerald-600 transition">Kontak</a>
            </div>

            <!-- Auth Button -->
            <div class="flex gap-3">
                <a href="{{ route('login') }}"
                    class="border border-emerald-600 text-emerald-700 px-4 py-2 rounded-lg hover:bg-emerald-600 hover:text-white font-semibold transition">
                    Login
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 font-semibold transition">
                        Register
                    </a>
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="pt-32 pb-24 bg-gradient-to-br from-emerald-50 to-white">
        <div class="max-w-7xl mx-auto grid md:grid-cols-2 items-center px-6">
            <div data-aos="fade-right">
                <h1 class="text-4xl md:text-5xl font-extrabold text-emerald-800 leading-tight mb-5">
                    Solusi Digital untuk Pengelolaan Inventaris Perusahaan
                </h1>
                <p class="text-gray-600 mb-6 text-lg">
                    Sistem Informasi Inventaris <span class="text-emerald-700 font-semibold">Artha Jaya Mandiri</span>
                    membantu perusahaan mencatat,
                    mengontrol, dan menganalisis aset serta transaksi secara efisien dan real-time.
                </p>
                <div class="flex gap-4">
                    <a href="{{ route('login') }}"
                        class="bg-emerald-600 text-white px-6 py-3 rounded-lg font-semibold shadow hover:bg-emerald-700 transition">
                        Mulai Sekarang
                    </a>
                    <a href="#fitur"
                        class="border border-emerald-600 text-emerald-700 px-6 py-3 rounded-lg font-semibold hover:bg-emerald-50 transition">
                        Lihat Fitur
                    </a>
                </div>
            </div>

            <div data-aos="fade-left" class="flex justify-center mt-10 md:mt-0">
                <img src="https://cdn.dribbble.com/users/2082701/screenshots/16769616/media/338e08ac9df33c7e7cb4d536efdf04a7.png"
                    alt="Dashboard Preview" class="rounded-2xl shadow-2xl w-full md:w-4/5">
            </div>
        </div>
    </section>

    <!-- Fitur -->
    <section id="fitur" class="py-20 bg-white">
        <div class="max-w-6xl mx-auto text-center px-6">
            <h2 class="text-3xl font-bold text-emerald-700 mb-12" data-aos="fade-up">Fitur Unggulan Sistem</h2>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-10">
                <div data-aos="zoom-in" class="p-8 bg-emerald-50 rounded-2xl shadow hover:shadow-lg transition">
                    <i class="fi fi-rr-box text-emerald-600 text-4xl mb-3"></i>
                    <h3 class="text-xl font-semibold mb-2">Manajemen Barang</h3>
                    <p class="text-gray-600 text-sm">Pantau dan kelola stok barang secara otomatis serta akurat.</p>
                </div>

                <div data-aos="zoom-in" data-aos-delay="100"
                    class="p-8 bg-emerald-50 rounded-2xl shadow hover:shadow-lg transition">
                    <i class="fi fi-rr-exchange text-emerald-600 text-4xl mb-3"></i>
                    <h3 class="text-xl font-semibold mb-2">Transaksi Terkontrol</h3>
                    <p class="text-gray-600 text-sm">Catat semua transaksi pemasukan dan pengeluaran dengan validasi
                        stok otomatis.</p>
                </div>
                <div data-aos="zoom-in" data-aos-delay="300"
                    class="p-8 bg-emerald-50 rounded-2xl shadow hover:shadow-lg transition">
                    <i class="fi fi-rr-users-alt text-emerald-600 text-4xl mb-3"></i>
                    <h3 class="text-xl font-semibold mb-2">Manajemen Pengguna</h3>
                    <p class="text-gray-600 text-sm">Atur hak akses admin, staf, dan pengguna agar sistem tetap aman.
                    </p>
                </div>

                <div data-aos="zoom-in" data-aos-delay="400"
                    class="p-8 bg-emerald-50 rounded-2xl shadow hover:shadow-lg transition">
                    <i class="fi fi-rr-database text-emerald-600 text-4xl mb-3"></i>
                    <h3 class="text-xl font-semibold mb-2">Integrasi Database</h3>
                    <p class="text-gray-600 text-sm">Menggunakan PostgreSQL untuk performa tinggi dan keamanan data
                        maksimal.</p>
                </div>

                <div data-aos="zoom-in" data-aos-delay="500"
                    class="p-8 bg-emerald-50 rounded-2xl shadow hover:shadow-lg transition">
                    <i class="fi fi-rr-lock text-emerald-600 text-4xl mb-3"></i>
                    <h3 class="text-xl font-semibold mb-2">Keamanan Sistem</h3>
                    <p class="text-gray-600 text-sm">Sistem dilengkapi autentikasi dan proteksi terhadap akses tidak
                        sah.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Tentang -->
    <section id="tentang" class="bg-emerald-50 py-20">
        <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-2 gap-10 items-center">
            <div data-aos="fade-right">
                <img src="https://cdn.dribbble.com/users/5484/screenshots/14246635/media/baaf3ed22da8307a5b9a7b5e13a42105.png"
                    alt="Team Illustration" class="rounded-2xl shadow-lg w-full">
            </div>
            <div data-aos="fade-left">
                <h2 class="text-3xl font-bold text-emerald-700 mb-4">Tentang SIAJM</h2>
                <p class="text-gray-600 leading-relaxed mb-4">
                    <strong>Sistem Inventaris Artha Jaya Mandiri (SIAJM)</strong> merupakan platform berbasis web yang
                    dikembangkan untuk mendukung pengelolaan inventaris perusahaan secara digital dan terintegrasi.
                </p>
                <p class="text-gray-600 leading-relaxed">
                    Dibangun menggunakan framework <strong>Laravel 11</strong> dan basis data
                    <strong>PostgreSQL</strong>,
                    sistem ini dirancang dengan standar keamanan tinggi, antarmuka modern, serta fitur yang fleksibel
                    untuk mendukung proses bisnis perusahaan.
                </p>
            </div>
        </div>
    </section>

    <!-- Kontak -->
    <section id="kontak" class="py-20 bg-white text-center">
        <div class="max-w-4xl mx-auto px-6" data-aos="fade-up">
            <h2 class="text-3xl font-bold text-emerald-700 mb-6">Hubungi Kami</h2>
            <p class="text-gray-600 mb-6">Untuk informasi lebih lanjut atau permintaan demo sistem, silakan hubungi
                kami.</p>
            <a href="mailto:info@arthajayamandiri.co.id"
                class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3 rounded-lg font-semibold shadow-lg transition transform hover:-translate-y-1">
                Kirim Email
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-emerald-800 text-white py-6">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row justify-between items-center px-6 text-sm">
            <p>&copy; {{ date('Y') }} Artha Jaya Mandiri. All rights reserved.</p>
            <div class="flex gap-4 mt-2 sm:mt-0">
                <a href="#" class="hover:text-emerald-300"><i class="fi fi-brands-facebook"></i></a>
                <a href="#" class="hover:text-emerald-300"><i class="fi fi-brands-instagram"></i></a>
                <a href="#" class="hover:text-emerald-300"><i class="fi fi-brands-linkedin"></i></a>
            </div>
        </div>
    </footer>

    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 900,
            once: true,
            offset: 120
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</body>

</html>
