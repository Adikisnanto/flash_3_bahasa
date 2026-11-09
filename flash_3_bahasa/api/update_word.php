<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id             = intval($_POST['id'] ?? 0);
    $indonesia      = trim($_POST['indonesia'] ?? '');
    $inggris        = trim($_POST['inggris'] ?? '');
    $jawa           = trim($_POST['jawa'] ?? '');
    $kategori       = trim($_POST['kategori'] ?? 'Umum');
    $contoh_kalimat = trim($_POST['contoh_kalimat'] ?? '');
    $gambar_url     = trim($_POST['gambar_url'] ?? '');

    // Validasi input wajib
    if ($id <= 0 || empty($indonesia) || empty($inggris) || empty($jawa)) {
        echo json_encode(["status" => "error", "message" => "Data tidak lengkap atau ID tidak valid!"]);
        exit;
    }

    // Proses Upload File Gambar Baru (Jika ada file gambar baru yang diunggah)
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
                // Hapus gambar lama jika merupakan file lokal dari folder uploads
                $getOldImgStmt = $conn->prepare("SELECT gambar_url FROM words WHERE id = ?");
                if ($getOldImgStmt) {
                    $getOldImgStmt->bind_param("i", $id);
                    $getOldImgStmt->execute();
                    $res = $getOldImgStmt->get_result();
                    if ($oldRow = $res->fetch_assoc()) {
                        $oldImg = $oldRow['gambar_url'];
                        if (!empty($oldImg) && strpos($oldImg, 'uploads/') === 0) {
                            @unlink('../' . $oldImg);
                        }
                    }
                    $getOldImgStmt->close();
                }

                $gambar_url = 'uploads/' . $newFileName;
            }
        }
    }

    // Query UPDATE menyertakan kolom contoh_kalimat
    $sql = "UPDATE words SET indonesia = ?, inggris = ?, jawa = ?, kategori = ?, contoh_kalimat = ?, gambar_url = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Query Error: " . $conn->error]);
        exit;
    }

    // Bind parameter (6 string, 1 integer -> "ssssssi")
    $stmt->bind_param("ssssssi", $indonesia, $inggris, $jawa, $kategori, $contoh_kalimat, $gambar_url, $id);

    if ($stmt->execute()) {
        echo json_encode([
            "status"  => "success",
            "message" => "Kosa kata berhasil diperbarui!",
            "data"    => [
                "id"             => $id,
                "indonesia"      => $indonesia,
                "inggris"        => $inggris,
                "jawa"           => $jawa,
                "kategori"       => $kategori,
                "contoh_kalimat" => $contoh_kalimat,
                "gambar_url"     => $gambar_url
            ]
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal memperbarui data: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Metode request tidak diizinkan."]);
}
?>