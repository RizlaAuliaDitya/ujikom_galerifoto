<?php
session_start();
require 'db.php'; // Pastikan Anda menyertakan file koneksi database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $albumid = $_POST['albumid'];
    $judulfoto = $_POST['judulfoto'];
    $deskripsifoto = $_POST['deskripsifoto'];
    $userid = $_SESSION['userid'];

    // Proses upload file
    $targetDir = "uploads/"; // Folder untuk menyimpan file
    $fileName = basename($_FILES["file"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    // Validasi dan upload file
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
        // Masukkan data foto ke database
        $stmt = $conn->prepare("INSERT INTO foto (judulfoto, deskripsifoto, lokasifile, albumid, userid) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$judulfoto, $deskripsifoto, $targetFilePath, $albumid, $userid])) {
            echo "<script>alert('Foto berhasil ditambahkan!'); window.location.href='albums.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan foto ke database.');</script>";
        }
    } else {
        echo "<script>alert('Gagal mengupload foto.');</script>";
    }
}
?>
