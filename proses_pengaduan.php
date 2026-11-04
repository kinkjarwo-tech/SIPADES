<?php
require_once "koneksi.php";
date_default_timezone_set("Asia/Jakarta");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

function kembali($status, $kode = "") {
    $url = "index.php?status=" . urlencode($status);
    if ($kode !== "") {
        $url .= "&kode=" . urlencode($kode);
    }
    header("Location: " . $url);
    exit;
}

$nama     = trim($_POST["nama_lengkap"] ?? "");
$nik      = trim($_POST["nik"] ?? "");
$wa       = trim($_POST["no_whatsapp"] ?? "");
$kategori = (int) ($_POST["id_kategori"] ?? 0);
$lokasi   = trim($_POST["lokasi"] ?? "");
$isi      = trim($_POST["isi_pengaduan"] ?? "");

if (
    $nama === "" || $lokasi === "" || $isi === "" ||
    mb_strlen($nama) > 100 || mb_strlen($lokasi) > 255 ||
    !preg_match('/^[0-9]{16}$/', $nik) ||
    !preg_match('/^[0-9]{10,15}$/', $wa) ||
    $kategori < 1
) {
    kembali("tidak_valid");
}

try {
    $stmt = $conn->prepare("SELECT id_kategori FROM kategori WHERE id_kategori = ?");
    $stmt->bind_param("i", $kategori);
    $stmt->execute();
    $ada = $stmt->get_result()->num_rows > 0;
    $stmt->close();
    if (!$ada) {
        kembali("tidak_valid");
    }

    $conn->begin_transaction();

    // 1. Simpan/Cek Pelapor berdasarkan NIK
    $stmt = $conn->prepare(
        "INSERT INTO pelapor (nik, nama_lengkap, no_whatsapp) VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE
            id_pelapor   = LAST_INSERT_ID(id_pelapor),
            nama_lengkap = VALUES(nama_lengkap),
            no_whatsapp  = VALUES(no_whatsapp)"
    );
    $stmt->bind_param("sss", $nik, $nama, $wa);
    $stmt->execute();
    
    // Ambil ID pelapor
    $id_pelapor = $stmt->insert_id;
    if ($id_pelapor == 0) {
        $stmt_cek = $conn->prepare("SELECT id_pelapor FROM pelapor WHERE nik = ?");
        $stmt_cek->bind_param("s", $nik);
        $stmt_cek->execute();
        $res_cek = $stmt_cek->get_result()->fetch_assoc();
        $id_pelapor = $res_cek['id_pelapor'];
        $stmt_cek->close();
    }
    $stmt->close();

    // 2. Simpan Pengaduan
    $sementara = "TMP" . bin2hex(random_bytes(8));
    $stmt = $conn->prepare(
        "INSERT INTO pengaduan (kode_pengaduan, id_pelapor, id_kategori, lokasi, isi_pengaduan, status)
         VALUES (?, ?, ?, ?, ?, 'Menunggu')"
    );
    $stmt->bind_param("siiss", $sementara, $id_pelapor, $kategori, $lokasi, $isi);
    $stmt->execute();
    $id_pengaduan = $conn->insert_id;
    $stmt->close();

    // 3. Buat Kode Unik Pengaduan (SPD-YYYYMMDD-ID)
    $kode = sprintf("SPD-%s-%04d", date("Ymd"), $id_pengaduan);
    $stmt = $conn->prepare("UPDATE pengaduan SET kode_pengaduan = ? WHERE id_pengaduan = ?");
    $stmt->bind_param("si", $kode, $id_pengaduan);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    kembali("sukses", $kode);
} catch (Throwable $e) {
    $conn->rollback();
    kembali("gagal");
}
?>