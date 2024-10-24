<?php
session_start(); // Memulai session
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

// Cek apakah id foto diatur
if (isset($_GET['fotoid'])) {
    $fotoid = $_GET['fotoid'];

    // Ambil lokasi file foto dari database sebelum dihapus
    $stmt = $conn->prepare("SELECT lokasifile, userid FROM foto WHERE fotoid = ?");
    $stmt->execute([$fotoid]);
    $foto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($foto) {
        // Cek apakah pengguna yang login adalah pemilik foto
        if ($foto['userid'] == $_SESSION['userid']) {
            $lokasifile = $foto['lokasifile'];

            // Hapus foto dari database
            $stmtDelete = $conn->prepare("DELETE FROM foto WHERE fotoid = ?");
            if ($stmtDelete->execute([$fotoid])) {
                // Hapus file fisik foto
                if (file_exists($lokasifile)) {
                    unlink($lokasifile);
                }
                echo "<script>alert('Foto berhasil dihapus!'); window.location.href='albums.php';</script>";
            } else {
                echo "<script>alert('Gagal menghapus foto dari database.'); window.location.href='albums.php';</script>";
            }
        } else {
            echo "<script>alert('Anda tidak memiliki izin untuk menghapus foto ini!'); window.location.href='albums.php';</script>";
        }
    } else {
        echo "<script>alert('Foto tidak ditemukan.'); window.location.href='albums.php';</script>";
    }
} else {
    echo "<script>alert('ID foto tidak disediakan.'); window.location.href='albums.php';</script>";
}
?>
