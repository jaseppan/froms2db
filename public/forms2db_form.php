<?php

/**
 * The public form called by forms2db_form function.
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

	}
	
	/**
	 * Initialize the front end view.
	 *
	 * @since    1.0.0
	 * @access   public
	 */    
    public function view() {

		global $forms2db_errors;

		if( isset($_POST['forms2db-form-user-action']) && empty($forms2db_errors) ) {
			echo "test";
			//require 'views/forms2db-thank-you.php';
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

		$form_values = $this->get_form_data(); // 

        foreach($form_fields as $id => $form_field) {
						
			if( is_array( $form_field ) ) { 
				// GET VALUE FROM $form_values
				if( isset($form_field['name']) ) {
					$name = esc_attr( $form_field['name'] );
					$value = isset($form_values[$name]) ? esc_attr( $form_values[$name] ) : '';
					$form_field['id'] = $id;
					$this->add_field_error($name);
					?>
						<div class="forms2db-field-container <?php echo isset($form_field['container-classes']) ? esc_attr($form_field['container-classes']) : ''  ?>">
							<?php echo $fields_obj->add_field($form_field, $value); ?>
						</div>
					<?php 
				}
			}
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
	 * Get saved data if allowed
	 * 
	 * @since    1.0.0
	 * @access   public
	 */

	public function get_form_data($cells = 'form_data') {

		global $wpdb;
		global $forms2db_record_id;

		if(current_user_can( 'edit_posts' ) && isset($_GET['form-id'])) {
			$where_data = array(
				intval($_GET['form-id']),
			);
			$where_arg = 'id = %d';
		} else {
			$form_settings = get_post_meta($this->form_id, '_forms2db_settings', true);
			$modifyable = $form_settings['modifyable'];
			
			// If form is modifyable show only just inserted values
			if($modifyable == false) {
				if( isset($forms2db_record_id) ){
					$where_data = array(
						$forms2db_record_id,
					);
		
					$where_arg = 'id = %s';
				} else {
					return;
				}
			// If logged in show values saved by current user
			} elseif( is_user_logged_in() ) {
				// If no capability to view others data
				$where_data = array(
					get_current_user_id(),
					$this->form_id,
				);
				$where_arg = 'user_id = %s && form_id = %s';
			// Get values by secret link	
			} elseif( isset($_GET['form-id']) && isset($_GET['form-key']) ){
				$where_data = array(
					intval($_GET['form-id']),
					sanitize_text_field($_GET['form-key']),
				);
	
				$where_arg = 'id = %s && form_key = %d';
			// Get just inserted values
			} else {
				if( isset($forms2db_record_id) ){
					$where_data = array(
						$forms2db_record_id,
					);
		
					$where_arg = 'id = %s';
				}
			}
		} 

		if(isset($where_arg) && isset($where_data)) {
			$sql = "SELECT {$cells} FROM {$wpdb->prefix}forms2db_data WHERE {$where_arg};";
			$results = $wpdb->get_results($wpdb->prepare($sql, $where_data));
			$form_data = json_decode($results[0]->form_data, ARRAY_A);

			return $form_data;
		}
		
	}

	/**
	 * Add notice before form
	 * 
	 * @since    1.0.0
	 * @access   public
	 */

	public function show_notices() {

		if(isset( $_POST['forms2db-form-user-action'] )) {
			global $forms2db_errors;
			if(isset($forms2db_errors) && count($forms2db_errors->errors) > 0) { ?>
				<div class="forms2db-error"><?php _e('Please check the form for errors', 'forms2db') ?></div>
			<?php } else { ?>
				<div class="forms2db-success"><?php _e('The form has been successfully submitted!', 'forms2db') ?></div>
			<?php }
		}
		
	}

	/**
	 * Add error messages before fields
	 * 
	 * @since    1.0.0
	 * @access   public
	 */

	public function add_field_error($name) {
		if(isset( $_POST['forms2db-form-user-action'] )) {
			global $forms2db_errors;
			$message = $forms2db_errors->get_error_messages($name);
			if( isset($message[0]) ) ?>
				<div class="forms2db-error"><?php echo $message[0]; ?></div>
		<?php }
	}
}
