<?php

namespace App\Support;

use InvalidArgumentException;

final class ImportTemplateDefinitions
{
    public static function get(string $type): array
    {
        return match ($type) {
            'mata-pelajaran' => [
                'filename' => 'template_mata_pelajaran.xlsx',
                'headers' => ['kode', 'nama', 'deskripsi', 'sks', 'kategori', 'status'],
                'samples' => [
                    ['mtk001', 'matematika', 'Pelajaran matematika dasar', 3, 'keahlian', 'aktif'],
                ],
                'guide' => [
                    ['kode', 'Ya', 'Teks unik', 'mtk001', 'Disimpan huruf kecil. Perbedaan huruf besar/kecil dianggap data yang sama.'],
                    ['nama', 'Ya', 'Teks unik', 'matematika', 'Disimpan huruf kecil. Perbedaan huruf besar/kecil dianggap data yang sama.'],
                    ['deskripsi', 'Tidak', 'Teks', 'Pelajaran matematika dasar', 'Deskripsi bebas.'],
                    ['sks', 'Tidak', 'Angka 0-10', '3', 'Jumlah SKS.'],
                    ['kategori', 'Tidak', 'Teks', 'keahlian', 'Disimpan huruf kecil.'],
                    ['status', 'Tidak', 'aktif/nonaktif', 'aktif', 'Default aktif.'],
                ],
            ],
            'siswa' => [
                'filename' => 'template_siswa.xlsx',
                'headers' => ['nis', 'nisn', 'nama', 'email', 'no_telp', 'kelas', 'alamat', 'jenis_kelamin', 'tanggal_lahir', 'nama_orang_tua', 'no_telp_orang_tua', 'status'],
                'samples' => [
                    ['2026001', '0012345678', 'Ahmad Fauzi', 'ahmad@example.com', '081234567890', 'x rpl 1', 'Jl. Merdeka No. 10', 'L', '2008-01-01', 'Budi Santoso', '081345678901', 'aktif'],
                ],
                'guide' => [
                    ['nis', 'Ya', 'Teks unik', '2026001', 'Gunakan NIS, bukan ID database.'],
                    ['nisn', 'Ya', 'Teks unik', '0012345678', 'Gunakan format teks agar nol di depan tidak hilang.'],
                    ['nama', 'Ya', 'Teks', 'Ahmad Fauzi', 'Nama siswa tidak dipaksa menjadi huruf kecil.'],
                    ['email', 'Tidak', 'Email', 'ahmad@example.com', 'Tidak digunakan sebagai relasi ID.'],
                    ['no_telp', 'Tidak', 'Teks', '081234567890', 'Gunakan format teks.'],
                    ['kelas', 'Ya', 'Nama kelas', 'x rpl 1', 'Isi nama kelas yang sudah terdaftar. Tidak perlu kelas_id. Pencarian tidak membedakan huruf besar/kecil.'],
                    ['alamat', 'Tidak', 'Teks', 'Jl. Merdeka No. 10', 'Alamat siswa.'],
                    ['jenis_kelamin', 'Ya', 'L/P', 'L', 'L untuk laki-laki, P untuk perempuan.'],
                    ['tanggal_lahir', 'Tidak', 'YYYY-MM-DD', '2008-01-01', 'Format tanggal.'],
                    ['nama_orang_tua', 'Tidak', 'Teks', 'Budi Santoso', 'Nama orang tua/wali.'],
                    ['no_telp_orang_tua', 'Tidak', 'Teks', '081345678901', 'Nomor telepon orang tua/wali.'],
                    ['status', 'Tidak', 'aktif/nonaktif/lulus/pindah', 'aktif', 'Default aktif.'],
                ],
            ],
            'guru' => [
                'filename' => 'template_guru.xlsx',
                'headers' => ['nip', 'nama', 'email', 'no_telp', 'alamat', 'jenis_kelamin', 'tanggal_lahir', 'status'],
                'samples' => [
                    ['19850101001', 'Ahmad Basuki', 'ahmad.basuki@example.com', '081234567890', 'Jl. Pendidikan No. 1', 'L', '1985-01-01', 'aktif'],
                ],
                'guide' => [
                    ['nip', 'Ya', 'Teks unik', '19850101001', 'Identitas guru. Tidak menggunakan ID database.'],
                    ['nama', 'Ya', 'Teks', 'Ahmad Basuki', 'Nama guru dipertahankan sesuai penulisan.'],
                    ['email', 'Ya', 'Email unik', 'ahmad.basuki@example.com', 'Email guru.'],
                    ['no_telp', 'Tidak', 'Teks', '081234567890', 'Gunakan format teks.'],
                    ['alamat', 'Tidak', 'Teks', 'Jl. Pendidikan No. 1', 'Alamat guru.'],
                    ['jenis_kelamin', 'Ya', 'L/P', 'L', 'L untuk laki-laki, P untuk perempuan.'],
                    ['tanggal_lahir', 'Tidak', 'YYYY-MM-DD', '1985-01-01', 'Format tanggal.'],
                    ['status', 'Tidak', 'aktif/nonaktif', 'aktif', 'Default aktif.'],
                ],
            ],
            'kelas' => [
                'filename' => 'template_kelas.xlsx',
                'headers' => ['nama', 'tingkat', 'jurusan', 'wali_kelas', 'kapasitas', 'jumlah_siswa', 'ruangan', 'status'],
                'samples' => [
                    ['x rpl 1', 10, 'rpl', 'Ahmad Basuki', 36, 0, 'ruang 101', 'aktif'],
                ],
                'guide' => [
                    ['nama', 'Ya', 'Teks unik', 'x rpl 1', 'Disimpan huruf kecil. X RPL 1 dan x rpl 1 dianggap sama.'],
                    ['tingkat', 'Ya', 'Angka 1-13', '10', 'Tingkat kelas.'],
                    ['jurusan', 'Ya', 'Teks', 'rpl', 'Disimpan huruf kecil agar jurusan tidak ganda karena kapitalisasi.'],
                    ['wali_kelas', 'Tidak', 'Nama guru', 'Ahmad Basuki', 'Isi nama guru yang sudah terdaftar. Tidak perlu wali_kelas_id.'],
                    ['kapasitas', 'Ya', 'Angka', '36', 'Kapasitas kelas.'],
                    ['jumlah_siswa', 'Ya', 'Angka', '0', 'Dapat diisi 0. Setelah normalisasi data lama jumlah akan diselaraskan dari data siswa.'],
                    ['ruangan', 'Tidak', 'Teks', 'ruang 101', 'Disimpan huruf kecil.'],
                    ['status', 'Tidak', 'aktif/nonaktif', 'aktif', 'Default aktif.'],
                ],
            ],
            'jadwal' => [
                'filename' => 'template_jadwal.xlsx',
                'headers' => ['kelas', 'mata_pelajaran', 'guru', 'hari', 'jam_ke', 'jam_mulai', 'jam_selesai', 'tahun_ajaran', 'ruangan', 'status', 'keterangan'],
                'samples' => [
                    ['x rpl 1', 'matematika', 'Ahmad Basuki', 'Senin', 1, '07:00', '07:45', '2026/2027', 'ruang 101', 'aktif', ''],
                ],
                'guide' => [
                    ['kelas', 'Ya', 'Nama kelas', 'x rpl 1', 'Isi nama kelas, bukan kelas_id. Pencarian tidak membedakan kapitalisasi.'],
                    ['mata_pelajaran', 'Ya', 'Nama mata pelajaran', 'matematika', 'Isi nama mata pelajaran, bukan mata_pelajaran_id.'],
                    ['guru', 'Ya', 'Nama guru', 'Ahmad Basuki', 'Isi nama guru, bukan guru_id. Jika ada nama guru yang sama persis, rapikan data master terlebih dahulu.'],
                    ['hari', 'Ya', 'Senin-Sabtu', 'Senin', 'Nama hari tetap mengikuti format aplikasi.'],
                    ['jam_ke', 'Ya', 'Angka 1-15', '1', 'Nomor jam pelajaran.'],
                    ['jam_mulai', 'Ya', 'HH:MM', '07:00', 'Jam mulai.'],
                    ['jam_selesai', 'Ya', 'HH:MM', '07:45', 'Jam selesai.'],
                    ['tahun_ajaran', 'Tidak', 'Teks', '2026/2027', 'Tahun ajaran.'],
                    ['ruangan', 'Tidak', 'Teks', 'ruang 101', 'Disimpan huruf kecil.'],
                    ['status', 'Tidak', 'aktif/nonaktif/libur/dibatalkan', 'aktif', 'Disimpan huruf kecil.'],
                    ['keterangan', 'Tidak', 'Teks', '', 'Catatan jadwal.'],
                ],
            ],
            'user' => [
                'filename' => 'template_user.xlsx',
                'headers' => ['name', 'email', 'password', 'role', 'guru', 'kelas'],
                'samples' => [
                    ['Admin Utama', 'admin@example.com', 'password123', 'admin', '', ''],
                    ['Guru User', 'guru@example.com', 'password123', 'guru', 'Ahmad Basuki', ''],
                    ['Siswa User', 'siswa@example.com', 'password123', 'siswa', '', 'x rpl 1'],
                ],
                'guide' => [
                    ['name', 'Ya', 'Teks', 'Admin Utama', 'Nama user tidak diubah menjadi huruf kecil.'],
                    ['email', 'Ya', 'Email unik', 'admin@example.com', 'Digunakan untuk login.'],
                    ['password', 'Untuk user baru', 'Minimal 8 karakter', 'password123', 'Tidak pernah dikonversi ke huruf kecil. Untuk update, kosongkan agar password lama tetap.'],
                    ['role', 'Ya', 'admin/kepsek/kurikulum/guru/siswa', 'guru', 'Role disimpan dalam format sistem.'],
                    ['guru', 'Jika role guru', 'Nama guru', 'Ahmad Basuki', 'Isi nama guru, bukan guru_id.'],
                    ['kelas', 'Jika role siswa', 'Nama kelas', 'x rpl 1', 'Isi nama kelas, bukan kelas_id.'],
                ],
            ],
            'izin-guru' => [
                'filename' => 'template_izin_guru.xlsx',
                'headers' => ['guru', 'jenis_izin', 'tanggal_mulai', 'tanggal_selesai', 'keterangan', 'file_surat', 'status_approval', 'disetujui_oleh', 'tanggal_approval', 'catatan_approval'],
                'samples' => [
                    ['Ahmad Basuki', 'sakit', '2026-08-20', '2026-08-20', 'Demam', '', 'pending', '', '', ''],
                ],
                'guide' => [
                    ['guru', 'Ya', 'Nama guru', 'Ahmad Basuki', 'Isi nama guru, bukan guru_id.'],
                    ['jenis_izin', 'Ya', 'sakit/izin/cuti/dinas_luar/lainnya', 'sakit', 'Disimpan huruf kecil.'],
                    ['tanggal_mulai', 'Ya', 'YYYY-MM-DD', '2026-08-20', 'Tanggal mulai izin.'],
                    ['tanggal_selesai', 'Ya', 'YYYY-MM-DD', '2026-08-20', 'Tidak boleh sebelum tanggal mulai.'],
                    ['keterangan', 'Tidak', 'Teks', 'Demam', 'Keterangan izin.'],
                    ['file_surat', 'Tidak', 'Teks/path', '', 'Nama/path dokumen jika digunakan.'],
                    ['status_approval', 'Tidak', 'pending/disetujui/ditolak', 'pending', 'Default pending.'],
                    ['disetujui_oleh', 'Tidak', 'Nama user', 'Admin Utama', 'Gunakan nama user, bukan ID.'],
                    ['tanggal_approval', 'Tidak', 'YYYY-MM-DD HH:MM:SS', '', 'Isi jika sudah diproses.'],
                    ['catatan_approval', 'Tidak', 'Teks', '', 'Catatan approval.'],
                ],
            ],
            'guru-pengganti' => [
                'filename' => 'template_guru_pengganti.xlsx',
                'headers' => ['kelas', 'mata_pelajaran', 'hari', 'jam_ke', 'tanggal', 'guru_asli', 'guru_pengganti', 'status_penggantian', 'keterangan', 'catatan_approval', 'disetujui_oleh'],
                'samples' => [
                    ['x rpl 1', 'matematika', 'Senin', 1, '2026-08-20', 'Ahmad Basuki', 'Siti Aminah', 'dijadwalkan', '', '', 'Admin Utama'],
                ],
                'guide' => [
                    ['kelas', 'Ya', 'Nama kelas', 'x rpl 1', 'Digunakan bersama mata pelajaran, hari, dan jam_ke untuk mencari jadwal.'],
                    ['mata_pelajaran', 'Ya', 'Nama mata pelajaran', 'matematika', 'Tidak menggunakan mata_pelajaran_id.'],
                    ['hari', 'Ya', 'Senin-Sabtu', 'Senin', 'Hari jadwal.'],
                    ['jam_ke', 'Ya', 'Angka', '1', 'Jam ke jadwal.'],
                    ['tanggal', 'Ya', 'YYYY-MM-DD', '2026-08-20', 'Tanggal penggantian.'],
                    ['guru_asli', 'Ya', 'Nama guru', 'Ahmad Basuki', 'Tidak menggunakan guru_asli_id.'],
                    ['guru_pengganti', 'Ya', 'Nama guru', 'Siti Aminah', 'Tidak menggunakan guru_pengganti_id.'],
                    ['status_penggantian', 'Tidak', 'dijadwalkan/selesai/tidak_hadir', 'dijadwalkan', 'Disimpan huruf kecil.'],
                    ['keterangan', 'Tidak', 'Teks', '', 'Catatan.'],
                    ['catatan_approval', 'Tidak', 'Teks', '', 'Catatan approval.'],
                    ['disetujui_oleh', 'Tidak', 'Nama user', 'Admin Utama', 'Gunakan nama user, bukan ID.'],
                ],
            ],
            'kehadiran' => [
                'filename' => 'template_kehadiran.xlsx',
                'headers' => ['siswa', 'kelas', 'mata_pelajaran', 'guru', 'hari', 'jam_ke', 'tanggal', 'status', 'keterangan', 'diinput_oleh'],
                'samples' => [
                    ['Ahmad Fauzi', 'x rpl 1', 'matematika', 'Ahmad Basuki', 'Senin', 1, '2026-08-20', 'hadir', '', 'Admin Utama'],
                ],
                'guide' => [
                    ['siswa', 'Ya', 'Nama siswa', 'Ahmad Fauzi', 'Gunakan nama siswa, bukan siswa_id. Jika nama siswa ganda, gunakan import dari menu siswa berdasarkan NIS terlebih dahulu dan rapikan master.'],
                    ['kelas', 'Ya', 'Nama kelas', 'x rpl 1', 'Bagian identitas jadwal.'],
                    ['mata_pelajaran', 'Ya', 'Nama mata pelajaran', 'matematika', 'Bagian identitas jadwal.'],
                    ['guru', 'Ya', 'Nama guru', 'Ahmad Basuki', 'Bagian identitas jadwal.'],
                    ['hari', 'Ya', 'Senin-Sabtu', 'Senin', 'Bagian identitas jadwal.'],
                    ['jam_ke', 'Ya', 'Angka', '1', 'Bagian identitas jadwal.'],
                    ['tanggal', 'Ya', 'YYYY-MM-DD', '2026-08-20', 'Tanggal presensi.'],
                    ['status', 'Ya', 'hadir/sakit/izin/tidak_hadir', 'hadir', 'Disimpan huruf kecil.'],
                    ['keterangan', 'Tidak', 'Teks', '', 'Keterangan.'],
                    ['diinput_oleh', 'Tidak', 'Nama user', 'Admin Utama', 'Gunakan nama user, bukan ID.'],
                ],
            ],
            default => throw new InvalidArgumentException('Jenis template tidak dikenal.'),
        };
    }
}
