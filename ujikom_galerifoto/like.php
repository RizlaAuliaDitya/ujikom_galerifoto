<?php
session_start();
$host = 'localhost';
$dbname = 'ujikom_galerifoto';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Pastikan userid ada dalam session
if (!isset($_SESSION['userid'])) {
    die("Anda harus login untuk memberi like.");
}

if (isset($_POST['like'])) {
    $fotoid = $_POST['fotoid'];
    $userid = $_SESSION['userid']; // Ambil userid dari session

    // Cek apakah user sudah like foto ini
    $stmt = $conn->prepare("SELECT * FROM likefoto WHERE fotoid = ? AND userid = ?");
    $stmt->execute([$fotoid, $userid]);

    if ($stmt->rowCount() == 0) {
        // User belum like, tambahkan like
        $stmt = $conn->prepare("INSERT INTO likefoto (fotoid, userid, tanggal_like) VALUES (?, ?, NOW())");
        $stmt->execute([$fotoid, $userid]);
    } else {
        // User sudah like, hapus like
        $stmt = $conn->prepare("DELETE FROM likefoto WHERE fotoid = ? AND userid = ?");
        $stmt->execute([$fotoid, $userid]);
    }
    header("Location: albums.php"); // Redirect ke halaman album
    exit();
}
?>
