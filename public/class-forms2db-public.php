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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/forms2db-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/forms2db-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Save form data
	 *
	 * @since    1.0.0
	 * @access   public
	 */  
	public function save_form() {
		if( isset($_POST['forms2db-form-user-action']) && $_POST['forms2db-form-user-action'] == 'saveform' ) {
			
			global $forms2db_errors;
			$forms2db_errors = new WP_Error();

			if(is_numeric($_POST['forms2db-form-id'])) {
				$form_id = $_POST['forms2db-form-id'];
				$form_fields = get_post_meta($form_id, '_forms2db_form', true);
				$form_settings = get_post_meta($form_id, '_forms2db_settings', true); // ADD HERE "MODIFYABLE" "CONFIRM_REQUIRED"
			} else {
				$forms2db_errors->add( 'form2db-errors', __('Invalid form id'), 'invalid_form_id' );
			}

			
			if(is_numeric($_POST['forms2db-post-id'])) {
				$post_id = $_POST['forms2db-post-id'];
			} else {
				$forms2db_errors->add( 'form2db-errors', __('Invalid post id'), 'invalid_post_id' );
			}
			
			if(!wp_verify_nonce( $_POST['forms2db-nonce'], esc_attr($form_fields['nonce']) )) {
				$forms2db_errors->add( 'form2db-errors', __('Invalid nonce'), 'invalid_nonce' );
			}
			
			$form_data_array = [];

			
			if( isset($form_fields) ) {
				foreach( $form_fields as $form_field ) {
					if(isset($form_field['name'])) {
						$name = esc_attr($form_field['name']);
						$options = isset($form_field['options']) ? $form_field['options'] : [];
						$value = forms2db_validate( $_POST[$name], $form_field['field-type'], $form_field['attributes'], $form_field['label'], $form_field['name'], $options );
						if(!empty($value)) {
							$form_data_array[$name] = $value;
						}
					}
				}
			}


			if(empty($form_data_array)) {
				$forms2db_errors->add( 'form2db-errors', __('Empty form'), 'missing_data' );
			}

			
			if( count( $forms2db_errors->get_error_messages() ) > 0 ) {
				return;
			}

			// TESTAUS
			$this->send_messages($form_id, $form_data_array);

			$form_data = json_encode($form_data_array);
			
			global $wpdb;

			// ADD USER IF THE FORM IS MODIFYABLE AND USER IS LOGGED IN OR IF FORM_ID EXIST
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

			$result = $wpdb->query($wpdb->prepare($sql, $data));

			if( $result == true ) {
				global $forms2db_record_id;
				$forms2db_record_id = $wpdb->insert_id;
				$this->send_messages($form_id, $form_data_array);
			}			
		}
	}

	public function send_messages( $form_id, $form_data_array ) {
		$emails['admin'] = get_post_meta( $form_id, '_forms2db_admin_emails',  true );
		$emails['user'] = isset($form_data_array['email']) ? $form_data_array['email'] : null;
		$subjects['admin'] = (get_post_meta( $form_id, '_forms2db_admin_email_subject',  true)) ? get_post_meta( $form_id, '_forms2db_admin_email_subject',  true ) : __('Message from ') . home_url();
		$subjects['user'] = (get_post_meta( $form_id, '_forms2db_user_email_subject',  true )) ? get_post_meta( $form_id, '_forms2db_user_email_subject',  true ) : __('Message from ') . home_url();
		$templates['admin'] = get_post_meta( $form_id, '_forms2db_admin_message',  true );
		$templates['user'] = get_post_meta( $form_id, '_forms2db_user_message',  true );
		
		foreach( $templates as $message_key => $message ) {
			if(isset($emails[$message_key]) && !empty($message)) {
				foreach($form_data_array as $key => $value) {
					$message = str_replace( "[$key]", $value, $message );
				}
				wp_mail( $emails[$message_key], $subjects[$message_key], $message );
			}
		}
	}

}
