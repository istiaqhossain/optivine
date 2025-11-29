<?php
namespace ISTIAQHOSSAIN\Optivine\Admin;

use ISTIAQHOSSAIN\Optivine\Base;

// Abort if called directly.
defined( 'WPINC' ) || die;

class Asset extends Base {

    public function init() {
        add_action( 'istiaqhossain_optivine_admin_scripts', array( $this, 'load_assets' ) );
    }

    public function load_assets() {
        wp_enqueue_style( 
            'istiaqhossain-optivine-admin-style', ISTIAQHOSSAIN_OPTIVINE_ASSETS_URL . 'build/admin/' . ISTIAQHOSSAIN_OPTIVINE_UI . '/css/style.css', 
            array(), 
            ISTIAQHOSSAIN_OPTIVINE_VERSION, 
            'all' 
        );
        wp_enqueue_script( 
            'istiaqhossain-optivine-admin-script', ISTIAQHOSSAIN_OPTIVINE_ASSETS_URL . 'build/admin/' . ISTIAQHOSSAIN_OPTIVINE_UI . '/js/script.js', 
            array(), 
            ISTIAQHOSSAIN_OPTIVINE_VERSION, 
            true 
        );

        wp_localize_script( 'istiaqhossain-optivine-admin-script', 'istiaqhossain_optivine_admin', $this->localize_scripts() );
    }

    private function localize_scripts() {
        return array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'istiaqhossain_optivine_admin_nonce' ),
        );
    }
}