<?php
session_start();
$host = 'localhost'; // Host database Anda
$dbname = 'ujikom_galerifoto'; // Nama database
$username = 'root'; // Username database Anda
$password = ''; // Password database Anda

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password untuk keamanan
    $email = $_POST['email'];
    $namalengkap = $_POST['namalengkap'];
    $alamat = $_POST['alamat'];

    // Siapkan pernyataan SQL untuk menyimpan data pengguna ke tabel 'user'
    $stmt = $conn->prepare("INSERT INTO user (username, password, email, namalengkap, alamat) VALUES (?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$username, $password, $email, $namalengkap, $alamat])) {
        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Registrasi gagal! Silakan coba lagi.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Registrasi - Galeri Foto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: linear-gradient(135deg, #8bff00, #00c3ff, #b2ff00, #ff007f);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        footer {
            background-color: #343a40;
            color: white;
            padding: 10px 0;
            margin-top: auto;
        }

        .form-container {
            max-width: 600px;
            width: 100%;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            background-color: rgba(255, 255, 255, 0.8); /* Transparan untuk melihat latar belakang */
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Galeri Foto</a>
            <div class="d-flex align-items-center ms-3">
                <a class="btn btn-outline-light me-2" href="home.php">Home</a>
                <a class="btn btn-outline-light me-2" href="albums.php">Albums</a>
                <a class="btn btn-outline-light me-2" href="tambah_foto.php">Tambah Foto</a>
                <button id="auth-button" class="btn btn-outline-light me-2" onclick="handleAuth()"></button>
            </div>
        </div>
    </nav>

    <div class="form-container mt-4">
        <h2 class="text-center">Registrasi ke Galeri Foto</h2>
        <form id="registerForm" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="namalengkap" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="namalengkap" name="namalengkap" required>
            </div>
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea class="form-control" id="alamat" name="alamat" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">Daftar</button>
        </form>
        <div class="mt-3 text-center">
            <a href="login.php">Sudah punya akun? Login di sini.</a>
        </div>
        <div class="mt-2 text-center">
            <a href="home.php">Kembali ke Home</a>
        </div>
    </div>

    <footer class="text-center">
        <div class="container">
            <p>&copy; 2024 Galeri Foto. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function handleAuth() {
            const isLoggedIn = localStorage.getItem('isLoggedIn');
            if (isLoggedIn) {
                handleLogout();
            } else {
                window.location.href = 'login.php';
            }
        }

        function handleLogout() {
            localStorage.removeItem('isLoggedIn');
            updateNavbar();
            window.location.href = 'login.php';
        }

        function updateNavbar() {
            const isLoggedIn = localStorage.getItem('isLoggedIn');
            const authButton = document.getElementById('auth-button');

            if (isLoggedIn) {
                authButton.innerText = 'Logout';
                authButton.onclick = handleLogout;
            } else {
                authButton.innerText = 'Login';
                authButton.onclick = () => window.location.href = 'login.php';
            }
        }

        window.onload = updateNavbar;
    </script>

</body>
</html>
