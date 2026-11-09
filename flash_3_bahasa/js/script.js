/**
 * Global Variable & State
 */
window.allWordsData = [];

document.addEventListener('DOMContentLoaded', function () {
    // DOM Element Reference
    const addWordForm = document.getElementById('addWordForm');
    const editWordForm = document.getElementById('editWordForm');
    const alertContainer = document.getElementById('alertContainer');
    const wordsTableBody = document.getElementById('wordsTableBody');
    const categoryFilter = document.getElementById('categoryFilter');
    const searchWord = document.getElementById('searchWord');
    const totalWordCount = document.getElementById('totalWordCount');
    const lastSavedCard = document.getElementById('lastSavedCard');
    
    // Preview Image Reference
    const gambarFileInput = document.getElementById('gambar_file');
    const gambarUrlInput = document.getElementById('gambar_url');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const imagePreview = document.getElementById('imagePreview');

    /**
     * Flip Card Preview
     */
    if (lastSavedCard) {
        lastSavedCard.addEventListener('click', function () {
            this.classList.toggle('is-flipped');
        });
    }

    /**
     * Live Preview Gambar
     */
    if (gambarFileInput) {
        gambarFileInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    if (imagePreview) imagePreview.src = e.target.result;
                    if (imagePreviewContainer) imagePreviewContainer.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
                if (gambarUrlInput) gambarUrlInput.value = '';
            } else if (!gambarUrlInput || !gambarUrlInput.value) {
                if (imagePreviewContainer) imagePreviewContainer.classList.add('d-none');
            }
        });
    }

    if (gambarUrlInput) {
        gambarUrlInput.addEventListener('input', function () {
            const url = this.value.trim();
            if (url) {
                if (imagePreview) imagePreview.src = url;
                if (imagePreviewContainer) imagePreviewContainer.classList.remove('d-none');
                if (gambarFileInput) gambarFileInput.value = '';
            } else if (!gambarFileInput || !gambarFileInput.files.length) {
                if (imagePreviewContainer) imagePreviewContainer.classList.add('d-none');
            }
        });
    }

    /**
     * Tampilkan Alert Notification
     */
    function showAlert(message, type = 'success') {
        if (!alertContainer) return;
        alertContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show rounded-3 py-2 px-3 small" role="alert">
                <i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'} me-2"></i>
                ${message}
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        setTimeout(() => {
            const alertEl = alertContainer.querySelector('.alert');
            if (alertEl) alertEl.remove();
        }, 4000);
    }
    window.showAlert = showAlert;

    /**
     * Fetch Data dari Backend
     */
    async function fetchWords() {
        try {
            const res = await fetch('api/get_words.php');
            const text = await res.text();
            
            let json;
            try {
                json = JSON.parse(text.trim());
            } catch (e) {
                console.error('Output PHP bukan JSON valid:', text);
                throw new Error('Respon server bukan format JSON yang valid.');
            }

            if (!res.ok) throw new Error(json.message || `HTTP Error ${res.status}`);

            window.allWordsData = Array.isArray(json) ? json : (json.data || []);

            updateCategoryDropdown(window.allWordsData);
            applyFilterAndSearch();
        } catch (error) {
            console.error('Fetch error:', error);
            if (wordsTableBody) {
                wordsTableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-danger py-4">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i> Gagal memuat data kata: ${escapeHtml(error.message)}
                        </td>
                    </tr>`;
            }
        }
    }
    window.fetchWords = fetchWords;

    /**
     * Update Option Filter Kategori
     */
    function updateCategoryDropdown(data) {
        if (!categoryFilter) return;
        const categories = [...new Set(data.map(item => item.kategori).filter(Boolean))];
        const currentValue = categoryFilter.value;

        categoryFilter.innerHTML = '<option value="Semua">🌐 Semua Kategori</option>';
        categories.forEach(cat => {
            const opt = document.createElement('option');
            opt.value = cat;
            opt.textContent = cat;
            categoryFilter.appendChild(opt);
        });

        if (categories.includes(currentValue)) {
            categoryFilter.value = currentValue;
        } else {
            categoryFilter.value = 'Semua';
        }
    }

    /**
     * Filter & Search Realtime
     */
    function applyFilterAndSearch() {
        let filtered = [...window.allWordsData];

        if (categoryFilter && categoryFilter.value !== 'Semua') {
            filtered = filtered.filter(item => item.kategori === categoryFilter.value);
        }

        if (searchWord && searchWord.value.trim() !== '') {
            const query = searchWord.value.trim().toLowerCase();
            filtered = filtered.filter(item => 
                (item.indonesia && item.indonesia.toLowerCase().includes(query)) ||
                (item.inggris && item.inggris.toLowerCase().includes(query)) ||
                (item.jawa && item.jawa.toLowerCase().includes(query)) ||
                (item.kategori && item.kategori.toLowerCase().includes(query))
            );
        }

        renderTable(filtered);
    }

    if (categoryFilter) categoryFilter.addEventListener('change', applyFilterAndSearch);
    if (searchWord) searchWord.addEventListener('input', applyFilterAndSearch);

    /**
     * Render Tabel
     */
    function renderTable(data) {
        if (!wordsTableBody) return;
        wordsTableBody.innerHTML = '';

        if (totalWordCount) {
            totalWordCount.textContent = `Total Kata: ${window.allWordsData.length}`;
        }

        if (!data || data.length === 0) {
            wordsTableBody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data kosa kata.</td></tr>`;
            return;
        }

        data.forEach(item => {
            const tr = document.createElement('tr');
            let imgSource = (item.gambar_url && item.gambar_url.trim() !== '') 
                ? item.gambar_url 
                : 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=100&q=80';

            tr.innerHTML = `
                <td class="text-center align-middle">
                    <input type="checkbox" class="form-check-input btn-memorize" data-id="${item.id}" ${item.is_memorized == 1 ? 'checked' : ''}>
                </td>
                <td class="align-middle">
                    <div class="d-flex align-items-center gap-2">
                        <img src="${imgSource}" alt="Img" class="rounded-2 object-fit-cover" style="width: 36px; height: 36px;" onerror="this.src='https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=100&q=80'">
                        <span class="fw-semibold">${escapeHtml(item.indonesia)}</span>
                    </div>
                </td>
                <td class="align-middle">${escapeHtml(item.inggris)}</td>
                <td class="align-middle">${escapeHtml(item.jawa)}</td>
                <td class="align-middle"><span class="badge bg-secondary rounded-pill">${escapeHtml(item.kategori || 'Umum')}</span></td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-sm btn-outline-primary border-0 rounded-circle me-1 btn-edit-action" data-id="${item.id}" title="Edit">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle btn-delete-action" data-id="${item.id}" title="Hapus">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            `;

            // Event listener klik tombol Edit
            const editBtn = tr.querySelector('.btn-edit-action');
            if (editBtn) {
                editBtn.addEventListener('click', function () {
                    openEditModal(item.id);
                });
            }

            // Event listener klik tombol Hapus
            const deleteBtn = tr.querySelector('.btn-delete-action');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function () {
                    deleteWord(item.id);
                });
            }

            // Event listener checkbox Hafal
            const memorizeChk = tr.querySelector('.btn-memorize');
            if (memorizeChk) {
                memorizeChk.addEventListener('change', function () {
                    toggleMemorized(item.id, this.checked);
                });
            }

            wordsTableBody.appendChild(tr);
        });
    }

    function updateCardPreview(word) {
        if (document.getElementById('cardCategory')) document.getElementById('cardCategory').textContent = word.kategori || 'Umum';
        if (document.getElementById('cardIndonesia')) document.getElementById('cardIndonesia').textContent = word.indonesia;
        if (document.getElementById('cardInggris')) document.getElementById('cardInggris').textContent = word.inggris;
        if (document.getElementById('cardJawa')) document.getElementById('cardJawa').textContent = word.jawa;

        if (document.getElementById('cardImg')) {
            document.getElementById('cardImg').src = (word.gambar_url && word.gambar_url.trim() !== '') 
                ? word.gambar_url 
                : 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=400&q=80';
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }

    /**
     * Submit Form Tambah Kata
     */
    if (addWordForm) {
        addWordForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const submitBtn = document.getElementById('btnSubmit');
            const originalBtnText = submitBtn ? submitBtn.innerHTML : '';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-1"></i> Menyimpan...`;
            }

            try {
                const formData = new FormData(addWordForm);
                const res = await fetch('api/add_word.php', { method: 'POST', body: formData });
                const text = await res.text();
                const data = JSON.parse(text.trim());

                if (data.status === 'success' || data.success) {
                    showAlert(data.message || 'Kata berhasil ditambahkan!', 'success');
                    addWordForm.reset();
                    if (imagePreviewContainer) imagePreviewContainer.classList.add('d-none');
                    if (data.data) updateCardPreview(data.data);
                    fetchWords();
                } else {
                    showAlert(data.message || 'Gagal menyimpan data.', 'danger');
                }
            } catch (err) {
                console.error(err);
                showAlert('Gagal terhubung ke server saat menyimpan.', 'danger');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            }
        });
    }

    /**
     * Submit Form Edit Kata
     */
    if (editWordForm) {
        editWordForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            
            const btnUpdate = document.getElementById('btnUpdate');
            const originalBtnHtml = btnUpdate ? btnUpdate.innerHTML : '';

            if (btnUpdate) {
                btnUpdate.disabled = true;
                btnUpdate.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-1"></i> Memperbarui...`;
            }

            try {
                const formData = new FormData(editWordForm);
                const editId = formData.get('id');
                if (!editId) {
                    showAlert('ID tidak ditemukan! Gagal memperbarui.', 'danger');
                    return;
                }

                const res = await fetch('api/update_word.php', { method: 'POST', body: formData });
                const text = await res.text();
                const data = JSON.parse(text.trim());

                if (data.status === 'success' || data.success) {
                    showAlert(data.message || 'Data berhasil diperbarui!', 'success');
                    closeEditModal();
                    fetchWords();
                } else {
                    showAlert(data.message || 'Gagal memperbarui kata.', 'danger');
                }
            } catch (err) {
                console.error('Update Error:', err);
                showAlert(`Terjadi kesalahan saat menyimpan perubahan.`, 'danger');
            } finally {
                if (btnUpdate) {
                    btnUpdate.disabled = false;
                    btnUpdate.innerHTML = originalBtnHtml;
                }
            }
        });
    }

    // Load data pertama kali
    fetchWords();
});

/**
 * GLOBAL FUNCTION: Buka Modal Edit (Aman dari Ketergantungan Library)
 */
window.openEditModal = function (id) {
    const item = window.allWordsData.find(w => w.id == id);
    if (!item) {
        alert('Data tidak ditemukan!');
        return;
    }

    // Isi Form Modal
    if (document.getElementById('edit_id')) document.getElementById('edit_id').value = item.id;
    if (document.getElementById('edit_indonesia')) document.getElementById('edit_indonesia').value = item.indonesia || '';
    if (document.getElementById('edit_inggris')) document.getElementById('edit_inggris').value = item.inggris || '';
    if (document.getElementById('edit_jawa')) document.getElementById('edit_jawa').value = item.jawa || '';
    if (document.getElementById('edit_kategori')) document.getElementById('edit_kategori').value = item.kategori || 'Umum';
    if (document.getElementById('edit_gambar_url')) document.getElementById('edit_gambar_url').value = item.gambar_url || '';

    // Tampilkan Modal
    const modalEl = document.getElementById('editWordModal');
    if (!modalEl) return;

    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
        modalInstance.show();
    } else {
        // Fallback jika Bootstrap JS tidak terload
        modalEl.style.display = 'block';
        modalEl.classList.add('show');
        document.body.classList.add('modal-open');
    }
};

/**
 * GLOBAL FUNCTION: Tutup Modal Edit
 */
window.closeEditModal = function () {
    const modalEl = document.getElementById('editWordModal');
    if (!modalEl) return;

    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) modalInstance.hide();
    } else {
        modalEl.style.display = 'none';
        modalEl.classList.remove('show');
        document.body.classList.remove('modal-open');
    }
};

/**
 * GLOBAL FUNCTION: Hapus Data Kata
 */
window.deleteWord = async function (id) {
    if (!confirm('Apakah Anda yakin ingin menghapus kata ini?')) return;

    try {
        const res = await fetch(`api/delete_word.php?id=${id}`, { method: 'GET' });
        const text = await res.text();
        const data = JSON.parse(text.trim());

        if (data.status === 'success' || data.success) {
            if (typeof window.showAlert === 'function') window.showAlert('Kata berhasil dihapus!', 'success');
            if (typeof window.fetchWords === 'function') window.fetchWords();
        } else {
            alert(data.message || 'Gagal menghapus data.');
        }
    } catch (err) {
        console.error(err);
        alert('Gagal terhubung ke server (api/delete_word.php).');
    }
};

/**
 * GLOBAL FUNCTION: Toggle Status Hafal
 */
window.toggleMemorized = async function (id, status) {
    try {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('is_memorized', status ? 1 : 0);

        await fetch('api/toggle_memorize.php', {
            method: 'POST',
            body: formData
        });

        const item = window.allWordsData.find(w => w.id == id);
        if (item) item.is_memorized = status ? 1 : 0;
    } catch (err) {
        console.error('Toggle status error:', err);
    }
};