<?php 
include 'db.php'; 

// Memulai session jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Home - Galeri Foto</title>
    <!-- Link Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
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

        main {
            flex: 1;
            display: flex; /* Menambahkan flex untuk memusatkan konten */
            align-items: center; /* Memusatkan secara vertikal */
            justify-content: center; /* Memusatkan secara horizontal */
            text-align: center; /* Menambahkan text align center */
            padding: 20px; /* Memberikan padding */
        }
        
        footer {
            background-color: #343a40;
            color: white;
            padding: 10px 0;
        }
        
        .btn-dark-custom {
            background-color: #343a40; /* Warna gelap */
            border-color: #343a40; /* Border dengan warna yang sama */
            color: white; /* Warna tulisan putih */
        }
        
        .btn-dark-custom:hover {
            background-color: #23272b; /* Warna saat hover */
            border-color: #1d2124; /* Border saat hover */
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Galeri Foto</a>
            <div class="d-flex align-items-center ms-3">
                <a class="btn btn-outline-light me-2" href="home.php">Home</a>
                <a class="btn btn-outline-light me-2" href="albums.php">Albums</a>
                <a class="btn btn-outline-light me-2" href="tambah_foto.php">Tambah Foto</a>
                
                <!-- Dynamic Auth Buttons -->
                <?php if (isset($_SESSION['loggedInUser'])): ?>
                    <a class="btn btn-outline-light me-2" href="logout.php">Logout</a>
                <?php else: ?>
                    <a class="btn btn-outline-light me-2" href="login.php">Login</a>
                    <a class="btn btn-outline-light me-2" href="register.php">Register</a>
                <?php endif; ?>

                <form class="d-flex" role="search">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-light" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <main class="container mt-4">
        <div>
            <h2>Galeri Foto Terbaru</h2>
            <p>Di sini kamu bisa menemukan berbagai album foto yang telah diunggah oleh pengguna.</p>
            <a class="btn btn-dark-custom" href="albums.php">Tekan Tombol Ini untuk ke Albums</a>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center">
        <div class="container">
            <p>&copy; 2024 Galeri Foto. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
