<?php
header('Content-Type: application/json');
require_once '../db.php';

$id = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID tidak ditemukan.']);
    exit;
}

// 1. Ambil data gambar_url terlebih dahulu sebelum menghapus baris dari DB
$selectStmt = $conn->prepare("SELECT gambar_url FROM words WHERE id = ?");
if ($selectStmt) {
    $selectStmt->bind_param("i", $id);
    $selectStmt->execute();
    $result = $selectStmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $gambarUrl = $row['gambar_url'];
        
        // Jika file berada di folder uploads lokal, hapus file fisik
        if (!empty($gambarUrl) && strpos($gambarUrl, 'uploads/') === 0) {
            $filePath = '../' . $gambarUrl;
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
    }
    $selectStmt->close();
}

// 2. Hapus data kata dari basis data
$stmt = $conn->prepare("DELETE FROM words WHERE id = ?");

if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Query Error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Data berhasil dihapus.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan atau sudah dihapus.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data dari database: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>