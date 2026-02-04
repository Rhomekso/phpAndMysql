<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Bereken leeftijd op basis van geboortedatum
 */
function berekenLeeftijd($geboortedatum) {
    $vandaag = new DateTime();
    $geboorte = new DateTime($geboortedatum);
    return $vandaag->diff($geboorte)->y;
}

/**
 * Haal contributie op voor een familielid op basis van leeftijd, soort lid en boekjaar
 */
function getContributieBedrag($pdo, $leeftijd, $soort_lid_id, $boekjaar_id) {
    $stmt = $pdo->prepare("
        SELECT bedrag 
        FROM Contributie 
        WHERE leeftijd = :leeftijd 
        AND soort_lid_id = :soort_lid_id 
        AND boekjaar_id = :boekjaar_id
    ");
    $stmt->execute([
        'leeftijd' => $leeftijd,
        'soort_lid_id' => $soort_lid_id,
        'boekjaar_id' => $boekjaar_id
    ]);
    
    $result = $stmt->fetch();
    return $result ? $result['bedrag'] : 0.00;
}

/**
 * Bereken totale contributie voor een familie
 */
function berekenFamilieContributie($pdo, $familie_id, $boekjaar_id = null) {
    // Als geen boekjaar opgegeven, pak het huidige jaar
    if ($boekjaar_id === null) {
        $stmt = $pdo->prepare("SELECT id FROM Boekjaar WHERE jaar = :jaar");
        $stmt->execute(['jaar' => date('Y')]);
        $boekjaar = $stmt->fetch();
        $boekjaar_id = $boekjaar ? $boekjaar['id'] : 1;
    }
    
    // Haal alle familieleden op
    $stmt = $pdo->prepare("
        SELECT id, geboortedatum, soort_lid_id 
        FROM Familielid 
        WHERE familie_id = :familie_id
    ");
    $stmt->execute(['familie_id' => $familie_id]);
    $familieleden = $stmt->fetchAll();
    
    $totaal = 0.00;
    foreach ($familieleden as $lid) {
        $leeftijd = berekenLeeftijd($lid['geboortedatum']);
        $contributie = getContributieBedrag($pdo, $leeftijd, $lid['soort_lid_id'], $boekjaar_id);
        $totaal += $contributie;
    }
    
    return $totaal;
}

/**
 * Formatteer bedrag als Euro
 */
function formatEuro($bedrag) {
    return '€ ' . number_format($bedrag, 2, ',', '.');
}

/**
 * Escape HTML output
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect naar een andere pagina
 */
function redirect($url) {
    header("Location: $url");
    exit;
}
