<?php

/**
 * Class responsible for manage data saved by forms
 *
 * @package    Forms2db
 * @subpackage Forms2db/admin
 * @author     Janne Seppänen <j.v.seppanen@gmail.com>
 */
class Forms2dbFormData {

    private $form_id;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct() {
        $this->form_id = isset($_GET['form-id']) ? intval($_GET['form-id']) : null;
        add_action('admin_menu', array($this, 'add_form_data_page'));
        
    }

    public function add_form_data_page() {
        add_submenu_page( 
            'edit.php?post_type=forms2db-forms',
            __('Saved data', 'forms2db'), 
            __('Saved data', 'forms2db'), 
            'edit_posts', 
            'ilmoittautuneet', 
            array($this, 'data_page_content'), 
            'dashicons-groups' 
    
        );
    }

    public function data_page_content() { 
        require('views/form-data-manager.php');    
    }

    public function form_selector() {

    }

    public function search_field() {

    }

    public function data_content() {

    }

    public function csv_export() {

    }

    public function get_form_data($cols) {
        global $wpdb;

        $sql = "SELECT {$cols} FROM {$wpdb->prefix}";
        
    }





}