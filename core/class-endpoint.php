<?php 
namespace ISTIAQHOSSAIN\Optivine;

use WP_REST_Controller;

// Abort if called directly.
defined( 'WPINC' ) || die;

class Endpoint extends WP_REST_Controller {

    protected $version = 1;

    protected $namespace = '';

    protected $endpoint = '';

    protected function __construct() {
        $this->namespace = 'istiaqhossain/optivine/v' . $this->version;
        
        $this->register_hooks();
    }

    public static function instance() {
        static $instances = array();

        $called_class_name = get_called_class();
        
        if ( ! isset( $instances[ $called_class_name ] ) ) {
            $instances[ $called_class_name ] = new $called_class_name();
        }
        
        return $instances[ $called_class_name ];
    }

    public function register_hooks() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    public function rest_permission( $request ) {
        $capable = current_user_can( 'manage_options' );

        return apply_filters( 'istiaqhossain_optivine_rest_permission', $capable, $request );
    }

    public function get_namespace() {
        return $this->namespace;
    }

    public function get_endpoint() {
        return $this->endpoint;
    }

    public function get_endpoint_url() {
        return trailingslashit( rest_url() ) . trailingslashit( $this->get_namespace() ) . $this->get_endpoint();
    }
}