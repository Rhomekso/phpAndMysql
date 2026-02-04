<?php
require_once __DIR__ . '/../includes/Auth.php';
Auth::requireLogin();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Familieleden Overzicht';
$pdo = getDBConnection();

// Filter op familie indien opgegeven
$familie_id = $_GET['familie_id'] ?? null;
$familie = null;

if ($familie_id) {
    $stmt = $pdo->prepare("SELECT * FROM Familie WHERE id = :id");
    $stmt->execute(['id' => $familie_id]);
    $familie = $stmt->fetch();
}

// Haal familieleden op
if ($familie_id) {
    $stmt = $pdo->prepare("
        SELECT f.*, s.omschrijving as soort_lid_naam, fa.naam as familie_naam
        FROM Familielid f
        JOIN Soort_lid s ON f.soort_lid_id = s.id
        JOIN Familie fa ON f.familie_id = fa.id
        WHERE f.familie_id = :familie_id
        ORDER BY f.naam
    ");
    $stmt->execute(['familie_id' => $familie_id]);
} else {
    $stmt = $pdo->query("
        SELECT f.*, s.omschrijving as soort_lid_naam, fa.naam as familie_naam
        FROM Familielid f
        JOIN Soort_lid s ON f.soort_lid_id = s.id
        JOIN Familie fa ON f.familie_id = fa.id
        ORDER BY fa.naam, f.naam
    ");
}
$familieleden = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<h1>Familieleden Overzicht<?php if ($familie): ?> - Familie <?php echo e($familie['naam']); ?><?php endif; ?></h1>

<?php if ($familie): ?>
    <a href="../families/index.php" class="btn btn-primary">Terug naar Families</a>
<?php endif; ?>
<a href="create.php<?php echo $familie_id ? '?familie_id=' . $familie_id : ''; ?>" class="btn btn-success">Nieuw Familielid Toevoegen</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Naam</th>
            <?php if (!$familie_id): ?>
                <th>Familie</th>
            <?php endif; ?>
            <th>Geboortedatum</th>
            <th>Leeftijd</th>
            <th>Soort Lid</th>
            <th>Contributie</th>
            <th>Acties</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($familieleden as $lid): ?>
            <?php
                $leeftijd = berekenLeeftijd($lid['geboortedatum']);
                $contributie = getContributieBedrag($pdo, $leeftijd, $lid['soort_lid_id'], 1);
            ?>
            <tr>
                <td><?php echo e($lid['id']); ?></td>
                <td><?php echo e($lid['naam']); ?></td>
                <?php if (!$familie_id): ?>
                    <td><?php echo e($lid['familie_naam']); ?></td>
                <?php endif; ?>
                <td><?php echo e(date('d-m-Y', strtotime($lid['geboortedatum']))); ?></td>
                <td><?php echo e($leeftijd); ?> jaar</td>
                <td><?php echo e($lid['soort_lid_naam']); ?></td>
                <td><?php echo formatEuro($contributie); ?></td>
                <td class="actions">
                    <a href="edit.php?id=<?php echo $lid['id']; ?>" class="btn btn-warning">Bewerken</a>
                    <?php if (Auth::isAdmin()): ?>
                    <a href="delete.php?id=<?php echo $lid['id']; ?><?php echo $familie_id ? '&familie_id=' . $familie_id : ''; ?>" class="btn btn-danger" onclick="return confirm('Weet je zeker dat je dit familielid wilt verwijderen?')">Verwijderen</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        
        <?php if (empty($familieleden)): ?>
            <tr>
                <td colspan="<?php echo $familie_id ? '7' : '8'; ?>" class="empty-message">Geen familieleden gevonden.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include __DIR__ . '/../includes/footer.php'; ?>
