<?php 
namespace ISTIAQHOSSAIN\Optivine;

use ISTIAQHOSSAIN\Optivine\Singleton;

// Abort if called directly.
defined( 'WPINC' ) || die;

abstract class Base extends Singleton {

    public function __get( $key ) {
        if ( isset( $this->{$key} ) ) {
            return $this->{$key};
        }

        return null;
    }

    public function __set( $key, $value ) {
        $this->{$key} = $value;
    }
}