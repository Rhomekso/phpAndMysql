<?php
require_once __DIR__ . '/../includes/Auth.php';
Auth::requireAdmin();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Nieuwe Gebruiker Toevoegen';
$pdo = getDBConnection();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $rol = $_POST['rol'] ?? 'user';
    $actief = isset($_POST['actief']) ? 1 : 0;
    
    if (empty($username)) {
        $error = 'Gebruikersnaam is verplicht.';
    } elseif (empty($email)) {
        $error = 'E-mail is verplicht.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Ongeldig e-mailadres.';
    } elseif (empty($password)) {
        $error = 'Wachtwoord is verplicht.';
    } elseif (strlen($password) < 6) {
        $error = 'Wachtwoord moet minimaal 6 tekens lang zijn.';
    } elseif ($password !== $password_confirm) {
        $error = 'Wachtwoorden komen niet overeen.';
    } else {
        try {
            // Check of gebruikersnaam al bestaat
            $stmt = $pdo->prepare("SELECT id FROM User WHERE username = :username");
            $stmt->execute(['username' => $username]);
            if ($stmt->fetch()) {
                $error = 'Deze gebruikersnaam bestaat al.';
            } else {
                // Check of email al bestaat
                $stmt = $pdo->prepare("SELECT id FROM User WHERE email = :email");
                $stmt->execute(['email' => $email]);
                if ($stmt->fetch()) {
                    $error = 'Dit e-mailadres is al in gebruik.';
                } else {
                    // Hash wachtwoord
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Nieuwe gebruikers krijgen altijd rol 'user', tenzij admin dit expliciet aanpast
                    $stmt = $pdo->prepare("
                        INSERT INTO User (username, email, password, rol, actief) 
                        VALUES (:username, :email, :password, :rol, :actief)
                    ");
                    $stmt->execute([
                        'username' => $username,
                        'email' => $email,
                        'password' => $password_hash,
                        'rol' => $rol,
                        'actief' => $actief
                    ]);
                    
                    redirect('index.php');
                }
            }
        } catch (PDOException $e) {
            $error = 'Er is een fout opgetreden: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<h1>Nieuwe Gebruiker Toevoegen</h1>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo e($error); ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo e($success); ?></div>
<?php endif; ?>

<form method="POST" action="">
    <div class="form-group">
        <label for="username">Gebruikersnaam:</label>
        <input type="text" id="username" name="username" value="<?php echo e($_POST['username'] ?? ''); ?>" required>
    </div>
    
    <div class="form-group">
        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" value="<?php echo e($_POST['email'] ?? ''); ?>" required>
    </div>
    
    <div class="form-group">
        <label for="password">Wachtwoord:</label>
        <input type="password" id="password" name="password" required>
        <small style="color: #666;">Minimaal 6 tekens</small>
    </div>
    
    <div class="form-group">
        <label for="password_confirm">Bevestig Wachtwoord:</label>
        <input type="password" id="password_confirm" name="password_confirm" required>
    </div>
    
    <div class="form-group">
        <label for="rol">Rol:</label>
        <select id="rol" name="rol" required>
            <option value="user" <?php echo (isset($_POST['rol']) && $_POST['rol'] == 'user') ? 'selected' : ''; ?>>Gebruiker</option>
            <option value="admin" <?php echo (isset($_POST['rol']) && $_POST['rol'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
        </select>
        <small style="color: #666;">Alleen admin en mekso hebben admin rechten</small>
    </div>
    
    <div class="form-group">
        <label>
            <input type="checkbox" name="actief" value="1" <?php echo (isset($_POST['actief']) || !isset($_POST['username'])) ? 'checked' : ''; ?>>
            Account actief
        </label>
    </div>
    
    <button type="submit" class="btn btn-success">Opslaan</button>
    <a href="index.php" class="btn btn-warning">Annuleren</a>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
