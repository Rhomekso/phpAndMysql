<?php
require_once __DIR__ . '/../includes/Auth.php';
Auth::requireAdmin();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = getDBConnection();
$id = $_GET['id'] ?? 0;

// Haal gebruiker op
$stmt = $pdo->prepare("SELECT * FROM User WHERE id = :id");
$stmt->execute(['id' => $id]);
$gebruiker = $stmt->fetch();

if (!$gebruiker) {
    redirect('index.php');
}

// Bescherm admin en mekso tegen verwijdering
if (in_array($gebruiker['username'], ['admin', 'mekso'])) {
    redirect('index.php?error=protected_user');
}

// Verwijder gebruiker
try {
    $stmt = $pdo->prepare("DELETE FROM User WHERE id = :id");
    $stmt->execute(['id' => $id]);
    
    redirect('index.php');
} catch (PDOException $e) {
    redirect('index.php?error=delete_failed');
}
