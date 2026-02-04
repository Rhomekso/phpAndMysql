<?php
require_once __DIR__ . '/../includes/Auth.php';
Auth::requireAdmin();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Gebruikersbeheer';
$pdo = getDBConnection();

// Haal alle gebruikers op
$stmt = $pdo->query("
    SELECT id, username, email, rol, actief, last_login, created_at 
    FROM User 
    ORDER BY created_at DESC
");
$gebruikers = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<h1>Gebruikersbeheer</h1>

<div style="margin-bottom: 20px;">
    <a href="create.php" class="btn btn-success">Nieuwe Gebruiker Toevoegen</a>
</div>

<?php if (empty($gebruikers)): ?>
    <p class="empty-message">Geen gebruikers gevonden.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Gebruikersnaam</th>
                <th>E-mail</th>
                <th>Rol</th>
                <th>Status</th>
                <th>Laatste Login</th>
                <th>Aangemaakt</th>
                <th>Acties</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($gebruikers as $gebruiker): ?>
                <tr>
                    <td><?php echo e($gebruiker['id']); ?></td>
                    <td><?php echo e($gebruiker['username']); ?></td>
                    <td><?php echo e($gebruiker['email']); ?></td>
                    <td>
                        <?php if ($gebruiker['rol'] === 'admin'): ?>
                            <span style="color: #dc3545; font-weight: bold;">Admin</span>
                        <?php else: ?>
                            <span style="color: #28a745;">Gebruiker</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($gebruiker['actief']): ?>
                            <span style="color: #28a745;">✓ Actief</span>
                        <?php else: ?>
                            <span style="color: #dc3545;">✗ Inactief</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $gebruiker['last_login'] ? e(date('d-m-Y H:i', strtotime($gebruiker['last_login']))) : 'Nooit'; ?></td>
                    <td><?php echo e(date('d-m-Y H:i', strtotime($gebruiker['created_at']))); ?></td>
                    <td class="actions">
                        <a href="edit.php?id=<?php echo $gebruiker['id']; ?>" class="btn btn-primary">Bewerken</a>
                        <?php if ($gebruiker['username'] !== 'admin' && $gebruiker['username'] !== 'mekso'): ?>
                            <a href="delete.php?id=<?php echo $gebruiker['id']; ?>" 
                               class="btn btn-danger" 
                               onclick="return confirm('Weet je zeker dat je deze gebruiker wilt verwijderen?');">Verwijderen</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
