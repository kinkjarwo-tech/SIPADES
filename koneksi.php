<?php
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "db_sipades";
$port = 3306;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass, $db, $port);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    error_log("Koneksi database gagal: " . $e->getMessage());
    http_response_code(500);
    exit("Koneksi ke basis data gagal.");
}
?>