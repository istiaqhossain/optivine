<?php
namespace ISTIAQHOSSAIN\Optivine\Admin;

use ISTIAQHOSSAIN\Optivine\Base;

// Abort if called directly.
defined( 'WPINC' ) || die;

class Menu extends Base {

    public function init() {
        add_action( 'admin_menu', array( $this, 'register_menu' ) );
        add_action( 'admin_bar_menu', array( $this, 'register_admin_bar_menu' ), 100 );
    }

    public function register_menu() {
        $admin_page_link = get_permalink( istiaqhossain_optivine_get_option( 'admin_page_id' ) );

        add_menu_page(
            __( 'Optivine', 'istiaqhossain-optivine' ),
            __( 'Optivine', 'istiaqhossain-optivine' ),
            'read',
            $admin_page_link
        );
    }

    public function register_admin_bar_menu() {
        $admin_page_link = get_permalink( istiaqhossain_optivine_get_option( 'admin_page_id' ) );

        global $wp_admin_bar;

        $wp_admin_bar->add_menu(
            [
                'id'    => 'optivine',
                'title' => __( 'Optivine', 'istiaqhossain-optivine' ), 
                'href'  => $admin_page_link,
                'meta'  => [
                    'class' => 'istiaqhossain-optivine-admin-bar',
                ],
            ]
        );
    }
}