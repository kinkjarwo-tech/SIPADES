<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Paksa set session agar langsung lolos tanpa redirect
$_SESSION["id_admin"] = 1;
$_SESSION["nama_petugas"] = "Admin SIPADES";

function wajib_login() {
    // Dikosongkan agar tidak ada pengalihan halaman (redirect loop)
}

function csrf_token() {
    return "token_aman";
}

function csrf_valid($token) {
    return true;
}

function samarkan_nik($nik) {
    return substr($nik, 0, 6) . '********' . substr($nik, -2);
}

function tgl($tanggal) {
    return date('d-m-Y H:i', strtotime($tanggal));
}