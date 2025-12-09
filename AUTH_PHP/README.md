# Rolapplicatie (AUTH_PHP)

Een eenvoudige PHP-applicatie voor gebruikersbeheer met authenticatie (inloggen) en autorisatie (rollen: admin en user).

## Functionaliteiten

*   **Inloggen & Registreren:** Gebruikers kunnen een account aanmaken en inloggen.
*   **Rollen:**
    *   **Admin:** Kan alle gebruikers zien, aanmaken, bewerken (inclusief rol en wachtwoord) en verwijderen.
    *   **User:** Kan alleen eigen gegevens en wachtwoord aanpassen.
*   **Database:** Automatische installatie van de database en tabel bij het opstarten.

## Installatie

1.  Zorg dat je een webserver (zoals XAMPP of Apache) en MySQL hebt draaien.
2.  Plaats de bestanden in je webfolder (bijv. `htdocs`).
3.  Open `index.php` in je browser.
4.  De applicatie maakt automatisch de database `rolapplicatie` en de gebruikerstabel aan.

## Standaard Inloggegevens

De applicatie maakt automatisch twee testgebruikers aan:

*   **Admin:**
    *   Gebruikersnaam: `admin`
    *   Wachtwoord: `admin123`
*   **Gebruiker:**
    *   Gebruikersnaam: `gebruiker`
    *   Wachtwoord: `gebruiker123`

## Bestandsstructuur

*   `index.php` - Hoofdpagina (dashboard).
*   `login.php` - Inlogpagina.
*   `register.php` - Registratiepagina voor nieuwe gebruikers.
*   `createUser.php` - Pagina voor admins om gebruikers toe te voegen.
*   `editUser.php` - Pagina om gebruikersgegevens aan te passen.
*   `deleteUser.php` - Script om gebruikers te verwijderen.
*   `db.php` - Databaseverbinding.
*   `createTable.php` - Script voor automatische database-installatie.
*   `style.css` - Opmaak van de applicatie.
