<?php
header('Content-Type: application/json');
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $indonesia      = trim($_POST['indonesia'] ?? '');
    $inggris        = trim($_POST['inggris'] ?? '');
    $jawa           = trim($_POST['jawa'] ?? '');
    $kategori       = trim($_POST['kategori'] ?? 'Umum');
    $contoh_kalimat = trim($_POST['contoh_kalimat'] ?? '');
    $gambar_url     = trim($_POST['gambar_url'] ?? '');

    // Validasi input wajib
    if (empty($indonesia) || empty($inggris) || empty($jawa)) {
        echo json_encode(["status" => "error", "message" => "Bahasa Indonesia, Inggris, dan Jawa wajib diisi!"]);
        exit;
    }

    // Proses Upload File Gambar
    if (isset($_FILES['gambar_file']) && $_FILES['gambar_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath   = $_FILES['gambar_file']['tmp_name'];
        $fileName      = $_FILES['gambar_file']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $uploadFileDir = '../uploads/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path   = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $gambar_url = 'uploads/' . $newFileName;
            }
        }
    }

    // Query INSERT menyertakan kolom contoh_kalimat
    $sql = "INSERT INTO words (indonesia, inggris, jawa, kategori, contoh_kalimat, gambar_url) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Query Error: " . $conn->error]);
        exit;
    }

    // Bind parameter (6 parameter string: ssssss)
    $stmt->bind_param("ssssss", $indonesia, $inggris, $jawa, $kategori, $contoh_kalimat, $gambar_url);

    if ($stmt->execute()) {
        echo json_encode([
            "status"  => "success",
            "message" => "Kata berhasil disimpan!",
            "data"    => [
                "id"             => $conn->insert_id,
                "indonesia"      => $indonesia,
                "inggris"        => $inggris,
                "jawa"           => $jawa,
                "kategori"       => $kategori,
                "contoh_kalimat" => $contoh_kalimat,
                "gambar_url"     => $gambar_url
            ]
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menyimpan data: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Metode request tidak diizinkan."]);
}
?>