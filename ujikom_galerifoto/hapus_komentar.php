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

// Cek apakah ada parameter komentarid yang dikirim
if (isset($_GET['komentarid'])) {
    $komentarid = $_GET['komentarid'];

    // Ambil komentar berdasarkan komentarid
    $stmt = $conn->prepare("SELECT * FROM komentar_foto WHERE komentarid = ?");
    $stmt->execute([$komentarid]);
    $komentar = $stmt->fetch();

    if ($komentar) {
        // Cek apakah userid komentar sama dengan userid session
        if ($komentar['userid'] == $_SESSION['userid']) {
            // Jika sama, hapus komentar dari database
            $stmtDelete = $conn->prepare("DELETE FROM komentar_foto WHERE komentarid = ?");
            if ($stmtDelete->execute([$komentarid])) {
                echo "<script>alert('Komentar berhasil dihapus!'); window.location.href='albums.php';</script>";
            } else {
                echo "<script>alert('Gagal menghapus komentar!'); window.location.href='albums.php';</script>";
            }
        } else {
            echo "<script>alert('Anda tidak memiliki izin untuk menghapus komentar ini!'); window.location.href='albums.php';</script>";
        }
    } else {
        echo "<script>alert('Komentar tidak ditemukan.'); window.location.href='albums.php';</script>";
    }
} else {
    // Jika tidak ada komentarid, redirect kembali
    echo "<script>alert('ID komentar tidak disediakan.'); window.location.href='albums.php';</script>";
}
?>
