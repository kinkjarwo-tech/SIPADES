<?php
session_start();
include 'koneksi.php';
include 'auth.php'; // Memastikan admin sudah login

// Cek apakah ada parameter aksi yang dikirimkan melalui URL (GET)
if (isset($_GET['aksi'])) {
    $aksi = $_GET['aksi'];

    // 1. Aksi Ubah Status (Contoh: Menaikkan status dari Menunggu ke Diproses / Selesai)
    if ($aksi === 'ubah' && isset($_GET['id'])) {
        $id = (int) $_GET['id'];
        
        // Ambil data status sekarang
        $query = "SELECT status FROM pengaduan WHERE id_pengaduan = $id";
        $res = mysqli_query($conn, $query);
        $data = mysqli_fetch_assoc($res);
        
        if ($data) {
            $status_sekarang = $data['status'];
            $status_baru = 'Menunggu';

            // Alur siklus status
            if ($status_sekarang == 'Menunggu') {
                $status_baru = 'Diproses';
            } elseif ($status_sekarang == 'Diproses') {
                $status_baru = 'Selesai';
            } else {
                $status_baru = 'Menunggu';
            }

            // Update ke database
            $update = "UPDATE pengaduan SET status = '$status_baru' WHERE id_pengaduan = $id";
            mysqli_query($conn, $update);
        }

        header("Location: admin.php");
        exit;
    }

    // 2. Aksi Hapus Laporan
    if ($aksi === 'hapus' && isset($_GET['id'])) {
        $id = (int) $_GET['id'];
        
        $stmt = $conn->prepare("DELETE FROM pengaduan WHERE id_pengaduan = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            header("Location: admin.php?status=hapus_sukses");
        } else {
            header("Location: admin.php?status=gagal");
        }
        $stmt->close();
        exit;
    }
}

// Jika diakses tanpa parameter yang jelas, kembalikan ke halaman admin
header("Location: admin.php");
exit;
?>