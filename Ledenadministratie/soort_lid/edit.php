<?php
require_once __DIR__ . '/../includes/Auth.php';
Auth::requireLogin();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Soort Lid Bewerken';
$pdo = getDBConnection();
$error = '';
$id = $_GET['id'] ?? 0;

// Haal soort lid op
$stmt = $pdo->prepare("SELECT * FROM Soort_lid WHERE id = :id");
$stmt->execute(['id' => $id]);
$soort_lid = $stmt->fetch();

if (!$soort_lid) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $omschrijving = trim($_POST['omschrijving'] ?? '');
    
    if (empty($omschrijving)) {
        $error = 'Omschrijving is verplicht.';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE Soort_lid SET omschrijving = :omschrijving WHERE id = :id");
            $stmt->execute([
                'omschrijving' => $omschrijving,
                'id' => $id
            ]);
            
            redirect('index.php');
        } catch (PDOException $e) {
            $error = 'Er is een fout opgetreden: ' . $e->getMessage();
        }
    }
} else {
    $_POST['omschrijving'] = $soort_lid['omschrijving'];
}

include __DIR__ . '/../includes/header.php';
?>

<h1>Soort Lid Bewerken</h1>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo e($error); ?></div>
<?php endif; ?>

<form method="POST" action="">
    <div class="form-group">
        <label for="omschrijving">Omschrijving:</label>
        <input type="text" id="omschrijving" name="omschrijving" value="<?php echo e($_POST['omschrijving']); ?>" required>
    </div>
    
    <button type="submit" class="btn btn-success">Opslaan</button>
    <a href="index.php" class="btn btn-warning">Annuleren</a>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
