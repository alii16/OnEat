<?php
$host = 'localhost';
$dbname = 'restoran_online2';
$username = 'root'; // Sesuaikan dengan username Anda
$password = ''; // Sesuaikan dengan password Anda

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}