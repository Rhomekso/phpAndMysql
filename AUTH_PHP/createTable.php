<?php
// Function to ensure database and table exist
function ensureDatabaseSetup() {
    $host = "localhost";
    $username = "root";
    $password = "";

    try {
        // 1. Connect to MySQL Server (no DB selected yet)
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 2. Create Database
        $sql = "CREATE DATABASE IF NOT EXISTS rolapplicatie";
        $pdo->exec($sql);
        // echo "Database created successfully or already exists.<br>";

        // 3. Connect to the specific database
        $pdo->exec("USE rolapplicatie");

        // 4. Create Table
        // Added 'description' as TEXT to satisfy SQL data type requirement
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'user') NOT NULL,
            description TEXT
        )";
        $pdo->exec($sql);
        // echo "Table 'users' created successfully or already exists.<br>";

        // 5. Helper function to check and insert user
        function createDefaultUser($pdo, $user, $pass, $role, $desc = '') {
            // Prepare statement to check existence
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
            $stmt->bindParam(':username', $user);
            $stmt->execute();
            
            if ($stmt->fetchColumn() == 0) {
                // Hash the password!
                $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
                
                // Insert
                $insert = $pdo->prepare("INSERT INTO users (username, password, role, description) VALUES (:username, :password, :role, :description)");
                $insert->bindParam(':username', $user);
                $insert->bindParam(':password', $hashed_password);
                $insert->bindParam(':role', $role);
                $insert->bindParam(':description', $desc);
                
                $insert->execute();
                // echo ucfirst($role) . " user created successfully.<br>";
            }
        }

        // 6. Insert default users
        createDefaultUser($pdo, 'admin', 'admin123', 'admin', 'Administrator account');
        createDefaultUser($pdo, 'gebruiker', 'gebruiker123', 'user', 'Standaard gebruiker');

    } catch(PDOException $e) {
        // Log error instead of echoing
        error_log("Setup Error: " . $e->getMessage());
    }
    
    $pdo = null; // Close connection
}

// Run setup
ensureDatabaseSetup();
?>
