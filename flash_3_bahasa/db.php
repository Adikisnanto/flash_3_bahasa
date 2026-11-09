<?php
$host = "localhost";
$user = "root";       // Default XAMPP adalah "root"
$pass = "";           // Default XAMPP biasanya kosong ""
$db   = "flash_3_bahasa"; // Sesuaikan dengan nama database Anda di phpMyAdmin

// Buat koneksi MySQLi
$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set charset ke UTF-8 agar karakter bahasa jawa/khusus aman
$conn->set_charset("utf8mb4");
?>