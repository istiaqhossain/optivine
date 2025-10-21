<?php 
namespace ISTIAQHOSSAIN\Optivine;

use ISTIAQHOSSAIN\Optivine\Base;

// Abort if called directly.
defined( 'WPINC' ) || die;

final class Loader extends Base {

    public $php_version = '7.4';
    
    public $wp_version = '6.1';

    protected function __construct() {
        if ( ! $this->can_boot() ) {
            return;
        }

        $this->init();
    }

    private function can_boot() {
        global $wp_version;

        return (
            version_compare( PHP_VERSION, $this->php_version, '>' ) && 
            version_compare( $wp_version, $this->wp_version, '>' )
        );
    }

    private function init() {
        error_log( 'Optivine loaded!' );
        Endpoints\V1\Workspace::instance()->init();
    }
}