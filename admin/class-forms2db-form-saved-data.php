<?php

/*
Plugin Name: WP_List_Table Class Example
Plugin URI: https://www.sitepoint.com/using-wp_list_table-to-create-wordpress-admin-tables/
Description: Demo on how WP_List_Table Class works
Version: 1.0
Author: Collins Agbonghama
Author URI:  https://w3guy.com
*/

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Saved_Data_List extends WP_List_Table {

	private $form_structure;

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Saved data', 'forms2db' ), //singular name of the listed records
			'plural'   => __( 'Saved data', 'forms2db' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		] );

		$this->form_structure = $this->get_form_structure();

	}


	/**
	 * Retrieve saved_data data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_saved_data( $per_page = 5, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}forms2db_data WHERE form_id = " . $_GET['form-id'];

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


        $results = $wpdb->get_results( $sql, ARRAY_A );

        $keys = [];
        $data = [];

        foreach( $results as $key => $row ) {
            $tmp_data = json_decode($row['form_data'], ARRAY_A);
            $keys = array_merge($keys, array_keys($tmp_data));
            $data[$key]['ID'] = $row['id'];
			$data[$key] = array_merge($data[$key], $tmp_data);
			$data[$key]['datetime'] = $row['datetime'];
		}

		return $data;
	}


	/**
	 * Delete a customer record.
	 *
	 * @param int $id customer ID
	 */
	public static function delete_customer( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}forms2db_data",
			[ 'id' => $id ],
			[ '%d' ]
		);
	}


	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}forms2db_data";

		return $wpdb->get_var( $sql );
	}


	/** Text displayed when no customer data is available */
	public function no_items() {
		_e( 'No saved_data avaliable.', 'forms2db' );
	}


	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {

		return $item[ $column_name ];

	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
		);
	}


	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_name( $item ) {

		$item_name = $this->form_structure[0]['name'];

		$delete_nonce = wp_create_nonce( 'sp_delete_customer' );

		$title = '<strong>' . $item[$item_name] . '</strong>';

		$actions = [
			'delete' => sprintf( '<a href="?page=%s&action=%s&customer=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['ID'] ), $delete_nonce )
		];

		return $title . $this->row_actions( $actions );
	}


	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {


		$columns['cb'] =  '<input type="checkbox" />';

		foreach ( $this->form_structure as $item ) {
			$columns[$item['name']] = $item['label']; 
		}

		$columns['datetime'] = __('Time');

		return $columns;
	}


	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'datetime' => array( 'datetime', true ),
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => 'Delete'
		];

		return $actions;
	}

	/**
	 * Returns an associative array containing the form structure.
	 *
	 * @return array
	 */
	public function get_form_structure() {
		if(isset($_GET['form-id'])) {
			$form_id = intval($_GET['form-id']);
			$form_structure = get_post_meta($form_id, '_forms2db_form_structure', true );
			return $form_structure;
		}
	}


	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'saved_data_per_page', 5 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );

		$this->items = self::get_saved_data( $per_page, $current_page );
	}

	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'sp_delete_customer' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				self::delete_customer( absint( $_GET['customer'] ) );

		                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		                // add_query_arg() return the current url
		                wp_redirect( esc_url_raw(add_query_arg()) );
				exit;
			}

		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_POST['bulk-delete'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_customer( $id );

			}

			// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		        // add_query_arg() return the current url
		        wp_redirect( esc_url_raw(add_query_arg()) );
			exit;
		}
	}

}


class Forms2db_Saved_Data_Page {

	// class instance
	static $instance;

	// customer WP_List_Table object
	public $saved_data_obj;

	// class constructor
	public function __construct() {
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
		add_action( 'admin_menu', [ $this, 'saved_data_menu' ] );
		$this->available_forms = $this->available_forms();
	}


	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	public function saved_data_menu() {

		$hook = add_submenu_page( 
            'edit.php?post_type=forms2db-forms',
            __('Saved data', 'forms2db'), 
            __('Saved data', 'forms2db'), 
            'edit_posts', 
            'forms2db-save-data', 
            array($this, 'saved_data_page'), 
            'dashicons-groups' 
        );

		add_action( "load-{$hook}", [ $this, 'screen_option' ] );

	}


	/**
	 * Plugin settings page
	 */
	public function saved_data_page() {
		?>
		<div class="wrap">
			<h2>Saved data</h2>

			<div id="poststuff">
				<div id="post-body" class="metabox-holder">
					<div id="post-body-content">
						<?php $this->form_selector(); ?>
						<?php if(isset($_GET['form-id'])) { ?>
							<div class="meta-box-sortables ui-sortable">
								<form method="post">
									<?php
									$this->saved_data_obj->prepare_items();
									$this->saved_data_obj->display(); ?>
								</form>
							</div>
						<?php } ?>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
	<?php
	}

	/**
	 * Screen options
	 */
	public function screen_option() {

		$option = 'per_page';
		$args   = [
			'label'   => 'Saved data',
			'default' => 5,
			'option'  => 'saved_data_per_page'
		];

		add_screen_option( $option, $args );

		$this->saved_data_obj = new Saved_Data_List();
	}

	public function form_selector() { 
        
        if(count($this->available_forms) == 0) {
            _e("No forms created", "forms2db");
            return;
        } elseif ( isset($this->form_id) && count($this->available_forms) == 1 ) {
            $this->form_id =  $this->available_forms[0]->ID;
            return;
        } elseif ( isset($this->form_id) && isset($_GET['form-id']) && is_numeric($_GET['form-id']) ) {
            $this->form_id = $_GET['form-id'];
        } else {
			$this->form_id = null;
		}

        require('views/partials/form-selector.php');    
	}
	
	public function available_forms() {

        $args = array(
            'post_type'=> 'forms2db-forms',
            'orderby'    => 'ID',
            'post_status' => 'publish',
            'order'    => 'DESC',
            'posts_per_page' => -1 // this will retrive all the post that is published 
        );
        
        return get_posts($args);

	}

	public function csv_export() {

    }


	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}



}