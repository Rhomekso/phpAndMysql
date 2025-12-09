<?php
session_start();
require 'db.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Role Control: Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    echo "Access Denied.";
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // DELETE statement
    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
             // Optional: message handled via redirect or just simple
        }
    } catch (PDOException $e) {
        // Handle error
    }
}

header("Location: index.php");
exit();
?>
