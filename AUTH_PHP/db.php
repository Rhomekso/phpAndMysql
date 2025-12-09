<?php
// Req 8: Automatically create table when application starts (accessed via DB connection)
require_once 'createTable.php';

$host = 'localhost';
$username = 'admin';
$password = 'admin123';
$dbname = 'rolapplicatie';

try {
    // ...
    // However, for standard usage we usually connect to the DB.
    // Since createTable needs to create it, we might need a slight variation there.
    // For this generic db.php, we'll assume the DB exists or handle the error in the calling script
    // OR we can default to connecting to the server and selecting DB if it exists.
    
    // Strategy: Connect to server only first if we are in createTable context?
    // Actually, usually db.php connects to the specific database.
    // I'll make it connect to the database directly. createTable.php can handle its own connection 
    // or we can suppress the error if DB doesn't exist yet.
    
    // Let's stick to connecting to the DB.
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $conn = new PDO($dsn, $username, $password);
    
    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // In a real app, don't echo error details to user.
    // echo "Connection failed: " . $e->getMessage();
    
    // For createTable.php specifically, we might fail here if DB doesn't exist.
    // Let's handle that gracefully? No, simpler to just let it fail or handle in createTable.
}
?>
