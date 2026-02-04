<?php
require_once __DIR__ . '/../includes/Auth.php';
Auth::requireLogin();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Familie Bewerken';
$pdo = getDBConnection();
$error = '';
$id = $_GET['id'] ?? 0;

// Haal familie op
$stmt = $pdo->prepare("SELECT * FROM Familie WHERE id = :id");
$stmt->execute(['id' => $id]);
$familie = $stmt->fetch();

if (!$familie) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naam = trim($_POST['naam'] ?? '');
    $adres = trim($_POST['adres'] ?? '');
    
    if (empty($naam)) {
        $error = 'Naam is verplicht.';
    } elseif (empty($adres)) {
        $error = 'Adres is verplicht.';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE Familie SET naam = :naam, adres = :adres WHERE id = :id");
            $stmt->execute([
                'naam' => $naam,
                'adres' => $adres,
                'id' => $id
            ]);
            
            redirect('index.php');
        } catch (PDOException $e) {
            $error = 'Er is een fout opgetreden: ' . $e->getMessage();
        }
    }
} else {
    $_POST['naam'] = $familie['naam'];
    $_POST['adres'] = $familie['adres'];
}

include __DIR__ . '/../includes/header.php';
?>

<h1>Familie Bewerken</h1>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo e($error); ?></div>
<?php endif; ?>

<form method="POST" action="">
    <div class="form-group">
        <label for="naam">Naam:</label>
        <input type="text" id="naam" name="naam" value="<?php echo e($_POST['naam']); ?>" required>
    </div>
    
    <div class="form-group">
        <label for="adres">Adres:</label>
        <input type="text" id="adres" name="adres" value="<?php echo e($_POST['adres']); ?>" required>
    </div>
    
    <button type="submit" class="btn btn-success">Opslaan</button>
    <a href="index.php" class="btn btn-warning">Annuleren</a>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
