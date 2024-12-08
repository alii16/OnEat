<?php
require 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_level'] = $user['level'];

        // Fetch restaurant ID if the user is a restaurant staff
        if ($user['level'] == 'resto') {
            $stmt = $pdo->prepare('SELECT id FROM restaurants WHERE user_id = ?');
            $stmt->execute([$user['id']]);
            $restaurant = $stmt->fetch();
            $_SESSION['restaurant_id'] = $restaurant ? $restaurant['id'] : null;
        }

        header("Location: index.php");
    } else {
        $error_message = "Username atau Password tidak valid!";
    }
}
?>



<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" />
    <link rel="shortcut icon" href="img/icon.png" type="image/x-icon">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <header>Login</header>
        <form method="post">
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            <div class="input-field">
                <span class="fa fa-user"></span>
                <input type="text" name="username" required>
                <label>Username</label>
            </div>
            <div class="input-field">
                <span class="fa fa-lock"></span>
                <input class="pswrd" type="password" name="password" required>
                <label>Password</label>
            </div>
            <div class="button">
                <div class="inner"></div>
                <button type="submit">LOGIN</button>
            </div>
        </form>
        <div class="auth">atau login dengan</div>
        <div class="links">
            <div class="facebook">
                <i class="fab fa-facebook-square"><span>Facebook</span></i>
            </div>
            <div class="google">
                <i class="fab fa-google-plus-g"><span>Google</span></i>
            </div>
        </div>
        <div class="signup">
            Belum punya akun? <a href="register.php">Daftar</a>
        </div>
    </div>
    <script>
        var input = document.querySelector('.pswrd');
        var show = document.querySelector('.show');
        show.addEventListener('click', active);
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
