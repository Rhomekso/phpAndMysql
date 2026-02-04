<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/Auth.php';

Auth::requireLogin();

$page_title = 'Ledenadministratie - Home';
$pdo = getDBConnection();

// Statistieken ophalen
$stmt = $pdo->query("SELECT COUNT(*) as aantal FROM Familie");
$aantal_families = $stmt->fetch()['aantal'];

$stmt = $pdo->query("SELECT COUNT(*) as aantal FROM Familielid");
$aantal_leden = $stmt->fetch()['aantal'];

$stmt = $pdo->query("SELECT COUNT(*) as aantal FROM Soort_lid");
$aantal_soort_leden = $stmt->fetch()['aantal'];

$stmt = $pdo->query("SELECT COUNT(*) as aantal FROM Boekjaar");
$aantal_boekjaren = $stmt->fetch()['aantal'];

// Bereken totale contributie voor huidig jaar
$huidig_jaar = date('Y');
$stmt = $pdo->prepare("SELECT id FROM Boekjaar WHERE jaar = :jaar");
$stmt->execute(['jaar' => $huidig_jaar]);
$boekjaar = $stmt->fetch();

$totale_contributie = 0;
if ($boekjaar) {
    $stmt = $pdo->query("SELECT id FROM Familie");
    $families = $stmt->fetchAll();
    
    foreach ($families as $familie) {
        $totale_contributie += berekenFamilieContributie($pdo, $familie['id'], $boekjaar['id']);
    }
}

include __DIR__ . '/includes/header.php';
?>

<h1>Welkom bij de Ledenadministratie</h1>

<p>Deze applicatie helpt bij het beheren van de ledenadministratie en contributie voor een vereniging.</p>

<h2>Statistieken</h2>

<div class="stats-grid">
    <div class="stat-card">
        <h3><?php echo $aantal_families; ?></h3>
        <p>Families</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $aantal_leden; ?></h3>
        <p>Familieleden</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $aantal_soort_leden; ?></h3>
        <p>Soort Leden</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $aantal_boekjaren; ?></h3>
        <p>Boekjaren</p>
    </div>
</div>

<?php if ($boekjaar): ?>
    <div class="contributie-card">
        <h3>Totale Contributie <?php echo $huidig_jaar; ?></h3>
        <p class="bedrag"><?php echo formatEuro($totale_contributie); ?></p>
    </div>
<?php endif; ?>

<h2>Snelle Links</h2>

<div class="quick-links">
    <a href="families/index.php" class="link-card">
        <strong>Families Beheren</strong><br>
        <small>Overzicht en beheer van families met contributie</small>
    </a>
    <a href="familieleden/index.php" class="link-card">
        <strong>Familieleden Beheren</strong><br>
        <small>Beheer van individuele familieleden</small>
    </a>
    <a href="soort_lid/index.php" class="link-card">
        <strong>Soort Leden</strong><br>
        <small>Beheer lidmaatschapscategorieën</small>
    </a>
    <a href="contributie/index.php" class="link-card">
        <strong>Contributies</strong><br>
        <small>Beheer contributietarieven</small>
    </a>
    <a href="boekjaar/index.php" class="link-card">
        <strong>Boekjaren</strong><br>
        <small>Beheer boekjaren</small>
    </a>
</div>

<h2>Contributie Staffels</h2>

<table>
    <thead>
        <tr>
            <th>Categorie</th>
            <th>Leeftijd</th>
            <th>Korting</th>
            <th>Bedrag (basis: €100)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Jeugd</td>
            <td>0-7 jaar</td>
            <td>50%</td>
            <td>€ 50,00</td>
        </tr>
        <tr>
            <td>Aspirant</td>
            <td>8-12 jaar</td>
            <td>40%</td>
            <td>€ 60,00</td>
        </tr>
        <tr>
            <td>Junior</td>
            <td>13-17 jaar</td>
            <td>25%</td>
            <td>€ 75,00</td>
        </tr>
        <tr>
            <td>Senior</td>
            <td>18-50 jaar</td>
            <td>0%</td>
            <td>€ 100,00</td>
        </tr>
        <tr>
            <td>Oudere</td>
            <td>51+ jaar</td>
            <td>45%</td>
            <td>€ 55,00</td>
        </tr>
    </tbody>
</table>

<?php include __DIR__ . '/includes/footer.php'; ?>
