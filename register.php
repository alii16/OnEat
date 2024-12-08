<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = $_POST['email'];
    $level = $_POST['level'];
    $image_user = ''; // Set nilai default atau sesuaikan dengan nilai yang diinginkan

    try {
        // Siapkan query dengan kolom yang sesuai pada tabel users
        $stmt = $pdo->prepare('INSERT INTO users (username, password, email, level, image_user) VALUES (?, ?, ?, ?, ?)');

        // Eksekusi query dengan data yang dimasukkan pengguna
        if ($stmt->execute([$username, $password, $email, $level, $image_user])) {
            echo "<script>alert('Registration successful'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Registration failed. Please try again.');</script>";
        }
    } catch (PDOException $e) {
        // Tangkap dan tampilkan pesan error
        echo "<script>alert('Kesalahan: " . htmlspecialchars($e->getMessage()) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>Form Registrasi</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" />
    <link rel="shortcut icon" href="img/icon.png" type="image/x-icon">
</head>

<body>
    <div class="container">
        <header>Daftar</header>
        <form method="post">
            <div class="input-field">
                <span class="fa fa-user"></span>
                <input type="text" name="username" required>
                <label>Buat Username</label>
            </div>
            <div class="input-field">
                <span class="fa fa-lock"></span>
                <input class="pswrd" type="password" name="password" required>
                <label>Buat Password</label>
            </div>
            <div class="input-field">
                <span class="fa fa-envelope"></span>
                <input class="pswrd" type="email" name="email" required>
                <label>Masukkan Email</label>
            </div>
            <div class="input-field">
                <span class="fa fa-list"></span>
                <select class="pswrd" id="level" name="level" required>
                    <option value="" disabled selected>Pilih Role</option>
                    <option value="user">Pengguna</option>
                    <option value="resto">Restoran</option>
                </select>
            </div>
            <div class="button">
                <div class="inner"></div>
                <button type="submit">DAFTAR</button>
            </div>
        </form>
        <div class="auth">atau daftar dengan</div>
        <div class="links">
            <div class="facebook">
                <i class="fab fa-facebook-square"><span>Facebook</span></i>
            </div>
            <div class="google">
                <i class="fab fa-google-plus-g"><span>Google</span></i>
            </div>
        </div>
        <div class="signup">
            Sudah punya akun? <a href="login.php">Login</a>
        </div>
    </div>
    <script>
        var input = document.querySelector('.pswrd');
        var show = document.querySelector('.show');
        if (show) {
            show.addEventListener('click', active);
        }
        function active() {
            if (input.type === "password") {
                input.type = "text";
                show.style.color = "#1DA1F2";
                show.textContent = "HIDE";
            } else {
                input.type = "password";
                show.textContent = "SHOW";
                show.style.color = "#111";
            }
        }
    </script>
</body>

</html>