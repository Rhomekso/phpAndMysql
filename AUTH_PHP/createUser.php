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
    echo "Access Denied: You do not have permission to view this page.";
    echo "<br><a href='index.php'>Go back to Home</a>";
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $description = $_POST['description'];

    if (!empty($username) && !empty($password) && !empty($role)) {
        try {
            // Check if user exists
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if ($stmt->fetchColumn() == 0) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // INSERT INTO and VALUES used here
                $sql = "INSERT INTO users (username, password, role, description) VALUES (:username, :password, :role, :description)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':role', $role);
                $stmt->bindParam(':description', $description);

                if ($stmt->execute()) {
                    $message = "Gebruiker succesvol aangemaakt!";
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
    <title>Gebruiker aanmaken</title>
</head>
<body>
    <h2>Gebruiker aanmaken</h2>
    <?php if ($message): ?>
        <p style="color: blue;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    
    <form method="post" action="createUser.php">
        <label>Gebruikersnaam: <input type="text" name="username" required></label><br><br>
        <label>Wachtwoord: <input type="password" name="password" required></label><br><br>
        <label>Rol: 
            <select name="role">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </label><br><br>
        <label>Beschrijving (optioneel):<br>
            <textarea name="description" rows="4" cols="50"></textarea>
        </label><br><br>
        <input type="submit" value="Aanmaken">
    </form>
    <br>
    <a href="index.php">Terug naar Home</a>
</body>
</html>
