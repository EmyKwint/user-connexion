<?php
// Test direct du plugin - appelle ce fichier pour déboguer
// URL: http://localhost:8888/laPlaceRouge/wp-load.php et après require ce fichier

if (!defined('ABSPATH')) {
    require_once '../../../../wp-load.php';
}

echo "WordPress chargé\n";

// Test les classes
try {
    require_once plugin_dir_path(__FILE__) . 'includes/class-db.php';
    echo "class-db.php chargé\n";
    
    require_once plugin_dir_path(__FILE__) . 'includes/class-auth.php';
    echo "class-auth.php chargé\n";
    
    require_once plugin_dir_path(__FILE__) . 'includes/class-ajax.php';
    echo "class-ajax.php chargé\n";
    
    $db = new UserConnexionDB();
    echo "UserConnexionDB instancié\n";
    
    $auth = new UserConnexionAuth();
    echo "UserConnexionAuth instancié\n";
    
    echo "\nTout semble OK !\n";
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
