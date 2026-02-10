<?php

/**
 * Gestion des requêtes AJAX pour le plugin User Connexion
 */

if (!defined('ABSPATH')) {
    exit;
}

class UserConnexionAJAX
{
    private $auth;

    public function __construct()
    {
        $this->auth = new UserConnexionAuth();
        
        // Hook debug
        add_action('wp_ajax_nopriv_user_connexion_debug', array($this, 'handle_debug'));
        add_action('wp_ajax_user_connexion_debug', array($this, 'handle_debug'));
        
        // Hook test
        add_action('wp_ajax_nopriv_user_connexion_test', array($this, 'handle_test'));
        add_action('wp_ajax_user_connexion_test', array($this, 'handle_test'));
        
        // Hooks AJAX
        add_action('wp_ajax_nopriv_user_connexion_login', array($this, 'handle_login'));
        add_action('wp_ajax_user_connexion_login', array($this, 'handle_login'));
        
        add_action('wp_ajax_nopriv_user_connexion_register', array($this, 'handle_register'));
        add_action('wp_ajax_user_connexion_register', array($this, 'handle_register'));

        add_action('wp_ajax_nopriv_user_connexion_logout', array($this, 'handle_logout'));
        add_action('wp_ajax_user_connexion_logout', array($this, 'handle_logout'));
    }

    public function handle_debug()
    {
        header('Content-Type: text/plain; charset=utf-8');
        echo "DEBUG: AJAX debug fonctionne\n";
        echo "POST vars: " . print_r($_POST, true) . "\n";
        echo "Auth loaded: " . (isset($this->auth) ? 'YES' : 'NO') . "\n";
        die;
    }

    public function handle_test()
    {
        wp_send_json_success(array('message' => 'AJAX fonctionne !'));
    }

    public function handle_login()
    {
        header('Content-Type: application/json');
        
        // Vérifier le nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'user_connexion_nonce')) {
            wp_send_json_error(array('message' => 'Erreur de sécurité.'));
            die;
        }

        // Récupérer et nettoyer les données
        $mail = isset($_POST['mail']) ? sanitize_email($_POST['mail']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        // Valider les données
        if (empty($mail) || empty($password)) {
            wp_send_json_error(array('message' => 'Email et mot de passe requis.'));
            die;
        }

        // Appeler la méthode de connexion
        $result = $this->auth->login($mail, $password);

        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
            die;
        }

        wp_send_json_success(array('message' => 'Connexion réussie !'));
        die;
    }

    public function handle_register()
    {
        header('Content-Type: application/json');
        
        // Vérifier le nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'user_connexion_nonce')) {
            wp_send_json_error(array('message' => 'Erreur de sécurité.'));
            die;
        }

        // Récupérer et nettoyer les données
        $mail = isset($_POST['mail']) ? sanitize_email($_POST['mail']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $lastname = isset($_POST['lastname']) ? sanitize_text_field($_POST['lastname']) : '';

        // Valider les données
        if (empty($mail) || empty($password)) {
            wp_send_json_error(array('message' => 'Email et mot de passe requis.'));
            die;
        }

        // Appeler la méthode d'inscription
        $result = $this->auth->register($mail, $password, $name, $lastname);

        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
            die;
        }

        wp_send_json_success(array('message' => 'Inscription réussie ! Vous pouvez maintenant vous connecter.'));
        die;
    }

    public function handle_logout()
    {
        header('Content-Type: application/json');
        
        // Vérifier le nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'user_connexion_nonce')) {
            wp_send_json_error(array('message' => 'Erreur de sécurité.'));
            die;
        }

        // Détruire la session
        $this->auth->logout();

        wp_send_json_success(array('message' => 'Déconnexion réussie !'));
        die;
    }
}

// Instancier la classe au hook 'init' 
add_action('init', function() {
    new UserConnexionAJAX();
});
