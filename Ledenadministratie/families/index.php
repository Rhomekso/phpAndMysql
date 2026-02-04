<?php
require_once __DIR__ . '/../includes/Auth.php';
Auth::requireLogin();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Families Overzicht';
$pdo = getDBConnection();

// Haal alle families op
$stmt = $pdo->query("SELECT * FROM Familie ORDER BY naam");
$families = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<h1>Families Overzicht</h1>

<a href="create.php" class="btn btn-success">Nieuwe Familie Toevoegen</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Naam</th>
            <th>Adres</th>
            <th>Aantal Leden</th>
            <th>Contributie</th>
            <th>Acties</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($families as $familie): ?>
            <?php
                // Tel aantal familieleden
                $stmt = $pdo->prepare("SELECT COUNT(*) as aantal FROM Familielid WHERE familie_id = :familie_id");
                $stmt->execute(['familie_id' => $familie['id']]);
                $aantal_leden = $stmt->fetch()['aantal'];
                
                // Bereken contributie
                $contributie = berekenFamilieContributie($pdo, $familie['id']);
            ?>
            <tr>
                <td><?php echo e($familie['id']); ?></td>
                <td><?php echo e($familie['naam']); ?></td>
                <td><?php echo e($familie['adres']); ?></td>
                <td><?php echo e($aantal_leden); ?></td>
                <td><?php echo formatEuro($contributie); ?></td>
                <td class="actions">
                    <a href="../familieleden/index.php?familie_id=<?php echo $familie['id']; ?>" class="btn btn-primary">Leden</a>
                    <a href="edit.php?id=<?php echo $familie['id']; ?>" class="btn btn-warning">Bewerken</a>
                    <?php if (Auth::isAdmin()): ?>
                    <a href="delete.php?id=<?php echo $familie['id']; ?>" class="btn btn-danger" onclick="return confirm('Weet je zeker dat je deze familie wilt verwijderen?')">Verwijderen</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        
        <?php if (empty($families)): ?>
            <tr>
                <td colspan="6" class="empty-message">Geen families gevonden.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include __DIR__ . '/../includes/footer.php'; ?>
