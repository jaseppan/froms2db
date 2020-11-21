<?php

/**
 * Class responsible for manage data saved by forms
 *
 * @package    Forms2db
 * @subpackage Forms2db/admin
 * @author     Janne Seppänen <j.v.seppanen@gmail.com>
 */
class Forms2dbFormData {

    private $form;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct() {
        
        $this->available_forms = $this->available_forms();
        $this->current_form_data = $this->current_form_data();
        
        add_action('admin_menu', array($this, 'add_form_data_page'));
        
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

    public function current_form_data() {
        
        global $wpdb;

        if( isset($this->form_id) ) {
            $form_id = $this->form_id;
        } elseif( isset($_GET['form-id']) && is_numeric($_GET['form-id'])  ) {
            $form_id = $_GET['form-id'];
        } else {
            return null;
        }

        

        $sql = "SELECT id, form_data FROM {$wpdb->prefix}forms2db_data WHERE form_id = {$form_id}";
        if(isset($_GET['s'])) {
            $sql .= " && form_data LIKE '%" . $_GET['s'] . "%'";
        } elseif( isset($_GET['item']) && is_numeric($_GET['item']) ) {
            $sql .= " && id = " . $_GET['item'];
        }
        
        /*if(isset($_GET['order-by'])) {
            $sql = "ORDER BY %s";
            $args[] = $_GET['order-by'];
        } else {
            $sql = "ORDER BY %s";
        }

        if(isset($_GET['order'])) {
            $sql = "ORDER BY %s";
            $args[] = $_GET['order-by'];
        }*/

        
        $results = $wpdb->get_results($wpdb->prepare($sql), ARRAY_A);

        $form_data = array_map(function( $row ) {
            return array('id' => $row['id'], 'data' => json_decode($row['form_data'], ARRAY_A));
        }, $results );

        $form_structure = get_post_meta($form_id, '_forms2db_form_structure', false);
        $form['structure'] = $form_structure[0];
        $form['data'] = $form_data;
        return $form;

    }
    
    public function add_form_data_page() {
        add_submenu_page( 
            'edit.php?post_type=forms2db-forms',
            __('Saved data', 'forms2db'), 
            __('Saved data', 'forms2db'), 
            'edit_posts', 
            'forms2db-save-data', 
            array($this, 'data_page_content'), 
            'dashicons-groups' 
    
        );
    }

    public function data_page_content() { 
        
        require('views/form-data-manager-header.php'); 

        if(isset( $_GET['item'] ) && is_numeric($_GET['item'])) {

            switch($_GET['action']) {
                case 'edit' :
                    require('views/form-data-editor.php');
                    break;
                case 'delete' :

                    break;
            }

        } else {
            require('views/form-data-manager.php');    
        }
        
        
    }

    public function form_selector() { 
        
        if(count($this->available_forms) == 0) {
            _e("No forms created", "forms2db");
            return;
        } elseif ( count($this->available_forms) == 1 ) {
            $this->form_id =  $this->available_forms[0]->ID;
            return;
        }

        require('views/partials/form-selector.php');    
    }

    public function search_field() {

    }

    public function form_data_table_link( $additional_args ) {
        $base_link_query_args = array(
            'post_type'     => 'forms2db-forms',
            'page'          => 'forms2db-save-data',
            'form-id'       => intval($_GET['form-id']),
        );

        $args = array_merge($base_link_query_args, $additional_args);
        $link = add_query_arg( $order_link_query_args, $args, admin_url('edit.php') );
        return $link;
    }

    public function data_content() {
        if(!empty($this->current_form_data['data'])) {
            require('views/partials/form-data-table.php');
        }

    }

    public function csv_export() {

    }

}