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

// Cek apakah fotoid ada di URL
if (isset($_GET['fotoid'])) {
    $fotoid = $_GET['fotoid'];
    
    // Ambil data foto dari database
    $stmt = $conn->prepare("SELECT * FROM foto WHERE fotoid = ?");
    $stmt->execute([$fotoid]);
    $foto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Cek jika foto ditemukan
    if (!$foto) {
        die("Foto tidak ditemukan.");
    }
} else {
    die("ID foto tidak diberikan.");
}

// Proses form edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judulfoto = $_POST['judulfoto'];
    $deskripsifoto = $_POST['deskripsifoto'];
    $nama_album = $_POST['nama_album']; // Nama Album
    $deskripsi_album = $_POST['deskripsi_album']; // Deskripsi Album

    // Menangani penggantian foto
    if (isset($_FILES['fotofile']) && $_FILES['fotofile']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['fotofile']['tmp_name'];
        $fileName = $_FILES['fotofile']['name'];
        $fileDestination = 'uploads/' . $fileName; // Pastikan folder 'uploads' ada dan memiliki izin

        // Pindahkan file ke direktori tujuan
        move_uploaded_file($fileTmpPath, $fileDestination);

        // Update foto di database dengan path file baru dan deskripsi album
        $updateStmt = $conn->prepare("UPDATE foto SET judulfoto = ?, deskripsifoto = ?, lokasifile = ?, albumid = (SELECT albumid FROM album WHERE nama_album = ?) WHERE fotoid = ?");
        if ($updateStmt->execute([$judulfoto, $deskripsifoto, $fileDestination, $nama_album, $fotoid])) {
            // Update deskripsi album
            $updateAlbumStmt = $conn->prepare("UPDATE album SET deskripsi = ? WHERE nama_album = ?");
            $updateAlbumStmt->execute([$deskripsi_album, $nama_album]);
            echo "Foto dan deskripsi album berhasil diperbarui.";
            header("Location: albums.php");
            exit;
        } else {
            echo "Gagal memperbarui foto.";
        }
    } else {
        // Update tanpa mengganti foto
        $updateStmt = $conn->prepare("UPDATE foto SET judulfoto = ?, deskripsifoto = ?, albumid = (SELECT albumid FROM album WHERE nama_album = ?) WHERE fotoid = ?");
        if ($updateStmt->execute([$judulfoto, $deskripsifoto, $nama_album, $fotoid])) {
            // Update deskripsi album
            $updateAlbumStmt = $conn->prepare("UPDATE album SET deskripsi = ? WHERE nama_album = ?");
            $updateAlbumStmt->execute([$deskripsi_album, $nama_album]);
            echo "Foto dan deskripsi album berhasil diperbarui.";
            header("Location: albums.php");
            exit;
        } else {
            echo "Gagal memperbarui foto.";
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
    <title>Edit Foto - Galeri Foto</title>
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
            <?php if (isset($_SESSION['loggedInUser'])): ?>
                <a class="btn btn-outline-light me-2" href="logout.php">Logout</a>
            <?php else: ?>
                <a class="btn btn-outline-light me-2" href="login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container mt-4 form-container">
    <h2>Edit Foto</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="judulfoto" class="form-label">Judul Foto</label>
            <input type="text" class="form-control" id="judulfoto" name="judulfoto" value="<?php echo htmlspecialchars($foto['judulfoto']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="deskripsifoto" class="form-label">Deskripsi Foto</label>
            <textarea class="form-control" id="deskripsifoto" name="deskripsifoto" rows="3" required><?php echo htmlspecialchars($foto['deskripsifoto']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="fotofile" class="form-label">Ganti Foto</label>
            <input type="file" class="form-control" id="fotofile" name="fotofile" accept="image/*">
            <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengganti foto.</small>
        </div>
        <div class="mb-3">
            <label for="nama_album" class="form-label">Nama Album</label>
            <input type="text" class="form-control" id="nama_album" name="nama_album" required>
        </div>
        <div class="mb-3">
            <label for="deskripsi_album" class="form-label">Deskripsi Album</label>
            <textarea class="form-control" id="deskripsi_album" name="deskripsi_album" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Perbarui Foto</button>
        <a href="albums.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

<footer class="text-center">
    <div class="container">
        <p>&copy; 2024 Galeri Foto. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
