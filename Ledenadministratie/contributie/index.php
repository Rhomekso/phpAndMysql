<?php
require_once __DIR__ . '/../includes/Auth.php';
Auth::requireLogin();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Contributies Overzicht';
$pdo = getDBConnection();

// Filter op boekjaar indien opgegeven
$boekjaar_id = $_GET['boekjaar_id'] ?? null;

// Haal boekjaren op voor filter
$stmt = $pdo->query("SELECT * FROM Boekjaar ORDER BY jaar DESC");
$boekjaren = $stmt->fetchAll();

// Haal contributies op
if ($boekjaar_id) {
    $stmt = $pdo->prepare("
        SELECT c.*, s.omschrijving as soort_lid_naam, b.jaar as boekjaar
        FROM Contributie c
        JOIN Soort_lid s ON c.soort_lid_id = s.id
        JOIN Boekjaar b ON c.boekjaar_id = b.id
        WHERE c.boekjaar_id = :boekjaar_id
        ORDER BY c.leeftijd, s.id
    ");
    $stmt->execute(['boekjaar_id' => $boekjaar_id]);
} else {
    $stmt = $pdo->query("
        SELECT c.*, s.omschrijving as soort_lid_naam, b.jaar as boekjaar
        FROM Contributie c
        JOIN Soort_lid s ON c.soort_lid_id = s.id
        JOIN Boekjaar b ON c.boekjaar_id = b.id
        ORDER BY b.jaar DESC, c.leeftijd, s.id
    ");
}
$contributies = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<h1>Contributies Overzicht</h1>

<div class="filter-bar">
    <form method="GET" action="">
        <label for="boekjaar_id">Filter op boekjaar:</label>
        <select id="boekjaar_id" name="boekjaar_id" onchange="this.form.submit()">
            <option value="">-- Alle boekjaren --</option>
            <?php foreach ($boekjaren as $bj): ?>
                <option value="<?php echo $bj['id']; ?>" <?php echo $boekjaar_id == $bj['id'] ? 'selected' : ''; ?>>
                    <?php echo e($bj['jaar']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
    <?php if ($boekjaar_id): ?>
        <a href="index.php" class="btn btn-primary">Reset Filter</a>
    <?php endif; ?>
</div>

<a href="create.php" class="btn btn-success">Nieuwe Contributie Toevoegen</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Boekjaar</th>
            <th>Leeftijd</th>
            <th>Soort Lid</th>
            <th>Basisbedrag</th>
            <th>Korting %</th>
            <th>Te Betalen</th>
            <th>Acties</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($contributies as $contributie): 
            // Bereken te betalen bedrag
            $basisbedrag = floatval($contributie['basisbedrag']);
            $kortingspercentage = floatval($contributie['kortingspercentage']);
            $korting = $basisbedrag * ($kortingspercentage / 100);
            $teBetalen = $basisbedrag - $korting;
        ?>
            <tr>
                <td><?php echo e($contributie['id']); ?></td>
                <td><?php echo e($contributie['boekjaar']); ?></td>
                <td><?php echo e($contributie['leeftijd']); ?> jaar</td>
                <td><?php echo e($contributie['soort_lid_naam']); ?></td>
                <td><?php echo formatEuro($basisbedrag); ?></td>
                <td><?php echo e($kortingspercentage); ?>%</td>
                <td style="font-weight: bold;"><?php echo formatEuro($teBetalen); ?></td>
                <td class="actions">
                    <a href="edit.php?id=<?php echo $contributie['id']; ?>" class="btn btn-warning">Bewerken</a>
                    <a href="delete.php?id=<?php echo $contributie['id']; ?>" class="btn btn-danger" onclick="return confirm('Weet je zeker dat je deze contributie wilt verwijderen?')">Verwijderen</a>
                </td>
            </tr>
        <?php endforeach; ?>
        
        <?php if (empty($contributies)): ?>
            <tr>
                <td colspan="8" class="empty-message">Geen contributies gevonden.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include __DIR__ . '/../includes/footer.php'; ?>
