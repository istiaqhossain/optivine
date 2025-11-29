<?php 
namespace ISTIAQHOSSAIN\Optivine;

use ISTIAQHOSSAIN\Optivine\Base;
use ISTIAQHOSSAIN\Optivine\DbTable;
use ISTIAQHOSSAIN\Optivine\Page;

// Abort if called directly.
defined( 'WPINC' ) || die;

class Installer extends Base {

    public static function optivine_activated() {
        $dbTable = new DbTable();
        $dbTable->create_table();

        $page = new Page();
        $page->create();
    }

    public static function optivine_deactivated() {}
}