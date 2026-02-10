<?php
/**
 * Plugin Name: User Connexion
 * Description: Module WordPress permettant de gerer la connexion des utilisateurs
 * Version: 0.1.0
 * Author: Louise
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}
// Inclure les classes nécessaires
require_once plugin_dir_path(__FILE__) . 'includes/class-db.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-auth.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-ajax.php';

// Activation du plugin : créer la table
register_activation_hook(__FILE__, function() {
    $db = new UserConnexionDB();
    $db->createTable();
});

// Initialiser la session et créer la table si absent
add_action('init', function() {
    // Démarrer la session
    if (!session_id()) {
        session_start();
    }
    
    // Créer la table si elle n'existe pas
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_connexion';
    
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $db = new UserConnexionDB();
        $db->createTable();
    }
});

// Enqueuer le CSS et JavaScript du plugin
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script(
        'user-connexion-js',
        plugin_dir_url(__FILE__) . 'assets/user-connexion.js',
        array(),
        '1.0.0',
        true
    );
    
    // Localiser le script avec l'URL AJAX et le nonce
    wp_localize_script(
        'user-connexion-js',
        'user_connexion',
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('user_connexion_nonce')
        )
    );
});

// Afficher le formulaire dans le footer
add_action('wp_footer', function() {
    include plugin_dir_path(__FILE__) . 'templates/form-connexion.php';
});

//Fonction Helper
function get_auth_manager() {
    static $auth_manager = null;
    if ($auth_manager === null) {
        $auth_manager = new UserConnexionAuth();
    }
    return $auth_manager;
}