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
?>
