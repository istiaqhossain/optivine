<?php 
namespace ISTIAQHOSSAIN\Optivine;

use ISTIAQHOSSAIN\Optivine\Base;
use ISTIAQHOSSAIN\Optivine\DbTable;

// Abort if called directly.
defined( 'WPINC' ) || die;

class Installer extends Base {

    public static function optivine_activated() {
        error_log( __LINE__ . ' ' . print_r( 'optivine_activated', true ));
        
        $dbTable = new DbTable();
        $dbTable->create_table();
    }

    public static function optivine_deactivated() {
        error_log( __LINE__ . ' ' . print_r( 'optivine_deactivated', true ));
    }
}