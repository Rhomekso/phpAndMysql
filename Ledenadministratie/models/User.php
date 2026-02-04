<?php
require_once __DIR__ . '/Model.php';

/**
 * User Model
 * Beheert gebruikers en authenticatie
 */
class User extends Model {
    protected $table = 'User';
    
    /**
     * Valideer login credentials
     */
    public function authenticate($username, $password) {
        $user = $this->findBy(['username' => $username, 'actief' => 1]);
        
        if ($user && password_verify($password, $user['password'])) {
            // Update last login
            $this->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);
            return $user;
        }
        
        return false;
    }
    
    /**
     * Registreer een nieuwe gebruiker
     */
    public function register($username, $email, $password, $rol = 'user') {
        // Check of username al bestaat
        if ($this->findBy(['username' => $username])) {
            throw new Exception('Gebruikersnaam bestaat al');
        }
        
        // Check of email al bestaat
        if ($this->findBy(['email' => $email])) {
            throw new Exception('E-mailadres bestaat al');
        }
        
        // Hash het wachtwoord
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        return $this->create([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword,
            'rol' => $rol
        ]);
    }
    
    /**
     * Update wachtwoord
     */
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update($userId, ['password' => $hashedPassword]);
    }
    
    /**
     * Check of gebruiker admin is
     */
    public function isAdmin($userId) {
        $user = $this->find($userId);
        return $user && $user['rol'] === 'admin';
    }
}
