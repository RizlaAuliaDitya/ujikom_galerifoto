<?php
session_start(); // Pastikan ini dipanggil di awal
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login - Galeri Foto</title>
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
        <h2 class="text-center">Login ke Galeri Foto</h2>
        <form id="loginForm" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <div class="mt-3 text-center">
            <a href="register.php">Belum punya akun? Daftar di sini.</a>
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
            const isLoggedIn = <?php echo json_encode(isset($_SESSION['loggedInUser'])); ?>;
            if (isLoggedIn) {
                handleLogout();
            } else {
                window.location.href = 'login.php';
            }
        }

        function handleLogout() {
            window.location.href = 'logout.php'; // Redirect ke halaman logout
        }
    </script>

</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Siapkan pernyataan SQL untuk memeriksa pengguna
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Set session variable untuk pengguna yang login
        $_SESSION['userid'] = $user['userid']; // Menyimpan userid di session
        $_SESSION['loggedInUser'] = $user['username']; // Menyimpan username di session
        header("Location: home.php"); // Redirect ke halaman home
        exit();
    } else {
        echo "<script>alert('Username atau password salah. Silakan coba lagi.');</script>";
    }
}
?>
