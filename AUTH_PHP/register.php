<?php
session_start();
require 'db.php';

// If already logged in, redirect to home
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    // Role is always 'user' for public registration
    $role = 'user';
    $description = $_POST['description'] ?? '';

    if (!empty($username) && !empty($password)) {
        try {
            // Check if user exists
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if ($stmt->fetchColumn() == 0) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $sql = "INSERT INTO users (username, password, role, description) VALUES (:username, :password, :role, :description)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':role', $role);
                $stmt->bindParam(':description', $description);

                if ($stmt->execute()) {
                    $message = "Account succesvol aangemaakt! U kunt nu <a href='login.php'>inloggen</a>.";
                } else {
                    $message = "Er ging iets mis.";
                }
            } else {
                $message = "Gebruikersnaam bestaat al.";
            }
        } catch (PDOException $e) {
            $message = "Fout: " . $e->getMessage();
        }
    } else {
        $message = "Vul alle verplichte velden in.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Registreren</title>
</head>
<body>
    <h2>Registreren</h2>
    <?php if ($message): ?>
        <p style="color: blue;"><?php echo $message; ?></p>
    <?php endif; ?>
    
    <form method="post" action="register.php">
        <label>Gebruikersnaam: <input type="text" name="username" required></label><br><br>
        <label>Wachtwoord: <input type="password" name="password" required></label><br><br>
        
        <label>Beschrijving (optioneel):<br>
            <textarea name="description" rows="4" cols="50"></textarea>
        </label><br><br>
        
        <input type="submit" value="Registreren">
    </form>
    <br>
    <a href="index.php">Terug naar Home</a>
</body>
</html>
