<?php
require_once __DIR__ . '/../includes/Auth.php';
Auth::requireLogin();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = getDBConnection();
$id = $_GET['id'] ?? 0;

// Haal boekjaar op
$stmt = $pdo->prepare("SELECT * FROM Boekjaar WHERE id = :id");
$stmt->execute(['id' => $id]);
$boekjaar = $stmt->fetch();

if (!$boekjaar) {
    redirect('index.php');
}

// Verwijder boekjaar (contributies worden automatisch verwijderd via CASCADE)
try {
    $stmt = $pdo->prepare("DELETE FROM Boekjaar WHERE id = :id");
    $stmt->execute(['id' => $id]);
    
    redirect('index.php');
} catch (PDOException $e) {
    die("Fout bij verwijderen: " . $e->getMessage());
}
