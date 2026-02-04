-- Migratie script: Voeg basisbedrag en kortingspercentage toe aan Contributie tabel
-- Dit script update de bestaande database zonder data te verliezen

USE ledenadministratie;

-- Stap 1: Voeg nieuwe kolommen toe
ALTER TABLE Contributie 
ADD COLUMN basisbedrag DECIMAL(10, 2) NOT NULL DEFAULT 100.00 AFTER soort_lid_id,
ADD COLUMN kortingspercentage DECIMAL(5, 2) NOT NULL DEFAULT 0.00 AFTER basisbedrag;

-- Stap 2: Bereken basisbedrag en kortingspercentage op basis van bestaande bedragen
-- Jeugd (50% korting): bedrag €50 → basisbedrag €100, korting 50%
UPDATE Contributie 
SET basisbedrag = 100.00, kortingspercentage = 50.00
WHERE soort_lid_id = 1;

-- Aspirant (40% korting): bedrag €60 → basisbedrag €100, korting 40%
UPDATE Contributie 
SET basisbedrag = 100.00, kortingspercentage = 40.00
WHERE soort_lid_id = 2;

-- Junior (25% korting): bedrag €75 → basisbedrag €100, korting 25%
UPDATE Contributie 
SET basisbedrag = 100.00, kortingspercentage = 25.00
WHERE soort_lid_id = 3;

-- Senior (0% korting): bedrag €100 → basisbedrag €100, korting 0%
UPDATE Contributie 
SET basisbedrag = 100.00, kortingspercentage = 0.00
WHERE soort_lid_id = 4;

-- Oudere (45% korting): bedrag €55 → basisbedrag €100, korting 45%
UPDATE Contributie 
SET basisbedrag = 100.00, kortingspercentage = 45.00
WHERE soort_lid_id = 5;

-- Stap 3: Verwijder oude bedrag kolom (OPTIONEEL - alleen als je zeker weet dat het werkt!)
-- WAARSCHUWING: Maak eerst een backup!
-- ALTER TABLE Contributie DROP COLUMN bedrag;

SELECT 'Migratie voltooid! Controleer de data:' as Status;
SELECT id, leeftijd, soort_lid_id, basisbedrag, kortingspercentage, 
       (basisbedrag - (basisbedrag * kortingspercentage / 100)) as te_betalen
FROM Contributie 
LIMIT 10;
