<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://cidedot.com
 * @since      1.0.0
 *
 * @package    Forms2db
 * @subpackage Forms2db/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Forms2db
 * @subpackage Forms2db/admin
 * @author     Janne Seppänen <j.v.seppanen@gmail.com>
 */
class Forms2db_Cpt {


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct() {
        add_action( 'init', array($this, 'add_post_type') );
        add_action( 'add_meta_boxes', array($this, 'metaboxes') );
    }


    /*
    * Creating a function to create our CPT
    */
    
    public function add_post_type() {
    
        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => _x( 'Forms', 'Post Type General Name', 'forms2db' ),
            'singular_name'       => _x( 'Form', 'Post Type Singular Name', 'forms2db' ),
            'menu_name'           => __( 'Forms', 'forms2db' ),
            'parent_item_colon'   => __( 'Parent Form', 'forms2db' ),
            'all_items'           => __( 'All Forms', 'forms2db' ),
            'view_item'           => __( 'View Form', 'forms2db' ),
            'add_new_item'        => __( 'Add New Form', 'forms2db' ),
            'add_new'             => __( 'Add New', 'forms2db' ),
            'edit_item'           => __( 'Edit Form', 'forms2db' ),
            'update_item'         => __( 'Update Form', 'forms2db' ),
            'search_items'        => __( 'Search Form', 'forms2db' ),
            'not_found'           => __( 'Not Found', 'forms2db' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'forms2db' ),
        );
        
        // Set other options for Custom Post Type
        
        $args = array(
            'label'               => __( 'forms', 'forms2db' ),
            'description'         => __( 'Form news and reviews', 'forms2db' ),
            'labels'              => $labels,
            // Features this CPT supports in Post Editor
            'supports'            => array( 'title' ),
            // You can associate this CPT with a taxonomy or custom taxonomy. 
            'taxonomies'          => array( 'genres' ),
            /* A hierarchical CPT is like Pages and can have
            * Parent and child items. A non-hierarchical CPT
            * is like Posts.
            */ 
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
            'show_in_rest' => true,
    
        );
        
        // Registering your Custom Post Type
        register_post_type( 'forms2db-forms', $args );
    
    }

    public function metaboxes() {
        add_meta_box(
            'form-fields',      // Unique ID
            esc_html__( 'Fields', 'forms2db' ),    // Title
            array($this, 'form_fields_meta_box'),   // Callback function
            'forms2db-forms',         // Admin page (or post type)
            'normal',         // Context
            'high'         // Priority
        );
    }

    function form_fields_meta_box() {

        $fields = array(
            array(
                'type'                  => 'select',
                'name'                  => 'eka',
                'label'                 => 'Eka',
                'value'                 => 'textarea',
                'options'               => array(
                    array(
                        'value' => 'input',
                        'text'  => __('Input'),
                    ),
                    array(
                        'value' => 'checkbox',
                        'text'  => __('Checkbox'),
                    ),
                    array(
                        'value' => 'radio',
                        'text'  => __('Radio'),
                    ),
                    array(
                        'value' => 'select',
                        'text'  => __('Select'),
                    ),
                    array(
                        'value' => 'textarea',
                        'text'  => __('Textarea'),
                    ),
                    array(
                        'value' => 'file',
                        'text'  => __('File'),
                    ),
                ),
            ),
            array(
                'type'                  => 'text',
                'name'                  => 'field-type',
                'label'                 => 'Nimi',
                'value'                 => 'Janne',  
            ),
        );

        require('partials/form-fields-metabox.php');
    }

    function field_row($fields) { 

        if( !isset($fields) ) {
            $fields = [0 => ''];
            $active = 'active';
        }
        
        foreach( $fields as $field ) { 
            ?>
            <div class="forms2db-field-container sortable <?php echo (isset($active)) ? $active : ''; ?>">
                <div class="forms2db-field-header">
                    <input type="text" name="label" value="<?php echo $field['label'] ?>" placeholder="<?php echo _e('label'); ?>">
                    <div class="forms2db-field-actions">
                        <span class="forms2db-field-delete"><span class="circle">x</span></span>
                        <span class="forms2db-field-toggle">></span>
                    </div>
                </div>
                <div class="forms2db-fields-row">
                    <div class="forms2db-fields-col col-2">
                        <div class="inner-col">
                            <label for="type" class="block"><?php _e('Type') ?></label>
                            <select name="type[]" id="type">
                                <option></option>
                                <optgroup label="Input">
                                    <option value="text" <?php echo ($field['type'] == 'text') ? 'selected' : '' ?>>text</option>
                                    <option value="number" <?php echo ($field['type'] == 'number') ? 'selected' : '' ?>>number</option>
                                    <option value="email" <?php echo ($field['type'] == 'email') ? 'selected' : '' ?>>email</option>
                                    <option value="hidden" <?php echo ($field['type'] == 'hidden') ? 'selected' : '' ?>>hidden</option>
                                <optgroup label="Choices">
                                    <option value="select" <?php echo ($field['type'] == 'select') ? 'selected' : '' ?>>select</option>
                                    <option value="checkbox" <?php echo ($field['type'] == 'checkbox') ? 'selected' : '' ?>>checkbox</option>
                                    <option value="radio" <?php echo ($field['type'] == 'radio') ? 'selected' : '' ?>>radio</option>
                                <optgroup label="Other">   
                                    <option value="textarea" <?php echo ($field['type'] == 'textarea') ? 'selected' : '' ?>>textarea</option>
                                    <option value="file" <?php echo ($field['type'] == 'file') ? 'selected' : '' ?>>file</option>
                            </select>
                        </div>
                        
                    </div>
                    <div class="forms2db-fields-col col-10">
                        <div class="forms2db-fields-row">
                            <div class="forms2db-fields-col col-4">
                                <div class="inner-col">
                                    <label for="name" class="block"><?php _e('Name') ?></label>
                                    <input type="text" name="name[]" value="<?php echo (isset($field['name'])) ? $field['name'] : '' ?>">
                                </div>
                            </div>
                            <div class="forms2db-fields-col col-4">
                                <div class="inner-col">
                                    <label for="type" class="block"><?php _e('Value') ?></label>
                                    <input type="text" name="value[]" value="<?php echo (isset($field['value'])) ? $field['value'] : '' ?>">
                                </div>
                            </div>
                            <div class="forms2db-fields-col col-4">
                                <div class="inner-col">
                                    <label for="type" class="block"><?php _e('Class') ?></label>
                                    <input type="text" name="class[]" value="<?php echo (isset($field['class'])) ? $field['class'] : '' ?>">
                                </div>
                            </div>
                            <div class="forms2db-fields-col col-4">
                                <div class="inner-col">
                                    <label for="type" class="block"><?php _e('Attributes') ?></label>
                                    <input type="text" name="attributes[]" value="<?php echo (isset($field['attributes'])) ? $field['attributes'] : '' ?>">
                                </div>
                            </div>
                            <div class="forms2db-fields-col col-4">
                                <div class="inner-col">
                                </div>
                            </div>
                            <div class="forms2db-fields-col col-4">
                                <div class="inner-col">
                                </div>
                            </div>
                            <div class="forms2db-fields-col col-12">
                                <div class="inner-col">
                                    <div id="options">
                                        <label for="type" class="block"><?php _e('Options') ?></label>
                                        <textarea name="options[]" id="" cols="30" rows="4"><?php echo (isset($field['options'])) ? $this->option_array_to_text($field['options']) : '' ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php }
    }

    function option_array_to_text($options) {
        
        $option_text = '';
        foreach( $options as $option ) {
            $option_text .= implode( ' : ', $option ) . "\r";
        }

        return $option_text;



    }


    function select_type($value = '') {

        $args = array(
            'type'                  => 'file',
            'value'                 => 'textarea',
            'label'                 => __('Field type'),
            'name'                  => 'field-type',
            'options'               => array(
                array(
                    'value' => 'input',
                    'text'  => __('Input'),
                ),
                array(
                    'value' => 'checkbox',
                    'text'  => __('Checkbox'),
                ),
                array(
                    'value' => 'radio',
                    'text'  => __('Radio'),
                ),
                array(
                    'value' => 'select',
                    'text'  => __('Select'),
                ),
                array(
                    'value' => 'textarea',
                    'text'  => __('Textarea'),
                ),
                array(
                    'value' => 'file',
                    'text'  => __('File'),
                ),
            ),
        );

        $fields_obj = new Forms2db_Fields();
        return $fields_obj->add_field($args);

    }


        



}


?>