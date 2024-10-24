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
    die("Anda harus login untuk memberikan komentar.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fotoid = $_POST['fotoid'];
    $userid = $_SESSION['userid']; // Ambil userid dari session
    $isikomentar = $_POST['isikomentar'];

    // Masukkan komentar ke dalam tabel komentar_foto
    $stmt = $conn->prepare("INSERT INTO komentar_foto (fotoid, userid, isikomentar, tanggalkomentar) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$fotoid, $userid, $isikomentar]);

    header("Location: albums.php"); // Redirect ke halaman album
    exit();
}
