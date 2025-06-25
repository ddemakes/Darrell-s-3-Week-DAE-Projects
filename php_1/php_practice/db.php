<?php
// ─────────────────────────────────────────────────────────────
// File: db.php - Database connection and shared config
// ─────────────────────────────────────────────────────────────
$host = "localhost";
$dbname = "LaughMD";
$username = "root";
$password = "root";

$conn = mysqli_connect($host, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>