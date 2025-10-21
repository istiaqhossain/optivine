<?php
namespace ISTIAQHOSSAIN\Optivine;

// Abort if called directly.
defined( 'WPINC' ) || die;

abstract class Singleton {

    protected function __construct( $props = array() ) {
        // Protect class from being initiated multiple times.
    }

    public static function instance() {
        static $instances = array();

        $called_class_name = get_called_class();

        if ( ! isset( $instances[ $called_class_name ] ) ) {
            $instances[ $called_class_name ] = new $called_class_name();
        }

        return $instances[ $called_class_name ];
    }
}