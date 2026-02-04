<?php
require_once __DIR__ . '/../includes/Auth.php';
Auth::requireLogin();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = getDBConnection();
$id = $_GET['id'] ?? 0;

// Haal soort lid op
$stmt = $pdo->prepare("SELECT * FROM Soort_lid WHERE id = :id");
$stmt->execute(['id' => $id]);
$soort_lid = $stmt->fetch();

if (!$soort_lid) {
    redirect('index.php');
}

// Verwijder soort lid
try {
    $stmt = $pdo->prepare("DELETE FROM Soort_lid WHERE id = :id");
    $stmt->execute(['id' => $id]);
    
    redirect('index.php');
} catch (PDOException $e) {
    die("Fout bij verwijderen: " . $e->getMessage() . " (mogelijk zijn er nog familieleden of contributies gekoppeld aan dit soort lid)");
}
