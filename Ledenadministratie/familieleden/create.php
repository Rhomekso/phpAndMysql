<?php
require_once __DIR__ . '/../includes/Auth.php';
Auth::requireLogin();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Nieuw Familielid Toevoegen';
$pdo = getDBConnection();
$error = '';

// Voorgeselecteerde familie indien opgegeven
$voorgeselecteerde_familie_id = $_GET['familie_id'] ?? null;

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
                INSERT INTO Familielid (naam, geboortedatum, soort_lid_id, familie_id) 
                VALUES (:naam, :geboortedatum, :soort_lid_id, :familie_id)
            ");
            $stmt->execute([
                'naam' => $naam,
                'geboortedatum' => $geboortedatum,
                'soort_lid_id' => $soort_lid_id,
                'familie_id' => $familie_id
            ]);
            
            redirect('index.php?familie_id=' . $familie_id);
        } catch (PDOException $e) {
            $error = 'Er is een fout opgetreden: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<h1>Nieuw Familielid Toevoegen</h1>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo e($error); ?></div>
<?php endif; ?>

<form method="POST" action="">
    <div class="form-group">
        <label for="naam">Naam:</label>
        <input type="text" id="naam" name="naam" value="<?php echo e($_POST['naam'] ?? ''); ?>" required>
    </div>
    
    <div class="form-group">
        <label for="geboortedatum">Geboortedatum:</label>
        <input type="date" id="geboortedatum" name="geboortedatum" value="<?php echo e($_POST['geboortedatum'] ?? ''); ?>" required>
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
        <label for="familie_id">Familie:</label>
        <select id="familie_id" name="familie_id" required>
            <option value="">-- Selecteer --</option>
            <?php foreach ($families as $familie): ?>
                <option value="<?php echo $familie['id']; ?>" 
                    <?php echo (isset($_POST['familie_id']) ? $_POST['familie_id'] : $voorgeselecteerde_familie_id) == $familie['id'] ? 'selected' : ''; ?>>
                    <?php echo e($familie['naam']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="info-box" style="margin-top: 20px; background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 5px;">
        <h3 style="margin-top: 0; margin-bottom: 15px; color: #495057;">Staffels Contributie 2026</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #e9ecef;">
                    <th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Categorie</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Leeftijd</th>
                    <th style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">Korting</th>
                    <th style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">Te Betalen</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">Jeugd</td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">0-7 jaar</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">50%</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">€50,00</td>
                </tr>
                <tr style="background-color: #f8f9fa;">
                    <td style="padding: 10px; border: 1px solid #dee2e6;">Aspirant</td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">8-12 jaar</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">40%</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">€60,00</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">Junior</td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">13-17 jaar</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">25%</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">€75,00</td>
                </tr>
                <tr style="background-color: #f8f9fa;">
                    <td style="padding: 10px; border: 1px solid #dee2e6;">Senior</td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">18-50 jaar</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">0%</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">€100,00</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">Oudere</td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">51+ jaar</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">45%</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">€55,00</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <button type="submit" class="btn btn-success">Opslaan</button>
    <a href="index.php<?php echo $voorgeselecteerde_familie_id ? '?familie_id=' . $voorgeselecteerde_familie_id : ''; ?>" class="btn btn-warning">Annuleren</a>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
