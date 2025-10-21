<?php 
namespace ISTIAQHOSSAIN\Optivine;

use ISTIAQHOSSAIN\Optivine\Base;

// Abort if called directly.
defined( 'WPINC' ) || die;

class DbTable extends Base {

    public function create_table() {
        global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

        // TODO:
        $table_name = $wpdb->prefix . 'ish_optivine_workspaces';
        
        if ( false === $this->table_exist( $table_name ) ) {
            $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
                id mediumint(9) UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(150) NOT NULL,
                is_active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY name (name)
            ) $charset_collate;";

            // Include the upgrade library to initialize a table.
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $wpdb->hide_errors();
            // Create table.
            $wpdb->query( $sql );
        }
    }

    public function table_exist( $table_name ) {
        global $wpdb;

        $table_exist = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) ) );
        
        return $table_exist ? true : false;
    }
}