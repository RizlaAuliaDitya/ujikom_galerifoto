<?php
session_start(); // Panggil session_start() di awal
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
    $judulfoto = $_POST['judulfoto'];
    $deskripsifoto = $_POST['deskripsifoto'];
    $lokasifile = ''; // Tempat untuk menyimpan lokasi file foto
    $tanggalunggah = date('Y-m-d H:i:s'); // Tanggal upload
    
    // Cek apakah userid tersedia di session
    if (!isset($_SESSION['userid'])) {
        echo "<script>alert('Anda harus login terlebih dahulu!'); window.location.href='login.php';</script>";
        exit;
    }

    $userid = $_SESSION['userid']; // Ambil userid dari session
    $albumid = $_POST['albumid']; // Pastikan albumid dikirim dari form
    $nama_album = $_POST['nama_album']; // Tambahkan nama album dari form
    $deskripsi_album = $_POST['deskripsi_album']; // Tambahkan deskripsi album dari form

    // Upload file
    if (isset($_FILES['photoInput'])) {
        $targetDir = "uploads/"; // Folder untuk menyimpan foto

        // Cek apakah folder uploads ada, jika tidak ada, buat folder
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $targetFile = $targetDir . basename($_FILES["photoInput"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Cek apakah gambar adalah gambar nyata atau palsu
        $check = getimagesize($_FILES["photoInput"]["tmp_name"]);
        if ($check === false) {
            echo "<script>alert('File bukan gambar.');</script>";
            $uploadOk = 0;
        }

        // Cek ukuran file
        if ($_FILES["photoInput"]["size"] > 500000) {
            echo "<script>alert('Maaf, ukuran file terlalu besar.');</script>";
            $uploadOk = 0;
        }

        // Biarkan hanya file gambar yang diizinkan
        if(!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            echo "<script>alert('Maaf, hanya file JPG, JPEG, PNG & GIF yang diizinkan.');</script>";
            $uploadOk = 0;
        }

        // Jika semua oke, coba untuk upload file
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["photoInput"]["tmp_name"], $targetFile)) {
                $lokasifile = $targetFile;

                // Siapkan pernyataan SQL untuk menyimpan data foto
                $stmt = $conn->prepare("INSERT INTO foto (judulfoto, deskripsifoto, tanggalunggah, lokasifile, albumid, userid) VALUES (?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([$judulfoto, $deskripsifoto, $tanggalunggah, $lokasifile, $albumid, $userid])) {
                    // Menyimpan data album ke tabel album
                    $stmtAlbum = $conn->prepare("INSERT INTO album (albumid, nama_album, deskripsi, tanggaldibuat, userid) VALUES (?, ?, ?, ?, ?)");
                    if ($stmtAlbum->execute([$albumid, $nama_album, $deskripsi_album, $tanggalunggah, $userid])) {
                        echo "<script>alert('Foto dan album berhasil ditambahkan!'); window.location.href='albums.php';</script>";
                    } else {
                        echo "<script>alert('Gagal menambahkan album!');</script>";
                    }
                } else {
                    echo "<script>alert('Gagal menambahkan foto!');</script>";
                }
            } else {
                echo "<script>alert('Maaf, terjadi kesalahan saat mengupload file.');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Tambah Foto - Galeri Foto</title>
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
                
                <!-- Tombol Login/Logout Dinamis -->
                <?php if (isset($_SESSION['loggedInUser'])): ?>
                    <a class="btn btn-outline-light me-2" href="logout.php">Logout</a>
                <?php else: ?>
                    <a class="btn btn-outline-light me-2" href="login.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="form-container mt-4">
        <h2 class="text-center">Tambah Foto</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="judulfoto" class="form-label">Judul Foto</label>
                <input type="text" class="form-control" id="judulfoto" name="judulfoto" required>
            </div>
            <div class="mb-3">
                <label for="photoInput" class="form-label">Pilih Foto</label>
                <input type="file" class="form-control" id="photoInput" name="photoInput" accept="image/*" required>
            </div>
            <div class="mb-3">
                <label for="deskripsifoto" class="form-label">Deskripsi Foto</label>
                <textarea class="form-control" id="deskripsifoto" name="deskripsifoto" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="nama_album" class="form-label">Nama Album</label>
                <input type="text" class="form-control" id="nama_album" name="nama_album" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi_album" class="form-label">Deskripsi Album</label>
                <textarea class="form-control" id="deskripsi_album" name="deskripsi_album" rows="3" required></textarea>
            </div>
            <input type="hidden" name="albumid" value="1"> <!-- Ganti dengan ID album yang sesuai -->
            <button type="submit" class="btn btn-primary w-100">Tambahkan Foto</button>
        </form>
    </div>

    <footer class="text-center">
        <div class="container">
            <p>&copy; 2024 Galeri Foto. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
