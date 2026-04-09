-- Menambahkan kolom gambar ke tabel data_aspirasi
ALTER TABLE `data_aspirasi` ADD COLUMN `gambar` VARCHAR(255) DEFAULT NULL AFTER `deskripsi`;
