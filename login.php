<?php
session_start();
require_once 'db_config.php';
require_once 'User.php'; 
require_once 'usermanager.php';

$usermanager = new usermanager($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = $usermanager->loginUser($username, $password); 

    if ($user) {
        header("Location: index.php");
        exit();
    } else {
        $error = "Username atau password salah.";
    }
}
?>