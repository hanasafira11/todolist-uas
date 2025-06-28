<?php
// signup.php
session_start();
require_once 'usermanager.php';
require_once 'db_config.php';

$usermanager = new usermanager($conn);
$error_message = '';
$success_message = '';

if ($userManager->getCurrentUser() !== null) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $email = trim($_POST['email']);

    if (empty($username) || empty($password) || empty($confirm_password || empty($email))) {
        $error_message = "Semua field harus diisi.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Konfirmasi password tidak cocok.";
    } else {
        $result = $userManager->registerUser($username, $password, $email);
        if ($result['status']) {
            $success_message = $result['message'] . " Silakan <a href='login.php'>Login</a>.";
        } else {
            $error_message = $result['message'];
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-container">
        <h2>Daftar Akun Baru</h2>
        <?php if ($error_message): ?>
            <p class="message error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <p class="message success"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <form action="signup.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit">Daftar</button>
        </form>
        <p>Sudah punya akun? <a href="login.php">Login di sini</a>.</p>
    </div>
</body>
</html>