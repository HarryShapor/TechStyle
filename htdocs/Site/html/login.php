<?php
// Настройки подключения к базе данных
$host = 'localhost'; // или адрес вашего сервера
$db = 'techstyle';
$user = 'postgres';
$pass = 'Fifa32rekrek';

try {
    // Подключение к базе данных PostgreSQL
    $pdo = new PDO("pgsql:host=$host;port=8088;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>