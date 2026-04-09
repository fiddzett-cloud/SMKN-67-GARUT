/* =====================
   HANDLE UPLOAD FOTO BEFORE/AFTER (ADMIN)
===================== */
$upload_message = '';
if (isset($_POST['upload_foto'])) {
    $sarana_upload = $_POST['sarana_upload'] ?? '';

    // mapping sarana ke nama file yang ada di folder img/
    $map = [
        'Kantin' => ['before' => 'kantinbefore.png', 'after' => 'kantinafter.png'],
        'Masjid' => ['before' => 'masjidbefore.png', 'after' => 'masjidafter.png'],
        'Laboratorium' => ['before' => 'labbefore.png', 'after' => 'labafter.png'],
        'Lapangan Sekolah' => ['before' => 'lapanganbefore.jpg', 'after' => 'lapanganafter.jpg'],
        'Ruang Kelas' => ['before' => 'kelasbefore.jpg', 'after' => 'kelasafter.jpg'],
        'Kamar Mandi dan Toilet' => ['before' => 'wcbefore.jpg', 'after' => 'wcafter.jpg'],
        'UKS' => ['before' => 'wcbefore.jpg', 'after' => 'wcafter.jpg']
    ];

    if (!isset($map[$sarana_upload])) {
        $upload_message = 'Sarana tidak dikenali.';
    } else {
        $targetBefore = __DIR__ . '/../img/' . $map[$sarana_upload]['before'];
        $targetAfter = __DIR__ . '/../img/' . $map[$sarana_upload]['after'];

        // proses file before
        if (!empty($_FILES['foto_before']) && $_FILES['foto_before']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['foto_before']['tmp_name'];
            $info = getimagesize($tmp);
            if ($info === false) {
                $upload_message .= ' File before bukan gambar.';
            } else {
                if (!move_uploaded_file($tmp, $targetBefore)) {
                    $upload_message .= ' Gagal menyimpan foto before.';
                }
            }
        }

        // proses file after
        if (!empty($_FILES['foto_after']) && $_FILES['foto_after']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['foto_after']['tmp_name'];
            $info = getimagesize($tmp);
            if ($info === false) {
                $upload_message .= ' File after bukan gambar.';
            } else {
                if (!move_uploaded_file($tmp, $targetAfter)) {
                    $upload_message .= ' Gagal menyimpan foto after.';
                }
            }
        }

        if (empty($upload_message)) $upload_message = 'Upload berhasil.';
    }
}


 <!-- UPLOAD FOTO BEFORE/AFTER -->
        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">Ubah Foto Before / After untuk Siswa</h2>

            <?php if (!empty($upload_message)): ?>
                <div class="mb-4 p-3 rounded bg-gray-50 text-sm text-gray-700">
                    <?= htmlspecialchars($upload_message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold mb-2">Sarana</label>
                    <select name="sarana_upload" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-sky-400">
                        <option value="">-- Pilih Sarana --</option>
                        <option value="Kantin">Kantin Sekolah</option>
                        <option value="Masjid">Masjid Sekolah</option>
                        <option value="Laboratorium">Laboratorium</option>
                        <option value="Lapangan Sekolah">Lapangan Olahraga</option>
                        <option value="Ruang Kelas">Ruang Kelas</option>
                        <option value="Kamar Mandi dan Toilet">Kamar Mandi dan Toilet</option>
                        <option value="UKS">UKS</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-2">Foto Before (sebelum)</label>
                        <input type="file" name="foto_before" accept="image/*" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Foto After (sesudah)</label>
                        <input type="file" name="foto_after" accept="image/*" class="w-full" />
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" name="upload_foto"
                        class="px-6 py-2 rounded-lg bg-green-500 hover:bg-green-600 text-white font-semibold">
                        Upload Foto
                    </button>
                </div>
            </form>
        </div>
