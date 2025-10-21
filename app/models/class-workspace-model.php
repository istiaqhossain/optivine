<?php
namespace ISTIAQHOSSAIN\Optivine\Models;

use WP_Error;
use ISTIAQHOSSAIN\Optivine\Base;

// Abort if called directly.
defined( 'WPINC' ) || die;

class Workspace extends Base {

    private $table = '';

    protected function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'ish_optivine_workspaces';
    }

    public function get_workspaces( $per_page = 20, $page = 1, $status = '' ) {
        global $wpdb;
        
        // Validate inputs
        $per_page = (int) $per_page > 0 ? (int) $per_page : 20;
        $page = (int) $page > 0 ? (int) $page : 1;
        $offset = ( $page - 1 ) * $per_page;
        
        // Build the query
        $sql = "SELECT id, name, is_active, created_at, updated_at FROM {$this->table}";
        
        // Add status filter if provided
        if ( ! empty( $status ) && in_array( $status, array( 'active', 'inactive' ), true ) ) {
            $is_active = 'active' === $status ? 1 : 0;
            $sql .= $wpdb->prepare( ' WHERE is_active = %d', $is_active );
        }
        
        $sql .= $wpdb->prepare( ' ORDER BY id DESC LIMIT %d OFFSET %d', $per_page, $offset );
        
        $results = $wpdb->get_results( $sql );
        
        if ( null === $results ) {
            return new WP_Error( 'db_query_error', 'Database query failed', array( 'status' => 500 ) );
        }
        
        return $results;
    }

    public function get_workspace( $workspace_id ) {
        global $wpdb;
        
        // Validate input
        $workspace_id = (int) $workspace_id;
        if ( $workspace_id <= 0 ) {
            return new WP_Error( 'invalid_id', 'Invalid workspace ID', array( 'status' => 400 ) );
        }
        
        $sql = $wpdb->prepare(
            "SELECT id, name, is_active, created_at, updated_at FROM {$this->table} WHERE id = %d",
            $workspace_id
        );
        
        $result = $wpdb->get_row( $sql );
        
        if ( null === $result ) {
            return new WP_Error( 'not_found', 'Workspace not found', array( 'status' => 404 ) );
        }
        
        return $result;
    }

    public function create_workspace( $data ) {
        global $wpdb;
        
        // Validate input
        if ( empty( $data['name'] ) ) {
            return new WP_Error( 'missing_name', 'Workspace name is required', array( 'status' => 400 ) );
        }
        
        $name = sanitize_text_field( $data['name'] );
        $is_active = isset( $data['is_active'] ) ? (int) $data['is_active'] : 1;
        
        // Check if workspace with same name already exists
        $existing = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM {$this->table} WHERE name = %s",
            $name
        ) );
        
        if ( $existing ) {
            return new WP_Error( 'duplicate_name', 'Workspace with this name already exists', array( 'status' => 409 ) );
        }
        
        // Insert the workspace
        $inserted = $wpdb->insert(
            $this->table,
            array(
                'name' => $name,
                'is_active' => $is_active,
            ),
            array( '%s', '%d' )
        );
        
        if ( false === $inserted ) {
            return new WP_Error( 'db_insert_error', 'Failed to insert workspace', array( 'status' => 500 ) );
        }
        
        // Get the newly created workspace
        $workspace_id = $wpdb->insert_id;
        $workspace = $this->get_workspace( $workspace_id );
        
        if ( is_wp_error( $workspace ) ) {
            return $workspace;
        }
        
        do_action( 'optivine_workspace_created', $workspace );
        
        return $workspace;
    }

    public function update_workspace( $workspace_id, $data ) {
        global $wpdb;
        
        // Validate inputs
        $workspace_id = (int) $workspace_id;
        if ( $workspace_id <= 0 ) {
            return new WP_Error( 'invalid_id', 'Invalid workspace ID', array( 'status' => 400 ) );
        }
        
        if ( empty( $data['name'] ) ) {
            return new WP_Error( 'missing_name', 'Workspace name is required', array( 'status' => 400 ) );
        }
        
        // Check if workspace exists
        $existing_workspace = $this->get_workspace( $workspace_id );
        if ( is_wp_error( $existing_workspace ) ) {
            return $existing_workspace;
        }
        
        $name = sanitize_text_field( $data['name'] );
        $is_active = isset( $data['is_active'] ) ? (int) $data['is_active'] : $existing_workspace->is_active;
        
        // Check if another workspace with the same name exists
        $duplicate = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM {$this->table} WHERE name = %s AND id != %d",
            $name,
            $workspace_id
        ) );
        
        if ( $duplicate ) {
            return new WP_Error( 'duplicate_name', 'Workspace with this name already exists', array( 'status' => 409 ) );
        }
        
        // Update the workspace
        $updated = $wpdb->update(
            $this->table,
            array(
                'name' => $name,
                'is_active' => $is_active,
                'updated_at' => current_time( 'mysql' ),
            ),
            array( 'id' => $workspace_id ),
            array( '%s', '%d', '%s' ),
            array( '%d' )
        );
        
        if ( false === $updated ) {
            return new WP_Error( 'db_update_error', 'Failed to update workspace', array( 'status' => 500 ) );
        }
        
        // Get the updated workspace
        $workspace = $this->get_workspace( $workspace_id );
        
        if ( is_wp_error( $workspace ) ) {
            return $workspace;
        }
        
        do_action( 'optivine_workspace_updated', $workspace );
        
        return $workspace;
    }
    
    public function delete_workspace( $workspace_id ) {
        global $wpdb;
        
        // Validate input
        $workspace_id = (int) $workspace_id;
        if ( $workspace_id <= 0 ) {
            return new WP_Error( 'invalid_id', 'Invalid workspace ID', array( 'status' => 400 ) );
        }
        
        // Check if workspace exists
        $workspace = $this->get_workspace( $workspace_id );
        if ( is_wp_error( $workspace ) ) {
            return $workspace;
        }
        
        do_action( 'optivine_before_workspace_deleted', $workspace_id );
        
        // Delete the workspace
        $deleted = $wpdb->delete(
            $this->table,
            array( 'id' => $workspace_id ),
            array( '%d' )
        );
        
        if ( false === $deleted ) {
            return new WP_Error( 'db_delete_error', 'Failed to delete workspace', array( 'status' => 500 ) );
        }
        
        do_action( 'optivine_workspace_deleted', $workspace_id );
        
        return array( 'id' => $workspace_id );
    }
    
    public function count_workspaces( $status = '' ) {
        global $wpdb;
        
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        
        // Add status filter if provided
        if ( ! empty( $status ) && in_array( $status, array( 'active', 'inactive' ), true ) ) {
            $is_active = 'active' === $status ? 1 : 0;
            $sql .= $wpdb->prepare( ' WHERE is_active = %d', $is_active );
        }
        
        $count = $wpdb->get_var( $sql );
        
        if ( null === $count ) {
            return new WP_Error( 'db_query_error', 'Database query failed', array( 'status' => 500 ) );
        }
        
        return (int) $count;
    }
}