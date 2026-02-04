<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/User.php';

// Als al ingelogd, redirect naar home
if (Auth::check()) {
    redirect('../index.php');
}

$page_title = 'Registreren';
$error = '';
$success = '';
$pdo = getDBConnection();
$userModel = new User($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Vul alle velden in.';
    } elseif ($password !== $password_confirm) {
        $error = 'Wachtwoorden komen niet overeen.';
    } elseif (strlen($password) < 6) {
        $error = 'Wachtwoord moet minimaal 6 karakters zijn.';
    } else {
        try {
            $userModel->register($username, $email, $password);
            $success = 'Account succesvol aangemaakt! Je kunt nu inloggen.';
            $_POST = []; // Clear form
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="auth-form">
    <h1>Registreren</h1>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo e($error); ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo e($success); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Gebruikersnaam:</label>
            <input type="text" id="username" name="username" value="<?php echo e($_POST['username'] ?? ''); ?>" required autofocus>
        </div>
        
        <div class="form-group">
            <label for="email">E-mailadres:</label>
            <input type="email" id="email" name="email" value="<?php echo e($_POST['email'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="password">Wachtwoord (min. 6 karakters):</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="password_confirm">Bevestig wachtwoord:</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>
        
        <button type="submit" class="btn btn-success">Registreren</button>
        <a href="login.php" class="btn btn-primary">Terug naar login</a>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
