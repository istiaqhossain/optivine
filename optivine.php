<?php
/**
 * Plugin Name:       Optivine by Istiaq Hossain
 * Description:       A plugin focused on testing coding skills.
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * Version:           0.1.0
 * Author:            Istiaq Hossain
 * Author URI:        https://istiaqhossain.com
 * Plugin URI:        github.com/istiaqhossain/optivine
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       istiaqhossain-optivine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

if ( ! defined( 'ISTIAQHOSSAIN_OPTIVINE_VERSION' ) ) {
	define( 'ISTIAQHOSSAIN_OPTIVINE_VERSION', '0.1.0' );
}

if ( ! defined( 'ISTIAQHOSSAIN_OPTIVINE_PLUGIN_FILE' ) ) {
	define( 'ISTIAQHOSSAIN_OPTIVINE_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'ISTIAQHOSSAIN_OPTIVINE_DIR' ) ) {
	define( 'ISTIAQHOSSAIN_OPTIVINE_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'ISTIAQHOSSAIN_OPTIVINE_URL' ) ) {
	define( 'ISTIAQHOSSAIN_OPTIVINE_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'ISTIAQHOSSAIN_OPTIVINE_ASSETS_URL' ) ) {
	define( 'ISTIAQHOSSAIN_OPTIVINE_ASSETS_URL', ISTIAQHOSSAIN_OPTIVINE_URL . '/assets' );
}

if ( ! defined( 'ISTIAQHOSSAIN_OPTIVINE_PREFIX' ) ) {
	define( 'ISTIAQHOSSAIN_OPTIVINE_PREFIX', 'istiaqhossain_optivine_' );
}

register_activation_hook( __FILE__, array( 'ISTIAQHOSSAIN\\Optivine\\Installer', 'optivine_activated' ) );
register_deactivation_hook( __FILE__, array( 'ISTIAQHOSSAIN\\Optivine\\Installer', 'optivine_deactivated' ) );

class ISTIAQHOSSAIN_Optivine {
    
    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function load() {
        load_textdomain(
            'istiaqhossain-optivine',
            false,
            dirname( plugin_basename( __FILE__ ) ) . '/languages'
        );

        ISTIAQHOSSAIN\Optivine\Loader::instance();
    }
}

add_action(
    'init',
    function() {
        ISTIAQHOSSAIN_Optivine::get_instance()->load();
    },
    9
);