<?php
// config/db.php
session_start();

$host     = 'localhost';
$user     = 'root';
$password = '';
$dbname   = 'project';

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

// Optional: set charset
mysqli_set_charset($conn, 'utf8mb4');
