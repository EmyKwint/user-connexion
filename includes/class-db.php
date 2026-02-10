<?php

/**
 * Gestion de la base de données pour le plugin User Connexion
 */

if (!defined('ABSPATH')) {
    exit;
}
//Classe de gestion de la base de données pour le plugin User Connexion
class UserConnexionDB
{
    public $table_name;

    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'user_connexion';
    }
    //créer la table dans la base de données
    public function createTable()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$this->table_name} (
            id_user mediumint(9) NOT NULL AUTO_INCREMENT,
            mail varchar(100) NOT NULL,
            password varchar(255) NOT NULL,
            name varchar(100),
            lastname varchar(100),
            last_login datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id_user)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }


}