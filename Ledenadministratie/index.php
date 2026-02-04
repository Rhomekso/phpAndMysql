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

<div class="info-inline">
    <p style="margin: 0;">Deze applicatie helpt bij het beheren van de ledenadministratie en contributie voor een vereniging.</p>
    <?php if (!Auth::isAdmin()): ?>
    <button type="button" class="info-icon" title="Toon gebruikersinformatie" aria-label="Toon gebruikersinformatie" onclick="window.openUserInfo && window.openUserInfo()">i</button>
    <?php endif; ?>
</div>

<?php if (!Auth::isAdmin()): ?>
<div id="user-info-box" class="info-box dismissible" style="display:none; background-color: #e3f2fd; border-left: 4px solid #2196f3; margin: 20px 0;">
    <button type="button" class="close-btn" aria-label="Sluiten" onclick="window.closeUserInfo && window.closeUserInfo()">&times;</button>
    <h3 style="margin-top: 0; color: #1976d2;">ℹ️ Gebruikersinformatie</h3>
    <p><strong>Je bent ingelogd als gewone gebruiker.</strong></p>
    <p>Je kunt:</p>
    <ul style="margin: 10px 0;">
        <li>✅ Families toevoegen en bewerken</li>
        <li>✅ Familieleden toevoegen en bewerken</li>
    </ul>
    <p>Je kunt <strong>niet</strong>:</p>
    <ul style="margin: 10px 0;">
        <li>❌ Families of familieleden verwijderen</li>
        <li>❌ Contributies beheren</li>
        <li>❌ Boekjaren beheren</li>
        <li>❌ Soort leden beheren</li>
    </ul>
    <p style="margin-top: 15px; font-style: italic;">Heb je iets nodig dat verwijderd of aangepast moet worden? Neem dan contact op met een admin.</p>
</div>
<script>
(function(){
  var key = 'hide_user_info_v1';
  var box = document.getElementById('user-info-box');
  if (!box) return;
  // standaard verborgen; toon alleen als expliciet geopend
  if (window.localStorage && localStorage.getItem(key) === '0') {
    box.style.display = 'block';
  }
  window.openUserInfo = function(){
    if (!box) return;
    box.style.display = 'block';
    try { if (window.localStorage) localStorage.setItem(key, '0'); } catch(e) {}
  };
  window.closeUserInfo = function(){
    if (box) box.style.display = 'none';
    try { if (window.localStorage) localStorage.setItem(key, '1'); } catch(e) {}
  };
})();
</script>
<?php endif; ?>

<?php if (Auth::isAdmin()): ?>
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
    <?php if (Auth::isAdmin()): ?>
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
    <a href="gebruikers/index.php" class="link-card" style="background: #ffd700; color: #333;">
        <strong>Gebruikersbeheer</strong><br>
        <small>Beheer gebruikers en rechten</small>
    </a>
    <?php endif; ?>
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
