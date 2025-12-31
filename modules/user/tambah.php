<?php
// modules/barang/tambah.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek login dan role admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    exit;
}

// Include koneksi database (melalui Database class)
include_once __DIR__ . "/../../class/Database.php";
include_once __DIR__ . "/../../class/Barang.php";

$db = new Database();
$barangObj = new Barang();
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/lab13_14_vivi/';

// Logika untuk menambah barang
$error = '';
$success = '';

if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $kategori = $_POST['kategori'];
    $harga_jual = str_replace('.', '', $_POST['harga_jual']);
    $harga_beli = str_replace('.', '', $_POST['harga_beli']);
    $stok = $_POST['stok'];
    $file_gambar = $_FILES['file_gambar'];
    $gambar = null;

    // Validasi harga
    if ($harga_jual <= $harga_beli) {
        $error = "Harga jual harus lebih besar dari harga beli!";
    } else {
        // Upload gambar jika ada
        if ($file_gambar['error'] == 0) {
            $filename = str_replace(' ', '_', $file_gambar['name']);
            $filename = time() . '_' . $filename; // Tambah timestamp untuk unique
            $destination = $_SERVER['DOCUMENT_ROOT'] . '/lab13_14_vivi/assets/gambar/' . $filename;
            
            // Validasi tipe file
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!in_array($file_gambar['type'], $allowed_types)) {
                $error = "Format file tidak didukung! Gunakan JPG, PNG, atau GIF";
            } elseif ($file_gambar['size'] > 2 * 1024 * 1024) { // 2MB
                $error = "Ukuran file terlalu besar! Maksimal 2MB";
            } elseif (move_uploaded_file($file_gambar['tmp_name'], $destination)) {
                $gambar = $filename;
            } else {
                $error = "Gagal mengupload gambar!";
            }
        }

        if (empty($error)) {
            // Data untuk disimpan
            $data = [
                'nama' => $nama,
                'kategori' => $kategori,
                'harga_jual' => $harga_jual,
                'harga_beli' => $harga_beli,
                'stok' => $stok,
                'gambar' => $gambar,
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Gunakan method dari class Database
            $result = $db->insert('data_barang', $data);
            
            if ($result !== false) {
                $success = "Barang berhasil ditambahkan! ID: " . $result;
                // Redirect setelah 2 detik
                echo '<script>
                    setTimeout(function() {
                        window.location.href = "/lab13_14_vivi/barang?success=tambah";
                    }, 2000);
                </script>';
            } else {
                $error = "Gagal menambahkan barang! " . $db->getError();
            }
        }
    }
}
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="main-content">
    <!-- Main Card -->
    <div class="card">
        <!-- Card Header -->
        <div class="card-header">
            <h1>
                <i class="fas fa-plus-circle"></i>
                Tambah Barang Baru
            </h1>
            <a href="/lab13_14_vivi/user/index" class="btn btn-warning">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Daftar
            </a>
        </div>

        <!-- Form Container -->
        <div class="form-container">
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= $success ?>
                    <p>Mengarahkan ke halaman daftar barang...</p>
                </div>
            <?php endif; ?>
            
            <div class="form-note">
                <i class="fas fa-info-circle"></i>
                Isi semua form berikut untuk menambahkan barang baru ke inventaris
            </div>
            
            <form method="post" action="" enctype="multipart/form-data" id="formTambah">
                <div class="form-grid">
                    <!-- Section 1: Informasi Dasar -->
                    <div class="form-section">
                        <div class="section-header">
                            <h3>
                                <div class="section-icon">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                Informasi Dasar
                            </h3>
                        </div>
                        
                        <div class="form-group">
                            <label for="nama">
                                <i class="fas fa-tag"></i>
                                Nama Barang
                            </label>
                            <input type="text" id="nama" name="nama" class="form-control" 
                                   placeholder="Masukkan nama barang" required
                                   value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '' ?>">
                            <small style="color: #b8a8ff; display: block; margin-top: 5px; font-size: 13px;">
                                <i class="fas fa-exclamation-circle"></i> Minimal 3 karakter, maksimal 100 karakter
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="kategori">
                                <i class="fas fa-folder"></i>
                                Kategori
                            </label>
                            <select id="kategori" name="kategori" class="form-control" required>
                                <option value="" disabled selected>Pilih Kategori</option>
                                <option value="Komputer" <?= (isset($_POST['kategori']) && $_POST['kategori'] == 'Komputer') ? 'selected' : '' ?>>Komputer</option>
                                <option value="Elektronik" <?= (isset($_POST['kategori']) && $_POST['kategori'] == 'Elektronik') ? 'selected' : '' ?>>Elektronik</option>
                                <option value="Hand Phone" <?= (isset($_POST['kategori']) && $_POST['kategori'] == 'Hand Phone') ? 'selected' : '' ?>>Hand Phone</option>
                            </select>
                        </div>
                    </div>

                    <!-- Section 2: Harga & Stok -->
                    <div class="form-section">
                        <div class="section-header">
                            <h3>
                                <div class="section-icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                Harga & Stok
                            </h3>
                        </div>

                        <div class="form-group">
                            <label for="harga_beli">
                                <i class="fas fa-shopping-cart"></i>
                                Harga Beli
                            </label>
                            <div style="position: relative;">
                                <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #7b4bff; font-weight: 600;">Rp</span>
                                <input type="text" id="harga_beli" name="harga_beli" class="form-control" 
                                       placeholder="Masukkan harga beli" 
                                       style="padding-left: 45px;"
                                       value="<?= isset($_POST['harga_beli']) ? htmlspecialchars($_POST['harga_beli']) : '' ?>"
                                       required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="harga_jual">
                                <i class="fas fa-tag"></i>
                                Harga Jual
                            </label>
                            <div style="position: relative;">
                                <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #7b4bff; font-weight: 600;">Rp</span>
                                <input type="text" id="harga_jual" name="harga_jual" class="form-control" 
                                       placeholder="Masukkan harga jual" 
                                       style="padding-left: 45px;"
                                       value="<?= isset($_POST['harga_jual']) ? htmlspecialchars($_POST['harga_jual']) : '' ?>"
                                       required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="stok">
                                <i class="fas fa-boxes"></i>
                                Stok
                            </label>
                            <input type="number" id="stok" name="stok" class="form-control" 
                                   placeholder="Masukkan jumlah stok" 
                                   value="<?= isset($_POST['stok']) ? htmlspecialchars($_POST['stok']) : '0' ?>" 
                                   min="0"
                                   required>
                        </div>
                    </div>

                    <!-- Section 3: Gambar -->
                    <div class="form-section" style="grid-column: span 2;">
                        <div class="section-header">
                            <h3>
                                <div class="section-icon">
                                    <i class="fas fa-image"></i>
                                </div>
                                Gambar Barang (Opsional)
                            </h3>
                        </div>

                        <div class="form-group">
                            <label>
                                <i class="fas fa-cloud-upload-alt"></i>
                                Upload Gambar
                            </label>
                            <div class="file-upload-area" onclick="document.getElementById('file_gambar').click()">
                                <div>
                                    <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #7b4bff; margin-bottom: 15px;"></i>
                                    <p style="color: #5a2fd4; font-weight: 500; margin-bottom: 5px;">
                                        Klik untuk upload gambar
                                    </p>
                                    <small style="color: #b8a8ff;">
                                        Format yang didukung: JPG, PNG, GIF (Maks: 2MB)
                                    </small>
                                </div>
                                <input type="file" id="file_gambar" name="file_gambar" 
                                       accept="image/*" style="display: none;" 
                                       onchange="previewImage(this)">
                            </div>

                            <div id="image-preview" style="display: none; text-align: center; margin-top: 20px;">
                                <img id="preview" src="" alt="Preview" style="max-width: 200px; border-radius: 12px; border: 3px solid #e9e1ff;">
                                <br>
                                <button type="button" class="btn-danger" onclick="removePreview()" style="margin-top: 15px; padding: 8px 16px;">
                                    <i class="fas fa-trash"></i> Hapus Gambar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="reset" class="btn btn-danger" onclick="removePreview()">
                        <i class="fas fa-times"></i>
                        Reset Form
                    </button>
                    <button type="submit" name="submit" class="btn btn-success">
                        <i class="fas fa-save"></i>
                        Simpan Barang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Format input harga dengan titik (ribuan separator)
function formatRupiah(angka) {
    return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function removeFormat(angka) {
    return angka.toString().replace(/\./g, '');
}

document.addEventListener('DOMContentLoaded', function() {
    const hargaBeli = document.getElementById('harga_beli');
    const hargaJual = document.getElementById('harga_jual');
    
    // Format harga beli
    hargaBeli.addEventListener('input', function() {
        let value = this.value.replace(/[^0-9]/g, '');
        if (value) {
            this.value = formatRupiah(value);
        }
    });
    
    // Format harga jual
    hargaJual.addEventListener('input', function() {
        let value = this.value.replace(/[^0-9]/g, '');
        if (value) {
            this.value = formatRupiah(value);
        }
    });
    
    // Hapus format sebelum submit
    document.getElementById('formTambah').addEventListener('submit', function() {
        hargaBeli.value = removeFormat(hargaBeli.value);
        hargaJual.value = removeFormat(hargaJual.value);
    });
});

function previewImage(input) {
    if (input.files && input.files[0]) {
        // Validasi ukuran file (max 2MB)
        const fileSize = input.files[0].size / 1024 / 1024; // MB
        if (fileSize > 2) {
            alert('Ukuran file terlalu besar! Maksimal 2MB');
            input.value = '';
            return;
        }
        
        // Validasi tipe file
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!validTypes.includes(input.files[0].type)) {
            alert('Format file tidak didukung! Gunakan JPG, PNG, atau GIF');
            input.value = '';
            return;
        }
        
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('image-preview').style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function removePreview() {
    document.getElementById('file_gambar').value = '';
    document.getElementById('image-preview').style.display = 'none';
    document.getElementById('preview').src = '';
}

// Validasi form sebelum submit
document.getElementById('formTambah').addEventListener('submit', function(e) {
    const nama = document.getElementById('nama').value;
    const hargaBeli = document.getElementById('harga_beli').value.replace(/\./g, '');
    const hargaJual = document.getElementById('harga_jual').value.replace(/\./g, '');
    
    // Validasi nama
    if (nama.length < 3 || nama.length > 100) {
        e.preventDefault();
        alert('Nama barang harus 3-100 karakter!');
        document.getElementById('nama').focus();
        return false;
    }
    
    // Validasi harga
    if (parseInt(hargaJual) <= parseInt(hargaBeli)) {
        e.preventDefault();
        alert('Harga jual harus lebih besar dari harga beli!');
        document.getElementById('harga_jual').focus();
        return false;
    }
    
    return true;
});
</script>