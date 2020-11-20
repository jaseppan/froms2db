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
        $this->current_form = $this->current_form();
        
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

    public function current_form() {
        global $wpdb;

        if( isset($this->form_id) ) {
            $form_id = $this->form_id;
        } elseif( isset($_GET['form-id']) && is_numeric($_GET['form-id'])  ) {
            $form_id = $_GET['form-id'];
        } else {
            return null;
        }

        $sql = "SELECT form_data FROM {$wpdb->prefix}forms2db_data";
        $results = $wpdb->get_results($wpdb->prepare($sql), ARRAY_A);
        $form_data = array_map(function( $row ) {
            return json_decode($row['form_data'], ARRAY_A);
        }, $results );

        $form['structure'] = get_post_meta($form_id, '_forms2db_form_structure', false);
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
        require('views/form-data-manager.php');    
    }

    public function form_selector() { 
        
        if(count($this->available_forms) == 0) {
            _e("No forms created", "forms2db");
            return;
        } elseif ( count($this->available_forms) == 1 ) {
            $this->form_id =  $this->available_forms[0]->ID;
            return;
        }

        ?>
        <form action="<?php echo admin_url('edit.php'); ?>" method="get">
            <input type="hidden" name="post_type" value="forms2db-forms"/><br />
            <input type="hidden" name="page" value="forms2db-save-data"/>
            <label for="forms2db-selector"><?php _e('Select form', 'forms2db') ?></label>
            <select name="form-id" id="forms2db-selector">
                <?php foreach($this->available_forms as $form) { ?>
                <option value="<?php echo $form->ID ?>"><?php echo $form->post_title ?></option>     
                <?php } ?>
            </select>
            <input type="submit" name="submit-forms2db-selector" value="<?php _e('Submit', 'forms2db') ?>" id="submit-forms2db-selector">
        </form>
    <?php }

    public function search_field() {

    }

    public function data_content() {
        echo "<pre>";
        var_dump ($this->current_form);
        echo "</pre>";

    }

    public function csv_export() {

    }

    public function get_form_data($cols) {
        global $wpdb;

        $sql = "SELECT {$cols} FROM {$wpdb->prefix}";
        
    }





}