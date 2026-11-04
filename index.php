<?php
require_once "koneksi.php";
$kategori_result = $conn->query("SELECT * FROM kategori");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPADES - Layanan Pengaduan Desa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary shadow-sm">
        <div class="container">
            <span class="navbar-brand mb-0 h1">SIPADES</span>
            <!-- Tombol diarahkan ke logout.php agar sesi terhapus dan wajib login ulang -->
            <a class="btn btn-outline-light btn-sm" href="logout.php">Akses Admin</a>
        </div>
    </nav>

    <div class="container mt-5 mb-5" style="max-width: 600px;">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="mb-4 text-center">Form Pengaduan Masyarakat</h3>

                <?php if (isset($_GET['status'])): ?>
                    <?php if ($_GET['status'] === 'sukses'): ?>
                        <div class="alert alert-success">
                            Pengaduan berhasil dikirim! Kode Pengaduan Anda: <strong><?= htmlspecialchars($_GET['kode'] ?? ''); ?></strong>
                        </div>
                    <?php elseif ($_GET['status'] === 'tidak_valid'): ?>
                        <div class="alert alert-danger">Data tidak valid. Periksa kembali NIK (16 digit) dan form yang kosong.</div>
                    <?php else: ?>
                        <div class="alert alert-danger">Terjadi kesalahan saat menyimpan pengaduan.</div>
                    <?php endif; ?>
                <?php endif; ?>

                <form action="proses_pengaduan.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" required maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">NIK (16 Digit)</label>
                        <input type="text" name="nik" class="form-control" required pattern="[0-9]{16}" maxlength="16">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor WhatsApp</label>
                        <input type="text" name="no_whatsapp" class="form-control" required pattern="[0-9]{10,15}" placeholder="Contoh: 081234567890">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori Pengaduan</label>
                        <select name="id_kategori" class="form-select" required>
                            <option value="">Pilih Kategori</option>
                            <?php while ($kat = $kategori_result->fetch_assoc()): ?>
                                <option value="<?= $kat['id_kategori']; ?>"><?= htmlspecialchars($kat['nama_kategori']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lokasi Kejadian</label>
                        <input type="text" name="lokasi" class="form-control" required maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Isi Pengaduan</label>
                        <textarea name="isi_pengaduan" class="form-control" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Kirim Pengaduan</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>