<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sarana Sekolah - SuaraSiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-slate-50 to-orange-50 text-gray-800 pb-24">

<!-- MINIMALIST HEADER -->
<header class="bg-gradient-to-r from-white via-orange-50 to-white shadow-lg border-b border-orange-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="../img/logostm.png" class="h-10">
            <div class="hidden sm:block">
                <span class="text-lg font-bold bg-gradient-to-r from-orange-600 to-orange-500 bg-clip-text text-transparent">STM</span>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">AL MADANI GARUT</p>
            </div>
        </div>
        <a href="../auth/logout.php" class="flex items-center gap-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-4 py-2 rounded-lg font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
            <i class="bi bi-box-arrow-right"></i>
            <span class="hidden md:inline">Logout</span>
        </a>
    </div>
</header>

<div class="flex min-h-screen">
    <main class="flex-1 p-8 w-full">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Sarana Sekolah</h1>
                <p class="text-sm text-gray-500 mt-1">Fasilitas pendukung belajar mengajar di lingkungan sekolah.</p>
            </div>
        </div>

        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col group">
                <div class="relative overflow-hidden">
                    <img src="../img/kantinafter.png?v=<?= time(); ?>" alt="Kantin" class="h-52 w-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-4 left-4">
                        <span class="bg-orange-500 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">Fasilitas Umum</span>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <h2 class="text-xl font-bold text-gray-800 mb-3">Kantin Sekolah</h2>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6">Pilihan makanan sehat untuk mendukung gizi siswa.</p>
                    <a href="#" class="open-progress mt-auto inline-flex items-center justify-center gap-2 text-sm font-bold py-3 px-4 rounded-xl bg-slate-50 text-orange-600 hover:bg-orange-500 hover:text-white transition-all border border-orange-100"
                        data-title="Kantin Sekolah"
                        data-desc="Perbandingan kondisi kantin sebelum dan sesudah renovasi."
                        data-before="../img/kantinbefore.png?v=<?= time(); ?>"
                        data-after="../img/kantinafter.png?v=<?= time(); ?>">
                        Lihat Kondisi
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col group">
                <div class="relative overflow-hidden">
                    <img src="../img/masjidafter.png?v=<?= time(); ?>" alt="Masjid" class="h-52 w-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-4 left-4">
                        <span class="bg-orange-500 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">Tempat Ibadah</span>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <h2 class="text-xl font-bold text-gray-800 mb-3">Masjid Sekolah</h2>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6">Tempat ibadah yang nyaman bagi siswa dan staf.</p>
                    <a href="#" class="open-progress mt-auto inline-flex items-center justify-center gap-2 text-sm font-bold py-3 px-4 rounded-xl bg-slate-50 text-orange-600 hover:bg-orange-500 hover:text-white transition-all border border-orange-100"
                        data-title="Masjid Sekolah"
                        data-desc="Peningkatan fasilitas masjid untuk kenyamanan beribadah."
                        data-before="../img/masjidbefore.png?v=<?= time(); ?>"
                        data-after="../img/masjidafter.png?v=<?= time(); ?>">
                        Lihat Kondisi
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col group">
                <div class="relative overflow-hidden">
                    <img src="../img/labafter.png?v=<?= time(); ?>" alt="Lab" class="h-52 w-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-4 left-4">
                        <span class="bg-orange-500 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">Praktek Teknik</span>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <h2 class="text-xl font-bold text-gray-800 mb-3">Laboratorium</h2>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6">Dilengkapi peralatan modern untuk praktek siswa.</p>
                    <a href="#" class="open-progress mt-auto inline-flex items-center justify-center gap-2 text-sm font-bold py-3 px-4 rounded-xl bg-slate-50 text-orange-600 hover:bg-orange-500 hover:text-white transition-all border border-orange-100"
                        data-title="Laboratorium"
                        data-desc="Update alat praktek dan penataan ruang lab."
                        data-before="../img/labbefore.png?v=<?= time(); ?>"
                        data-after="../img/labafter.png?v=<?= time(); ?>">
                        Lihat Kondisi
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col group">
                <div class="relative overflow-hidden">
                    <img src="../img/lapanganafter.jpg?v=<?= time(); ?>" alt="Lapangan" class="h-52 w-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-4 left-4">
                        <span class="bg-orange-500 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">Fasilitas Olahraga</span>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <h2 class="text-xl font-bold text-gray-800 mb-3">Lapangan Sekolah</h2>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6">Area multi-fungsi untuk olahraga dan upacara.</p>
                    <a href="#" class="open-progress mt-auto inline-flex items-center justify-center gap-2 text-sm font-bold py-3 px-4 rounded-xl bg-slate-50 text-orange-600 hover:bg-orange-500 hover:text-white transition-all border border-orange-100"
                        data-title="Lapangan Sekolah"
                        data-desc="Perbaikan drainase dan pengecatan ulang lapangan semi-indoor."
                        data-before="../img/lapanganbefore.jpg?v=<?= time(); ?>"
                        data-after="../img/lapanganafter.jpg?v=<?= time(); ?>">
                        Lihat Kondisi
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col group">
                <div class="relative overflow-hidden">
                    <img src="../img/kelasafter.jpg?v=<?= time(); ?>" alt="Kelas" class="h-52 w-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-4 left-4">
                        <span class="bg-orange-500 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">Ruang Belajar</span>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <h2 class="text-xl font-bold text-gray-800 mb-3">Ruang Kelas</h2>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6">Ruang kelas teori yang bersih dan kondusif.</p>
                    <a href="#" class="open-progress mt-auto inline-flex items-center justify-center gap-2 text-sm font-bold py-3 px-4 rounded-xl bg-slate-50 text-orange-600 hover:bg-orange-500 hover:text-white transition-all border border-orange-100"
                        data-title="Ruang Kelas Teori"
                        data-desc="Penggantian mebel baru dan pemasangan proyektor pembelajaran."
                        data-before="../img/kelasbefore.jpg?v=<?= time(); ?>"
                        data-after="../img/kelasafter.jpg?v=<?= time(); ?>">
                        Lihat Kondisi
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col group">
                <div class="relative overflow-hidden">
                    <img src="../img/wcafter.jpg?v=<?= time(); ?>" alt="WC" class="h-52 w-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-4 left-4">
                        <span class="bg-orange-500 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">Sanitasi</span>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <h2 class="text-xl font-bold text-gray-800 mb-3">Kamar Mandi & WC</h2>
                    <p class="text-gray-500 text-sm leading-relaxed mb-6">Fasilitas sanitasi bersih untuk kenyamanan siswa.</p>
                    <a href="#" class="open-progress mt-auto inline-flex items-center justify-center gap-2 text-sm font-bold py-3 px-4 rounded-xl bg-slate-50 text-orange-600 hover:bg-orange-500 hover:text-white transition-all border border-orange-100"
                        data-title="Kamar Mandi & WC Siswa"
                        data-desc="Renovasi total kloset, wastafel, dan sistem pengairan."
                        data-before="../img/wcbefore.jpg?v=<?= time(); ?>"
                        data-after="../img/wcafter.jpg?v=<?= time(); ?>">
                        Lihat Kondisi
                    </a>
                </div>
            </div>

        </div>

        <div class="mt-12 py-6 border-t text-center md:text-left">
            <a href="dashboard.php" class="text-gray-500 hover:text-orange-500 font-semibold flex items-center justify-center md:justify-start gap-2 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Dashboard
            </a>
        </div>
    </main>
</div>

<div id="progress-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-md p-4">
    <div class="bg-white rounded-3xl max-w-4xl w-full overflow-hidden shadow-2xl transition-all scale-95 opacity-0 duration-300" id="modal-container">
        <div class="flex items-center justify-between p-6 border-b">
            <div>
                <h3 id="modal-title" class="text-xl font-bold text-gray-800 uppercase">Kondisi Fasilitas</h3>
                <p id="modal-desc" class="text-sm text-gray-500 mt-1"></p>
            </div>
            <button id="modal-close" class="p-2 hover:bg-red-50 text-gray-400 hover:text-red-500 rounded-full transition">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="p-6 bg-slate-50">
            <div class="relative w-full aspect-video md:aspect-[21/9] bg-gray-200 rounded-2xl overflow-hidden shadow-inner border-4 border-white">
                <img id="before-img" src="" alt="Before" class="absolute inset-0 h-full w-full object-cover">
                
                <div id="after-wrap" class="absolute inset-0 overflow-hidden border-r-4 border-orange-500 shadow-[10px_0_15px_rgba(0,0,0,0.3)]" style="width:50%;">
                    <img id="after-img" src="" alt="After" class="h-full w-full object-cover">
                    <span class="absolute top-4 left-4 bg-orange-600 text-white text-[10px] px-3 py-1 rounded-full font-bold uppercase tracking-wider">Kondisi Sekarang</span>
                </div>
                
                <span class="absolute top-4 right-4 bg-gray-800/80 text-white text-[10px] px-3 py-1 rounded-full font-bold uppercase tracking-wider">Kondisi Lama</span>
                
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none px-4">
                    <input id="ba-range" type="range" min="0" max="100" value="50" class="w-full accent-orange-500 h-2 bg-transparent appearance-none cursor-pointer pointer-events-auto">
                </div>
            </div>
            <p class="text-center text-[11px] text-gray-400 mt-6 font-bold uppercase tracking-[0.2em]">Geser slider untuk membandingkan</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('progress-modal');
    const modalContainer = document.getElementById('modal-container');
    const modalTitle = document.getElementById('modal-title');
    const modalDesc = document.getElementById('modal-desc');
    const beforeImg = document.getElementById('before-img');
    const afterImg = document.getElementById('after-img');
    const afterWrap = document.getElementById('after-wrap');
    const range = document.getElementById('ba-range');
    const closeBtn = document.getElementById('modal-close');

    function adjustAfterSize() {
        const fullWidth = beforeImg.clientWidth;
        afterImg.style.width = fullWidth + 'px';
    }

    function openModal(data) {
        modalTitle.textContent = data.title;
        modalDesc.textContent = data.desc;
        beforeImg.src = data.before;
        afterImg.src = data.after;
        range.value = 50;
        afterWrap.style.width = '50%';
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        setTimeout(() => {
            modalContainer.classList.remove('scale-95', 'opacity-0');
            modalContainer.classList.add('scale-100', 'opacity-100');
            adjustAfterSize();
        }, 50);
    }

    function closeModal() {
        modalContainer.classList.remove('scale-100', 'opacity-100');
        modalContainer.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }, 300);
    }

    document.querySelectorAll('.open-progress').forEach(el => {
        el.addEventListener('click', e => {
            e.preventDefault();
            openModal({
                title: el.dataset.title,
                desc: el.dataset.desc,
                before: el.dataset.before,
                after: el.dataset.after
            });
        });
    });

    range.addEventListener('input', function () {
        afterWrap.style.width = this.value + '%';
    });

    window.addEventListener('resize', adjustAfterSize);
    closeBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
});
</script>

</main>
</div>

<!-- BOTTOM NAVIGATION -->
<nav class="fixed bottom-0 left-0 right-0 bg-white shadow-2xl border-t border-gray-200 z-40">
    <div class="max-w-7xl mx-auto flex justify-around items-center h-20 px-4">
        <a href="dashboard.php" class="flex flex-col items-center justify-center py-2 px-3 rounded-lg text-gray-600 hover:text-orange-600 font-semibold text-sm transition-all duration-200">
            <i class="bi bi-house-door-fill text-xl mb-1"></i>
            <span>Dashboard</span>
        </a>
        <a href="datasiswa.php" class="flex flex-col items-center justify-center py-2 px-3 rounded-lg text-gray-600 hover:text-orange-600 font-semibold text-sm transition-all duration-200">
            <i class="bi bi-person-badge text-xl mb-1"></i>
            <span>Profil</span>
        </a>
        <a href="sarana.php" class="flex flex-col items-center justify-center py-2 px-3 rounded-lg bg-orange-100 text-orange-600 font-semibold text-sm transition-all duration-200">
            <i class="bi bi-building text-xl mb-1"></i>
            <span>Sarana</span>
        </a>
        <a href="chatsiswa.php" class="flex flex-col items-center justify-center py-2 px-3 rounded-lg text-gray-600 hover:text-orange-600 font-semibold text-sm transition-all duration-200">
            <i class="bi bi-chat-dots-fill text-xl mb-1"></i>
            <span>Chat</span>
        </a>
        <a href="histori-aspirasi.php" class="flex flex-col items-center justify-center py-2 px-3 rounded-lg text-gray-600 hover:text-orange-600 font-semibold text-sm transition-all duration-200">
            <i class="bi bi-clock-history text-xl mb-1"></i>
            <span>Histori</span>
        </a>
    </div>
</nav>

</body>
</html>