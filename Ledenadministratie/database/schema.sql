-- Database aanmaken
CREATE DATABASE IF NOT EXISTS ledenadministratie CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ledenadministratie;

-- Tabel: User (voor authenticatie)
CREATE TABLE IF NOT EXISTS User (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol VARCHAR(20) DEFAULT 'user',
    actief BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel: Familie
CREATE TABLE IF NOT EXISTS Familie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100) NOT NULL,
    adres VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel: Soort_lid
CREATE TABLE IF NOT EXISTS Soort_lid (
    id INT AUTO_INCREMENT PRIMARY KEY,
    omschrijving VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel: Boekjaar
CREATE TABLE IF NOT EXISTS Boekjaar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jaar INT NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel: Familielid
CREATE TABLE IF NOT EXISTS Familielid (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100) NOT NULL,
    geboortedatum DATE NOT NULL,
    soort_lid_id INT NOT NULL,
    familie_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (soort_lid_id) REFERENCES Soort_lid(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (familie_id) REFERENCES Familie(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel: Contributie
CREATE TABLE IF NOT EXISTS Contributie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    leeftijd INT NOT NULL,
    soort_lid_id INT NOT NULL,
    bedrag DECIMAL(10, 2) NOT NULL,
    boekjaar_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (soort_lid_id) REFERENCES Soort_lid(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (boekjaar_id) REFERENCES Boekjaar(id) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY unique_contributie (leeftijd, soort_lid_id, boekjaar_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Basisgegevens invoegen
-- Standaard gebruikers
-- Admin: wachtwoord = admin123
-- Mekso: wachtwoord = klopklop123
INSERT INTO User (username, email, password, rol) VALUES 
    ('admin', 'admin@ledenadmin.nl', '$2y$12$nHdJAWUJun4x4ixLq80L7uU5NFIG1Hs3dxugCDzhSiwMGTAm8cKwq', 'admin'),
    ('mekso', 'mekso@ledenadmin.nl', '$2y$12$jDZTZ8evwtezHac6yjyJduu4TPj0t0uakaEIWAzKRp9G2lzOsxWxy', 'user');

INSERT INTO Soort_lid (omschrijving) VALUES
    ('Jeugd'),
    ('Aspirant'),
    ('Junior'),
    ('Senior'),
    ('Oudere');

-- Standaard boekjaar
INSERT INTO Boekjaar (jaar) VALUES (2026);

-- Standaard contributie tarieven voor 2026 (basisbedrag €100 met kortingen)
INSERT INTO Contributie (leeftijd, soort_lid_id, bedrag, boekjaar_id) VALUES
    -- Jeugd: jonger dan 8 jaar, 50% korting
    (0, 1, 50.00, 1), (1, 1, 50.00, 1), (2, 1, 50.00, 1), (3, 1, 50.00, 1),
    (4, 1, 50.00, 1), (5, 1, 50.00, 1), (6, 1, 50.00, 1), (7, 1, 50.00, 1),
    -- Aspirant: van 8 tot 12 jaar, 40% korting
    (8, 2, 60.00, 1), (9, 2, 60.00, 1), (10, 2, 60.00, 1), (11, 2, 60.00, 1), (12, 2, 60.00, 1),
    -- Junior: van 13 tot 17 jaar, 25% korting
    (13, 3, 75.00, 1), (14, 3, 75.00, 1), (15, 3, 75.00, 1), (16, 3, 75.00, 1), (17, 3, 75.00, 1),
    -- Senior: van 18 tot 50 jaar, 0% korting
    (18, 4, 100.00, 1), (19, 4, 100.00, 1), (20, 4, 100.00, 1), (21, 4, 100.00, 1),
    (22, 4, 100.00, 1), (23, 4, 100.00, 1), (24, 4, 100.00, 1), (25, 4, 100.00, 1),
    (26, 4, 100.00, 1), (27, 4, 100.00, 1), (28, 4, 100.00, 1), (29, 4, 100.00, 1),
    (30, 4, 100.00, 1), (31, 4, 100.00, 1), (32, 4, 100.00, 1), (33, 4, 100.00, 1),
    (34, 4, 100.00, 1), (35, 4, 100.00, 1), (36, 4, 100.00, 1), (37, 4, 100.00, 1),
    (38, 4, 100.00, 1), (39, 4, 100.00, 1), (40, 4, 100.00, 1), (41, 4, 100.00, 1),
    (42, 4, 100.00, 1), (43, 4, 100.00, 1), (44, 4, 100.00, 1), (45, 4, 100.00, 1),
    (46, 4, 100.00, 1), (47, 4, 100.00, 1), (48, 4, 100.00, 1), (49, 4, 100.00, 1), (50, 4, 100.00, 1),
    -- Oudere: vanaf 51 jaar, 45% korting
    (51, 5, 55.00, 1), (52, 5, 55.00, 1), (53, 5, 55.00, 1), (54, 5, 55.00, 1),
    (55, 5, 55.00, 1), (56, 5, 55.00, 1), (57, 5, 55.00, 1), (58, 5, 55.00, 1),
    (59, 5, 55.00, 1), (60, 5, 55.00, 1), (61, 5, 55.00, 1), (62, 5, 55.00, 1),
    (63, 5, 55.00, 1), (64, 5, 55.00, 1), (65, 5, 55.00, 1), (66, 5, 55.00, 1),
    (67, 5, 55.00, 1), (68, 5, 55.00, 1), (69, 5, 55.00, 1), (70, 5, 55.00, 1),
    (71, 5, 55.00, 1), (72, 5, 55.00, 1), (73, 5, 55.00, 1), (74, 5, 55.00, 1),
    (75, 5, 55.00, 1), (76, 5, 55.00, 1), (77, 5, 55.00, 1), (78, 5, 55.00, 1),
    (79, 5, 55.00, 1), (80, 5, 55.00, 1), (81, 5, 55.00, 1), (82, 5, 55.00, 1),
    (83, 5, 55.00, 1), (84, 5, 55.00, 1), (85, 5, 55.00, 1), (86, 5, 55.00, 1),
    (87, 5, 55.00, 1), (88, 5, 55.00, 1), (89, 5, 55.00, 1), (90, 5, 55.00, 1),
    (91, 5, 55.00, 1), (92, 5, 55.00, 1), (93, 5, 55.00, 1), (94, 5, 55.00, 1),
    (95, 5, 55.00, 1), (96, 5, 55.00, 1), (97, 5, 55.00, 1), (98, 5, 55.00, 1),
    (99, 5, 55.00, 1), (100, 5, 55.00, 1);
