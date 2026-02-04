<?php
require_once __DIR__ . '/../includes/Auth.php';
Auth::requireLogin();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Soort Leden Overzicht';
$pdo = getDBConnection();

// Haal alle soort leden op
$stmt = $pdo->query("SELECT * FROM Soort_lid ORDER BY id");
$soort_leden = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<h1>Soort Leden Overzicht</h1>

<a href="create.php" class="btn btn-success">Nieuw Soort Lid Toevoegen</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Omschrijving</th>
            <th>Acties</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($soort_leden as $soort): ?>
            <tr>
                <td><?php echo e($soort['id']); ?></td>
                <td><?php echo e($soort['omschrijving']); ?></td>
                <td class="actions">
                    <a href="edit.php?id=<?php echo $soort['id']; ?>" class="btn btn-warning">Bewerken</a>
                    <a href="delete.php?id=<?php echo $soort['id']; ?>" class="btn btn-danger" onclick="return confirm('Weet je zeker dat je dit soort lid wilt verwijderen?')">Verwijderen</a>
                </td>
            </tr>
        <?php endforeach; ?>
        
        <?php if (empty($soort_leden)): ?>
            <tr>
                <td colspan="3" class="empty-message">Geen soort leden gevonden.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include __DIR__ . '/../includes/footer.php'; ?>
