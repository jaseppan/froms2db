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
        add_action( 'save_post_forms2db-forms', array($this, 'save_fields') );
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
                'field-type'                  => 'select',
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
                'field-type'                  => 'text',
                'name'                  => 'field-type',
                'label'                 => 'Nimi',
                'value'                 => 'Janne',  
            ),
        );

        ob_start();        
        require('views/form-fields-metabox.php');
        $fields = ob_get_contents();
        ob_clean();
        echo $fields;
    }

    function field_row($fields) { 

        if( !isset($fields) ) {
            $fields = [0 => ''];
            $active = 'active';
        }

        // Types for input fields
        $inputs = ['text', 'number', 'email', 'hidden'];
        
        // Checkboxes and radio buttons requires options and they are handled like select field
        $checkboxes = ['checkbox', 'radio', 'select'];  
        
        foreach( $fields as $key => $field ) { 
            require('views/partials/form-fields.php');            
        }
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
            'field-type'                  => 'file',
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

    public function save_fields($post_id) {
        
        //if(!isset( $_POST['name'] ) || $_POST['name'] )
            //return;

        $allowed_types = [
            'text',
            'number',
            'email',
            'hidden',
            'select',
            'checkbox',
            'radio',
            'textarea',
            'file'

        ];

        $checkboxes = ['checkbox', 'radio', 'select'];  // These types require options
        
        $errors =  [];

        foreach ($_POST['name'] as $key => $name ) {

            if( isset($name) && isset($_POST['field-type'][$key] ) && in_array($_POST['field-type'][$key], $allowed_types) ) {

                if( in_array($_POST['field-type'][$key],  $checkboxes) ) {
                    if(!isset($_POST['options'][$key]) ) {
                        $errors[$key]['text'] = __('Options are required');
                        $errors[$key]['value'] = 'missing-options';
                    } else {
                        $options = forms2db_parse_options($_POST['options'][$key]);
                    }
                }
                $name = sanitize_text_field( $name );
                $type = $_POST['field-type'][$key]; // Only values listed in $allowed_types are possible
                $label = isset($_POST['label'][$key] ) ? sanitize_text_field( $_POST['label'][$key] ) : '';
                $value = isset($_POST['value'][$key] ) ? sanitize_text_field( $_POST['value'][$key] ) : '';
                $attributes = isset($_POST['attributes'][$key]) ? sanitize_text_field( $_POST['attributes'][$key] ) : '';
    
                $fields[$key] = array(
                    'field-type'                  => $type,
                    'name'                  => $name,
                    'label'                 => $label,
                    'value'                 => $value,
                    'attributes'            => $attributes, 
                );

                if( in_array($type, $checkboxes) ) {
                    $fields[$key]['options'] = $options;
                }

            } else {
                if( !isset($name) ) {
                    $errors[$key]['text'] = __('Name is required');
                    $errors[$key]['value'] = 'missing-name';
                }
                if( !isset($_POST['field-type'][$key]) ) {
                    $errors[$key]['text'] = __('Type is required');
                    $errors[$key]['value'] = 'missing-type';
                } elseif( in_array($_POST['field-type'][$key], $allowed_types) ) {
                    $errors[$key]['text'] = __('Invalid type');
                    $errors[$key]['value'] = 'invalid-type';
                }           

            }

        }

        if( empty($errors) ) {
            update_post_meta( $post_id, '_forms2db_form', $fields );
        } else {
            // Handle errors
        }




    }

}

?>