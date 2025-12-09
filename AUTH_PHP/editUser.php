<?php
session_start();
require 'db.php';

// Check login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$current_username = $_SESSION['username'];
$current_role = $_SESSION['role'];

// Get current user ID
$stmt = $conn->prepare("SELECT id FROM users WHERE username = :username");
$stmt->bindParam(':username', $current_username);
$stmt->execute();
$current_user_id = $stmt->fetchColumn();

$message = "";
$user_data = null;
$target_id = $_GET['id'] ?? ($_POST['id'] ?? null);

// Check permissions
if ($target_id) {
    // Only admin or self can edit
    if ($current_role !== 'admin' && $target_id != $current_user_id) {
        echo "Access Denied. You can only edit your own profile.";
        exit();
    }
} else {
    // No ID provided
    echo "No user specified.";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    // Admins can change roles; others keep their role
    
    $description = $_POST['description'];
    $new_password = $_POST['password']; // New password field

    if (!empty($id)) {
        try {
            // Fetch current role
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $current_target_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Determine role to save
            if ($current_role === 'admin') {
                $role_to_save = $_POST['role'];
            } else {
                $role_to_save = $current_target_data['role'];
            }

            // Update user
            $sql = "UPDATE users SET role = :role, description = :description";
            
            // Update password if provided
            if (!empty($new_password)) {
                $sql .= ", password = :password";
            }
            
            $sql .= " WHERE id = :id";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':role', $role_to_save);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':id', $id);
            
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt->bindParam(':password', $hashed_password);
            }

            if ($stmt->execute()) {
                $message = "Gebruiker bijgewerkt!";
            }
        } catch (PDOException $e) {
            $message = "Fout: " . $e->getMessage();
        }
    }
}

// Fetch user data
if ($target_id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $target_id);
    $stmt->execute();
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Gebruiker bewerken</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Gebruiker bewerken</h2>
    <?php if ($message): ?>
        <p style="color: blue;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if ($user_data): ?>
        <form method="post" action="editUser.php">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($user_data['id']); ?>">
            <p>Gebruiker: <strong><?php echo htmlspecialchars($user_data['username']); ?></strong></p>
            
            <label>Nieuw Wachtwoord (laat leeg om te behouden):<br>
                <input type="password" name="password">
            </label><br><br>

            <?php if ($current_role === 'admin'): ?>
                <label>Rol: 
                    <select name="role">
                        <option value="user" <?php if($user_data['role'] == 'user') echo 'selected'; ?>>User</option>
                        <option value="admin" <?php if($user_data['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                    </select>
                </label><br><br>
            <?php else: ?>
                <p>Rol: <?php echo htmlspecialchars($user_data['role']); ?></p>
                <!-- Display role text for non-admins -->
            <?php endif; ?>
            
            <label>Beschrijving:<br>
                <textarea name="description" rows="4" cols="50"><?php echo htmlspecialchars($user_data['description'] ?? ''); ?></textarea>
            </label><br><br>
            
            <input type="submit" value="Bijwerken">
        </form>
    <?php else: ?>
        <p>Gebruiker niet gevonden.</p>
    <?php endif; ?>
    
    <br>
    <a href="index.php">Terug naar Home</a>
</body>
</html>
