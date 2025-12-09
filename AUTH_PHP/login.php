<?php
session_start();
require 'db.php'; // Includes database connection using PDO

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Use prepared statements
    $sql = "SELECT * FROM users WHERE username = :username";
    
    if ($conn) {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        // Fetch user data
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify user and password
        if (!$user) {
            $error_message = "Gebruiker bestaat niet, controleer gegevens of registreer.";
        } elseif (password_verify($password, $user['password'])) {
            // Login success
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            header("Location: index.php");
            exit();
        } else {
            $error_message = "Ongeldige gebruikersnaam of wachtwoord.";
        }
    } else {
         $error_message = "Database connection failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Login</h2>
    <?php
    if (isset($_GET['registered']) && $_GET['registered'] == 1) {
        echo "<p style='color:green; text-align: center; margin-bottom: 20px;'>Account succesvol aangemaakt! U kunt nu inloggen.</p>";
    }
    if (!empty($error_message)) {
        echo "<p style='color:red;'>$error_message</p>";
    }
    ?>
    <form method="post" action="login.php">
        <label for="username">Gebruikersnaam:</label><br>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">Wachtwoord:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        
        <div style="display: flex; gap: 10px; align-items: center;">
            <input type="submit" value="Inloggen" style="margin-bottom: 0;">
            <?php
            if (!empty($error_message) && strpos($error_message, 'Gebruiker bestaat niet') !== false) {
                echo "<button type='button' onclick=\"window.location.href='register.php'\" style='background-color: #10b981; padding: 0.75rem 1.5rem; border: none; border-radius: 0.375rem; color: white; cursor: pointer; font-size: 1rem; font-weight: 500;'>Nu Registreren</button>";
            }
            ?>
        </div>
    </form>
</body>
</html>
