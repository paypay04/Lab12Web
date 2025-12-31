<?php
// modules/auth/login.php

// START SESSION jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . '/');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi!";
    } else {
        // Load class User
        if (file_exists(__DIR__ . '/../../class/User.php')) {
            include_once __DIR__ . '/../../class/User.php';
            
            // PERBAIKAN DI SINI: Gunakan objek dengan benar
            $userObj = new User($username, $password);
            
            if ($userObj->login()) {
                // Dapatkan data user sebagai array/properti
                $userData = $userObj->getUserData(); // Asumsi ada method getUserData()
                // ATAU jika class User punya properti public:
                // $userData = [
                //     'id_user' => $userObj->id_user,
                //     'username' => $userObj->username
                // ];
                
                // Tentukan role berdasarkan username
                $admin_users = ['admin'];
                $role = in_array($username, $admin_users) ? 'admin' : 'user';
                
                // Simpan ke session
                $_SESSION['user'] = [
                    'id_user' => $userData['id_user'] ?? $userObj->id_user ?? 0,
                    'username' => $username,
                    'role' => $role // PERBAIKAN: Gunakan $role, bukan 'user'
                ];
                
                header('Location: ' . BASE_URL . '/' . $redirect);
                exit;
            } else {
                $error = "Username atau password salah!";
            }
        } else {
            $error = "System error: User class not found!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .login-container h2 {
            text-align: center;
            color: #7b4bff;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: #7b4bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        
        .btn-login:hover {
            background: #5a2fd4;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .demo {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2><i class="fas fa-sign-in-alt"></i> Login</h2>
        
        <?php if ($error): ?>
            <div class="error">
                <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Username</label>
                <input type="text" name="username" class="form-control" 
                       placeholder="Username" required>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" class="form-control" 
                       placeholder="Password" required>
            </div>
            
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
    </div>
</body>
</html>