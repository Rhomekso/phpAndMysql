# Ledenadministratie Applicatie - Verslag

## Voorblad

**Naam: Rhomekso Azwar**  
**Studentnummer: 311031242**  
**Datum: 02-02-2026**  

---

## 1. Beschrijving Gebruikte Tools

### Ontwikkelomgeving
- **Besturingssysteem:** Ubuntu Linux
- **Webserver:** Apache (voor het draaien van de website)
- **Editor:** VS Code (voor het schrijven van code)

### Programmeertalen en Versies
- **PHP:** 7.4 of hoger (de hoofdtaal voor de website)
- **SQL:** MySQL 5.7+ / MariaDB 10.2+ (voor de database)
- **HTML5:** Voor de structuur van webpagina's
- **CSS3:** Voor het uiterlijk en design van de website
- **JavaScript:** Voor interactieve elementen in formulieren

### Database Beheer
- **MySQL:** Het systeem dat de database beheert
- **PDO:** Een manier om veilig met de database te communiceren

### Versiebeheer
- **Git:** Voor het bijhouden van wijzigingen in de code
- **GitHub:** Voor het zien van de code online.

### Overige Tools
- **Browser Developer Tools:** Voor het testen en debuggen/om de kijken of de Cookies werken.
- **Postman:** Om te kijken of de endpoint wel goed aankomen en werken.

### Benodigde PHP Onderdelen
- `pdo` - voor database communicatie
- `pdo_mysql` - specifiek voor MySQL databases
- `session` - voor het onthouden van ingelogde gebruikers
- `hash` - voor het veilig opslaan van wachtwoorden

---

## 2. Beschrijving Database

### Database Overzicht
**Database naam:** `ledenadministratie`  
**Tekenset:** utf8mb4 (ondersteunt Nederlandse tekens en emoji's)  
**Aantal tabellen:** 6

### Database Structuur

#### Tabel: User
**Doel:** Opslaan van gebruikersgegevens voor het inloggen

**Kolommen:**
| Kolomnaam | Datatype | Beperkingen | Beschrijving |
|-----------|----------|-------------|--------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Uniek nummer per gebruiker |
| username | VARCHAR(50) | UNIQUE, NOT NULL | Gebruikersnaam voor inloggen |
| email | VARCHAR(100) | UNIQUE, NOT NULL | Email adres gebruiker |
| password | VARCHAR(255) | NOT NULL | Versleuteld wachtwoord |
| rol | ENUM('admin','user') | DEFAULT 'user' | Type gebruiker (beheerder of gewone gebruiker) |
| actief | TINYINT(1) | DEFAULT 1 | Of het account actief is |
| last_login | DATETIME | NULL | Wanneer de gebruiker voor het laatst inlogde |

**Extra snelheid:**
- Snelle opzoekingen op username en email

---

#### Tabel: Familie
**Doel:** Opslaan van familiegegevens

**Kolommen:**
| Kolomnaam | Datatype | Beperkingen | Beschrijving |
|-----------|----------|-------------|--------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Uniek nummer per familie |
| naam | VARCHAR(100) | NOT NULL | Familienaam |
| adres | TEXT | NOT NULL | Volledig adres |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Wanneer aangemaakt |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE | Wanneer laatst gewijzigd |

---

#### Tabel: Soort_lid
**Doel:** De verschillende soorten leden (jeugd, senior, etc.)

**Kolommen:**
| Kolomnaam | Datatype | Beperkingen | Beschrijving |
|-----------|----------|-------------|--------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Uniek nummer per soort |
| omschrijving | VARCHAR(50) | NOT NULL | Naam van de categorie |

**Standaard data:**
1. Jeugd (0-7 jaar, 50% korting)
2. Aspirant (8-12 jaar, 40% korting)
3. Junior (13-17 jaar, 25% korting)
4. Senior (18-50 jaar, geen korting)
5. Oudere (51+ jaar, 45% korting)

---

#### Tabel: Familielid
**Doel:** Opslaan van individuele leden die bij een familie horen

**Kolommen:**
| Kolomnaam | Datatype | Beperkingen | Beschrijving |
|-----------|----------|-------------|--------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Uniek nummer per lid |
| naam | VARCHAR(100) | NOT NULL | Voor- en achternaam |
| geboortedatum | DATE | NOT NULL | Geboortedatum (voor leeftijd berekenen) |
| soort_lid_id | INT | FOREIGN KEY, NOT NULL | Verwijzing naar de soort lid |
| familie_id | INT | FOREIGN KEY, NOT NULL | Verwijzing naar de familie |

**Koppelingen:**
- `soort_lid_id` → verwijst naar `Soort_lid` (kan niet verwijderd worden als er leden zijn)
- `familie_id` → verwijst naar `Familie` (bij verwijderen familie worden leden ook verwijderd)

**Werking:** Als je een familie verwijdert, worden alle leden van die familie automatisch ook verwijderd. Een soort lid (bijv. "Junior") kan alleen verwijderd worden als er geen leden meer van die soort zijn.

---

#### Tabel: Boekjaar
**Doel:** Opslaan van verschillende jaren (voor verschillende contributietarieven per jaar)

**Kolommen:**
| Kolomnaam | Datatype | Beperkingen | Beschrijving |
|-----------|----------|-------------|--------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Uniek nummer per boekjaar |
| jaar | INT | UNIQUE, NOT NULL | Jaartal (bijv. 2024, 2025) |

**Controle:** Jaar moet tussen 1900 en 2100 liggen

---

#### Tabel: Contributie
**Doel:** Opslaan van contributietarieven per leeftijd, soort lid en jaar

**Kolommen:**
| Kolomnaam | Datatype | Beperkingen | Beschrijving |
|-----------|----------|-------------|--------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Uniek nummer per tarief |
| leeftijd | INT | NOT NULL | Leeftijd (0-100) |
| soort_lid_id | INT | FOREIGN KEY, NOT NULL | Verwijzing naar soort lid |
| bedrag | DECIMAL(10,2) | NOT NULL | Contributiebedrag in euro's |
| boekjaar_id | INT | FOREIGN KEY, NOT NULL | Verwijzing naar boekjaar |

**Koppelingen:**
- `soort_lid_id` → verwijst naar `Soort_lid`
- `boekjaar_id` → verwijst naar `Boekjaar` (bij verwijderen jaar worden tarieven ook verwijderd)

**Extra regel:** Dezelfde combinatie van leeftijd, soort en jaar kan maar één keer voorkomen

**Startgegevens:** 505 tarieven (101 leeftijden × 5 soorten) voor het huidige jaar

---

### Hoe de Tabellen Met Elkaar Verbonden Zijn

```
User (voor inloggen)
   [staat los van de andere tabellen]

Familie ━━━━━━━━━━━━━━━━━━━> Familielid
   (1 familie heeft meerdere leden)
   
 Familielid ━━━━━━━━━━━━━> Soort_lid
   (elk lid is van 1 soort)
   
Soort_lid ━━━━━━━━━━━━━> Contributie
   (elke soort heeft meerdere tarieven)
   
Boekjaar ━━━━━━━━━━━━━━> Contributie
   (elk jaar heeft eigen tarieven)
```

### Uitleg Verbindingen

**Familie → Familielid (1 naar meerdere)**
- Één familie kan meerdere leden hebben
- Als je een familie verwijdert, worden alle bijbehorende leden ook verwijderd
```
Voorbeeld: Als "Familie Jansen" wordt verwijderd, verdwijnen Jan, Piet en Marie ook
```

**Familielid → Soort_lid (meerdere naar 1)**
- Elk lid hoort bij één soort (bijvoorbeeld "Junior" of "Senior")
- Je kunt een soort alleen verwijderen als er geen leden meer van die soort zijn
```
Voorbeeld: "Junior" kan pas verwijderd worden als er geen junior-leden meer zijn
```

**Soort_lid → Contributie (1 naar meerdere)**
- Voor elke soort lid zijn er meerdere tarieven (voor verschillende leeftijden en jaren)
- Je kunt een soort alleen verwijderen als er geen tarieven meer voor bestaan
```
Voorbeeld: "Junior" heeft 101 tarieven (één voor elke leeftijd van 0-100)
```

**Boekjaar → Contributie (1 naar meerdere)**
- Elk jaar heeft zijn eigen set tarieven
- Als je een jaar verwijdert, worden alle tarieven van dat jaar ook verwijderd
```
Voorbeeld: Als je boekjaar 2023 verwijdert, verdwijnen alle 505 tarieven van 2023
```

### Snelheid en Efficiency
- **Snelle opzoekingen:** De database kan snel zoeken op gebruikersnaam en email
- **Automatische verbindingen:** De links tussen tabellen zorgen voor snelle verbindingen
- **Tekens:** Ondersteunt alle Nederlandse tekens en emoji's

### Gegevens Bij Start
- 2 gebruikers (admin en mekso)
- 5 soorten leden (Jeugd, Aspirant, Junior, Senior, Oudere)
- 1 boekjaar (het huidige jaar)
- 505 contributietarieven (101 leeftijden × 5 soorten)

---

## 3. Beschrijving Werking Applicatie

### Wat Kan de Applicatie?

De Ledenadministratie applicatie is een website voor het beheren van een vereniging. De belangrijkste functies zijn:

#### Hoofdfuncties
1. **Inloggen:** Gebruikers kunnen inloggen met een gebruikersnaam en wachtwoord
2. **Familie- en Ledenbeheer:** Je kunt families en leden toevoegen, bekijken, aanpassen en verwijderen. De leeftijd wordt automatisch berekend
3. **Contributiebeheer:** De contributie wordt automatisch berekend op basis van leeftijd, soort lid en het jaar
4. **Overzichtspagina:** Een startpagina met cijfers en overzichten van alle gegevens
5. **Gebruikersrechten:** Er zijn twee soorten gebruikers: beheerders (admin) en gewone gebruikers

### Bestandsstructuur
```
Ledenadministratie/
│
├── 📁 auth/                        # Authenticatie module (3 bestanden)
│   ├── login.php                   # Login met remember me
│   ├── logout.php                  # Logout, verwijder sessions/cookies
│   └── register.php                # Registratie nieuwe gebruikers
│
├── 📁 models/                      # OOP Model Layer (7 bestanden)
│   ├── Model.php                   # Abstract base class met CRUD
│   ├── User.php                    # User model met authenticate(), register()
│   ├── Familie.php                 # Familie model
│   ├── Familielid.php             # Familielid model
│   ├── SoortLid.php               # Soort lid model
│   ├── Contributie.php            # Contributie model
│   └── Boekjaar.php               # Boekjaar model
│
├── 📁 includes/                    # Helper files (4 bestanden)
│   ├── Auth.php                    # Session/cookie management class
│   ├── functions.php               # Utility functies (6 functies)
│   ├── header.php                  # HTML header + navigatie + CSS
│   └── footer.php                  # HTML footer
│
├── 📁 config/                      # Configuratie (1 bestand)
│   └── database.php                # Database config + PDO connectie
│
├── 📁 database/                    # Database schema (1 bestand)
│   └── schema.sql                  # Complete database schema (580+ regels)
│
├── 📁 families/                    # Familie CRUD (4 bestanden)
│   ├── index.php                   # Overzicht families
│   ├── create.php                  # Nieuwe familie
│   ├── edit.php                    # Familie bewerken
│   └── delete.php                  # Familie verwijderen
│
├── 📁 familieleden/               # Familielid CRUD (4 bestanden)
│   ├── index.php                   # Overzicht leden
│   ├── create.php                  # Nieuw lid
│   ├── edit.php                    # Lid bewerken
│   └── delete.php                  # Lid verwijderen
│
├── 📁 soort_lid/                  # Soort Lid CRUD (4 bestanden)
│   ├── index.php                   # Overzicht soorten
│   ├── create.php                  # Nieuwe soort
│   ├── edit.php                    # Soort bewerken
│   └── delete.php                  # Soort verwijderen
│
├── 📁 contributie/                # Contributie CRUD (4 bestanden)
│   ├── index.php                   # Overzicht tarieven
│   ├── create.php                  # Nieuw tarief
│   ├── edit.php                    # Tarief bewerken
│   └── delete.php                  # Tarief verwijderen
│
├── 📁 boekjaar/                   # Boekjaar CRUD (4 bestanden)
│   ├── index.php                   # Overzicht boekjaren
│   ├── create.php                  # Nieuw boekjaar
│   ├── edit.php                    # Boekjaar bewerken
│   └── delete.php                  # Boekjaar verwijderen
│
├── 📁 gebruikers/                # Gebruikersbeheer (4 bestanden)
│   ├── index.php                   # Overzicht gebruikers (admin-only)
│   ├── create.php                  # Nieuwe gebruiker (admin-only)
│   ├── edit.php                    # Gebruiker bewerken (admin-only)
│   └── delete.php                  # Gebruiker verwijderen (admin-only)
│
├── index.php                       # Dashboard / Home pagina
└── README.md                       # Deze documentatie
```

**Totaal:** 36 PHP bestanden, 1 SQL bestand, 2485+ regels code

---

### Beschrijving per Module

#### Module: Authenticatie (auth/)

**Bestand: login.php**
- **Functie:** Gebruikers authenticatie met username/password
- **Features:**
  - CSRF token validatie
  - Remember me functionaliteit (30 dagen cookie)
  - Session regeneration tegen session fixation | Dit is een standaard beveiligingstechniek om te voorkomen dat hackers toegang krijgen tot accounts door sessie-ID's te stelen of te manipuleren.
  - Foutafhandeling met duidelijke berichten
- **Database interactie:** User::authenticate($username, $password) met prepared statement
- **Flow:** 
  1. Formulier weergave met CSRF token
  2. POST validatie
  3. User model authenticate() methode
  4. Bij success: Auth::login() met cookie optie
  5. Redirect naar dashboard

**Bestand: register.php**
- **Functie:** Nieuwe gebruikers kunnen account aanmaken
- **Validatie:**
  - Username uniekheid checken
  - Email format en uniekheid
  - Wachtwoord minimum 6 karakters
- **Database interactie:** User::register() met bcrypt password hashing
- **Beveiliging:** Password hashed met PASSWORD_DEFAULT (bcrypt)

**Bestand: logout.php**
- **Functie:** Gebruiker uitloggen en sessie beëindigen
- **Acties:**
  1. Auth::logout() verwijdert sessie data
  2. Remember me cookie wordt verwijderd
  3. Session destroy
  4. Redirect naar login

---

#### Module: Models (models/)

**Bestand: Model.php**
- **Functie:** Abstract base class met herbruikbare CRUD operaties
- **Belangrijkste methoden:**
  - `all($orderBy = '')` - SELECT * met optionele ORDER BY
  - `find($id)` - SELECT WHERE id = ?
  - `create($data)` - INSERT met prepared statement
  - `update($id, $data)` - UPDATE WHERE id = ?
  - `delete($id)` - DELETE WHERE id = ?
  - `count($criteria = [])` - COUNT met WHERE condities
  - `query($sql, $params)` - Custom SELECT queries
- **Design patroon:** Geprobeerd om DRY (Dont Repeat Yourself)toe te passen zodat ik geen herhalende code gebruik.
- **PDO gebruik:** Alle queries met prepared statements tegen SQL injection

**Bestand: User.php (extends Model)**
- **Functie:** Gebruikersbeheer met authenticatie
- **Speciale methoden:**
  - `authenticate($username, $password)` - Login validatie met password_verify()
  - `register($username, $email, $password)` - Nieuwe user met validatie
  - `updatePassword($userId, $newPassword)` - Wachtwoord wijzigen
  - `isAdmin($userId)` - Check admin rol
- **Beveiliging:** Wachtwoord word omgezet naar onleesbare codes, invoer van gerbuikers word schoongemaakt zodat ze geen kwade code kunnen invoeren.

**Bestand: Familie.php (extends Model)**
- **Functie:** Familie CRUD operaties
- **Velden:** naam, adres, created_at, updated_at
- **Inheritance:** Gebruikt alle Model.php methoden

**Bestand: Familielid.php (extends Model)**
- **Functie:** Familielid CRUD met relaties
- **Velden:** naam, geboortedatum, soort_lid_id (FK), familie_id (FK)
- **Business logic:** Leeftijd wordt berekend uit geboortedatum

**Bestand: SoortLid.php (extends Model)**
- **Functie:** Lidmaatschapscategorieën beheer
- **Velden:** omschrijving
- **Standaard:** 5 soorten (Jeugd, Aspirant, Junior, Senior, Oudere)

**Bestand: Contributie.php (extends Model)**
- **Functie:** Tariefbeheer per leeftijd/soort/jaar
- **Velden:** leeftijd, soort_lid_id (FK), bedrag, boekjaar_id (FK)
- **Unieke constraint:** Geen dubbele tarieven per combinatie

**Bestand: Boekjaar.php (extends Model)**
- **Functie:** Boekjaar periodes beheer
- **Velden:** jaar (UNIQUE)
- **Validatie:** Jaar tussen 1900-2100

---

#### Module: Includes (includes/)

**Bestand: Auth.php**
- **Functie:** Centraal authenticatie en autorisatie management
- **Session methoden:**
  - `init()` - Start sessie met secure settings
  - `login($user, $remember)` - Login met optionele remember cookie
  - `logout()` - Destroy sessie en cookies
  - `check()` - Is gebruiker ingelogd?
  - `user()` - Haal huidige gebruiker data
  - `userId()` - Haal user ID
- **Access Control:**
  - `requireLogin()` - Forceer login, redirect anders
  - `requireAdmin()` - Forceer admin rol
  - `isAdmin()` - Check admin status
- **CSRF Protection:**
  - `generateCsrfToken()` - Maak token in sessie
  - `csrfField()` - Output hidden input field
  - `validateCsrfToken($token)` - Valideer POST token
- **Cookie management:** Secure, HttpOnly, SameSite=Strict cookies voor remember me
- **Beveiliging:** Sessie word vernieuwd bij inloggen, andere mensen kunnen niet meekijken op je oude sessie.

**Bestand: functions.php**
- **Functie:** Utility functies voor hele applicatie
- **Functies:**
  - `berekenLeeftijd($geboortedatum)` - DateTime verschil berekening
  - `getContributieBedrag($pdo, $leeftijd, $soortLidId, $boekjaarId)` - Query tarief uit database
  - `berekenFamilieContributie($pdo, $familieId, $boekjaarId)` - Loop leden, tel bedragen op
  - `formatEuro($bedrag)` - Number format met € prefix
  - `e($string)` - XSS bescherming via htmlspecialchars()
  - `redirect($url)` - Header location met exit

**Bestand: header.php**
- **Functie:** HTML head + navigatie menu + CSS styling
- **Bevat:**
  - HTML5 DOCTYPE en meta tags
  - Embedded CSS (modern grid/flexbox layout)
  - Navigatiemenu met conditionele links (admin/user)
  - Logout knop met user indicator
- **Responsive:** Mobile-first CSS met media queries

**Bestand: footer.php**
- **Functie:** HTML footer met copyright
- **Eenvoudig:** Sluit container en body tags

---

#### Module: Families (families/)

**Pagina: index.php**
- **Functie:** Overzicht alle families met statistieken
- **Database queries:**
  - Familie::all() voor families lijst
  - COUNT familieleden per familie (subquery)
  - Contributie berekening per familie via helper functie
- **UI elementen:**
  - Tabel met kolommen: naam, adres, aantal leden, contributie
  - "Nieuwe familie" knop
  - Edit/Delete acties per rij
- **Empty state:** Bericht wanneer geen families

**Pagina: create.php**
- **Functie:** Formulier nieuwe familie aanmaken
- **Velden:** naam (text), adres (textarea)
- **Validatie:**
  - Server-side: naam en adres verplicht
  - CSRF token check
- **Database interactie:** Familie::create($data) met POST data
- **Success:** Redirect naar index met success bericht

**Pagina: edit.php**
- **Functie:** Bestaande familie bewerken
- **Flow:**
  1. GET id parameter
  2. Familie::find($id) ophalen
  3. Pre-filled formulier tonen
  4. POST: Familie::update($id, $data)
- **Validatie:** Zelfde als create
- **Error handling:** 404 als familie niet bestaat

**Pagina: delete.php**
- **Functie:** Familie verwijderen
- **Methode:** POST met CSRF token
- **Database:** Familie::delete($id)
- **CASCADE:** Alle familieleden worden automatisch verwijderd
- **Confirmatie:** JavaScript confirm dialog in index.php

---

#### Module: Familieleden (familieleden/)

**Pagina: index.php**
- **Functie:** Overzicht alle leden met relaties
- **JOIN query:** Familielid LEFT JOIN Familie LEFT JOIN Soort_lid
- **Kolommen:**
  - Naam lid
  - Geboortedatum
  - Leeftijd (berekend via berekenLeeftijd())
  - Soort lid
  - Familie naam
  - Acties
- **Features:** ORDER BY familielid.naam

**Pagina: create.php**
- **Functie:** Nieuw familielid toevoegen
- **Formulier velden:**
  - naam (text input)
  - geboortedatum (date picker, HTML5)
  - familie_id (dropdown uit Familie::all())
  - soort_lid_id (dropdown uit SoortLid::all())
- **Validatie:**
  - Alle velden verplicht
  - Geboortedatum geldig date format
  - Foreign keys bestaan in database
- **Database:** Familielid::create() met POST data

**Pagina: edit.php**
- **Functie:** Lid gegevens wijzigen
- **Pre-fill:** Familielid::find($id) voor huidige waardes
- **Dropdowns:** Selected option op huidige familie/soort
- **Update:** Familielid::update($id, $data)

**Pagina: delete.php**
- **Functie:** Lid verwijderen
- **Simpel:** Geen CASCADE, alleen record delete
- **Check:** Familielid::delete($id)

---

#### Module: Soort Lid (soort_lid/)

**Pagina: index.php**
- **Functie:** Lijst 5 lidmaatschapscategorieën
- **Query:** SoortLid::all('omschrijving')
- **Info:** Toont contributie staffels per categorie
- **CRUD:** Create/Edit/Delete links

**Pagina: create.php**
- **Functie:** Nieuwe soort lid categorie
- **Veld:** omschrijving (text)
- **Gebruik:** Voor custom categorieën naast standaard 5

**Pagina: edit.php**
- **Functie:** Soort omschrijving wijzigen
- **Update:** SoortLid::update($id, $data)

**Pagina: delete.php**
- **Functie:** Soort verwijderen
- **RESTRICT:** Fails als er familieleden of contributie tarieven zijn
- **Error:** Foreign key constraint error afvangen

---

#### Module: Contributie (contributie/)

**Pagina: index.php**
- **Functie:** Overzicht alle contributie tarieven
- **Filter:** Dropdown boekjaar selectie
- **JOIN:** Contributie met Soort_lid en Boekjaar
- **Kolommen:** leeftijd, soort omschrijving, bedrag, jaar
- **Paginering:** Bij 505+ records (alle leeftijden)

**Pagina: create.php**
- **Functie:** Nieuw tarief toevoegen
- **Velden:**
  - leeftijd (number 0-100)
  - soort_lid_id (dropdown)
  - bedrag (decimal, 2 decimalen)
  - boekjaar_id (dropdown)
- **Validatie:**
  - Unieke constraint (leeftijd, soort, jaar)
  - Bedrag positief getal
- **Use case:** Custom tarieven voor specifieke leeftijden

**Pagina: edit.php**
- **Functie:** Tarief bedrag aanpassen
- **Vaak gebruikt:** Jaarlijkse tarief updates

**Pagina: delete.php**
- **Functie:** Tarief verwijderen
- **Check:** Unique constraint verwijderen mogelijk

---

#### Module: Boekjaar (boekjaar/)

**Pagina: index.php**
- **Functie:** Lijst boekjaren
- **Query:** Boekjaar::all('jaar DESC')
- **Info:** Toont aantal tarieven per boekjaar

**Pagina: create.php**
- **Functie:** Nieuw boekjaar aanmaken
- **Veld:** jaar (integer)
- **Validatie:** 1900-2100, UNIQUE constraint
- **Use case:** Start nieuw verenigingsjaar

**Pagina: edit.php**
- **Functie:** Jaar nummer wijzigen
- **Zeldzaam:** Meestal alleen create/delete

**Pagina: delete.php**
- **Functie:** Boekjaar verwijderen
- **CASCADE:** Alle contributie tarieven worden verwijderd
- **Waarschuwing:** Data loss mogelijk, confirmatie vereist

---

#### Dashboard (index.php)

**Functie:** Centrale overzichtspagina na login

**Statistiek Cards (4):**
1. **Totaal Families** - COUNT query op Familie tabel
2. **Totaal Familieleden** - COUNT query op Familielid tabel
3. **Soort Leden** - COUNT query op Soort_lid tabel
4. **Boekjaren** - COUNT query op Boekjaar tabel

**Contributie Overzicht:**
- **Totale Contributie Huidig Jaar:**
  - Loop alle families
  - Per familie: berekenFamilieContributie()
  - Tel alle bedragen op
  - Format als Euro bedrag
- **Staffels Tabel:** Statisch overzicht 5 categorieën met leeftijd/korting

**Quick Links (5):**
1. Families Beheren → families/index.php
2. Familieleden Beheren → familieleden/index.php
3. Soort Leden → soort_lid/index.php
4. Contributie Tarieven → contributie/index.php
5. Boekjaren Beheren → boekjaar/index.php

**Design:** Grid layout met cards, responsive, moderne CSS

---

### Gebruikersinterface

#### Design Principes
- **Mobile-first:** Responsive layout werkt op alle schermen
- **Consistent:** Zelfde styling door hele applicatie
- **Clean:** Geen clutter, focus op functionaliteit
- **Accessible:** Semantische HTML, labels op inputs

#### UI Componenten

**Navigatie Menu:**
- Horizontaal menu boven aan pagina
- Links: Dashboard, Families, Leden, Soort Leden, Contributie, Boekjaren
- Rechts: Gebruiker indicator + Logout knop
- Sticky top bij scrollen

**Formulieren:**
- Labels boven inputs
- Required fields met * indicator
- Submit + Cancel knoppen
- CSRF hidden input
- Client-side HTML5 validatie
- Server-side validatie met error messages

**Tabellen:**
- Striped rows voor leesbaarheid
- Actions kolom met edit/delete icons
- Hover effect op rows
- Empty state bericht bij geen data

**Buttons:**
- Primary: Blauwe button voor main actions
- Success: Groene button voor create
- Danger: Rode button voor delete
- Ghost: Transparante button voor cancel

**Cards:**
- Dashboard statistics in grid
- Border, shadow, padding voor depth
- Icon + number + label layout

---

### Workflow Voorbeelden

#### Workflow 1: Nieuwe Familie en Leden Toevoegen
1. Login via auth/login.php met credentials (admin/admin123 of mekso/klopklop123)
2. Dashboard toont:
   - Voor admins: statistieken (0 families, 0 leden, 5 soort leden, 2 boekjaren) en totale contributie 2026
   - Voor gewone gebruikers: geen statistieken, wel snelle links naar Families en Familieleden
3. Klik "Families" in navigatie → families/index.php (of gebruik snelle link)
4. Klik "Nieuwe Familie Toevoegen" → families/create.php
5. Vul formulier: naam "Familie Jansen", adres "Dorpsstraat 1, 1234AB Amsterdam"
6. Submit → INSERT INTO Familie → Redirect naar families/index.php
7. In families/index.php: klik "Leden" button bij Familie Jansen → familieleden/index.php?familie_id=X
8. Klik "Nieuw Familielid Toevoegen" → familieleden/create.php?familie_id=X
9. Vul formulier:
   - Naam: "Jan Jansen"
   - Geboortedatum: "2010-05-15" (13 jaar)
   - Soort Lid: selecteer "Junior" → kortingspercentage wordt automatisch ingevuld (25%)
   - Familie: voorgeselecteerd "Familie Jansen"
   - Basisbedrag: €100 (readonly)
   - Kortingspercentage: 25% (readonly, automatisch)
10. Onderaan formulier: Staffels tabel toont alle categorieën met leeftijd, korting en bedrag
11. Submit → INSERT INTO Familielid → Redirect naar familieleden/index.php?familie_id=X
12. Herhaal stap 8-11 voor andere leden (Piet, Marie)
13. Dashboard toont nu (alleen voor admins): 1 familie, 3 leden, totale contributie berekend

#### Workflow 2: Contributie Berekening Controleren
1. Ga naar "Families" → families/index.php
2. Tabel toont per familie: ID, Naam, Adres, Aantal Leden, Contributie en Acties
3. Contributie wordt automatisch berekend via berekenFamilieContributie():
   - Haal alle leden van familie op uit Familielid tabel
   - Per lid: berekenLeeftijd(geboortedatum) → leeftijd in jaren
   - Query contributie tarief: SELECT basisbedrag, kortingspercentage WHERE leeftijd = X AND soort_lid_id = Y AND boekjaar_id = 1 (2026)
   - Bereken: Te betalen = basisbedrag - (basisbedrag × kortingspercentage / 100)
   - Tel alle bedragen op
4. Voorbeeld Familie Jansen met 3 leden:
   - Jan (geb. 2010-05-15, 15 jaar, Junior): basisbedrag €100 - 25% = **€75,00**
   - Piet (geb. 1978-03-20, 47 jaar, Senior): basisbedrag €100 - 0% = **€100,00**
   - Marie (geb. 2016-08-10, 9 jaar, Aspirant): basisbedrag €100 - 40% = **€60,00**
   - **Totaal Familie Jansen: € 235,00**
5. Bij Familieleden overzicht: elke rij toont naam, geboortedatum, leeftijd, soort lid en individuele contributie
6. Contributie Staffels tabel (op dashboard en contributie formulieren): overzicht van alle 5 categorieën met leeftijdsbereik, korting% en te betalen bedrag

#### Workflow 3: Contributietarieven Beheren (alleen admin)
1. Login als admin → navigatie toont "Contributies" link
2. Klik "Contributies" → contributie/index.php
3. Pagina toont:
   - Filter dropdown: selecteer boekjaar (standaard: alle boekjaren)
   - Tabel met kolommen: ID, Boekjaar, Leeftijd, Soort Lid, Basisbedrag, Korting%, Te Betalen, Acties
4. Huidige data: 505 tarieven voor boekjaar 2026:
   - Jeugd (0-7 jaar): basisbedrag €100, korting 50%, te betalen €50
   - Aspirant (8-12 jaar): basisbedrag €100, korting 40%, te betalen €60
   - Junior (13-17 jaar): basisbedrag €100, korting 25%, te betalen €75
   - Senior (18-50 jaar): basisbedrag €100, korting 0%, te betalen €100
   - Oudere (51-100 jaar): basisbedrag €100, korting 45%, te betalen €55
5. Nieuwe contributie toevoegen:
   - Klik "Nieuwe Contributie Toevoegen" → contributie/create.php
   - Selecteer boekjaar, leeftijd (0-100), soort lid
   - Voer basisbedrag in (bijv. €100)
   - Selecteer soort lid → kortingspercentage wordt automatisch ingevuld (readonly)
   - Berekend bedrag wordt getoond: Te betalen = basisbedrag - (basisbedrag × korting / 100)
   - Staffels tabel onderaan formulier toont overzicht
6. Contributie bewerken:
   - Klik "Bewerken" bij een tarief → contributie/edit.php?id=X
   - Wijzig basisbedrag of kortingspercentage
   - Berekening wordt automatisch bijgewerkt
7. Let op: gewone gebruikers hebben geen toegang tot deze module (Auth::requireAdmin())

#### Workflow 4A: Admin (beheerder)
1. Login als admin of mekso (beiden hebben rol 'admin').
2. Navigatie toont: Families, Familieleden, Soort Leden, Contributies, Boekjaren en Gebruikersbeheer.
3. Rechten: toevoegen, bewerken én verwijderen in alle modules.
4. Voorbeeld (verwijderen): ga naar Familieleden → klik Verwijderen bij een lid → bevestig → record verdwijnt.
5. Voorbeeld (gebruikersbeheer): Gebruikers → Nieuwe gebruiker → vul gegevens → rol kan op 'user' of 'admin' worden gezet. Admin en mekso kunnen zelf niet worden verwijderd en hun rol kan niet worden aangepast.
6. Beveiliging: alle beheerpagina's zijn beschermd met Auth::requireAdmin().

#### Workflow 4B: Gewone gebruiker
1. Login als een gebruiker met rol 'user' (bijv. Marije).
2. Navigatie toont alleen: Families en Familieleden. De links naar Soort Leden, Contributies, Boekjaren en Gebruikersbeheer zijn verborgen.
3. Rechten: wel toevoegen en bewerken, niet verwijderen. Verwijderknoppen zijn onzichtbaar en de achterliggende delete-scripts eisen admin-rechten.
4. Toegang: directe URL naar contributie/ of boekjaar/ resulteert in access denied en redirect naar de startpagina.
5. Startpagina: er staat een blauw 'i'-icoon naast de introductietekst. Klik daarop om de Gebruikersinformatie te tonen of te verbergen (onthoudt keuze via localStorage).
6. Voorbeeld (toevoegen): ga naar Families → Nieuwe familie; daarna Familieleden → Nieuw familielid. Verwijderen is niet mogelijk; neem contact op met een admin voor verwijderverzoeken.

---

### Technische Details

#### Code Organisatie
- **Models:** Bestanden die met de database praten (in models/ map)
- **Views:** Bestanden die de webpagina's laten zien (in de verschillende mappen)
- **Controllers:** Bestanden die de logica bevatten (individuele PHP bestanden)
- **Opzet:** Geen strenge scheiding maar wel logisch opbouw.

#### Database Communicatie
- **PDO:** Een veilige manier om met de database te praten
- **Model bestanden:** Herbruikbare code voor database acties
- **Veilige queries:** Alle database vragen zijn beschermd tegen hackers

#### Beveiliging
1. **Inloggen:**
   - Wachtwoorden worden versleuteld opgeslagen
   - Sessies worden gebruikt om bij te houden wie ingelogd is
   - "Onthoud mij" functie werkt 30 dagen
2. **Toegangsrechten:**
   - Verschillende rechten voor beheerders en gewone gebruikers
   - Alle pagina's controleren of je ingelogd bent
3. **Invoer Controle:**
   - Alle invoer wordt gecontroleerd voordat het wordt opgeslagen
   - Type controles (nummer, tekst, datum)
   - Lengte beperkingen
4. **Uitvoer Bescherming:**
   - Alle tekst die getoond wordt is veilig gemaakt
   - Beschermt tegen kwaadaardige code
5. **Formulier Bescherming:**
   - Speciale beveiligingstoken bij elk formulier
   - Voorkomt ongewenste acties
6. **Database Beveiliging:**
   - Alle database queries zijn veilig
   - Geen directe tekstvervanging in queries

#### Snelheid Verbeteringen
- **Snelle opzoekingen:** Extra indexen op veel gebruikte velden
- **Efficiënt laden:** Data wordt alleen opgehaald als het nodig is
- **Slimme queries:** Gebruik van verbindingen tussen tabellen voor snelheid
- **Geheugen:** (Voor de toekomst) Vaak gebruikte data tijdelijk onthouden

---

## 4. Reflectieverslag

### 4.1 Technische Keuzes

#### Hoe ***IK*** het Project Heb Aangepakt

**Werkwijze:**
De opdracht was het maken van een ledenadministratie website met PHP en MySQL. Ik heb dit in stappen gedaan:
1. **Database Eerst:** Eerst de database structuur gemaakt met alle koppelingen
2. **Basis Code:** Daarna de basis code geschreven die met de database praat
3. **Inloggen:** Vervolgens het inlogsysteem gemaakt
4. **Functies:** Stap voor stap alle functies gebouwd (toevoegen, wijzigen, verwijderen, bekijken/CRUD)
5. **Overzichtspagina:** Tot slot de startpagina met cijfers gemaakt

Deze aanpak zorgde ervoor dat ik een goede basis had voordat ik aan de lastigere onderdelen begon.

**Wat Ik Belangrijk Vond:**
1. Functionaliteit - de website moet werken
2. Beveiliging - bescherming tegen hackers
3. Code kwaliteit - leesbare en makkelijk te onderhouden code
4. Gebruiksvriendelijkheid - makkelijk te gebruiken

---

#### Hoe de Code is Opgebouwd

**Herbruikbare Code:**
Ik heb gekozen voor een aanpak waarbij ik niet steeds dezelfde code hoef te schrijven. Ik heb een basis-bestand gemaakt (Model.php) waar alle standaard database acties instaan (toevoegen, wijzigen, verwijderen, ophalen). Elk onderdeel (User, Familie, Familielid, etc.) gebruikt deze basis. Dit heeft voordelen:
- **Geen herhaling:** Ik hoef niet voor elk onderdeel dezelfde code te schrijven
- **Alles werkt hetzelfde:** Alle onderdelen werken op dezelfde manier
- **Makkelijk aanpassen:** Als ik iets wijzig in de basis, verandert het overal
- **Makkelijk uitbreiden:** Nieuwe onderdelen toevoegen gaat snel

**Overzichtelijke Mappen:**
Volgens de opdracht heb ik elke functie in een eigen map gezet:
- families/ (voor familie beheer)
- familieleden/ (voor leden beheer)
- soort_lid/ (voor soorten beheer)
- contributie/ (voor tarieven beheer)
- boekjaar/ (voor jaren beheer)

Dit zorgt voor:
- Overzichtelijke code
- Makkelijk te vinden
- Logisch gegroepeerd
- Duidelijke structuur

**Veilige Database Communicatie:**
Voor het praten met de database heb ik PDO gekozen in plaats van de oudere mysqli omdat:
- PDO werkt met meerdere databases (niet alleen MySQL)
- Veilige queries zijn standaard
- Betere foutmeldingen
- Moderner en meer ondersteund

---

#### Belangrijke Keuzes

**1. Inlogsysteem:**
- **Versleutelde Wachtwoorden:** Wachtwoorden worden veilig opgeslagen met versleuteling
- **Sessies + Cookies:** Sessies om bij te houden wie ingelogd is, cookies voor "onthoud mij" functie
- **Beveiligingstokens:** Bij elk formulier een speciaal token om ongewenste acties te voorkomen
- **Sessie Vernieuwing:** Bij inloggen wordt de sessie vernieuwd voor extra veiligheid

**Waarom:** Beveiliging is heel belangrijk voor een applicatie met gevoelige ledengegevens. Deze technieken zijn bewezen veilig.

**2. Database Ontwerp:**
- **Koppelingen:** Duidelijke verbindingen tussen tabellen met regels voor verwijderen
- **Geen dubbele data:** Alles staat maar één keer in de database
- **Snelle opzoekingen:** Extra indexen op veel gebruikte velden
- **Tijdstempels:** Bijhouden wanneer iets is aangemaakt of gewijzigd

**Waarom:** Een goed database ontwerp voorkomt fouten en zorgt dat de applicatie snel blijft.

**3. Contributie Berekening:**
- **Tarief Tabel:** 101 leeftijden × 5 soorten = 505 tarieven per jaar
- **Automatische Berekening:** Leeftijd wordt automatisch berekend uit geboortedatum
- **Flexibel:** Tarieven kunnen verschillen per leeftijd

**Waarom:** Dit systeem is flexibel genoeg voor verschillende tarief structuren zonder de code aan te passen.

**4. Hulpfuncties:**
- **Handige Functies:** berekenLeeftijd(), formatEuro(), e() in functions.php
- **Overal te gebruiken:** Deze functies worden door de hele applicatie gebruikt
- **Één Taak:** Elke functie doet één specifieke taak

**Waarom:** Voorkomt dat je dezelfde code steeds opnieuw schrijft.

**5. CSS Styling:**
- **In het bestand:** CSS staat in header.php in plaats van een apart bestand
- **Moderne Technieken:** Grid en Flexbox voor een mooi design
- **Werkt op Mobiel:** Past zich aan aan verschillende schermformaten

**Waarom:** Voor dit project was een apart CSS bestand niet nodig. Dit maakt het eenvoudiger.

---

### 4.2 Fouten en Oplossingen per Versie

#### Versie 0.1 - Database Schema (Week 1)
**Toegevoegde functionaliteit:**  
- Initiële database schema met 5 tabellen
- Foreign key relaties
- Basis test data

**Gevonden fouten:**

1. **Fout:** Foreign key constraint failed bij familie delete
   - **Oorzaak:** Geen CASCADE delete op familieleden bij familie verwijderen
   - **Oplossing:** Changed foreign key constraint van RESTRICT naar CASCADE op familie_id in Familielid tabel
   ```sql
   -- Was:
   FOREIGN KEY (familie_id) REFERENCES Familie(id) ON DELETE RESTRICT
   -- Werd:
   FOREIGN KEY (familie_id) REFERENCES Familie(id) ON DELETE CASCADE
   ```
   - **Geleerd:** Foreign key constraints moeten matchen met business logic. Als een familie verwijderd wordt, moeten de leden ook weg (CASCADE).

2. **Fout:** Contributie tabel miste boekjaar kolom
   - **Oorzaak:** Vergeten om boekjaar dimensie toe te voegen in eerste schema versie
   - **Oplossing:** Added Boekjaar tabel en boekjaar_id foreign key in Contributie tabel
   - **Geleerd:** Database design vereist goede planning. Alle dimensies van tariefstelling moeten al vroeg geïdentificeerd worden.

3. **Fout:** Character set problemen met Nederlandse characters (é, ë, etc.)
   - **Oorzaak:** Database was aangemaakt met latin1 charset
   - **Oplossing:** Database en tabellen omgezet naar utf8mb4:
   ```sql
   ALTER DATABASE ledenadministratie CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
   - **Geleerd:** Altijd utf8mb4 gebruiken voor Nederlandse text, niet utf8 (utf8mb4 ondersteunt ook emoji).

---

#### Versie 0.2 - OOP Model Layer (Week 1-2)
**Toegevoegde functionaliteit:**  
- Abstract Model.php base class
- Concrete model classes (User, Familie, etc.)
- PDO database connectie
- CRUD methoden

**Gevonden fouten:**

1. **Fout:** PDO error "SQLSTATE[HY000] [2002] Connection refused"
   - **Oorzaak:** MySQL server was niet gestart
   - **Oplossing:** Start MySQL service: `sudo systemctl start mysql`
   - **Geleerd:** Altijd controleren of database server draait voordat code debuggen.

2. **Fout:** Model::create() returned geen ID van nieuwe record
   - **Oorzaak:** Vergeten `lastInsertId()` te returnen na INSERT
   - **Oplossing:** 
   ```php
   public function create($data) {
       // ... INSERT query ...
       return $this->pdo->lastInsertId();
   }
   ```
   - **Geleerd:** Return values zijn belangrijk voor flow control, vooral bij create operaties waar je de nieuwe ID nodig hebt.

3. **Fout:** SQL injection mogelijk in Model::all($orderBy)
   - **Oorzaak:** $orderBy parameter werd direct in query string gezet zonder validatie
   - **Oplossing:** Whitelist van toegestane kolommen voor ORDER BY:
   ```php
   protected function sanitizeOrderBy($orderBy) {
       $allowed = $this->getColumns();
       $parts = explode(' ', $orderBy);
       if (!in_array($parts[0], $allowed)) {
           return $this->primaryKey; // default
       }
       return $orderBy;
   }
   ```
   - **Geleerd:** Ook ORDER BY clauses kunnen SQL injection vector zijn. Prepared statements werken niet voor ORDER BY, dus whitelist validatie nodig.

4. **Fout:** PHP Warning: "Undefined property: Model::$table"
   - **Oorzaak:** Vergeten $table property te definiëren in concrete model classes
   - **Oplossing:** Toegevoegd aan alle model classes:
   ```php
   class Familie extends Model {
       protected $table = 'Familie';
       protected $primaryKey = 'id';
   }
   ```
   - **Geleerd:** Abstract classes kunnen niet weten welke tabel naam te gebruiken. Concrete classes moeten dit definiëren.

---

#### Versie 0.3 - Authenticatie Systeem (Week 2)
**Toegevoegde functionaliteit:**  
- Login/logout functionaliteit
- Registratie formulier
- Auth helper class
- Session management
- Remember me cookies
- CSRF protection

**Gevonden fouten:**

1. **Fout:** Session niet behouden tussen pagina's
   - **Oorzaak:** `session_start()` werd niet op elke pagina aangeroepen
   - **Oplossing:** Auth::init() toegevoegd in header.php die altijd included wordt:
   ```php
   // header.php
   require_once __DIR__ . '/Auth.php';
   Auth::init();
   ```
   - **Geleerd:** Sessions moeten ge-initialiseerd worden op ELKE pagina voordat je session variabelen kan lezen/schrijven.

2. **Fout:** Password verify altijd false, zelfs met correcte wachtwoord
   - **Oorzaak:** Password werd twee keer gehashed - één keer in formulier, één keer in User::register()
   - **Oplossing:** Removed hashing in formulier, alleen hash in model:
   ```php
   // User.php register method
   public function register($username, $email, $password) {
       $hash = password_hash($password, PASSWORD_DEFAULT);
       return $this->create([
           'username' => $username,
           'email' => $email,
           'password' => $hash
       ]);
   }
   ```
   - **Geleerd:** Password hashing moet op één plek gebeuren (backend), niet in frontend én backend.

3. **Fout:** CSRF token validation failed na 30 minuten
   - **Oorzaak:** PHP default session timeout was 30 minuten, token werd out of sync
   - **Oplossing:** CSRF token regenereren bij elke formulier render, niet alleen bij login:
   ```php
   // Bij elke GET request
   Auth::generateCsrfToken();
   // In formulier
   echo Auth::csrfField();
   ```
   - **Geleerd:** CSRF tokens moeten regelmatig ge-regenereerd worden, maar consistent binnen één formulier submission flow.

4. **Fout:** Remember me cookie werkte niet na browser herstart
   - **Oorzaak:** Cookie expiry was set to 0 (session cookie)
   - **Oplossing:** Set expiry naar 30 dagen in de toekomst:
   ```php
   $expire = time() + (30 * 24 * 60 * 60); // 30 dagen
   setcookie('remember_token', $token, $expire, '/', '', false, true);
   ```
   - **Geleerd:** Session cookies (expire = 0) worden verwijderd bij browser close. Voor persistent cookies moet je expliciet toekomstige timestamp opgeven.

5. **Fout:** XSS vulnerability in username display
   - **Oorzaak:** Username werd direct ge-echo'd zonder escaping
   - **Oplossing:** Created e() helper function:
   ```php
   function e($string) {
       return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
   }
   // Gebruik:
   echo "Welcome " . e($user['username']);
   ```
   - **Geleerd:** ALLE user input moet escaped worden bij output om XSS te voorkomen, zelfs "veilige" velden zoals username.

---

#### Versie 0.4 - Familie CRUD (Week 3)
**Toegevoegde functionaliteit:**  
- Familie index/create/edit/delete pagina's
- Formulier validatie
- Success/error berichten
- Familie-leden count

**Gevonden fouten:**

1. **Fout:** Redirect loop tussen index.php en auth/login.php
   - **Oorzaak:** Auth::requireLogin() redirect naar login.php, login.php redirect naar index.php als al ingelogd
   - **Oplossing:** Check in login.php of user al ingelogd is VOOR redirect:
   ```php
   // login.php top
   if (Auth::check()) {
       redirect('../index.php');
       exit;
   }
   ```
   - **Geleerd:** Always check state before redirect to prevent loops.

2. **Fout:** Familie edit formulier toonde geen data
   - **Oorzaak:** $familie variabele was null, find() returned false
   - **Oplossing:** Error handling toegevoegd:
   ```php
   $familie = Familie::find($_GET['id']);
   if (!$familie) {
       $_SESSION['error'] = "Familie niet gevonden";
       redirect('index.php');
       exit;
   }
   ```
   - **Geleerd:** Altijd checken of database queries data retourneren voordat je aanneemt dat het werkt.

3. **Fout:** HTML niet properly closed na delete
   - **Oorzaak:** redirect() functie had geen exit; na header(), rest van HTML werd nog ge-output
   - **Oplossing:** Added exit after header():
   ```php
   function redirect($url) {
       header("Location: $url");
       exit;
   }
   ```
   - **Geleerd:** After header() redirect, always exit to stop script execution.

4. **Fout:** Familie naam met apostrof (') brak form
   - **Oorzaak:** HTML attribute value niet ge-escaped
   - **Oplossing:** 
   ```php
   // Was:
   <input value="<?php echo $familie['naam']; ?>">
   // Werd:
   <input value="<?php echo e($familie['naam']); ?>">
   ```
   - **Geleerd:** e() function moet ook in HTML attributes gebruikt worden, niet alleen in text nodes.

---

#### Versie 0.5 - Familieleden CRUD (Week 3-4)
**Toegevoegde functionaliteit:**  
- Familielid CRUD pagina's
- JOIN queries met Familie en Soort_lid
- Geboortedatum date picker
- Leeftijd berekening
- Dropdown foreign key selectie

**Gevonden fouten:**

1. **Fout:** SQL error "Unknown column 'familie.naam'"
   - **Oorzaak:** Table naam in JOIN was wrong case, MySQL on Linux is case-sensitive
   - **Oplossing:** Consistent table naming in schema én queries:
   ```php
   // Zorg dat tabel namen consistent zijn
   SELECT f.*, fam.naam as familie_naam 
   FROM Familielid f 
   LEFT JOIN Familie fam ON f.familie_id = fam.id
   ```
   - **Geleerd:** MySQL table/column names zijn case-sensitive op Linux, case-insensitive op Windows. Always gebruik consistent casing.

2. **Fout:** Leeftijd berekening was off-by-one error
   - **Oorzaak:** DateTime::diff() vergat huidige dag te includen
   - **Oplossing:** 
   ```php
   function berekenLeeftijd($geboortedatum) {
       $geboorte = new DateTime($geboortedatum);
       $nu = new DateTime('today'); // Gebruik 'today' ipv 'now'
       return $geboorte->diff($nu)->y;
   }
   ```
   - **Geleerd:** Voor leeftijd berekening, gebruik 'today' niet 'now' om tijd component te negeren.

3. **Fout:** Familielid create form: familie dropdown leeg
   - **Oorzaak:** Familie::all() werd aangeroepen VOOR database connectie was geïnitialiseerd
   - **Oplossing:** Moved database init naar top van script:
   ```php
   require_once '../config/database.php';
   require_once '../models/Model.php';
   require_once '../models/Familie.php';
   
   $pdo = getPDO(); // Init eerst
   Familie::setPDO($pdo); // Dan set
   $families = Familie::all(); // Dan query
   ```
   - **Geleerd:** Database connectie moet eerst tot stand komen voordat je queries kan doen.

4. **Fout:** Date format in database was wrong (DD-MM-YYYY ipv YYYY-MM-DD)
   - **Oorzaak:** Nederlandse date format gepost, MySQL verwacht ISO format
   - **Oplossing:** Convert in PHP:
   ```php
   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
       $geboortedatum = $_POST['geboortedatum'];
       // Convert if needed
       $date = DateTime::createFromFormat('d-m-Y', $geboortedatum);
       if ($date) {
           $geboortedatum = $date->format('Y-m-d');
       }
   }
   ```
   - **Geleerd:** Altijd is eerst controleren en dan omzetten van datum formats voordat je database injecteerd.

---

#### Versie 0.6 - Contributie Berekening (Week 4-5)
**Toegevoegde functionaliteit:**  
- Contributie tabel met 505 tarieven
- getContributieBedrag() helper functie
- berekenFamilieContributie() functie
- Contributie kolom in families index
- Dashboard totaal contributie

**Gevonden fouten:**

1. **Fout:** Contributie berekening returned NULL voor alle leden
   - **Oorzaak:** Query had WHERE conditie met wrong leeftijd type (string vs int)
   - **Oplossing:** Type cast toegevoegd:
   ```php
   function getContributieBedrag($pdo, $leeftijd, $soortLidId, $boekjaarId) {
       $leeftijd = (int)$leeftijd; // Force int
       $query = "SELECT bedrag FROM Contributie 
                 WHERE leeftijd = ? AND soort_lid_id = ? AND boekjaar_id = ?";
       // ...
   }
   ```
   - **Geleerd:** PHP is loosely typed, maar MySQL is strict. Altijd omzetten naar juiste type.

2. **Fout:** Performance probleem: families index page laadde 10+ seconden
   - **Oorzaak:** N+1 query probleem - per familie werd loop gedaan met queries per lid
   - **Oplossing:** Optimized berekenFamilieContributie() met JOIN:
   ```php
   // In plaats van loop per lid:
   $query = "SELECT SUM(c.bedrag) as totaal
             FROM Familielid fl
             JOIN Contributie c ON 
                 YEAR(CURDATE()) - YEAR(fl.geboortedatum) = c.leeftijd
                 AND fl.soort_lid_id = c.soort_lid_id
             WHERE fl.familie_id = ? AND c.boekjaar_id = ?";
   ```
   - **Geleerd:** 
   Ga niet 100 keer naar de database voor informatie die je in 1 keer kunt ophalen!

3. **Fout:** Totale contributie op dashboard was incorrect (te laag)
   - **Oorzaak:** Leden zonder matching tarief werden niet geteld (NULL bedragen)
   - **Oplossing:** Default bedrag bij geen match:
   ```php
   $bedrag = getContributieBedrag($pdo, $leeftijd, $soortLidId, $boekjaarId);
   if ($bedrag === null) {
       // Log warning
       error_log("No tariff found for age $leeftijd, soort $soortLidId");
       $bedrag = 0; // Default
   }
   $totaal += $bedrag;
   ```
   - **Geleerd:** Altijd NULL cases goed beschrijven, geef zinnige alternatief.

4. **Fout:** Contributie tarieven voor leeftijd > 100 miste
   - **Oorzaak:** Schema.sql genereerde alleen 0-100, er was een lid van 101 jaar
   - **Oplossing:** Extend tarieven tabel tot 120 jaar en add validation:
   ```php
   // In create form
   if ($leeftijd < 0 || $leeftijd > 120) {
       $errors[] = "Leeftijd moet tussen 0 en 120 zijn";
   }
   ```
   - **Geleerd:** Edge cases happen. Controleer invoer en zord dat systeem genoeg ruimte heeft voor uitzonderingen.

---

#### Versie 0.7 - Dashboard & Statistieken (Week 5)
**Toegevoegde functionaliteit:**  
- Dashboard met 4 statistiek cards
- Totale contributie berekening
- Staffels tabel
- Quick links naar modules
- Responsive grid layout

**Gevonden fouten:**

1. **Fout:** Dashboard totale contributie toonde wetenschappelijke notatie (1.5E+4)
   - **Oorzaak:** Groot getal niet ge-format
   - **Oplossing:** formatEuro() function:
   ```php
   function formatEuro($bedrag) {
       return '€ ' . number_format($bedrag, 2, ',', '.');
   }
   ```
   - **Geleerd:** Always format currency voor display, helemaal voor Nederlandse format (komma voor decimalen).

2. **Fout:** CSS grid broke op mobile devices
   - **Oorzaak:** Fixed grid columns (4 columns) te breed voor small screens
   - **Oplossing:** Responsive grid:
   ```css
   .stats-grid {
       display: grid;
       grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
       gap: 20px;
   }
   ```
   - **Geleerd:** Use auto-fit en minmax voor responsive grids zonder media queries.

3. **Fout:** COUNT queries returnden string "5" ipv int 5
   - **Oorzaak:** PDO fetch returned strings by default
   - **Oplossing:** Type cast in PHP of gebruik PDO::ATTR_STRINGIFY_FETCHES false
   ```php
   $count = (int)$result['count'];
   // Of in PDO setup:
   $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
   ```
   - **Geleerd:** PDO returned alles als strings unless je attributes set. Cast types na fetch.

---

#### Versie 0.8 - Soort Lid & Boekjaar CRUD (Week 5-6)
**Toegevoegde functionaliteit:**  
- Soort_lid CRUD pagina's
- Boekjaar CRUD pagina's
- Contributie filter op boekjaar
- Admin-only features (basis)

**Gevonden fouten:**

1. **Fout:** Delete soort_lid gaf database error
   - **Oorzaak:** Foreign key RESTRICT constraint, er waren familieleden van die soort
   - **Oplossing:** User-friendly error message:
   ```php
   try {
       SoortLid::delete($id);
   } catch (PDOException $e) {
       if ($e->getCode() == 23000) { // Integrity constraint
           $_SESSION['error'] = "Kan niet verwijderen: er zijn leden van dit soort";
       } else {
           throw $e;
       }
   }
   ```
   - **Geleerd:** Database constraints geven cryptische errors. Catch en vertaal naar user-friendly messages.

2. **Fout:** Boekjaar jaar validatie accepteerde "abc"
   - **Oorzaak:** Geen type checking op POST data
   - **Oplossing:** Server-side validatie:
   ```php
   $jaar = $_POST['jaar'];
   if (!is_numeric($jaar) || $jaar < 1900 || $jaar > 2100) {
       $errors[] = "Jaar moet tussen 1900 en 2100 zijn";
   }
   ```
   - **Geleerd:** Never trust client-side validatie. Always validate server-side.

3. **Fout:** Contributie filter dropdown selected wrong option
   - **Oorzaak:** String compare ipv int compare
   - **Oplossing:**
   ```php
   foreach ($boekjaren as $bj) {
       $selected = ($bj['id'] == $filterBoekjaar) ? 'selected' : '';
       echo "<option value='{$bj['id']}' $selected>{$bj['jaar']}</option>";
   }
   ```
   - **Geleerd:** PHP loose comparison (==) can cause issues. Use strict comparison (===) waar mogelijk.

---

#### Versie 0.9 - UI/UX Improvements (Week 6)
**Toegevoegde functionaliteit:**  
- Confirmation dialogs bij delete
- Empty state berichten
- Success flash messages
- Error handling improvements
- Loading states

**Gevonden fouten:**

1. **Fout:** JavaScript confirm() blocking workflow
   - **Oorzaak:** Sync confirm dialog blocked UI thread
   - **Oplossing:** Form-based delete met POST:
   ```php
   <form method="POST" action="delete.php" onsubmit="return confirm('Weet je het zeker?')">
       <input type="hidden" name="id" value="<?php echo $id; ?>">
       <?php echo Auth::csrfField(); ?>
       <button type="submit">Verwijder</button>
   </form>
   ```
   - **Geleerd:** Confirm in onsubmit werkt beter dan separate JS event. Form POST is veiliger dan GET.

2. **Fout:** Success message toonde na page refresh
   - **Oorzaak:** Flash message niet verwijderd na display
   - **Oplossing:**
   ```php
   if (isset($_SESSION['success'])) {
       echo '<div class="alert success">' . e($_SESSION['success']) . '</div>';
       unset($_SESSION['success']); // Verwijder na display
   }
   ```
   - **Geleerd:** Flash messages moeten once-only zijn. Unset na display.

3. **Fout:** Empty state message had geen styling
   - **Oorzaak:** Vergeten CSS class toe te voegen
   - **Oplossing:**
   ```css
   .empty-message {
       text-align: center;
       padding: 40px;
       color: #666;
       font-style: italic;
   }
   ```
   - **Geleerd:** UI states (empty, loading, error) need dedicated styling for good UX.

---

#### Versie 1.0 - Final Polish & Testing (Week 6-7)
**Toegevoegde functionaliteit:**  
- Code cleanup en refactoring
- Security audit
- Performance testing
- Browser compatibility
- Documentation (README.md)

**Laatste aanpassingen:**

1. **Security Audit Fixes:**
   - Added HttpOnly flag op alle cookies
   - Added SameSite=Strict op session cookie
   - Reviewed all user input escaping
   - Verified all SQL queries gebruik prepared statements
   - Added rate limiting op login (toekomstig)

2. **Performance Optimizations:**
   - Added indexes op foreign keys
   - Optimized contributie berekening query
   - Reduced N+1 queries in families index
   - Added PDO persistent connections (testing)

3. **Code Quality:**
   - Consistent indentation (4 spaces)
   - Added docblocks op alle functies
   - Removed debug echo's en var_dumps
   - Consistent naming conventions

**Gevonden fouten:**

1. **Fout:** CSRF token expired na lang formulier invullen
   - **Oorzaak:** Session timeout tijdens form fill
   - **Oplossing:** Extended session lifetime:
   ```php
   ini_set('session.gc_maxlifetime', 3600); // 1 uur
   ```
   - **Geleerd:** Session timeouts should match user behavior. 30 min te kort voor complexe forms.

2. **Fout:** SQL error bij familie met veel leden (50+)
   - **Oorzaak:** Query timeout na 30 seconden
   - **Oplossing:** Optimized query en verhoogde timeout:
   ```php
   $pdo->setAttribute(PDO::ATTR_TIMEOUT, 60);
   ```
   - **Geleerd:** Default timeouts kunnen te laag zijn voor complex queries. Monitor en adjust.

3. **Fout:** Date picker niet werkend in Safari
   - **Oorzaak:** Safari heeft andere HTML5 date input implementatie
   - **Oplossing:** Added fallback formatting:
   ```html
   <input type="date" name="geboortedatum" 
          pattern="\d{4}-\d{2}-\d{2}" 
          placeholder="YYYY-MM-DD">
   ```
   - **Geleerd:** Test op multiple browsers. HTML5 features hebben different support.

4. **Fout:** README.md had verkeerde database credentials
   - **Oorzaak:** Copy-paste error from development setup
   - **Oplossing:** Updated naar generieke placeholders:
   ```php
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```
   - **Geleerd:** Commit nooit productie informatie naar versiebeheer. Gebruik placeholders of andere variabele.

---

### 4.3 Reflectie op het Proces

#### Wat ging goed

**Database Ontwerp:**
De database was vanaf het begin goed opgezet. De verbindingen tussen tabellen (families, leden, soorten, contributie) werkten logisch en snel. De regels voor het verwijderen van data voorkwamen fouten. Het contributie systeem met aparte regels per leeftijd/soort/jaar was flexibel zonder de code aan te passen.

**Herbruikbare Code:**
De keuze voor een basis Model bestand voorkwam dat ik steeds dezelfde code moest schrijven. Alle basis acties (toevoegen, wijzigen, verwijderen, ophalen) waren herbruikbaar. Dit maakte het toevoegen van nieuwe onderdelen (zoals Boekjaar) heel makkelijk - alleen een nieuw bestand maken en klaar.

**Beveiliging:**
Door vanaf het begin aan beveiliging te denken (veilige database queries, versleutelde wachtwoorden, beveiligingstokens) had ik weinig problemen later. Het Auth bestand maakte het makkelijk om alle pagina's te beveiligen.

**Overzichtelijke Mappen:**
Het verdelen van functies in aparte mappen (families/, familieleden/, etc.) maakte de code overzichtelijk en makkelijk te vinden. Het was altijd duidelijk waar bepaalde code stond.

---

#### Wat kon beter

**Testen:**
Ik heb alles handmatig getest via de browser. Dit kostte veel tijd - elke code aanpassing betekende alles opnieuw testen. Voor grotere projecten zou ik kijken naar automatisch testen.

**Foutmeldingen:**
Foutmeldingen waren niet overal hetzelfde. Sommige pagina's toonden begrijpelijke foutmeldingen, andere toonden technische PHP fouten. Één centrale manier om fouten te tonen was beter geweest.

**Code Scheiding:**
Hoewel ik de code redelijk gescheiden had (database code apart, weergave apart), was het niet perfect. De weergave bestanden hadden ook logica (if/else, loops). Dit volledig scheiden was netter geweest, maar ook complexer voor dit project.

**CSS Organisatie:**
Alle CSS staat in header.php. Voor een grotere applicatie was een apart CSS bestand beter geweest. Nu is het moeilijker om de styling aan te passen.

**Invoer Controle:**
De controle op invoer (bijvoorbeeld: is het email adres geldig?) was op meerdere plekken hetzelfde. Één centraal bestand met alle controles was efficiënter geweest.

**Database Wijzigingen:**
Het schema.sql bestand moet handmatig gedraaid worden. Voor een teamproject was een systeem om database wijzigingen bij te houden handiger geweest.

---

#### Belangrijkste Leerpunten

**1. Beveiliging moet vanaf het begin:**
Beveiliging moet vanaf het begin ingebouwd worden. Het is veel moeilijker om beveiligingsproblemen te fixen nadat je de code al geschreven hebt. Door direct veilige methoden te gebruiken had ik weinig problemen.

**2. Goed database ontwerp is belangrijk:**
Een goede database structuur voorkomt problemen later. Tijd nemen om de database goed op te zetten aan het begin scheelt veel werk later. Mijn keuze voor de Contributie tabel met regels per leeftijd was flexibel.

**3. Herbruikbare code werkt:**
Het basis Model bestand met herbruikbare functies heeft honderden regels dubbele code voorkomen. Elke hulpfunctie (berekenLeeftijd, formatEuro) wordt op meerdere plekken gebruikt. Zonder dit was de code veel groter en moeilijker te onderhouden.

**4. Let op datatypes:**
PHP maakt soms verkeerde aannames over datatypes (tekst "5" vs nummer 5, datumformaten). Expliciet aangeven wat het type moet zijn voorkomt veel fouten.

**5. Snelheid is belangrijk:**
Een probleem met database queries in de families pagina zorgde voor 10+ seconden laadtijd. Één geöptimaliseerde query maakte het minder dan 1 seconde. Altijd testen hoe snel pagina's laden.

**6. Kleine details maken veel uit:**
Kleine dingen maken groot verschil: bevestigingsdialogen, lege-status berichten, success meldingen. Deze details maken een applicatie professioneel.

**7. Verschillende browsers werken anders:**
Wat werkt in Chrome werkt niet altijd in Safari of Firefox. Testen in meerdere browsers is belangrijk, vooral voor moderne functies zoals datumvelden.

**8. Documentatie is waardevol:**
Het schrijven van de README.md zette mij aan om alles goed te volgen. Goede documentatie helpt niet alleen anderen, maar ook mezelf later.

---

## 5. Conclusie

De Ledenadministratie applicatie is een werkende website voor verenigingsbeheer, gebouwd met PHP en MySQL. Het project laat moderne technieken zien zoals herbruikbare code, veilige database queries, versleutelde wachtwoorden, beveiligingstokens en een ontwerp dat op alle apparaten werkt.

De applicatie bevat alle gevraagde functies:
- Volledig beheer van families, leden, lidsoorten, contributietarieven en boekjaren (toevoegen, bekijken, wijzigen, verwijderen)
- Inlogsysteem met registratie en uitloggen
- Automatische contributieberekening op basis van leeftijd en lidsoort
- Overzichtspagina met cijfers en statistieken
- Veilige implementatie die beschermd is tegen hackers

Door het project in stappen te ontwikkelen en fouten per versie te documenteren, heb ik veel geleerd over database ontwerp, beveiliging, snelheid verbeteren en gebruiksvriendelijkheid. De overzichtelijke mappenstructuur (aparte mappen per functie) maakte de code makkelijk te begrijpen en te onderhouden.

---

