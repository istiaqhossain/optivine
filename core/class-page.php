<?php
namespace ISTIAQHOSSAIN\Optivine;

use ISTIAQHOSSAIN\Optivine\Base;

// Abort if called directly.
defined( 'WPINC' ) || die;

class Page extends Base {

    private function get_default_pages() {
        $pages = array(
            'admin_page_id' => [
                'title' => __( 'Optivine Admin', 'istiaqhossain-optivine' ),
                'description' => 'This is the admin app page. Do not delete this page.',
            ],
        );

        return $pages;
    }

    public function create() {
        $default_pages = $this->get_default_pages();

        if ( empty( $default_pages ) ) {
            return;
        }

        foreach ( $default_pages as $option_key => $page ) {
            $page_id = get_option( ISTIAQHOSSAIN_OPTIVINE_PREFIX . $option_key );

            $page_object = get_post( $page_id );

            if ( ! $page_object ) {
                $page_id = wp_insert_post(
                    array(
                        'post_title'     => $page['title'],
                        'post_content'   => $page['description'],
                        'post_status'    => 'publish',
                        'post_type'      => 'page',
                        'comment_status' => 'closed',
                        'ping_status'    => 'closed',
                    )
                );
                update_option( ISTIAQHOSSAIN_OPTIVINE_PREFIX . $option_key, $page_id );
            }
        }
    }
}