<?php
/**
 * Auth Class
 * Beheert sessies, cookies en authenticatie
 */
class Auth {
    private static $initialized = false;
    
    /**
     * Start sessie
     */
    public static function init() {
        if (self::$initialized) {
            return;
        }
        
        if (session_status() === PHP_SESSION_NONE) {
            // Veilige session configuratie
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS
            ini_set('session.cookie_samesite', 'Strict');
            
            session_start();
        }
        
        self::$initialized = true;
        
        // Check "remember me" cookie
        self::checkRememberMeCookie();
    }
    
    /**
     * Login gebruiker
     */
    public static function login($user, $rememberMe = false) {
        self::init();
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['rol'] = $user['rol'];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        
        // Regenerate session ID voor veiligheid
        session_regenerate_id(true);
        
        // "Remember me" cookie (30 dagen)
        if ($rememberMe) {
            $token = bin2hex(random_bytes(32));
            $expiry = time() + (30 * 24 * 60 * 60);
            
            setcookie('remember_token', $token, $expiry, '/', '', false, true);
            setcookie('remember_user', $user['id'], $expiry, '/', '', false, true);
            
            // Sla token op in database (in productie zou je dit beter beveiligen)
            $_SESSION['remember_token'] = $token;
        }
    }
    
    /**
     * Logout gebruiker
     */
    public static function logout() {
        self::init();
        
        // Verwijder cookies
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
            setcookie('remember_user', '', time() - 3600, '/', '', false, true);
        }
        
        // Verwijder alle session data
        $_SESSION = [];
        
        // Verwijder session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Vernietig sessie
        session_destroy();
    }
    
    /**
     * Check of gebruiker ingelogd is
     */
    public static function check() {
        self::init();
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Haal ingelogde gebruiker ID op
     */
    public static function userId() {
        self::init();
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Haal ingelogde gebruiker op
     */
    public static function user() {
        self::init();
        if (!self::check()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'rol' => $_SESSION['rol'] ?? null
        ];
    }
    
    /**
     * Check of gebruiker admin is
     */
    public static function isAdmin() {
        self::init();
        return self::check() && ($_SESSION['rol'] ?? '') === 'admin';
    }
    
    /**
     * Forceer login (redirect naar login pagina)
     */
    public static function requireLogin() {
        self::init();
        if (!self::check()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header('Location: /phpAndMysql/Ledenadministratie/auth/login.php');
            exit;
        }
    }
    
    /**
     * Forceer admin rechten
     */
    public static function requireAdmin() {
        self::requireLogin();
        if (!self::isAdmin()) {
            header('Location: /phpAndMysql/Ledenadministratie/index.php?error=access_denied');
            exit;
        }
    }
    
    /**
     * Check remember me cookie
     */
    private static function checkRememberMeCookie() {
        if (!self::check() && isset($_COOKIE['remember_token']) && isset($_COOKIE['remember_user'])) {
            // In productie zou je de token uit de database moeten valideren
            // Voor deze demo doen we een simpele check
            
            require_once __DIR__ . '/../config/database.php';
            require_once __DIR__ . '/../models/User.php';
            
            $pdo = getDBConnection();
            $userModel = new User($pdo);
            $user = $userModel->find($_COOKIE['remember_user']);
            
            if ($user && $user['actief']) {
                self::login($user, false);
            }
        }
    }
    
    /**
     * Genereer CSRF token
     */
    public static function generateCsrfToken() {
        self::init();
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Valideer CSRF token
     */
    public static function validateCsrfToken($token) {
        self::init();
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * CSRF token veld voor formulieren
     */
    public static function csrfField() {
        $token = self::generateCsrfToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
}
