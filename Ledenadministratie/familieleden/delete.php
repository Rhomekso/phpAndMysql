<?php
require_once __DIR__ . '/../includes/Auth.php';
Auth::requireAdmin();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = getDBConnection();
$id = $_GET['id'] ?? 0;
$familie_id = $_GET['familie_id'] ?? null;

// Haal familielid op
$stmt = $pdo->prepare("SELECT * FROM Familielid WHERE id = :id");
$stmt->execute(['id' => $id]);
$familielid = $stmt->fetch();

if (!$familielid) {
    redirect('index.php');
}

// Verwijder familielid
try {
    $stmt = $pdo->prepare("DELETE FROM Familielid WHERE id = :id");
    $stmt->execute(['id' => $id]);
    
    redirect('index.php' . ($familie_id ? '?familie_id=' . $familie_id : ''));
} catch (PDOException $e) {
    die("Fout bij verwijderen: " . $e->getMessage());
}
