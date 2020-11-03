<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://cidedot.com
 * @since      1.0.0
 *
 * @package    Forms2db
 * @subpackage Forms2db/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Forms2db
 * @subpackage Forms2db/public
 * @author     Janne Seppänen <j.v.seppanen@gmail.com>
 */
class Forms2dbForm {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $form_id;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $form_id ) {

		$this->form_id = $form_id;
        

    }
    
    public function view() {
        require 'views/forms2db-form.php';
    }
    
    public function add_fields() {
        
        $fields_obj = new Forms2db_Fields();
        $form_fields = get_post_meta($this->form_id, '_forms2db_form', true);

        foreach($form_fields as $form_field) {
            echo $fields_obj->add_field($form_field);
        }
    }

}
