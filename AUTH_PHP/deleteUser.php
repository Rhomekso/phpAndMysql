<?php
session_start();
require 'db.php';

// Check login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check admin permissions
if ($_SESSION['role'] !== 'admin') {
    echo "Access Denied.";
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Delete user
    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
             // Success
        }
    } catch (PDOException $e) {
        // Ignore errors
    }
}

header("Location: index.php");
exit();
?>
