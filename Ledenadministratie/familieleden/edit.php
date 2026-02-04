<?php
require_once __DIR__ . '/../includes/Auth.php';
Auth::requireLogin();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Familielid Bewerken';
$pdo = getDBConnection();
$error = '';
$id = $_GET['id'] ?? 0;

// Haal familielid op
$stmt = $pdo->prepare("SELECT * FROM Familielid WHERE id = :id");
$stmt->execute(['id' => $id]);
$familielid = $stmt->fetch();

if (!$familielid) {
    redirect('index.php');
}

// Haal families op voor dropdown
$stmt = $pdo->query("SELECT id, naam FROM Familie ORDER BY naam");
$families = $stmt->fetchAll();

// Haal soort leden op voor dropdown
$stmt = $pdo->query("SELECT id, omschrijving FROM Soort_lid ORDER BY id");
$soort_leden = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naam = trim($_POST['naam'] ?? '');
    $geboortedatum = $_POST['geboortedatum'] ?? '';
    $soort_lid_id = $_POST['soort_lid_id'] ?? 0;
    $familie_id = $_POST['familie_id'] ?? 0;
    
    if (empty($naam)) {
        $error = 'Naam is verplicht.';
    } elseif (empty($geboortedatum)) {
        $error = 'Geboortedatum is verplicht.';
    } elseif (empty($soort_lid_id)) {
        $error = 'Soort lid is verplicht.';
    } elseif (empty($familie_id)) {
        $error = 'Familie is verplicht.';
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE Familielid 
                SET naam = :naam, geboortedatum = :geboortedatum, 
                    soort_lid_id = :soort_lid_id, familie_id = :familie_id
                WHERE id = :id
            ");
            $stmt->execute([
                'naam' => $naam,
                'geboortedatum' => $geboortedatum,
                'soort_lid_id' => $soort_lid_id,
                'familie_id' => $familie_id,
                'id' => $id
            ]);
            
            redirect('index.php?familie_id=' . $familie_id);
        } catch (PDOException $e) {
            $error = 'Er is een fout opgetreden: ' . $e->getMessage();
        }
    }
} else {
    $_POST['naam'] = $familielid['naam'];
    $_POST['geboortedatum'] = $familielid['geboortedatum'];
    $_POST['soort_lid_id'] = $familielid['soort_lid_id'];
    $_POST['familie_id'] = $familielid['familie_id'];
}

include __DIR__ . '/../includes/header.php';
?>

<h1>Familielid Bewerken</h1>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo e($error); ?></div>
<?php endif; ?>

<form method="POST" action="">
    <div class="form-group">
        <label for="naam">Naam:</label>
        <input type="text" id="naam" name="naam" value="<?php echo e($_POST['naam']); ?>" required>
    </div>
    
    <div class="form-group">
        <label for="geboortedatum">Geboortedatum:</label>
        <input type="date" id="geboortedatum" name="geboortedatum" value="<?php echo e($_POST['geboortedatum']); ?>" required>
    </div>
    
    <div class="form-group">
        <label for="soort_lid_id">Soort Lid:</label>
        <select id="soort_lid_id" name="soort_lid_id" required>
            <option value="">-- Selecteer --</option>
            <?php foreach ($soort_leden as $soort): ?>
                <option value="<?php echo $soort['id']; ?>" <?php echo $_POST['soort_lid_id'] == $soort['id'] ? 'selected' : ''; ?>>
                    <?php echo e($soort['omschrijving']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="form-group">
        <label for="familie_id">Familie:</label>
        <select id="familie_id" name="familie_id" required>
            <option value="">-- Selecteer --</option>
            <?php foreach ($families as $familie): ?>
                <option value="<?php echo $familie['id']; ?>" <?php echo $_POST['familie_id'] == $familie['id'] ? 'selected' : ''; ?>>
                    <?php echo e($familie['naam']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <button type="submit" class="btn btn-success">Opslaan</button>
    <a href="index.php?familie_id=<?php echo $_POST['familie_id']; ?>" class="btn btn-warning">Annuleren</a>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
