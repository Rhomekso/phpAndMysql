<?php
require_once __DIR__ . '/../includes/Auth.php';
Auth::requireLogin();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Nieuwe Contributie Toevoegen';
$pdo = getDBConnection();
$error = '';

// Haal soort leden op
$stmt = $pdo->query("SELECT id, omschrijving FROM Soort_lid ORDER BY id");
$soort_leden = $stmt->fetchAll();

// Haal boekjaren op
$stmt = $pdo->query("SELECT id, jaar FROM Boekjaar ORDER BY jaar DESC");
$boekjaren = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $leeftijd = trim($_POST['leeftijd'] ?? '');
    $soort_lid_id = $_POST['soort_lid_id'] ?? 0;
    $bedrag = trim($_POST['bedrag'] ?? '');
    $boekjaar_id = $_POST['boekjaar_id'] ?? 0;
    
    if (empty($leeftijd) || !is_numeric($leeftijd) || $leeftijd < 0 || $leeftijd > 120) {
        $error = 'Voer een geldige leeftijd in (0-120).';
    } elseif (empty($soort_lid_id)) {
        $error = 'Soort lid is verplicht.';
    } elseif (empty($bedrag) || !is_numeric($bedrag) || $bedrag < 0) {
        $error = 'Voer een geldig bedrag in.';
    } elseif (empty($boekjaar_id)) {
        $error = 'Boekjaar is verplicht.';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO Contributie (leeftijd, soort_lid_id, bedrag, boekjaar_id) 
                VALUES (:leeftijd, :soort_lid_id, :bedrag, :boekjaar_id)
            ");
            $stmt->execute([
                'leeftijd' => $leeftijd,
                'soort_lid_id' => $soort_lid_id,
                'bedrag' => $bedrag,
                'boekjaar_id' => $boekjaar_id
            ]);
            
            redirect('index.php');
        } catch (PDOException $e) {
            $error = 'Er is een fout opgetreden: ' . $e->getMessage() . ' (mogelijk bestaat deze combinatie al)';
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<h1>Nieuwe Contributie Toevoegen</h1>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo e($error); ?></div>
<?php endif; ?>

<form method="POST" action="">
    <div class="form-group">
        <label for="boekjaar_id">Boekjaar:</label>
        <select id="boekjaar_id" name="boekjaar_id" required>
            <option value="">-- Selecteer --</option>
            <?php foreach ($boekjaren as $bj): ?>
                <option value="<?php echo $bj['id']; ?>" <?php echo (isset($_POST['boekjaar_id']) && $_POST['boekjaar_id'] == $bj['id']) ? 'selected' : ''; ?>>
                    <?php echo e($bj['jaar']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="form-group">
        <label for="leeftijd">Leeftijd:</label>
        <input type="number" id="leeftijd" name="leeftijd" value="<?php echo e($_POST['leeftijd'] ?? ''); ?>" min="0" max="120" required>
    </div>
    
    <div class="form-group">
        <label for="soort_lid_id">Soort Lid:</label>
        <select id="soort_lid_id" name="soort_lid_id" required>
            <option value="">-- Selecteer --</option>
            <?php foreach ($soort_leden as $soort): ?>
                <option value="<?php echo $soort['id']; ?>" <?php echo (isset($_POST['soort_lid_id']) && $_POST['soort_lid_id'] == $soort['id']) ? 'selected' : ''; ?>>
                    <?php echo e($soort['omschrijving']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="form-group">
        <label for="bedrag">Bedrag (€):</label>
        <input type="number" id="bedrag" name="bedrag" value="<?php echo e($_POST['bedrag'] ?? ''); ?>" min="0" step="0.01" required>
    </div>
    
    <button type="submit" class="btn btn-success">Opslaan</button>
    <a href="index.php" class="btn btn-warning">Annuleren</a>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
