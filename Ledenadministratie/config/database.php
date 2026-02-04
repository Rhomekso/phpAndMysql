<?php
// Database configuratie
define('DB_HOST', 'localhost');
define('DB_NAME', 'ledenadministratie');
define('DB_USER', 'mekso');
define('DB_PASS', 'klopklop123');
define('DB_CHARSET', 'utf8mb4');

// Database connectie functie
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        die("Database connectie fout: " . $e->getMessage());
    }
}
