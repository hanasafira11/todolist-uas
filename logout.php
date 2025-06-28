<?php
// logout.php
session_start();
require_once 'usermanager.php';
require_once 'db_config.php';
$userManager = new UserManager($conn);
$userManager->logoutUser();
?>