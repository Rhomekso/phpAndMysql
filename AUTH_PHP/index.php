<?php
session_start();

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    // Req 5: Without logging in, only the registration button is displayed.
    // (We also provide a login link for usability)
    ?>
    <!DOCTYPE html>
    <html lang="nl">
    <head>
        <meta charset="UTF-8">
        <title>Start</title>
    </head>
    <body>
        <h1>Welkom bij de Rolapplicatie</h1>
        <p>U bent niet ingelogd.</p>
        
        <button onclick="window.location.href='register.php'">Registreren</button>
        <br><br>
        <a href="login.php">Inloggen</a>
    </body>
    </html>
    <?php
    exit();
}

require 'db.php';
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Get current user ID for self-editing
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
</head>
<body>
    <h1>Welkom, <?php echo htmlspecialchars($username); ?>!</h1>
    <p>Je bent ingelogd als: <strong><?php echo htmlspecialchars($role); ?></strong></p>
    
    <!-- Link for everyone to edit their own profile/password (Req 6) -->
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
                // Modified query to include ORDER BY
                $stmt = $conn->prepare("SELECT id, username, role FROM users ORDER BY username ASC");
                $stmt->execute();
                
                // Requirement: rowCount
                $count = $stmt->rowCount();
                echo "<p>Totaal aantal gebruikers: $count</p>";
                
                // Requirement: fetchAll
                $users = $stmt->fetchAll();
                
                if ($count > 0) {
                    echo "<table border='1' cellpadding='5' cellspacing='0'>";
                    echo "<tr><th>ID</th><th>Gebruikersnaam</th><th>Rol</th><th>Acties</th></tr>";
                    
                    // Requirement: foreach
                    foreach ($users as $user) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                        echo "<td>";
                        echo "<a href='editUser.php?id=" . $user['id'] . "'>Bewerk</a> | ";
                        echo "<a href='deleteUser.php?id=" . $user['id'] . "' onclick='return confirm(\"Weet u het zeker?\")'>Verwijder</a>";
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
    <a href="index.php?logout=true">Uitloggen</a>
</body>
</html>
