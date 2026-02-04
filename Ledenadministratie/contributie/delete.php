<?php
require_once __DIR__ . '/../includes/Auth.php';
Auth::requireAdmin();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = getDBConnection();
$id = $_GET['id'] ?? 0;

// Haal contributie op
$stmt = $pdo->prepare("SELECT * FROM Contributie WHERE id = :id");
$stmt->execute(['id' => $id]);
$contributie = $stmt->fetch();

if (!$contributie) {
    redirect('index.php');
}

// Verwijder contributie
try {
    $stmt = $pdo->prepare("DELETE FROM Contributie WHERE id = :id");
    $stmt->execute(['id' => $id]);
    
    redirect('index.php');
} catch (PDOException $e) {
    die("Fout bij verwijderen: " . $e->getMessage());
}
