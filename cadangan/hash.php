<?php
// Password yang ingin di-hash
$password = 'user123';

// Menghasilkan hash password menggunakan algoritma default (bcrypt)
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Menampilkan hash password
echo "Password asli: " . $password . "<br>";
echo "Hash password: " . $passwordHash;
