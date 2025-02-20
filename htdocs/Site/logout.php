<?php
session_start();

// Очищаем cookie
setcookie('user_id', '', time() - 3600, '/');
setcookie('username', '', time() - 3600, '/');
setcookie('email', '', time() - 3600, '/');
setcookie('role', '', time() - 3600, '/');

// Очищаем корзину перед выходом
if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
}

session_destroy();
setcookie(session_name(), '', time() - 3600, '/');
header("Location: html/Main.php");
exit();
?> 