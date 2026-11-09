<?php
// Mencegah pesan error PHP bercampur ke dalam output JSON
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

// Panggil file koneksi (diselaraskan dengan file backend lainnya)
require_once '../db.php';

// Pastikan variabel $conn dari db.php tersedia
if (!isset($conn) || $conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Koneksi database tidak tersedia."
    ]);
    exit;
}

try {
    // Ambil seluruh data kosa kata diurutkan dari yang terbaru
    $sql = "SELECT id, indonesia, inggris, jawa, kategori, contoh_kalimat, gambar_url, is_memorized, created_at FROM words ORDER BY id DESC";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception($conn->error);
    }

    $data = [];
    while ($row = $result->fetch_assoc()) {
        // Konversi tipe data numerik agar sesuai untuk JavaScript
        $row['id'] = (int)$row['id'];
        $row['is_memorized'] = isset($row['is_memorized']) ? (int)$row['is_memorized'] : 0;
        
        $data[] = $row;
    }

    // Mengembalikan data JSON
    echo json_encode($data);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Query Error: " . $e->getMessage()
    ]);
}

if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>