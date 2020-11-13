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
	 * The id of current form.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param      string    $form_id    The id of current form.
	 */
	private $form_id;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $form_id    The id of current form.
	 */
	public function __construct( $form_id ) {

		$this->form_id = intval($form_id);
		add_action('init', array($this, 'save_form'));

	}
	
	/**
	 * Initialize the front end view.
	 *
	 * @since    1.0.0
	 * @access   public
	 */    
    public function view() {
		if( isset($_POST['forms2db-form-user-action']) ) {
			require 'views/forms2db-thank-you.php';
		} else {
			require 'views/forms2db-form.php';
		}
	}
	
	/**
	 * Add fields to the form.
	 *
	 * @since    1.0.0
	 * @access   public
	 */      
    public function add_fields() {
		
		global $post;

        $fields_obj = new Forms2DbFields();
		$form_fields = get_post_meta($this->form_id, '_forms2db_form', true);
		$post_id = $post->ID;
		
		do_action('before_form');
        foreach($form_fields as $form_field) {
			
			// ADD VALUES FROM $_POST IF EXIST!!!
			
			if( is_array( $form_field ) ) { ?>
				<div class="forms2db-field-container <?php echo isset($form_field['container-classes']) ? esc_attr($form_field['container-classes']) : ''  ?>">
					<?php echo $fields_obj->add_field($form_field); ?>
				</div>
			<?php }
		}

		wp_nonce_field( esc_attr($form_fields['nonce']), 'forms2db-nonce' );
		echo '<input type="hidden" name="forms2db-form-user-action" value="saveform">';
		echo sprintf('<input type="hidden" name="forms2db-form-id" value="%d">', $this->form_id);
		echo sprintf('<input type="hidden" name="forms2db-post-id" value="%d">', $post_id);
		do_action('before_submit');
		echo sprintf('<div class="forms2db-field-container submit-container"><input type="submit" name="submit" value="%s"></div>', esc_html($form_fields['submit-text']));
		do_action('after_form');
		
	}

	/**
	 * Save form data
	 *
	 * @since    1.0.0
	 * @access   public
	 */  
	public function save_form() {
		
		if( isset($_POST['forms2db-form-user-action']) && $_POST['forms2db-form-user-action'] == 'saveform' ) {

			$errors = [];
			if(is_numeric($_POST['forms2db-form-id'])) {
				$form_id = $_POST['forms2db-form-id'];
				$form_fields = get_post_meta($form_id, '_forms2db_form', true);
				$form_settings = get_post_meta($form_id, '_forms2db_settings', true); // ADD HERE "MODIFYABLE" "CONFIRM_REQUIRED"
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
			
		}
	}

}
