<?php
namespace ISTIAQHOSSAIN\Optivine\Admin;

use ISTIAQHOSSAIN\Optivine\Base;
use ISTIAQHOSSAIN\Optivine\Admin\Action;
use ISTIAQHOSSAIN\Optivine\Admin\Asset;

// Abort if called directly.
defined( 'WPINC' ) || die;

class Template extends Base {

    public function init() {
        add_action( 'template_redirect', array( $this, 'handle_admin_template' ) );
    }

    public function handle_admin_template() {
        if ( get_the_ID() !== intval( istiaqhossain_optivine_get_option( 'admin_page_id' ) ) ) {
            return;
        }

        if ( ! is_user_logged_in() ) {
            wp_redirect( wp_login_url( get_permalink() ) );
            exit;
        }

        Action::instance()->init();
        Asset::instance()->init();

        do_action( 'istiaqhossain_optivine_admin_init' );

        istiaqhossain_optivine_load_view( 'admin/index' );
		exit(0);
    }
}