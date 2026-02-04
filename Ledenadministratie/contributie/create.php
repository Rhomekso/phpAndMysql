<?php
require_once __DIR__ . '/../includes/Auth.php';
Auth::requireAdmin();

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
    $basisbedrag = trim($_POST['basisbedrag'] ?? '');
    $kortingspercentage = trim($_POST['kortingspercentage'] ?? '');
    $boekjaar_id = $_POST['boekjaar_id'] ?? 0;
    
    if (empty($leeftijd) || !is_numeric($leeftijd) || $leeftijd < 0 || $leeftijd > 120) {
        $error = 'Voer een geldige leeftijd in (0-120).';
    } elseif (empty($soort_lid_id)) {
        $error = 'Soort lid is verplicht.';
    } elseif (empty($basisbedrag) || !is_numeric($basisbedrag) || $basisbedrag < 0) {
        $error = 'Voer een geldig basisbedrag in.';
    } elseif (empty($kortingspercentage) || !is_numeric($kortingspercentage) || $kortingspercentage < 0 || $kortingspercentage > 100) {
        $error = 'Voer een geldig kortingspercentage in (0-100).';
    } elseif (empty($boekjaar_id)) {
        $error = 'Boekjaar is verplicht.';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO Contributie (leeftijd, soort_lid_id, basisbedrag, kortingspercentage, boekjaar_id) 
                VALUES (:leeftijd, :soort_lid_id, :basisbedrag, :kortingspercentage, :boekjaar_id)
            ");
            $stmt->execute([
                'leeftijd' => $leeftijd,
                'soort_lid_id' => $soort_lid_id,
                'basisbedrag' => $basisbedrag,
                'kortingspercentage' => $kortingspercentage,
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
        <select id="soort_lid_id" name="soort_lid_id" required onchange="updateKortingspercentage()">
            <option value="">-- Selecteer --</option>
            <?php foreach ($soort_leden as $soort): ?>
                <option value="<?php echo $soort['id']; ?>" <?php echo (isset($_POST['soort_lid_id']) && $_POST['soort_lid_id'] == $soort['id']) ? 'selected' : ''; ?>>
                    <?php echo e($soort['omschrijving']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="form-group">
        <label for="basisbedrag">Basisbedrag (€):</label>
        <input type="number" id="basisbedrag" name="basisbedrag" value="<?php echo e($_POST['basisbedrag'] ?? '100.00'); ?>" min="0" step="0.01" required readonly style="background-color: #f0f0f0;">
        <small>Het volledige contributietarief (standaard €100)</small>
    </div>
    
    <div class="form-group">
        <label for="kortingspercentage">Kortingspercentage (%):</label>
        <input type="number" id="kortingspercentage" name="kortingspercentage" value="<?php echo e($_POST['kortingspercentage'] ?? '0.00'); ?>" min="0" max="100" step="0.01" required readonly style="background-color: #f0f0f0;">
        <small>Wordt automatisch ingevuld op basis van soort lid</small>
    </div>
    
    <script>
    function updateKortingspercentage() {
        var soortLidId = document.getElementById('soort_lid_id').value;
        var kortingspercentage = document.getElementById('kortingspercentage');
        
        // Kortingspercentages per soort lid
        var kortingen = {
            '1': 50.00,  // Jeugd
            '2': 40.00,  // Aspirant
            '3': 25.00,  // Junior
            '4': 0.00,   // Senior
            '5': 45.00   // Oudere
        };
        
        if (soortLidId && kortingen[soortLidId] !== undefined) {
            kortingspercentage.value = kortingen[soortLidId].toFixed(2);
        } else {
            kortingspercentage.value = '0.00';
        }
    }
    </script>
    
    <div style="margin: 20px 0;">
        <h3>Contributie Staffels</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #333; color: white;">
                    <th style="padding: 10px; text-align: left;">Categorie</th>
                    <th style="padding: 10px; text-align: left;">Leeftijd</th>
                    <th style="padding: 10px; text-align: left;">Korting</th>
                    <th style="padding: 10px; text-align: left;">Bedrag (basis: €100)</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 10px;">Jeugd</td>
                    <td style="padding: 10px;">0-7 jaar</td>
                    <td style="padding: 10px;">50%</td>
                    <td style="padding: 10px;">€ 50,00</td>
                </tr>
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 10px;">Aspirant</td>
                    <td style="padding: 10px;">8-12 jaar</td>
                    <td style="padding: 10px;">40%</td>
                    <td style="padding: 10px;">€ 60,00</td>
                </tr>
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 10px;">Junior</td>
                    <td style="padding: 10px;">13-17 jaar</td>
                    <td style="padding: 10px;">25%</td>
                    <td style="padding: 10px;">€ 75,00</td>
                </tr>
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 10px;">Senior</td>
                    <td style="padding: 10px;">18-50 jaar</td>
                    <td style="padding: 10px;">0%</td>
                    <td style="padding: 10px;">€ 100,00</td>
                </tr>
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 10px;">Oudere</td>
                    <td style="padding: 10px;">51+ jaar</td>
                    <td style="padding: 10px;">45%</td>
                    <td style="padding: 10px;">€ 55,00</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <button type="submit" class="btn btn-success">Opslaan</button>
    <a href="index.php" class="btn btn-warning">Annuleren</a>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
