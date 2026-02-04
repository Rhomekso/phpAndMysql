<?php
require_once __DIR__ . '/../includes/Auth.php';
Auth::requireLogin();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Nieuwe Familie Toevoegen';
$pdo = getDBConnection();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Valideer CSRF token
    if (!Auth::validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Ongeldige aanvraag. Probeer het opnieuw.';
    } else {
        $naam = trim($_POST['naam'] ?? '');
        $adres = trim($_POST['adres'] ?? '');
        
        if (empty($naam)) {
            $error = 'Naam is verplicht.';
        } elseif (empty($adres)) {
            $error = 'Adres is verplicht.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO Familie (naam, adres) VALUES (:naam, :adres)");
                $stmt->execute([
                    'naam' => $naam,
                    'adres' => $adres
                ]);
                
                redirect('index.php');
            } catch (PDOException $e) {
                $error = 'Er is een fout opgetreden: ' . $e->getMessage();
            }
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<h1>Nieuwe Familie Toevoegen</h1>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo e($error); ?></div>
<?php endif; ?>

<form method="POST" action="">
    <?php echo Auth::csrfField(); ?>
    
    <div class="form-group">
        <label for="naam">Naam:</label>
        <input type="text" id="naam" name="naam" value="<?php echo e($_POST['naam'] ?? ''); ?>" required>
    </div>
    
    <div class="form-group">
        <label for="adres">Adres:</label>
        <input type="text" id="adres" name="adres" value="<?php echo e($_POST['adres'] ?? ''); ?>" required>
    </div>
    
    <button type="submit" class="btn btn-success">Opslaan</button>
    <a href="index.php" class="btn btn-warning">Annuleren</a>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
