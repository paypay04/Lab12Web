<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek login
if (!isset($_SESSION['user'])) {
    header('Location: /lab13_14_vivi/modules/auth/login');
    exit;
}

// include class Barang
include_once __DIR__ . "/../../class/Barang.php";

// instance Barang
$barangObj = new Barang();

// ============================
// SEARCH + PAGINATION PARAMS
// ============================
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) $page = 1;

$per_page = 3; // silakan ubah (mis. 5 / 10 / 15)

// Total data (sesuai search)
$total_rows = $barangObj->countBarang($q);
$num_page = (int) ceil($total_rows / $per_page);
if ($num_page < 1) $num_page = 1;

// Validasi page agar tidak lewat batas
if ($page > $num_page) $page = $num_page;

$offset = ($page - 1) * $per_page;

// Data list (sesuai pagination + search)
$barangList = $barangObj->getBarangPaged($per_page, $offset, $q);

// base url (untuk gambar)
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/lab13_14_vivi/';

// route halaman ini (untuk form & link pagination)
$route_index = '/lab13_14_vivi/user/index';

// fungsi badge stok
function getStockBadge($stok) {
    if ($stok >= 10) {
        return 'stock-high';
    } elseif ($stok >= 5) {
        return 'stock-medium';
    } else {
        return 'stock-low';
    }
}

// helper: bangun query string untuk pagination (agar q & success tetap kebawa)
function buildQuery(array $extra = []) {
    $params = $_GET;
    foreach ($extra as $k => $v) {
        $params[$k] = $v;
    }
    // rapikan: hilangkan param kosong
    foreach ($params as $k => $v) {
        if ($v === '' || $v === null) unset($params[$k]);
    }
    return http_build_query($params);
}
?>

<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/style.css">

<div class="main-content">
    <div class="card">

        <div class="card-header">
            <h1><i class="fas fa-boxes"></i> Data Barang</h1>

            <?php if ($_SESSION['user']['role'] == 'admin'): ?>
                <a href="/lab13_14_vivi/user/tambah" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Barang Baru
                </a>
            <?php endif; ?>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php
                $msg = [
                    'tambah' => 'Data berhasil ditambahkan!',
                    'ubah' => 'Data berhasil diubah!',
                    'hapus' => 'Data berhasil dihapus!'
                ];
                echo $msg[$_GET['success']] ?? 'Operasi berhasil!';
                ?>
            </div>
        <?php endif; ?>

        <!-- SEARCH FORM (sesuai Praktikum 14): taruh setelah tombol tambah dan sebelum tabel -->
        <div class="search-bar" style="margin: 12px 0 16px;">
            <form method="GET" action="<?= $route_index; ?>" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                <input
                    type="text"
                    name="q"
                    placeholder="Cari nama barang..."
                    value="<?= htmlspecialchars($q); ?>"
                    style="padding:10px 12px; border:1px solid #ddd; border-radius:8px; min-width:260px;"
                />
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Cari
                </button>

                <?php if ($q !== ''): ?>
                    <a href="<?= $route_index; ?>" class="btn" style="border:1px solid #ddd;">
                        Reset
                    </a>
                    <span style="color:#666;">
                        Menampilkan hasil untuk: <strong><?= htmlspecialchars($q); ?></strong>
                        (<?= (int)$total_rows; ?> data)
                    </span>
                <?php else: ?>
                    <span style="color:#666;">
                        Total: <strong><?= (int)$total_rows; ?></strong> data
                    </span>
                <?php endif; ?>
            </form>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <?php if ($_SESSION['user']['role'] == 'admin'): ?>
                            <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>

                <?php if (!empty($barangList)) : ?>
                    <?php foreach ($barangList as $row) : ?>

                        <?php
                        $gambar = $row->gambar;
                        $path_gambar = $_SERVER['DOCUMENT_ROOT'] . '/lab13_14_vivi/assets/gambar/' . $gambar;
                        $gambar_ada = ($gambar && file_exists($path_gambar));
                        ?>

                        <tr>
                            <td>
                                <?php if ($gambar_ada) : ?>
                                    <img src="<?= $base_url . 'assets/gambar/' . $row->gambar ?>"
                                         width="60" height="60" alt="<?= htmlspecialchars($row->nama) ?>"
                                         onerror="this.onerror=null; this.src='<?= $base_url ?>assets/gambar/default.jpg';">
                                <?php else : ?>
                                    <div class="image-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td><?= htmlspecialchars($row->nama); ?></td>
                            <td><?= htmlspecialchars($row->kategori); ?></td>
                            <td>Rp <?= number_format($row->harga_beli, 0, ',', '.'); ?></td>
                            <td>Rp <?= number_format($row->harga_jual, 0, ',', '.'); ?></td>

                            <td>
                                <span class="stock-badge <?= getStockBadge($row->stok); ?>">
                                    <?= (int)$row->stok; ?>
                                </span>
                            </td>

                            <?php if ($_SESSION['user']['role'] == 'admin'): ?>
                            <td class="action-buttons">
                                <a href="/lab13_14_vivi/user/edit?id=<?= (int)$row->id_barang; ?>"
                                   class="btn-action btn-edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="/lab13_14_vivi/user/delete?id=<?= (int)$row->id_barang; ?>"
                                   class="btn-action btn-delete"
                                   onclick="return confirm('Yakin ingin menghapus barang <?= htmlspecialchars($row->nama) ?>?')"
                                   title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                            <?php endif; ?>
                        </tr>

                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="<?= $_SESSION['user']['role'] == 'admin' ? '7' : '6' ?>" style="text-align:center; padding: 20px;">
                            <div class="empty-state">
                                <i class="fas fa-box-open" style="font-size: 48px; color: #ccc; margin-bottom: 10px;"></i>

                                <?php if ($q !== ''): ?>
                                    <p>Data dengan kata kunci <strong><?= htmlspecialchars($q) ?></strong> tidak ditemukan.</p>
                                    <a href="<?= $route_index; ?>" class="btn btn-primary">
                                        Lihat Semua Data
                                    </a>
                                <?php else: ?>
                                    <p>Belum ada data barang</p>
                                    <?php if ($_SESSION['user']['role'] == 'admin'): ?>
                                        <a href="/lab13_14_vivi/user/tambah" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Tambah Barang Pertama
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>

                </tbody>
            </table>
        </div>

        <!-- PAGINATION (sesuai Praktikum 13): taruh setelah tabel -->
        <?php if ($num_page > 1): ?>
            <div class="pagination-wrap" style="margin-top: 16px; display:flex; justify-content:center;">
                <ul class="pagination" style="list-style:none; display:flex; gap:6px; padding:0; margin:0;">
                    <!-- Previous -->
                    <?php if ($page > 1): ?>
                        <li>
                            <a href="<?= $route_index . '?' . buildQuery(['page' => $page - 1]); ?>">&laquo; Prev</a>
                        </li>
                    <?php else: ?>
                        <li><span class="disabled">&laquo; Prev</span></li>
                    <?php endif; ?>

                    <!-- Numbered pages -->
                    <?php for ($i = 1; $i <= $num_page; $i++): ?>
                        <?php if ($i == $page): ?>
                            <li><span class="active"><?= $i; ?></span></li>
                        <?php else: ?>
                            <li>
                                <a href="<?= $route_index . '?' . buildQuery(['page' => $i]); ?>"><?= $i; ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <!-- Next -->
                    <?php if ($page < $num_page): ?>
                        <li>
                            <a href="<?= $route_index . '?' . buildQuery(['page' => $page + 1]); ?>">Next &raquo;</a>
                        </li>
                    <?php else: ?>
                        <li><span class="disabled">Next &raquo;</span></li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle image errors
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        img.addEventListener('error', function() {
            this.src = '<?= $base_url ?>assets/images/default.jpg';
            this.alt = 'Gambar tidak tersedia';
        });
    });

    // Styling untuk badge stok
    const badges = document.querySelectorAll('.stock-badge');
    badges.forEach(badge => {
        const stok = parseInt(badge.textContent);
        if (stok >= 10) {
            badge.style.backgroundColor = '#d4edda';
            badge.style.color = '#155724';
        } else if (stok >= 5) {
            badge.style.backgroundColor = '#fff3cd';
            badge.style.color = '#856404';
        } else {
            badge.style.backgroundColor = '#f8d7da';
            badge.style.color = '#721c24';
        }
    });
});
</script>
