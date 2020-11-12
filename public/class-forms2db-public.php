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
class Forms2db_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action('init', array($this, 'save_form'));

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Forms2db_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Forms2db_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/forms2db-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Forms2db_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Forms2db_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/forms2db-public.js', array( 'jquery' ), $this->version, false );

	}

	public function save_form() {
		
		if( isset($_POST['forms2db-form-user-action']) && $_POST['forms2db-form-user-action'] == 'saveform' ) {

			$errors = [];
			if(is_numeric($_POST['forms2db-form-id'])) {
				$form_id = $_POST['forms2db-form-id'];
				$form_fields = get_post_meta($form_id, '_forms2db_form', true);
			} else {
				$errors[] = 'invalid_form_id'; 
				return;
			}

			
			if(is_numeric($_POST['forms2db-post-id'])) {
				$post_id = $_POST['forms2db-post-id'];
			} else {
				$errors[] = 'invalid_post_id'; 
				return;
			}

			var_dump($_POST['forms2db-form-id']);

			
			if(!wp_verify_nonce( $_POST['forms2db-nonce'], $form_fields['nonce'] )) {
				$errors[] = 'invalid_nonce'; 
				return;
			}
			
			$form_data_array = [];

			foreach( $form_fields as $form_field ) {
				if(isset($form_field['name'])) {
					$name = esc_attr($form_field['name']);
					$value = sanitize_text_field( $_POST[$name] );
					if(!empty($value)) {
						$form_data_array[$name] = $value;
					}
				}
			}

			if(empty($form_data_array)) {
				$errors[] = 'empty_form'; 
				return;
			}

			$form_data = json_encode($form_data_array);

			global $wpdb;

			if( is_user_logged_in() ) {
				$user_id = get_current_user_id();
				$ids = array(
					$form_id,
					$post_id,
					$user_id,
				);
				$sql = "SELECT id FROM {$wpdb->prefix}forms2db_data WHERE form_id = %d && post_id = %d && user_id = %d";
				$results = $wpdb->get_results($wpdb->prepare($sql, $ids));
				$user_data_id = $results[0]->id;
			} else {
				$user_id = NULL;
			}

			if(isset($user_data_id)) {
				$data = array(
					'form_data' 	=> $form_data,
					'id'			=> $user_data_id
				);
				
				$sql = "UPDATE {$wpdb->prefix}forms2db_data SET form_data = %s WHERE id = %d";
			
			} else {
				$data = array(
					'post_id' 	=>  $post_id,
					'form_id' 	=>  $form_id,
					'user_id' 	=>  $user_id,
					'form_data' =>  $form_data,
				);

				$sql = "INSERT INTO {$wpdb->prefix}forms2db_data (post_id, form_id, user_id, form_data) VALUES (%d, %d, %d, %s)";

			}
			var_dump($sql);

			$result = $wpdb->query($wpdb->prepare($sql, $data));

			


			exit();
			
		}
	}

}
