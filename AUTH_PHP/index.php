<?php
session_start();

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    // Display registration button only when not logged in
    ?>
    <!DOCTYPE html>
    <html lang="nl">
    <head>
        <meta charset="UTF-8">
        <title>Start</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <h1>Welkom bij de Rolapplicatie</h1>
        <p>U bent niet ingelogd.</p>
        
        <div style="display: flex; gap: 10px; margin-top: 1rem;">
            <button onclick="window.location.href='register.php'">Registreren</button>
            <button onclick="window.location.href='login.php'" style="background-color: #10b981; color: white;">Inloggen</button>
        </div>
    </body>
    </html>
    <?php
    exit();
}

require 'db.php';
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Get current user ID
$stmt = $conn->prepare("SELECT id FROM users WHERE username = :username");
$stmt->bindParam(':username', $username);
$stmt->execute();
$current_user_id = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Welkom, <?php echo htmlspecialchars($username); ?>!</h1>
    <p>Je bent ingelogd als: <strong><?php echo htmlspecialchars($role); ?></strong></p>
    
    <!-- Link to edit profile/password -->
    <p>
        <a href="editUser.php?id=<?php echo $current_user_id; ?>">Mijn gegevens / Wachtwoord wijzigen</a>
    </p>

    <?php if ($role === 'admin'): ?>
        <div class="admin-controls">
            <h3>Admin Opties:</h3>
            <button onclick="window.location.href='createUser.php'">Gebruiker aanmaken</button>
            <button onclick="window.location.href='editUser.php'">Gebruiker bewerken</button>
        </div>

        <div class="user-list">
            <h3>Gebruikerslijst</h3>
            <?php
            require 'db.php';
            try {
                // Fetch users ordered by username
                $stmt = $conn->prepare("SELECT id, username, role, description FROM users ORDER BY username ASC");
                $stmt->execute();
                
                // Count users
                $count = $stmt->rowCount();
                echo "<p>Totaal aantal gebruikers: $count</p>";
                
                // Fetch all users
                $users = $stmt->fetchAll();
                
                if ($count > 0) {
                    echo "<table border='1' cellpadding='5' cellspacing='0'>";
                    echo "<tr><th>ID</th><th>Gebruikersnaam</th><th>Rol</th><th>Beschrijving</th><th>Acties</th></tr>";
                    
                    // Loop through users
                    foreach ($users as $user) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['description'] ?? '') . "</td>";
                        echo "<td>";
                        echo "<a href='editUser.php?id=" . $user['id'] . "' class='btn-edit'>Bewerk</a> ";
                        echo "<a href='deleteUser.php?id=" . $user['id'] . "' class='btn-delete' onclick='return confirm(\"Weet u het zeker?\")'>Verwijder</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "Geen gebruikers gevonden.";
                }
            } catch (PDOException $e) {
                echo "Fout bij ophalen gebruikers: " . $e->getMessage();
            }
            ?>
        </div>
    <?php endif; ?>

    <br><br>
    <div>
        <a href="index.php?logout=true" class="btn-logout">Uitloggen</a>
    </div>
</body>
</html>
