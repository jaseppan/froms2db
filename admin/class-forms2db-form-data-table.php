<?php

if(!class_exists('Link_List_Table')){

   require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

}

/**
 * Core class used to implement displaying posts in a list table.
 *
 * @since 3.1.0
 * @access private
 *
 * @see WP_List_Table
 */
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Forms2db_Data_Table extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'form_data', 'sp' ), //singular name of the listed records
			'plural'   => __( 'forms2db_data', 'sp' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		] );

	}


	/**
	 * Retrieve forms2db_data data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_form_data( $per_page = 5, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}forms2db_data";

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


        $results = $wpdb->get_results( $sql, ARRAY_A );

        //$form_data = array_map(function( $row ) {
        //    return array('id' => $row['id'], 'data' => json_decode($row['form_data'], ARRAY_A));
        //}, $result );
        $keys = [];
        $data = [];

        foreach( $results as $key => $row ) {
            $tmp_data = json_decode($row['form_data'], ARRAY_A);
            $keys = array_merge($keys, array_keys($tmp_data));
            $data[$key]['ID'] = $row['id'];
            $data[$key] = array_merge($data[$key], $tmp_data);
        }
        
        //var_dump($data);

		return $data;
	}


	/**
	 * Delete a form_data record.
	 *
	 * @param int $id form_data ID
	 */
	public static function delete_form_data( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}forms2db_data",
			[ 'ID' => $id ],
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


	/** Text displayed when no form_data data is available */
	public function no_items() {
		_e( 'No forms2db_data avaliable.', 'sp' );
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
        var_dump($column_name);
		switch ( $column_name ) {
			case 'lisatty':
			case 'field-type':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
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

		$delete_nonce = wp_create_nonce( 'sp_delete_form_data' );

		$title = '<strong>' . $item['eka'] . '</strong>';

		$actions = [
			'delete' => sprintf( '<a href="?page=%s&action=%s&form_data=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['ID'] ), $delete_nonce )
		];

		return $title . $this->row_actions( $actions );
	}


	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = [
			'cb'      => '<input type="checkbox" />',
			'eka'    => __( 'eka', 'sp' ),
			'lisatty' => __( 'lisatty', 'sp' ),
			'field-type'    => __( 'field-type', 'sp' )
		];

		return $columns;
	}


	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'eka' => array( 'eka', true ),
			'field-type' => array( 'field-type', false )
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
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'forms2db_data_per_page', 5 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );

        $this->items = self::get_form_data( $per_page, $current_page );
    
    }

	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'sp_delete_form_data' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				self::delete_form_data( absint( $_GET['form_data'] ) );

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
				self::delete_form_data( $id );

			}

			// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		        // add_query_arg() return the current url
		        wp_redirect( esc_url_raw(add_query_arg()) );
			exit;
		}
    }
    
    /**
	 * @global WP_Query $wp_query WordPress Query object.
	 * @global int $per_page
	 * @param array $posts
	 * @param int   $level
	 */
	public function display_rows( $posts = array(), $level = 0 ) {
		global $wp_query, $per_page;

		if ( empty( $posts ) ) {
			$posts = $wp_query->posts;
		}

		add_filter( 'the_title', 'esc_html' );

		if ( $this->hierarchical_display ) {
			$this->_display_rows_hierarchical( $posts, $this->get_pagenum(), $per_page );
		} else {
			$this->_display_rows( $posts, $level );
		}
	}
}



$forms2db_data_table = new Forms2db_Data_Table();  

if( isset($_GET['s']) ){

    $forms2db_data_table->prepare_items($_GET['s']);

} else {

    $forms2db_data_table->prepare_items();

}

$forms2db_data_table->display();
 
?>