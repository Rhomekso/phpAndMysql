<?php
require_once __DIR__ . '/../includes/Auth.php';
Auth::requireAdmin();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Gebruiker Bewerken';
$pdo = getDBConnection();
$error = '';
$id = $_GET['id'] ?? 0;

// Haal gebruiker op
$stmt = $pdo->prepare("SELECT * FROM User WHERE id = :id");
$stmt->execute(['id' => $id]);
$gebruiker = $stmt->fetch();

if (!$gebruiker) {
    redirect('index.php');
}

// Bescherm admin en mekso tegen rol wijziging
$is_protected = in_array($gebruiker['username'], ['admin', 'mekso']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $rol = $is_protected ? $gebruiker['rol'] : ($_POST['rol'] ?? 'user');
    $actief = isset($_POST['actief']) ? 1 : 0;
    
    if (empty($username)) {
        $error = 'Gebruikersnaam is verplicht.';
    } elseif (empty($email)) {
        $error = 'E-mail is verplicht.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Ongeldig e-mailadres.';
    } elseif (!empty($password) && strlen($password) < 6) {
        $error = 'Wachtwoord moet minimaal 6 tekens lang zijn.';
    } elseif (!empty($password) && $password !== $password_confirm) {
        $error = 'Wachtwoorden komen niet overeen.';
    } else {
        try {
            // Check of gebruikersnaam al bestaat (behalve huidige gebruiker)
            $stmt = $pdo->prepare("SELECT id FROM User WHERE username = :username AND id != :id");
            $stmt->execute(['username' => $username, 'id' => $id]);
            if ($stmt->fetch()) {
                $error = 'Deze gebruikersnaam bestaat al.';
            } else {
                // Check of email al bestaat (behalve huidige gebruiker)
                $stmt = $pdo->prepare("SELECT id FROM User WHERE email = :email AND id != :id");
                $stmt->execute(['email' => $email, 'id' => $id]);
                if ($stmt->fetch()) {
                    $error = 'Dit e-mailadres is al in gebruik.';
                } else {
                    // Update gebruiker
                    if (!empty($password)) {
                        $password_hash = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("
                            UPDATE User 
                            SET username = :username, email = :email, password = :password, rol = :rol, actief = :actief 
                            WHERE id = :id
                        ");
                        $stmt->execute([
                            'username' => $username,
                            'email' => $email,
                            'password' => $password_hash,
                            'rol' => $rol,
                            'actief' => $actief,
                            'id' => $id
                        ]);
                    } else {
                        $stmt = $pdo->prepare("
                            UPDATE User 
                            SET username = :username, email = :email, rol = :rol, actief = :actief 
                            WHERE id = :id
                        ");
                        $stmt->execute([
                            'username' => $username,
                            'email' => $email,
                            'rol' => $rol,
                            'actief' => $actief,
                            'id' => $id
                        ]);
                    }
                    
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

<h1>Gebruiker Bewerken</h1>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo e($error); ?></div>
<?php endif; ?>

<?php if ($is_protected): ?>
    <div class="info-box" style="background-color: #fff3cd; border-color: #ffc107;">
        <strong>Let op:</strong> Dit is een beschermde gebruiker. De rol kan niet worden gewijzigd.
    </div>
<?php endif; ?>

<form method="POST" action="">
    <div class="form-group">
        <label for="username">Gebruikersnaam:</label>
        <input type="text" id="username" name="username" value="<?php echo e($_POST['username'] ?? $gebruiker['username']); ?>" required>
    </div>
    
    <div class="form-group">
        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" value="<?php echo e($_POST['email'] ?? $gebruiker['email']); ?>" required>
    </div>
    
    <div class="form-group">
        <label for="password">Nieuw Wachtwoord:</label>
        <input type="password" id="password" name="password">
        <small style="color: #666;">Laat leeg om het wachtwoord niet te wijzigen. Minimaal 6 tekens als je het wijzigt.</small>
    </div>
    
    <div class="form-group">
        <label for="password_confirm">Bevestig Nieuw Wachtwoord:</label>
        <input type="password" id="password_confirm" name="password_confirm">
    </div>
    
    <div class="form-group">
        <label for="rol">Rol:</label>
        <select id="rol" name="rol" required <?php echo $is_protected ? 'disabled' : ''; ?>>
            <option value="user" <?php echo (isset($_POST['rol']) ? $_POST['rol'] : $gebruiker['rol']) == 'user' ? 'selected' : ''; ?>>Gebruiker</option>
            <option value="admin" <?php echo (isset($_POST['rol']) ? $_POST['rol'] : $gebruiker['rol']) == 'admin' ? 'selected' : ''; ?>>Admin</option>
        </select>
        <?php if ($is_protected): ?>
            <small style="color: #666;">Admin en mekso rollen zijn beschermd</small>
        <?php endif; ?>
    </div>
    
    <div class="form-group">
        <label>
            <input type="checkbox" name="actief" value="1" <?php echo (isset($_POST['actief']) ? isset($_POST['actief']) : $gebruiker['actief']) ? 'checked' : ''; ?>>
            Account actief
        </label>
    </div>
    
    <button type="submit" class="btn btn-success">Opslaan</button>
    <a href="index.php" class="btn btn-warning">Annuleren</a>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
