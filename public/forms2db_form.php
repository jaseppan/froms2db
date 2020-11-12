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

		$this->form_id = intval($form_id);

    }
    
    public function view() {
		if( isset($_POST['forms2db-form-user-action']) ) {
			require 'views/forms2db-thank-you.php';
		} else {
			require 'views/forms2db-form.php';
		}
    }
    
    public function add_fields() {
		
		global $post;

        $fields_obj = new Forms2db_Fields();
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

}
