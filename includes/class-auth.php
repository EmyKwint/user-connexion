<?php

/**
 * Gestion de l'authentification pour le plugin User Connexion
 */

if (!defined('ABSPATH')) {
    exit;
}
//Classe de gestion de l'authentification pour le plugin User Connexion
class UserConnexionAuth
{
    private $db;

    public function __construct()
    {
        $this->db = new UserConnexionDB();
    }
    // Enregistrer un nouvel utilisateur
    public function register($mail, $password, $name = '', $lastname = '')
    {
        global $wpdb;

        // Vérifier si l'utilisateur existe déjà
        $existing_user = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->db->table_name} WHERE mail = %s", $mail));
        if ($existing_user) {
            return new WP_Error('user_exists', 'Un utilisateur avec cet email existe déjà.');
        }

        // Hasher le mot de passe
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insérer l'utilisateur dans la base de données
        $result = $wpdb->insert(
            $this->db->table_name,
            [
                'mail' => $mail,
                'password' => $hashed_password,
                'name' => $name,
                'lastname' => $lastname,
            ]
        );
        if($result === false) {
            return new WP_Error('db_error', 'Erreur lors de l\'inscription : ' . $wpdb->last_error);
        }

        return true;
    }
    // Connexion utilisateur
    public function login($mail, $password)
    {
        global $wpdb;

        // Récupérer l'utilisateur par email
        $user = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->db->table_name} WHERE mail = %s", $mail));
        if (!$user) {
            return new WP_Error('invalid_credentials', 'Email ou mot de passe incorrect.');
        }

        // Vérifier le mot de passe
        if (!password_verify($password, $user->password)) {
            return new WP_Error('invalid_credentials', 'Email ou mot de passe incorrect.');
        }

        // Stocker les informations de l'utilisateur dans la session
        $_SESSION['user_id'] = $user->id_user;
        $_SESSION['user_mail'] = $user->mail;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_lastname'] = $user->lastname;

        // Mettre à jour la date du dernier login
        $wpdb->update(
            $this->db->table_name,
            ['last_login' => current_time('mysql')],
            ['id_user' => $user->id_user]
        );

        return true;
    }
    // Déconnexion utilisateur
    public function logout()
    {
        session_unset();
        session_destroy();
    }
    // Vérifier si l'utilisateur est connecté
    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
}