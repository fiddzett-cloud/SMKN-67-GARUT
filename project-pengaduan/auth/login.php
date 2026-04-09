<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SuaraSiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-b from-[#F8FAFC] to-[#E0F2FE] font-sans">

    <div class="min-h-screen flex">

        <!-- LEFT (FORM) -->
        <div class="w-full md:w-1/2 flex items-center justify-center px-6">

            <form action="proseslogin.php" method="POST"
                class="w-full max-w-md bg-white rounded-3xl shadow-xl p-8 ring-1 ring-slate-200">

                <!-- LOGO -->
                <div class="flex flex-col items-center mb-6">
                    <img src="../img/logostm.png" class="w-16 h-16 object-contain mb-2">
                    <h2 class="text-2xl font-extrabold text-orange-500">STM Al Madani</h2>
                    <p class="text-sm text-slate-500">Login ke Sistem Pengaduan</p>
                </div>

                <!-- USERNAME -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Username
                    </label>
                    <input type="text" name="username" required
                        class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-orange-400"
                        placeholder="Masukkan username">
                </div>

                <!-- PASSWORD -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Password
                    </label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-orange-400"
                        placeholder="Masukkan password">
                </div>

                <!-- BUTTON -->
                <button type="submit"
                    class="w-full bg-orange-500 text-white py-2 rounded-xl font-semibold shadow hover:bg-orange-600 transition">
                    Login
                </button>

                <!-- REGISTER -->
                <p class="text-center text-sm text-slate-500 mt-6">
                    Belum punya akun?
                    <a href="register.php" class="text-orange-500 font-semibold hover:underline">
                        Daftar
                    </a>
                </p>

            </form>
        </div>

        <!-- RIGHT (INFO + LOGO BESAR) -->
        <div class="hidden md:flex w-1/2 items-center justify-center bg-gradient-to-b from-[#F8FAFC] to-[#E0F2FE] relative">

            <div class="text-center px-10 text-white">

                <img src="../img/logostm.png"
                    class="w-40 h-40 object-contain mx-auto mb-6 drop-shadow-2xl">

                <h1 class="text-4xl font-extrabold mb-4 text-slate-900">
                    Selamat Datang 
                </h1>

                <p class="text-slate-900 text-lg leading-relaxed">
                    Masuk ke sistem pengaduan sarana sekolah <br>
                    dan bantu ciptakan lingkungan belajar yang lebih baik.
                </p>

            </div>

        </div>

    </div>

</body>

</html>