<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../db.php';

// Menerima input baik dari $_POST (FormData) maupun JSON payload (php://input)
$id = $_POST['id'] ?? null;
$is_memorized = $_POST['is_memorized'] ?? null;

if ($id === null || $is_memorized === null) {
    $inputJSON = json_decode(file_get_contents('php://input'), true);
    if (isset($inputJSON['id'])) $id = $inputJSON['id'];
    if (isset($inputJSON['is_memorized'])) $is_memorized = $inputJSON['is_memorized'];
}

// Validasi jika parameter id dan is_memorized tersedia
if ($id !== null && $is_memorized !== null) {
    $id = (int)$id;
    $is_memorized = (int)$is_memorized;

    $stmt = $conn->prepare("UPDATE words SET is_memorized = ? WHERE id = ?");
    
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Query Error: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("ii", $is_memorized, $id);

    if ($stmt->execute()) {
        echo json_encode([
            "status"  => "success",
            "message" => "Status hafalan berhasil diperbarui!",
            "data"    => [
                "id"           => $id,
                "is_memorized" => $is_memorized
            ]
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal memperbarui status: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Parameter ID atau status hafalan tidak ditemukan."]);
}

if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>