<?php
session_start();
include 'koneksi.php';
include 'auth.php';

$query = "SELECT pengaduan.*, pelapor.nama_lengkap, pelapor.nik, pelapor.no_whatsapp, kategori.nama_kategori 
          FROM pengaduan 
          JOIN pelapor ON pengaduan.id_pelapor = pelapor.id_pelapor 
          JOIN kategori ON pengaduan.id_kategori = kategori.id_kategori 
          ORDER BY pengaduan.id_pengaduan DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - SIPADES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-primary shadow-sm">
        <div class="container">
            <span class="navbar-brand mb-0 h1">SIPADES - Panel Admin</span>
            <div>
                <a href="index.php" class="btn btn-outline-light btn-sm me-2" target="_blank">Lihat Web Warga</a>
                <a href="logout.php" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <h2 class="mb-4 text-primary">Daftar Pengaduan Masyarakat</h2>

        <div class="card shadow-sm border-primary">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-primary text-dark">
                            <tr>
                                <th>No</th>
                                <th>Kode Pengaduan</th>
                                <th>Tanggal</th>
                                <th>Nama Pelapor</th>
                                <th>NIK</th>
                                <th>No WhatsApp</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Isi Pengaduan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><strong><?php echo htmlspecialchars($row['kode_pengaduan']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nik']); ?></td>
                                        <td><?php echo htmlspecialchars($row['no_whatsapp']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_kategori']); ?></td>
                                        <td><?php echo htmlspecialchars($row['lokasi']); ?></td>
                                        <td><?php echo htmlspecialchars($row['isi_pengaduan']); ?></td>
                                        <td>
                                            <?php 
                                                $status = $row['status'];
                                                $badgeClass = 'bg-secondary';
                                                if ($status == 'Diproses') $badgeClass = 'bg-warning text-dark';
                                                if ($status == 'Selesai') $badgeClass = 'bg-success';
                                                if ($status == 'Ditolak') $badgeClass = 'bg-danger';
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>"><?php echo $status; ?></span>
                                        </td>
                                        <td>
                                            <a href="proses_admin.php?aksi=ubah&id=<?php echo $row['id_pengaduan']; ?>" class="btn btn-warning btn-sm mb-1">Ubah Status</a>
                                            <a href="proses_admin.php?aksi=hapus&id=<?php echo $row['id_pengaduan']; ?>" 
                                               class="btn btn-danger btn-sm mb-1" 
                                               onclick="return confirm('Yakin ingin menghapus laporan ini?');">
                                               Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="text-center text-muted">Belum ada data pengaduan masuk.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>