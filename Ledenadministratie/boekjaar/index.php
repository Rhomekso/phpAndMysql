<?php
require_once __DIR__ . '/../includes/Auth.php';
Auth::requireLogin();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Boekjaren Overzicht';
$pdo = getDBConnection();

// Haal alle boekjaren op
$stmt = $pdo->query("SELECT * FROM Boekjaar ORDER BY jaar DESC");
$boekjaren = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<h1>Boekjaren Overzicht</h1>

<a href="create.php" class="btn btn-success">Nieuw Boekjaar Toevoegen</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Jaar</th>
            <th>Acties</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($boekjaren as $boekjaar): ?>
            <tr>
                <td><?php echo e($boekjaar['id']); ?></td>
                <td><?php echo e($boekjaar['jaar']); ?></td>
                <td class="actions">
                    <a href="edit.php?id=<?php echo $boekjaar['id']; ?>" class="btn btn-warning">Bewerken</a>
                    <a href="delete.php?id=<?php echo $boekjaar['id']; ?>" class="btn btn-danger" onclick="return confirm('Weet je zeker dat je dit boekjaar wilt verwijderen?')">Verwijderen</a>
                </td>
            </tr>
        <?php endforeach; ?>
        
        <?php if (empty($boekjaren)): ?>
            <tr>
                <td colspan="3" class="empty-message">Geen boekjaren gevonden.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include __DIR__ . '/../includes/footer.php'; ?>
