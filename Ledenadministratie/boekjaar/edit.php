<?php
require_once __DIR__ . '/../includes/Auth.php';
Auth::requireLogin();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Boekjaar Bewerken';
$pdo = getDBConnection();
$error = '';
$id = $_GET['id'] ?? 0;

// Haal boekjaar op
$stmt = $pdo->prepare("SELECT * FROM Boekjaar WHERE id = :id");
$stmt->execute(['id' => $id]);
$boekjaar = $stmt->fetch();

if (!$boekjaar) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jaar = trim($_POST['jaar'] ?? '');
    
    if (empty($jaar)) {
        $error = 'Jaar is verplicht.';
    } elseif (!is_numeric($jaar) || $jaar < 1900 || $jaar > 2100) {
        $error = 'Voer een geldig jaar in (tussen 1900 en 2100).';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE Boekjaar SET jaar = :jaar WHERE id = :id");
            $stmt->execute([
                'jaar' => $jaar,
                'id' => $id
            ]);
            
            redirect('index.php');
        } catch (PDOException $e) {
            $error = 'Er is een fout opgetreden: ' . $e->getMessage() . ' (mogelijk bestaat dit jaar al)';
        }
    }
} else {
    $_POST['jaar'] = $boekjaar['jaar'];
}

include __DIR__ . '/../includes/header.php';
?>

<h1>Boekjaar Bewerken</h1>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo e($error); ?></div>
<?php endif; ?>

<form method="POST" action="">
    <div class="form-group">
        <label for="jaar">Jaar:</label>
        <input type="number" id="jaar" name="jaar" value="<?php echo e($_POST['jaar']); ?>" min="1900" max="2100" required>
    </div>
    
    <button type="submit" class="btn btn-success">Opslaan</button>
    <a href="index.php" class="btn btn-warning">Annuleren</a>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
