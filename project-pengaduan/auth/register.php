<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - SuaraSiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-b from-[#F8FAFC] to-[#E0F2FE] font-sans">

<div class="min-h-screen flex">

    <!-- Left: Register Form -->
    <div class="w-full md:w-1/2 flex items-center justify-center">
        <div class="w-full max-w-md px-6">
            <form action="prosesregister.php" method="POST"
                class="bg-white shadow-lg rounded-xl px-8 pt-8 pb-8">

                <!-- Logo / Title -->
                <h2 class="text-4xl font-extrabold mb-6 text-center text-[#38BDF8]">
                    SuaraSiswa
                </h2>

                <p class="text-center text-sm text-slate-500 mb-6">
                    Daftar akun siswa untuk mengirim aspirasi
                </p>

                <!-- Nama -->
                <div class="mb-4">
                    <label class="block text-[#1F2937] text-sm font-semibold mb-2">
                        Nama Lengkap
                    </label>
                    <input
                        class="shadow-sm border rounded-lg w-full py-2 px-3 text-[#1F2937]
                               focus:outline-none focus:ring-2 focus:ring-[#38BDF8]"
                        name="nama"
                        type="text"
                        placeholder="Masukkan nama lengkap"
                        required>
                </div>

                <!-- Username -->
                <div class="mb-4">
                    <label class="block text-[#1F2937] text-sm font-semibold mb-2">
                        Username
                    </label>
                    <input
                        class="shadow-sm border rounded-lg w-full py-2 px-3 text-[#1F2937]
                               focus:outline-none focus:ring-2 focus:ring-[#38BDF8]"
                        name="username"
                        type="text"
                        placeholder="Masukkan username"
                        required>
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label class="block text-[#1F2937] text-sm font-semibold mb-2">
                        Password
                    </label>
                    <input
                        class="shadow-sm border rounded-lg w-full py-2 px-3 text-[#1F2937]
                               focus:outline-none focus:ring-2 focus:ring-[#38BDF8]"
                        name="password"
                        type="password"
                        placeholder="Masukkan password"
                        required>
                </div>

                <!-- Konfirmasi Password -->
                <div class="mb-6">
                    <label class="block text-[#1F2937] text-sm font-semibold mb-2">
                        Konfirmasi Password
                    </label>
                    <input
                        class="shadow-sm border rounded-lg w-full py-2 px-3 text-[#1F2937]
                               focus:outline-none focus:ring-2 focus:ring-[#38BDF8]"
                        name="password2"
                        type="password"
                        placeholder="Ulangi password"
                        required>
                </div>

                <!-- Button -->
                <div class="flex flex-col items-center gap-3">
                    <button
                        class="bg-[#38BDF8] hover:bg-[#0EA5E9] text-white font-semibold
                               py-2 px-8 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BAE6FD]"
                        type="submit">
                        Daftar
                    </button>

                    <p class="text-sm text-slate-500">
                        Sudah punya akun?
                        <a href="login.php" class="text-[#38BDF8] font-semibold hover:underline">
                            Login
                        </a>
                    </p>
                </div>

            </form>
        </div>
    </div>

    <!-- Right: Welcome Section -->
    <div class="hidden md:flex w-1/2 bg-[#38BDF8] items-center justify-center">
        <div class="text-center px-6">
            <h1 class="text-white text-5xl font-extrabold mb-4">
                SuaraSiswa
            </h1>
            <p class="text-[#E0F2FE] text-lg">
                Buat akun untuk menyampaikan aspirasi dan pengaduan<br>
                sarana sekolah dengan aman dan nyaman
            </p>
        </div>
    </div>

</div>

</body>
</html>
