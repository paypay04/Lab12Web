<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek login
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /lab13_14_vivi/modules/auth/login.php');
    exit;
}

// Include class yang diperlukan
require_once __DIR__ . '/../../class/Database.php';
require_once __DIR__ . '/../../class/Barang.php';

$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/lab13_14_vivi/';

// Inisialisasi Barang
$barang = new Barang();

// Validasi ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: ' . $base_url . 'index.php?error=noid');
    exit;
}

$id_barang = $_GET['id'];

// Ambil data barang (PERBAIKAN: Method ini harus ada di Barang.php)
$barang_data = null;
// Coba ambil sebagai array dulu
if (method_exists($barang, 'getBarangById')) {
    $result = $barang->getBarangById($id_barang);
    // Jika result adalah object Barang, konversi ke array
    if (is_object($result)) {
        $barang_data = [
            'id_barang' => $result->id_barang,
            'nama' => $result->nama,
            'kategori' => $result->kategori,
            'harga_beli' => $result->harga_beli,
            'harga_jual' => $result->harga_jual,
            'stok' => $result->stok,
            'gambar' => $result->gambar
        ];
    } else {
        $barang_data = $result; // asumsi sudah array
    }
}

if (!$barang_data) {
    // Fallback: Query langsung ke database
    $db = new Database();
    $result = $db->get('data_barang', "id_barang = '{$id_barang}'");
    $barang_data = !empty($result) ? $result[0] : null;
    
    if (!$barang_data) {
        header('Location: ' . $base_url . 'index.php?error=notfound');
        exit;
    }
}

// Pesan error
$error = '';
$success = '';

// Proses form jika ada POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $kategori = $_POST['kategori'] ?? 'Elektronik';
    $harga_beli = $_POST['harga_beli'] ?? 0;
    $harga_jual = $_POST['harga_jual'] ?? 0;
    $stok = $_POST['stok'] ?? 0;
    
    // Handle gambar upload
    $gambar = $barang_data['gambar']; // Keep existing image
    
    if (isset($_FILES['file_gambar']) && $_FILES['file_gambar']['error'] == 0) {
        $file_gambar = $_FILES['file_gambar'];
        $filename = str_replace(' ', '_', $file_gambar['name']);
        $destination = $_SERVER['DOCUMENT_ROOT'] . '/lab13_14_vivi/assets/gambar/' . $filename;
        
        if (move_uploaded_file($file_gambar['tmp_name'], $destination)) {
            $gambar = $filename;
        }
    }
    
    // Validasi input
    if (empty($nama)) {
        $error = 'Nama barang harus diisi!';
    } elseif ($harga_beli <= 0) {
        $error = 'Harga beli harus lebih dari 0!';
    } elseif ($harga_jual <= 0) {
        $error = 'Harga jual harus lebih dari 0!';
    } elseif ($stok < 0) {
        $error = 'Stok tidak boleh negatif!';
    } else {
        // Update data langsung ke database
        $update_data = [
            'nama' => $nama,
            'kategori' => $kategori,
            'harga_beli' => $harga_beli,
            'harga_jual' => $harga_jual,
            'stok' => $stok,
            'gambar' => $gambar
        ];
        
        $db = new Database();
        if ($db->update('data_barang', $update_data, "id_barang = '{$id_barang}'")) {
            header('Location: ' . $base_url . 'index.php?success=ubah');
            exit;
        } else {
            $error = 'Gagal mengupdate barang!';
        }
    }
}

// Helper function untuk select option
function is_select($var, $val) {
    if ($var == $val) return 'selected="selected"';
    return '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Barang - Sistem Inventaris</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?= $base_url ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="main-container">
        <div class="card">
            <!-- Card Header -->
            <div class="card-header">
                <h1>
                    <i class="fas fa-edit"></i>
                    Ubah Barang
                </h1>
                <a href="<?= $base_url ?>user/index" class="btn btn-warning">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Daftar
                </a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error" style="
                    background: linear-gradient(135deg, #f8d7da, #f5c6cb);
                    color: #721c24;
                    padding: 15px 20px;
                    border-radius: 12px;
                    margin-bottom: 25px;
                    border-left: 5px solid #ff6b6b;
                    display: flex;
                    align-items: center;
                    gap: 12px;
                ">
                    <i class="fas fa-exclamation-triangle" style="font-size: 20px;"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <!-- Form Container -->
            <div class="form-container">
                <form method="post" action="" enctype="multipart/form-data">
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
                                       value="<?= htmlspecialchars($barang_data['nama']) ?>" 
                                       placeholder="Masukkan nama barang" required>
                            </div>

                            <div class="form-group">
                                <label for="kategori">
                                    <i class="fas fa-folder"></i>
                                    Kategori
                                </label>
                                <select id="kategori" name="kategori" class="form-control" required>
                                    <option value="Komputer" <?= is_select('Komputer', $barang_data['kategori']) ?>>Komputer</option>
                                    <option value="Elektronik" <?= is_select('Elektronik', $barang_data['kategori']) ?>>Elektronik</option>
                                    <option value="Hand Phone" <?= is_select('Hand Phone', $barang_data['kategori']) ?>>Hand Phone</option>
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
                                <input type="number" id="harga_beli" name="harga_beli" class="form-control" 
                                       value="<?= $barang_data['harga_beli'] ?>" 
                                       placeholder="Masukkan harga beli" required>
                            </div>

                            <div class="form-group">
                                <label for="harga_jual">
                                    <i class="fas fa-tag"></i>
                                    Harga Jual
                                </label>
                                <input type="number" id="harga_jual" name="harga_jual" class="form-control" 
                                       value="<?= $barang_data['harga_jual'] ?>" 
                                       placeholder="Masukkan harga jual" required>
                            </div>

                            <div class="form-group">
                                <label for="stok">
                                    <i class="fas fa-boxes"></i>
                                    Stok
                                </label>
                                <input type="number" id="stok" name="stok" class="form-control" 
                                       value="<?= $barang_data['stok'] ?>" 
                                       placeholder="Masukkan jumlah stok" required>
                            </div>
                        </div>

                        <!-- Section 3: Gambar -->
                        <div class="form-section" style="grid-column: span 2;">
                            <div class="section-header">
                                <h3>
                                    <div class="section-icon">
                                        <i class="fas fa-image"></i>
                                    </div>
                                    Gambar Barang
                                </h3>
                            </div>

                            <?php if($barang_data['gambar']): ?>
                            <div class="current-image">
                                <p>
                                    <i class="fas fa-image"></i> Gambar Saat Ini:
                                </p>
                                <img src="<?= $base_url ?>assets/gambar/<?= $barang_data['gambar'] ?>" 
                                     alt="<?= htmlspecialchars($barang_data['nama']) ?>" 
                                     style="max-width: 200px; border-radius: 12px; border: 3px solid #e9e1ff;">
                                <p style="color: #7b4bff; margin-top: 10px; font-size: 14px;">
                                    <?= $barang_data['gambar'] ?>
                                </p>
                            </div>
                            <?php endif; ?>

                            <div class="file-upload-area" onclick="document.getElementById('file_gambar').click()">
                                <div>
                                    <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #7b4bff; margin-bottom: 15px;"></i>
                                    <p>Klik untuk upload gambar baru</p>
                                    <small style="color: #b8a8ff;">
                                        Format yang didukung: JPG, PNG, GIF (Maks: 2MB)
                                    </small>
                                </div>
                                <input type="file" id="file_gambar" name="file_gambar" 
                                       accept="gambar/*" style="display: none;" 
                                       onchange="previewImage(this)">
                            </div>

                            <div id="image-preview" style="display: none; text-align: center; margin-top: 20px;">
                                <img id="preview" src="" alt="Preview" style="max-width: 200px; border-radius: 12px; border: 3px solid #e9e1ff;">
                                <br>
                                <button type="button" class="btn-danger" onclick="removePreview()" style="margin-top: 15px; padding: 8px 16px;">
                                    <i class="fas fa-trash"></i> Hapus Preview
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <a href="<?= $base_url ?>index.php" class="btn btn-danger">
                            <i class="fas fa-times"></i>
                            Batal
                        </a>
                        <button type="submit" name="submit" class="btn btn-success">
                            <i class="fas fa-save"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                    
                    <input type="hidden" name="id" value="<?= $barang_data['id_barang'] ?>" />
                </form>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
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
        }
        
        // Auto-hide alerts
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s ease';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 500);
            }, 5000);
        });
    </script>
</body>
</html>