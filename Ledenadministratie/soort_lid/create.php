<?php
require_once __DIR__ . '/../includes/Auth.php';
Auth::requireAdmin();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Nieuw Soort Lid Toevoegen';
$pdo = getDBConnection();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $omschrijving = trim($_POST['omschrijving'] ?? '');
    
    if (empty($omschrijving)) {
        $error = 'Omschrijving is verplicht.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO Soort_lid (omschrijving) VALUES (:omschrijving)");
            $stmt->execute(['omschrijving' => $omschrijving]);
            
            redirect('index.php');
        } catch (PDOException $e) {
            $error = 'Er is een fout opgetreden: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<h1>Nieuw Soort Lid Toevoegen</h1>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo e($error); ?></div>
<?php endif; ?>

<form method="POST" action="">
    <div class="form-group">
        <label for="omschrijving">Omschrijving:</label>
        <input type="text" id="omschrijving" name="omschrijving" value="<?php echo e($_POST['omschrijving'] ?? ''); ?>" required>
    </div>
    
    <button type="submit" class="btn btn-success">Opslaan</button>
    <a href="index.php" class="btn btn-warning">Annuleren</a>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
