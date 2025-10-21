<?php
namespace ISTIAQHOSSAIN\Optivine\Endpoints\V1;

use WP_REST_Server;
use WP_Error;
use ISTIAQHOSSAIN\Optivine\Endpoint;
use ISTIAQHOSSAIN\Optivine\Models\Workspace as WorkspaceModel;

// Abort if called directly.
defined( 'WPINC' ) || die;

class Workspace extends Endpoint {

    private $workspace_model;

    public function init() {
        $this->endpoint = 'workspaces';
        $this->workspace_model = WorkspaceModel::instance();
    }

    public function register_routes() {
        // Collection routes
        register_rest_route(
            $this->get_namespace(),
            '/' . $this->get_endpoint(),
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_items' ),
                    'permission_callback' => array( $this, 'rest_permission' ),
                    'args'                => $this->get_collection_params(),
                ),
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'create_item' ),
                    'permission_callback' => array( $this, 'rest_permission' ),
                    'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
                ),
                'schema' => array( $this, 'get_public_item_schema' ),
            )
        );

        // Individual item routes
        register_rest_route(
            $this->get_namespace(),
            '/' . $this->get_endpoint() . '/(?P<id>[\d]+)',
            array(
                'args' => array(
                    'id' => array(
                        'description' => __( 'Unique identifier for the workspace.', 'istiaqhossain-optivine' ),
                        'type'        => 'integer',
                        'required'    => true,
                    ),
                ),
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_item' ),
                    'permission_callback' => array( $this, 'rest_permission' ),
                    'args'                => array(
                        'context' => $this->get_context_param( array( 'default' => 'view' ) ),
                    ),
                ),
                array(
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_item' ),
                    'permission_callback' => array( $this, 'rest_permission' ),
                    'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
                ),
                array(
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => array( $this, 'delete_item' ),
                    'permission_callback' => array( $this, 'rest_permission' ),
                ),
                'schema' => array( $this, 'get_public_item_schema' ),
            )
        );
    }

    public function get_items( $request ) {
        // Get query parameters
        $per_page = isset( $request['per_page'] ) ? (int) $request['per_page'] : 20;
        $page = isset( $request['page'] ) ? (int) $request['page'] : 1;
        $status = isset( $request['status'] ) ? sanitize_text_field( $request['status'] ) : '';

        // Validate parameters
        if ( $per_page < 1 || $per_page > 100 ) {
            return new WP_Error(
                'rest_invalid_per_page',
                __( 'Per page must be between 1 and 100.', 'istiaqhossain-optivine' ),
                array( 'status' => 400 )
            );
        }

        if ( $page < 1 ) {
            return new WP_Error(
                'rest_invalid_page_number',
                __( 'Page number must be greater than or equal to 1.', 'istiaqhossain-optivine' ),
                array( 'status' => 400 )
            );
        }

        if ( ! empty( $status ) && ! in_array( $status, array( 'active', 'inactive' ), true ) ) {
            return new WP_Error(
                'rest_invalid_status',
                __( 'Status must be either "active" or "inactive".', 'istiaqhossain-optivine' ),
                array( 'status' => 400 )
            );
        }

        // Get items from model
        $items = $this->workspace_model->get_workspaces( $per_page, $page, $status );

        if ( is_wp_error( $items ) ) {
            return $items;
        }

        // Get total count for pagination headers
        $total = $this->workspace_model->count_workspaces( $status );
        if ( is_wp_error( $total ) ) {
            return $total;
        }

        // Calculate pagination values
        $max_pages = ceil( $total / $per_page );
        $response = rest_ensure_response( $items );

        // Add pagination headers
        $response->header( 'X-WP-Total', (int) $total );
        $response->header( 'X-WP-TotalPages', (int) $max_pages );

        // Calculate base URL for pagination
        $base = add_query_arg( $request->get_query_params(), rest_url( $this->get_namespace() . '/' . $this->get_endpoint() ) );

        // Add pagination links
        if ( $page > 1 ) {
            $prev_page = $page - 1;
            $prev_link = add_query_arg( 'page', $prev_page, $base );
            $response->link_header( 'prev', $prev_link );
        }

        if ( $page < $max_pages ) {
            $next_page = $page + 1;
            $next_link = add_query_arg( 'page', $next_page, $base );
            $response->link_header( 'next', $next_link );
        }

        return $response;
    }

    public function get_item( $request ) {
        $workspace_id = (int) $request['id'];
        $item = $this->workspace_model->get_workspace( $workspace_id );

        if ( is_wp_error( $item ) ) {
            return $item;
        }

        return rest_ensure_response( $item );
    }

    public function create_item( $request ) {
        if ( ! empty( $request['id'] ) ) {
            return new WP_Error(
                'rest_workspace_exists',
                __( 'Cannot create existing workspace.', 'istiaqhossain-optivine' ),
                array( 'status' => 400 )
            );
        }

        $prepared_workspace = $this->prepare_item_for_database( $request );
        if ( is_wp_error( $prepared_workspace ) ) {
            return $prepared_workspace;
        }

        $result = $this->workspace_model->create_workspace( $prepared_workspace );

        if ( is_wp_error( $result ) ) {
            return $result;
        }

        $response = rest_ensure_response( $result );
        $response->set_status( 201 );

        return $response;
    }

    public function update_item( $request ) {
        $workspace_id = (int) $request['id'];
        $prepared_workspace = $this->prepare_item_for_database( $request );

        if ( is_wp_error( $prepared_workspace ) ) {
            return $prepared_workspace;
        }

        $result = $this->workspace_model->update_workspace( $workspace_id, $prepared_workspace );

        if ( is_wp_error( $result ) ) {
            return $result;
        }

        $response = rest_ensure_response( $result );
        return $response;
    }

    public function delete_item( $request ) {
        $workspace_id = (int) $request['id'];
        $result = $this->workspace_model->delete_workspace( $workspace_id );

        if ( is_wp_error( $result ) ) {
            return $result;
        }

        $response = rest_ensure_response( $result );
        return $response;
    }

    protected function prepare_item_for_database( $request ) {
        $prepared_workspace = array();

        // Required field: name
        if ( isset( $request['name'] ) ) {
            $name = sanitize_text_field( $request['name'] );
            if ( empty( $name ) ) {
                return new WP_Error(
                    'rest_workspace_name_required',
                    __( 'Workspace name is required.', 'istiaqhossain-optivine' ),
                    array( 'status' => 400 )
                );
            }
            $prepared_workspace['name'] = $name;
        }

        // Optional field: is_active
        if ( isset( $request['is_active'] ) ) {
            $is_active = rest_sanitize_boolean( $request['is_active'] );
            $prepared_workspace['is_active'] = $is_active;
        }

        return $prepared_workspace;
    }

    public function get_item_schema() {
        if ( $this->schema ) {
            return $this->add_additional_fields_schema( $this->schema );
        }

        $schema = array(
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => 'workspace',
            'type'       => 'object',
            'properties' => array(
                'id'         => array(
                    'description' => __( 'Unique identifier for the workspace.', 'istiaqhossain-optivine' ),
                    'type'        => 'integer',
                    'context'     => array( 'view', 'edit', 'embed' ),
                    'readonly'    => true,
                ),
                'name'       => array(
                    'description' => __( 'The name of the workspace.', 'istiaqhossain-optivine' ),
                    'type'        => 'string',
                    'required'    => true,
                    'context'     => array( 'view', 'edit' ),
                ),
                'is_active'  => array(
                    'description' => __( 'Whether the workspace is active.', 'istiaqhossain-optivine' ),
                    'type'        => 'boolean',
                    'context'     => array( 'view', 'edit' ),
                    'default'     => true,
                ),
                'created_at' => array(
                    'description' => __( "The date the workspace was created, in the site's timezone.", 'istiaqhossain-optivine' ),
                    'type'        => 'string',
                    'format'      => 'date-time',
                    'context'     => array( 'view' ),
                    'readonly'    => true,
                ),
                'updated_at' => array(
                    'description' => __( "The date the workspace was last updated, in the site's timezone.", 'istiaqhossain-optivine' ),
                    'type'        => 'string',
                    'format'      => 'date-time',
                    'context'     => array( 'view' ),
                    'readonly'    => true,
                ),
            ),
        );

        $this->schema = $schema;
        return $this->add_additional_fields_schema( $this->schema );
    }

    public function get_collection_params() {
        $query_params = parent::get_collection_params();

        $query_params['status'] = array(
            'description' => __( 'Limit result set to workspaces with a specific status.', 'istiaqhossain-optivine' ),
            'type'        => 'string',
            'enum'        => array( 'active', 'inactive' ),
            'sanitize_callback' => 'sanitize_text_field',
        );

        return $query_params;
    }
}