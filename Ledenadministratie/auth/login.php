<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/User.php';

// Als al ingelogd, redirect naar home
if (Auth::check()) {
    redirect('../index.php');
}

$page_title = 'Inloggen';
$error = '';
$pdo = getDBConnection();
$userModel = new User($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($username) || empty($password)) {
        $error = 'Vul alle velden in.';
    } else {
        $user = $userModel->authenticate($username, $password);
        
        if ($user) {
            Auth::login($user, $remember);
            
            // Redirect naar oorspronkelijke pagina of home
            $redirect = $_SESSION['redirect_after_login'] ?? '../index.php';
            unset($_SESSION['redirect_after_login']);
            redirect($redirect);
        } else {
            $error = 'Ongeldige gebruikersnaam of wachtwoord.';
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="auth-form">
    <h1>Inloggen</h1>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo e($error); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Gebruikersnaam:</label>
            <input type="text" id="username" name="username" value="<?php echo e($_POST['username'] ?? ''); ?>" required autofocus>
        </div>
        
        <div class="form-group">
            <label for="password">Wachtwoord:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label>
                <input type="checkbox" name="remember" value="1">
                Onthoud mij (30 dagen)
            </label>
        </div>
        
        <button type="submit" class="btn btn-success">Inloggen</button>
        <a href="register.php" class="btn btn-primary">Registreren</a>
    </form>
    
    <div class="info-box">
        <strong>Demo Account:</strong><br>
        Gebruikersnaam: <code>admin</code><br>
        Wachtwoord: <code>admin123</code>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
