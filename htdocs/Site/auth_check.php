<?php
function checkAuth() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Проверяем наличие cookie если сессия не активна
    if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
        $_SESSION['user_id'] = $_COOKIE['user_id'];
        $_SESSION['username'] = $_COOKIE['username'];
        $_SESSION['email'] = $_COOKIE['email'];
        $_SESSION['role'] = $_COOKIE['role'];
    }
    
    return isset($_SESSION['user_id']);
}

function requireAuth() {
    if (!checkAuth()) {
        header("Location: Main.php");
        exit();
    }
}

function checkAdminRole() {
    if (!checkAuth() || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        return false;
    }
    return true;
}

function requireAdminRole() {
    if (!checkAdminRole()) {
        header("Location: Main.php");
        exit();
    }
}
?> 