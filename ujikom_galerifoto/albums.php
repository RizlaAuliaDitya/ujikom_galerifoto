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

// Ambil semua foto dari tabel foto
$stmt = $conn->prepare("SELECT f.*, COUNT(l.likeid) as jumlah_like FROM foto f LEFT JOIN likefoto l ON f.fotoid = l.fotoid GROUP BY f.fotoid");
$stmt->execute();
$fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Albums - Galeri Foto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

        .card {
            margin-bottom: 20px;
        }
        .heart-icon {
            color: gray; /* Default color */
        }
        .heart-icon.liked {
            color: red; /* Color when liked */
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

    <div class="container mt-4">
        <h2 class="text-center">Albums</h2>
        <div class="row">
            <?php foreach ($fotos as $foto): ?>
                <div class="col-md-4">
                    <div class="card">
                        <img src="<?php echo $foto['lokasifile']; ?>" class="card-img-top" alt="<?php echo $foto['judulfoto']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $foto['judulfoto']; ?></h5>
                            <p class="card-text"><?php echo $foto['deskripsifoto']; ?></p>
                            <p class="card-text"><small class="text-muted">Tanggal Unggah: <?php echo $foto['tanggalunggah']; ?></small></p>
                            <p>Likes: <?php echo $foto['jumlah_like']; ?></p>

                            <!-- Link Edit Foto -->
                            <a href="edit_foto.php?fotoid=<?php echo $foto['fotoid']; ?>" class="btn btn-warning">Edit Foto</a>

                            <form method="POST" action="like.php">
                                <input type="hidden" name="fotoid" value="<?php echo $foto['fotoid']; ?>">
                                <button type="submit" name="like" class="btn">
                                    <i class="fas fa-heart heart-icon" style="color:red" <?php
                                    // Cek jika pengguna sudah memberi like
                                    if (isset($_SESSION['loggedInUser'])) {
                                        $userid = $_SESSION['loggedInUser'];
                                        $likeCheckStmt = $conn->prepare("SELECT * FROM likefoto WHERE fotoid = ? AND userid = ?");
                                        $likeCheckStmt->execute([$foto['fotoid'], $userid]);
                                        if ($likeCheckStmt->fetch()) {
                                            echo 'liked'; // Tambahkan kelas liked jika sudah like
                                        }
                                    }
                                    ?>"></i>
                                </button>
                            </form>

                            <!-- Tombol Hapus Foto -->
                            <?php if (isset($_SESSION['loggedInUser'])): ?>
                                <a href="hapus_foto.php?fotoid=<?php echo $foto['fotoid']; ?>" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus foto ini?');">Hapus Foto</a>
                            <?php endif; ?>

                            <hr>
                            <!-- Komentar -->
                            <h6>Komentar:</h6>
                            <ul class="list-unstyled">
                                <?php
                                // Ambil komentar untuk foto ini
                                $komentarStmt = $conn->prepare("SELECT * FROM komentar_foto WHERE fotoid = ?");
                                $komentarStmt->execute([$foto['fotoid']]);
                                $komentarList = $komentarStmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($komentarList as $komentar) {
                                    echo "<li><strong>" . htmlspecialchars($komentar['userid']) . ":</strong> " . htmlspecialchars($komentar['isikomentar']) . " <small class='text-muted'>" . $komentar['tanggalkomentar'] . "</small>";

                                    // Tombol Hapus Komentar
                                    if (isset($_SESSION['loggedInUser'])): ?>
                                        <a href="hapus_komentar.php?komentarid=<?php echo $komentar['komentarid']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus komentar ini?');">Hapus</a>
                                    <?php endif; 

                                    echo "</li>";
                                }
                                ?>
                            </ul>
                            <?php if (isset($_SESSION['loggedInUser'])): ?>
                                <form method="POST" action="komentar.php">
                                    <input type="hidden" name="fotoid" value="<?php echo $foto['fotoid']; ?>">
                                    <div class="mb-3">
                                        <input type="text" class="form-control" name="isikomentar" placeholder="Tulis komentar..." required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Kirim</button>
                                </form>
                            <?php else: ?>
                                <p class="text-muted">Silakan <a href="login.php">login</a> untuk berkomentar.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer class="text-center">
        <div class="container">
            <p>&copy; 2024 Galeri Foto. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
