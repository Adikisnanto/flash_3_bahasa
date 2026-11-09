<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kamus Flashcard 3 Bahasa</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light">

    <!-- Header Navbar Top -->
    <nav class="navbar navbar-dark bg-primary shadow-sm py-3 sticky-top">
        <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
            <span class="navbar-brand fw-bold m-0 fs-5 d-flex align-items-center">
                <i class="fa-solid fa-layer-group text-warning me-2 fs-4"></i>
                <span>Kosa Kata Flashcard 3 Bahasa</span>
            </span>
            <span id="totalWordCount" class="badge bg-white text-primary shadow-sm px-3 py-2 rounded-pill fw-bold fs-6">
                Total Kata: 0
            </span>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container-fluid px-4 py-4">
        <div class="row g-4">
            
            <!-- SEBELAH KIRI: Form Input & Flashcard Utama -->
            <div class="col-lg-5 col-xl-4">
                
                <!-- Form Input -->
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-white text-dark mb-4">
                    <h5 class="fw-bold text-primary mb-3 d-flex align-items-center">
                        <i class="fa-solid fa-circle-plus me-2"></i>Tambah Kata & Kategori
                    </h5>

                    <!-- Container Notifikasi Pesan -->
                    <div id="alertContainer"></div>

                    <form id="addWordForm" autocomplete="off">
                        <div class="mb-2">
                            <label for="inputIndonesia" class="form-label fw-semibold small mb-1">Bahasa Indonesia</label>
                            <input type="text" name="indonesia" id="inputIndonesia" class="form-control form-control-sm rounded-3" placeholder="Contoh: Belajar" required>
                        </div>
                        <div class="mb-2">
                            <label for="inputInggris" class="form-label fw-semibold small mb-1">Bahasa Inggris</label>
                            <input type="text" name="inggris" id="inputInggris" class="form-control form-control-sm rounded-3" placeholder="Contoh: Study" required>
                        </div>
                        <div class="mb-2">
                            <label for="inputJawa" class="form-label fw-semibold small mb-1">Bahasa Jawa</label>
                            <input type="text" name="jawa" id="inputJawa" class="form-control form-control-sm rounded-3" placeholder="Contoh: Sinau" required>
                        </div>
                        <div class="mb-2">
                            <label for="inputKategori" class="form-label fw-semibold small mb-1">Kategori</label>
                            <input type="text" name="kategori" id="inputKategori" list="categoryList" class="form-control form-control-sm rounded-3" placeholder="Ketik atau pilih kategori..." required>
                            <datalist id="categoryList">
                                <option value="Kegiatan">
                                <option value="Pendidikan">
                                <option value="Benda">
                                <option value="Salam">
                                <option value="Umum">
                                <option value="Hewan">
                                <option value="Makanan">
                            </datalist>
                        </div>
                        
                        <!-- Upload File & URL -->
                        <div class="mb-2">
                            <label for="gambar_file" class="form-label fw-semibold small mb-1">Upload Gambar (Lokal)</label>
                            <input type="file" name="gambar_file" id="gambar_file" class="form-control form-control-sm rounded-3" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label for="gambar_url" class="form-label fw-semibold small mb-1">Atau Gunakan URL Gambar</label>
                            <input type="url" name="gambar_url" id="gambar_url" class="form-control form-control-sm rounded-3" placeholder="https://images.unsplash.com/...">
                            <div class="form-text text-muted fs-7">Kosongkan jika ingin menggunakan gambar otomatis.</div>
                        </div>

                        <!-- Preview Gambar Sebelum Simpan -->
                        <div id="imagePreviewContainer" class="mb-3 text-center d-none">
                            <small class="text-muted d-block mb-1 fw-semibold fs-7">Pratinjau Gambar:</small>
                            <img id="imagePreview" src="" alt="Preview" class="img-thumbnail rounded-3 shadow-sm" style="max-height: 110px; object-fit: cover;">
                        </div>

                        <button type="submit" id="btnSubmit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Kata
                        </button>
                    </form>
                </div>

                <!-- Kartu Flashcard Utama -->
                <div class="card border-0 bg-transparent text-center">
                    <h6 class="text-secondary fw-bold mb-3 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-id-card me-2 text-warning fs-5"></i> Flashcard Terbaru
                    </h6>
                    <div class="flashcard-container mx-auto">
                        <div class="flashcard" id="lastSavedCard">
                            <!-- Sisi Depan -->
                            <div class="flashcard-face flashcard-front shadow-sm border rounded-4 p-3 bg-white d-flex flex-column justify-content-between">
                                <div>
                                    <span class="badge bg-primary rounded-pill badge-lang" id="cardCategory">Umum</span>
                                </div>
                                <div class="card-img-wrapper my-2">
                                    <img id="cardImg" src="https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=400&q=80" alt="Visual Kata" class="img-fluid rounded-3">
                                </div>
                                <div>
                                    <small class="text-uppercase text-muted fw-bold d-block mb-1 fs-7">Bahasa Indonesia 🇮🇩</small>
                                    <div class="word-text fw-bold fs-5 text-dark" id="cardIndonesia">Belum ada data</div>
                                </div>
                                <div class="text-muted small mt-2 fs-7">
                                    <i class="fa-solid fa-rotate me-1"></i> Klik untuk membalik
                                </div>
                            </div>
                            
                            <!-- Sisi Belakang -->
                            <div class="flashcard-face flashcard-back shadow-sm border rounded-4 p-3 bg-white d-flex flex-column justify-content-between">
                                <span class="badge bg-success rounded-pill badge-lang">Terjemahan</span>
                                <div class="my-auto w-100 text-center">
                                    <div class="mb-3">
                                        <small class="text-uppercase text-muted fw-bold d-block fs-7">Bahasa Inggris 🇬🇧</small>
                                        <div class="fs-4 fw-bold text-success" id="cardInggris">-</div>
                                    </div>
                                    <hr class="my-2 opacity-25">
                                    <div>
                                        <small class="text-uppercase text-muted fw-bold d-block fs-7">Bahasa Jawa 🇮🇩</small>
                                        <div class="fs-4 fw-bold text-primary" id="cardJawa">-</div>
                                    </div>
                                </div>
                                <div class="text-muted small fs-7">
                                    <i class="fa-solid fa-rotate me-1"></i> Klik untuk kembali
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- SEBELAH KANAN: Filter, Search & Tabel Daftar Kata -->
            <div class="col-lg-7 col-xl-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-dark bg-white">
                    
                    <!-- Header Control Section -->
                    <div class="row g-3 align-items-center mb-3">
                        <div class="col-md-4">
                            <h5 class="fw-bold m-0 text-dark d-flex align-items-center">
                                <i class="fa-solid fa-list-check me-2 text-primary"></i>Daftar Kosa Kata
                            </h5>
                        </div>
                        <div class="col-md-8">
                            <div class="d-flex gap-2 justify-content-md-end flex-wrap">
                                <!-- Search Bar -->
                                <div class="input-group input-group-sm" style="max-width: 220px;">
                                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                                    <input type="text" id="searchWord" class="form-control border-start-0 bg-light" placeholder="Cari kata...">
                                </div>
                                <!-- Filter Dropdown -->
                                <select id="categoryFilter" class="form-select form-select-sm rounded-3 fw-semibold border-primary" style="width: 170px;">
                                    <option value="Semua">🌐 Semua Kategori</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Data -->
                    <div class="table-responsive rounded-3 border" style="max-height: 580px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="text-center" style="width: 60px;">Hafal</th>
                                    <th>Indonesia</th>
                                    <th>Inggris</th>
                                    <th>Jawa</th>
                                    <th>Kategori</th>
                                    <th class="text-center" style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="wordsTableBody">
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                        Memuat data kosa kata...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- MODAL EDIT DATA -->
    <div class="modal fade" id="editWordModal" tabindex="-1" aria-labelledby="editWordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4 text-dark">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-primary" id="editWordModalLabel">
                        <i class="fa-solid fa-pen-to-square me-2"></i>Edit Kosa Kata
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editWordForm" autocomplete="off">
                    <div class="modal-body">
                        <input type="hidden" id="edit_id" name="id">

                        <div class="mb-2">
                            <label for="edit_indonesia" class="form-label fw-semibold small">Bahasa Indonesia</label>
                            <input type="text" class="form-control form-control-sm rounded-3" id="edit_indonesia" name="indonesia" required>
                        </div>
                        <div class="mb-2">
                            <label for="edit_inggris" class="form-label fw-semibold small">Bahasa Inggris</label>
                            <input type="text" class="form-control form-control-sm rounded-3" id="edit_inggris" name="inggris" required>
                        </div>
                        <div class="mb-2">
                            <label for="edit_jawa" class="form-label fw-semibold small">Bahasa Jawa</label>
                            <input type="text" class="form-control form-control-sm rounded-3" id="edit_jawa" name="jawa" required>
                        </div>
                        <div class="mb-2">
                            <label for="edit_kategori" class="form-label fw-semibold small">Kategori</label>
                            <input type="text" class="form-control form-control-sm rounded-3" id="edit_kategori" name="kategori" list="categoryList" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_gambar_url" class="form-label fw-semibold small">URL Gambar (Opsional)</label>
                            <input type="url" class="form-control form-control-sm rounded-3" id="edit_gambar_url" name="gambar_url" placeholder="https://...">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-sm btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3" id="btnUpdate">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/bootstrap.bundle.min.js"></script>
    <!-- Script JS Utama -->
    <script src="js/script.js?v=2.0"></script>
</body>
</html>