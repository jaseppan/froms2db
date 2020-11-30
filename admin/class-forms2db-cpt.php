<?php

/**
 * Define Form CPT, form content and metaboxes
 *
 * @package    Forms2db
 * @subpackage Forms2db/admin
 * @author     Janne Seppänen <j.v.seppanen@gmail.com>
 */
class Forms2db_Cpt {

    private $form_id;
    private $fields;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct() {

        $this->form_id = isset($_GET['post']) ? intval($_GET['post']) : null;
        $this->fields = get_post_meta($this->form_id, '_forms2db_form', true);
        
        add_action( 'init', array($this, 'add_post_type') );
        add_action( 'save_post_forms2db-forms', array($this, 'save_forms_data') );
        add_action( 'add_meta_boxes', array($this, 'metaboxes') );
        if($this->fields)
            add_action( 'edit_form_after_title', array($this, 'form_shortcode') );
        add_action( 'edit_form_after_title', array($this, 'form_content') );
    }


    /**
     * Create Fields CPT
     */
    
    public function add_post_type() {
    
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
        
        $args = array(
            'label'               => __( 'forms', 'forms2db' ),
            'description'         => __( 'Form news and reviews', 'forms2db' ),
            'labels'              => $labels,
            'supports'            => array( 'title' ),
            'hierarchical'        => false,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
            'rewrite'             => false,
            'show_in_rest'        => false,
    
        );
        
        register_post_type( 'forms2db-forms', $args );
    
    }

    /**
     * Add Metaboxes
     * @since      1.0.0
     */

    public function metaboxes() {
       
        if($this->fields) {

            add_meta_box(
                'admin-message',      // Unique ID
                esc_html__( 'Admin message', 'forms2db' ),    // Title
                array($this, 'admin_message'),   // Callback function
                'forms2db-forms',         // Admin page (or post type)
                'normal',         // Context
                'high'         // Priority
            );
    
            add_meta_box(
                'user-message',      // Unique ID
                esc_html__( 'User message', 'forms2db' ),    // Title
                array($this, 'user_message'),   // Callback function
                'forms2db-forms',         // Admin page (or post type)
                'normal',         // Context
                'high'         // Priority
            );        
        
        }

        add_meta_box(
            'form-settings',      // Unique ID
            esc_html__( 'Settings', 'forms2db' ),    // Title
            array($this, 'settings'),   // Callback function
            'forms2db-forms',         // Admin page (or post type)
            'side',         // Context
        );

    }

    /**
     * Show the form shortcode 
     * @since      1.0.0
     */

    public function form_shortcode() {
        echo sprintf('<div id="form2db-form-shortcode-conteiner">
            <strong>Shortcode: </strong><span id="form2db-form-shortcode" class="copyable">[forms2db-form id=%d]</span> <i class="fas fa-copy copier"></i>
            </div>', 
            $this->form_id,
            __('Click to copy shortcode to clipboard')
        );
    }

    /**
     * Show the form content
     * 
     * @since      1.0.0
     */

    public function form_content() {    
        $fields = $this->fields;
        
        // Default values
        if(!isset($fields) || !is_array($fields)) {
            $fields = array(
                array (
                    "field-type"  => "text",
                    "label"       => "",
                    "name"        => "",
                    "value"       => "",
                    "attributes"  => "",
                ),
                "submit-text" => "Submit",
            );       
        }
        
        $submit_text = isset($fields['submit-text']) ? $fields['submit-text'] : __('Submit');
        
        ob_start();        
        require('views/form-content.php');
        $fields = ob_get_contents();
        ob_clean();
        echo $fields;
    }

    /**
     * Add fields into form content. 
     * Called from view admin/form-settings.php
     * 
     * @since      1.0.0
     * @param      array    $fields       The arguments for fields.
     */

    public function field_rows($fields) { 

        if( !isset($fields) ) {
            $fields = [0 => ''];
            $active = 'active';
        }

        // Types for input fields
        $inputs = ['text', 'number', 'email', 'hidden'];
        
        // Checkboxes and radio buttons requires options and they are handled like select field
        $checkboxes = ['checkbox', 'radio', 'select'];  
        
        foreach( $fields as $key => $field ) { 
            if(is_array($field))
                require('views/partials/form-fields.php');            
        }
    }

    /**
     * Handle options for select, checkbox and radio fields 
     * 
     * @since      1.0.0
     * @param      array    $options       The options of the field/fields.
     */

    public function option_array_to_text($options) {
        
        $option_text = '';
        foreach( $options as $option ) {
            $option_text .= implode( ' : ', $option );
        }

        return $option_text;

    }

    /**
     * Save the form data: Fields and metaboxes
     * 
     * @since      1.0.0
     * @param      array    $post_id       Id of the form.
     */

    public function save_forms_data($post_id) {

        $required_capability = (get_option( 'forms2db_edit_cap' )) ? get_option( 'forms2db_edit_cap' ) : 'edit_posts';

        if(!current_user_can($required_capability))
            return;
        
        if(isset($_POST["forms2db-admin-action"]) && $_POST["forms2db-admin-action"] == 'editform') {

            /**
             * save fields
             */

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
                    $field_classes = isset($_POST['field-classes'][$key]) ? sanitize_text_field( $_POST['field-classes'][$key] ) : '';
                    $container_classes = isset($_POST['container-classes'][$key]) ? sanitize_text_field( $_POST['container-classes'][$key] ) : '';
                    
                    $fields[$key] = array(
                        'field-type'            => $type,
                        'name'                  => $name,
                        'label'                 => $label,
                        'value'                 => $value,
                        'attributes'            => $attributes, 
                        'field-classes'         => $field_classes, 
                        'container-classes'     => $container_classes, 
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

                $form_structure = array_map( function($field) {
                    return array(
                        'name' => $field['name'],
                        'label' => $field['label'],
                    );
                },  $fields );
                $fields['submit-text'] = sanitize_text_field( $_POST['submit-text'] );
                $fields['nonce'] = wp_generate_password();
                update_post_meta( $post_id, '_forms2db_form', $fields );
                update_post_meta( $post_id, '_forms2db_form_structure', $form_structure );
    
            } else {
                // Handle errors
            }

            /**
             * Save settings
             */

            if(isset( $_POST['modifyable'] ))
                $settings['modifyable'] = (boolean) $_POST['modifyable'];
            
            if(isset( $_POST['confirmation-required'] ))
                $settings['confirmation-required'] = (boolean) $_POST['confirmation-required'];
            
            update_post_meta( $post_id, '_forms2db_settings',  $settings );

            /**
             * Save admin message
             */

            if(isset( $_POST['_forms2db_admin_message'] )) {
                $forms2db_admin_message = sanitize_post( $_POST['_forms2db_admin_message']);
                update_post_meta( $post_id, '_forms2db_admin_message',  $forms2db_admin_message );
            }

            if(isset( $_POST['forms2db-admin-emails'] )) {
                if(is_email( $_POST['forms2db-admin-emails'] )) {
                    $forms2db_admin_emails = sanitize_text_field($_POST['forms2db-admin-emails']);
                    update_post_meta( $post_id, '_forms2db_admin_emails',  $forms2db_admin_emails );
                } else {
                    // handle error
                }
            }

            // ADD: _forms2db_admin_email_subject
            if(isset( $_POST["forms2db-admin-email-subject"] )) {
                $forms2db_admin_email_subject = sanitize_text_field($_POST["forms2db-admin-email-subject"]);
                update_post_meta( $post_id, '_forms2db_admin_email_subject',  $forms2db_admin_email_subject );
            }
            
            /**
             * Save user message
             */
            
            if(isset( $_POST["_forms2db_user_message"] )) {
                $forms2db_user_message = sanitize_post($_POST["_forms2db_user_message"]);
                update_post_meta( $post_id, '_forms2db_user_message',  $forms2db_user_message );
            }

            if(isset( $_POST["forms2db-user-email-subject"] )) {
                $forms2db_user_email_subject = sanitize_text_field($_POST["forms2db-user-email-subject"]);
                update_post_meta( $post_id, '_forms2db_user_email_subject',  $forms2db_user_email_subject );
            }
            
            if(isset( $_POST["forms2db-user-email-address-field"] )) {
                $forms2db_user_email_address_field = sanitize_text_field($_POST["forms2db-user-email-address-field"]);
                update_post_meta( $post_id, '_forms2db_user_email_address_field',  $forms2db_user_email_address_field );
            }

        }
    }

    /**
     * Metabox for admin message and admin message receiver
     * @since      1.0.0
     */

    public function admin_message() {
        global $post;

        $content = get_post_meta($post->ID, '_forms2db_admin_message' , true );
        $receivers = get_post_meta($post->ID, '_forms2db_admin_emails' , true ) ? get_post_meta($post->ID, '_forms2db_admin_emails' , true ) : get_option( 'admin_email' );
        $subject = get_post_meta( $post->ID, '_forms2db_admin_email_subject',  true );

        echo sprintf("<label>%s</label><br /><input type='text' name='forms2db-admin-emails' value='%s'><br />", __('Receiver emails', 'forms2db'), $receivers );
        echo sprintf("<label>%s</label><br /><input type='text' name='forms2db-admin-email-subject' value='%s'><br />", __('Message subject', 'forms2db'), $subject );
        wp_editor( htmlspecialchars_decode($content), '_forms2db_admin_message', array("media_buttons" => false) );?>
        <div class="row">
            <span class="forms2db-field-name-list"></span>
        </div>
        <div class="row">
            <?php echo sprintf("<button id='forms2db-generate-admin-message' class='button'>%s</button>", __('Generate admin message') ); ?>
        </div>
        <?php
    }

    /**
     * Metabox for user message
     * @since      1.0.0
     */

    public function user_message() {
        global $post;

        $content = get_post_meta($post->ID, '_forms2db_user_message' , true );
        $subject = get_post_meta( $post->ID, '_forms2db_user_email_subject',  true );
        $user_email_field = get_post_meta($post->ID, '_forms2db_user_email_address_field',  true );
        echo sprintf("<label>%s</label><br /><input type='text' name='forms2db-user-email-subject' value='%s'><br />", __('Message subject', 'forms2db'), $subject );
        echo sprintf("<label>%s</label><br /><input type='text' name='forms2db-user-email-address-field' value='%s'><br />", __('Get email from field', 'forms2db'), $user_email_field );
        wp_editor( htmlspecialchars_decode($content), '_forms2db_user_message', array("media_buttons" => false) );
    }

    /**
     * Metabox for settings
     * @since      1.0.0
     */

    public function settings() {

        global $post;

        if(get_post_meta($post->ID, '_forms2db_settings', true )) {
            $settings = get_post_meta($post->ID, '_forms2db_settings', true );
        } else {
            // default settings
            $settings = array(
                'modifyable' => false, 
                'confirmation-required' => false, 
            );
        }

        
        
        require('views/form-settings.php');

    }

    

}
