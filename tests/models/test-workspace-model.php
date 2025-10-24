<?php 
use ISTIAQHOSSAIN\Optivine\DbTable;
use ISTIAQHOSSAIN\Optivine\Models\Workspace;

class Test_Workspace_Model extends WP_UnitTestCase {
    
    private $workspace_model;
    
    public function setUp(): void {
        parent::setUp();

        DbTable::instance()->create_table();
        $this->workspace_model = Workspace::instance();
    }
    
    public function tearDown(): void {
        parent::tearDown();
    }

    public function test_it_creates_a_workspace() {
        $data = array( 'name' => 'My Workspace', 'is_active' => 1 );
        $result = $this->workspace_model->create_workspace( $data );

        $this->assertNotWPError( $result );
        $this->assertEquals( 'My Workspace', $result->name );
        $this->assertEquals( 1, $result->is_active );
    }

    public function test_it_fails_to_create_workspace_without_name() {
        $result = $this->workspace_model->create_workspace( array() );

        $this->assertWPError( $result );
        $this->assertEquals( 'missing_name', $result->get_error_code() );
    }

    public function test_it_fails_to_create_duplicate_workspace() {
        $data = [ 'name' => 'Duplicate Workspace' ];
        $this->workspace_model->create_workspace( $data );
        $result = $this->workspace_model->create_workspace( $data );

        $this->assertWPError( $result );
        $this->assertEquals( 'duplicate_name', $result->get_error_code() );
    }

    public function test_it_gets_a_workspace_by_id() {
        $created = $this->workspace_model->create_workspace( [ 'name' => 'Workspace 1' ] );
        $fetched = $this->workspace_model->get_workspace( $created->id );

        $this->assertNotWPError( $fetched );
        $this->assertEquals( $created->name, $fetched->name );
    }

    public function test_it_fails_to_get_non_existing_workspace() {
        $result = $this->workspace_model->get_workspace( 9999 );

        $this->assertWPError( $result );
        $this->assertEquals( 'not_found', $result->get_error_code() );
    }

    public function test_it_updates_a_workspace() {
        $created = $this->workspace_model->create_workspace( [ 'name' => 'Old Name' ] );
        $updated = $this->workspace_model->update_workspace( $created->id, [ 'name' => 'New Name' ] );

        $this->assertNotWPError( $updated );
        $this->assertEquals( 'New Name', $updated->name );
    }

    public function test_it_fails_to_update_with_duplicate_name() {
        $w1 = $this->workspace_model->create_workspace( [ 'name' => 'A' ] );
        $w2 = $this->workspace_model->create_workspace( [ 'name' => 'B' ] );

        $result = $this->workspace_model->update_workspace( $w2->id, [ 'name' => 'A' ] );

        $this->assertWPError( $result );
        $this->assertEquals( 'duplicate_name', $result->get_error_code() );
    }

    public function test_it_deletes_a_workspace() {
        $created = $this->workspace_model->create_workspace( [ 'name' => 'To Delete' ] );
        $deleted = $this->workspace_model->delete_workspace( $created->id );

        $this->assertNotWPError( $deleted );
        $this->assertEquals( [ 'id' => $created->id ], $deleted );

        // Verify it's gone
        $check = $this->workspace_model->get_workspace( $created->id );
        $this->assertWPError( $check );
        $this->assertEquals( 'not_found', $check->get_error_code() );
    }

    public function test_it_counts_workspace_correctly() {
        $count_empty = $this->workspace_model->count_workspaces();
        
        $this->workspace_model->create_workspace( array( 'name' => 'Workspace 1' ) );
        $this->workspace_model->create_workspace( array( 'name' => 'Workspace 2', 'is_active' => 0 ) );

        $count_all = $this->workspace_model->count_workspaces();
        $count_active = $this->workspace_model->count_workspaces( 'active' );
        $count_inactive = $this->workspace_model->count_workspaces( 'inactive' );

        $this->assertEquals( 0, $count_empty );
        $this->assertEquals( 2, $count_all );
        $this->assertEquals( 1, $count_active );
        $this->assertEquals( 1, $count_inactive );
    }
}