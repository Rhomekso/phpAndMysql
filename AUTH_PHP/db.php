<?php
// Auto-create database/table on startup
require_once 'createTable.php';

$host = 'localhost';
$username = 'admin';
$password = 'admin123';
$dbname = 'rolapplicatie';

try {
    // Connect to database
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $conn = new PDO($dsn, $username, $password);
    
    // Set error mode
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // Connection failed (createTable handles setup errors)
}
?>
