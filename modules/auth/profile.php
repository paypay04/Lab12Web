<?php
// modules/auth/profile.php

// Wajib login
if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . '/auth/login');
    exit;
}

$user = $_SESSION['user'];

// (Opsional) proses ganti password - Anda bisa aktifkan jika sudah siap
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST['old_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $new2 = $_POST['confirm_password'] ?? '';

    if ($new === '' || $new2 === '' || $old === '') {
        $_SESSION['error'] = 'Semua field password wajib diisi.';
        header('Location: ' . BASE_URL . '/auth/profile');
        exit;
    }

    if ($new !== $new2) {
        $_SESSION['error'] = 'Konfirmasi password tidak sama.';
        header('Location: ' . BASE_URL . '/auth/profile');
        exit;
    }

    if (strlen($new) < 6) {
        $_SESSION['error'] = 'Password baru minimal 6 karakter.';
        header('Location: ' . BASE_URL . '/auth/profile');
        exit;
    }

    // Kalau Anda sudah punya tabel users & kolom password hash:
    // 1) ambil hash password user dari DB
    // 2) password_verify($old, $hash_db)
    // 3) update password dengan password_hash($new, PASSWORD_DEFAULT)

    // Sementara tampilkan sukses dummy agar UI terlihat bekerja
    $_SESSION['success'] = 'Permintaan ubah password diterima (aktifkan logika DB jika sudah siap).';
    header('Location: ' . BASE_URL . '/auth/profile');
    exit;
}
?>

<style>
/* Minimal styling lokal agar konsisten dengan layout card halaman Anda */
.profile-wrap {
    width: 85%; /* Sama dengan header-container */
    max-width: auto; /* Tanpa batas maksimum seperti header-container */
    margin: 30px auto;
    min-height: 60vh;
    background: #fff;
    border-radius: 18px;
    padding: 26px 28px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.08);
    border: 1px solid rgba(0,0,0,0.04);
}

.profile-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding-bottom: 14px;
    border-bottom: 1px solid rgba(0,0,0,0.06);
    margin-bottom: 18px;
}

.profile-title {
    display: flex;
    align-items: center;
    gap: 12px;
}
.profile-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(128, 90, 213, 0.12);
}
.profile-icon i { font-size: 18px; }

.profile-title h2 {
    margin: 0;
    font-size: 22px;
}
.profile-title p {
    margin: 2px 0 0 0;
    opacity: 0.7;
    font-size: 16px;
}

.btn-soft {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    background: rgba(255, 153, 51, 0.95);
    color: #fff;
    font-weight: 600;
    text-decoration: none;
}

.profile-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
    margin-top: 16px;
}

.profile-card {
    background: rgba(128, 90, 213, 0.04);
    border: 1px solid rgba(128, 90, 213, 0.10);
    border-radius: 16px;
    padding: 18px;
}

.profile-card h3 {
    margin: 0 0 12px 0;
    font-size: 16px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    padding: 10px 0;
    border-bottom: 1px dashed rgba(0,0,0,0.08);
}
.info-row:last-child { border-bottom: none; }

.label {
    opacity: 0.7;
    font-size: 13px;
}
.value {
    font-weight: 700;
    font-size: 14px;
    text-align: right;
}

.form-group {
    margin-top: 12px;
}
.form-group label {
    display: block;
    font-weight: 700;
    font-size: 14px;
    margin-bottom: 8px;
}
.form-group input {
    width: 100%;
    padding: 12px 14px;
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,0.12);
    outline: none;
}
.form-group small {
    display: block;
    margin-top: 6px;
    opacity: 0.7;
    font-size: 12px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 14px;
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    background: #6d43ff;
    color: #fff;
    font-weight: 700;
}

@media (max-width: 900px) {
    .profile-grid { grid-template-columns: 1fr; }
}
</style>

<div class="profile-wrap">
    <div class="profile-head">
        <div class="profile-title">
            <div class="profile-icon">
                <i class="fas fa-user"></i>
            </div>
            <div>
                <h2>Profile</h2>
                <p>Kelola informasi akun dan ubah password Anda.</p>
            </div>
        </div>

        <a class="btn-soft" href="<?= BASE_URL ?>/dashboard">
            <i class="fas fa-arrow-left"></i>
            Kembali ke Dashboard
        </a>
    </div>

    <div class="profile-grid">
        <!-- Kartu Info User -->
        <div class="profile-card">
            <h3><i class="fas fa-id-badge"></i> Informasi Akun</h3>

            <div class="info-row">
                <div class="label">Username</div>
                <div class="value"><?= htmlspecialchars($user['username'] ?? '-') ?></div>
            </div>

            <div class="info-row">
                <div class="label">Role</div>
                <div class="value">
                    <?= (isset($user['role']) && $user['role'] === 'admin') ? 'Administrator' : 'User' ?>
                </div>
            </div>

            <div class="info-row">
                <div class="label">Status</div>
                <div class="value">Aktif</div>
            </div>
        </div>

        <!-- Kartu Form Ubah Password -->
        <div class="profile-card">
            <h3><i class="fas fa-key"></i> Ubah Password</h3>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Password Lama</label>
                    <input type="password" name="old_password" placeholder="Masukkan password lama" required>
                </div>

                <div class="form-group">
                    <label>Password Baru</label>
                    <input type="password" name="new_password" placeholder="Masukkan password baru" required>
                    <small>Minimal 6 karakter.</small>
                </div>

                <div class="form-group">
                    <label>Konfirmasi Password Baru</label>
                    <input type="password" name="confirm_password" placeholder="Ulangi password baru" required>
                </div>

                <div class="form-actions">
                    <button class="btn-primary" type="submit">
                        <i class="fas fa-save"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
