<?php
namespace ISTIAQHOSSAIN\Optivine\Admin;

use ISTIAQHOSSAIN\Optivine\Base;

// Abort if called directly.
defined( 'WPINC' ) || die;

class Action extends Base {

    public function init() {
        add_action( 'istiaqhossain_optivine_admin_init', array( $this, 'istiaqhossain_optivine_admin_init' ), 100 );
        add_action( 'istiaqhossain_optivine_admin_head', array( $this, 'istiaqhossain_optivine_admin_head' ), 100 );
        add_action( 'istiaqhossain_optivine_admin_footer', array( $this, 'istiaqhossain_optivine_admin_footer' ), 100 );
    }

    public function istiaqhossain_optivine_admin_init() {
        do_action( 'istiaqhossain_optivine_admin_scripts' );
    }

    public function istiaqhossain_optivine_admin_head() {
        wp_print_styles( 'istiaqhossain-optivine-admin-style' );
    }

    public function istiaqhossain_optivine_admin_footer() {
        wp_print_scripts( 'istiaqhossain-optivine-admin-script' );
    }
}