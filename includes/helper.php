<?php 

function forms2db_get_form_data( $args = array() ) {
    if( current_user_can( 'edit_posts' ) ) {

        global $wpdb;

        $conditions = '';
        $conditions .= ( isset($args['form_id']) || isset($args['post_id']) || isset($args['user_id']) || isset($args['content']) ) ? " WHERE " : "";
        $conditions .= ( isset( $args['form_id'] ) ) ? $args['form_id'] . " = i%" : '';
        $conditions .= ( isset( $args['post_id'] ) ) ? $args['post_id'] . " = i%" : '';
        $conditions .= ( isset( $args['user_id'] ) ) ? $args['user_id'] . " = i%" : '';
        $conditions .= ( isset( $args['content'] ) ) ? $args['content'] . " LIKE s%" : '';
        $conditions .= ( isset( $args['order_by'] ) ) ? $args['order_by'] . " ORDER BY s%" : '';

        $sql = "SELECT * FROM " . $wpdb->prefix . "forms2db_data"  . $conditions;
        $result = $wpdb->get_results($wpdb->prepare($sql), $args);

        return $result;
        
    }
}

function forms2db_parse_options($options_str) {
    
    $option_rows = explode(PHP_EOL, $options_str);
    foreach($option_rows as $key => $row) {
        $tmp = explode( ' : ', $row);
        if( !empty($tmp[0]) && !empty($tmp[1]) ) {
            $options[$key]['value'] = $tmp[0];
            $options[$key]['text'] = $tmp[1];
        }
    }

    return $options;
    
}

function forms2db_form($args) {

    $form_id = $args['id'];
    $form = new Forms2dbForm($form_id);
    ob_start();
    $form->view();
    $content = ob_get_contents();
    ob_clean();
    return $content;

}

add_shortcode( 'forms2db-form', 'forms2db_form' );

function forms2db_validate($value, $type, $attributes, $label, $name, $options ) {

    global $forms2db_errors;

    $label = isset($label) ? $label : __('Field');

    if(is_numeric(strpos($attributes, "required"))) {
        $is_required = true;
    } else {
        $is_required = false;
    }

    if( $is_required && empty($value) ) {
        $message = sprintf(__('%s is required'), $label);
        $forms2db_errors->add( $name, $message );
        // $forms2db_errors->add( 'form2db-field-errors', $message, $name );
    } else {
        
        // Stop validating if empty and not required
        if(empty($value))
            return;

        $texts = array(
            'input',
            'textarea'
        );
        $choices = array(
            'checkbox',
            'radio',
            'select'
        );

        // Text
        switch( $type ) {
            case in_array($type, $texts):
                $value = sanitize_text_field( $value );
                
                break;
            case in_array($type, $choices):
                $option_values = array_map( function( $option ) {
                    return $option['value'];
                }, $options );

                if(in_array($value, $option_values)) {
                    $value = $value;
                } else {
                    $value = '';
                    $forms2db_errors->add( $name, __('Invalid value ').$value);
                }
                break;
            case 'number':
                
                if(is_numeric($value)) {
                    $value = $value;
                } else {
                    $value = '';
                    $forms2db_errors->add( $name, __('Invalid value') . '. ' . __('Please insert numeric value') , $name);
                }
                break;
            case 'date':
                if(strtotime($value)) {
                    $value = $value;
                } else {
                    $value = '';
                    $forms2db_errors->add( $name, __('Invalid date'));
                }
                break;
            case 'email':
                if(is_email($value)) {
                    $value = $value;
                } else {
                    $value = '';
                    $forms2db_errors->add( $name, __('Invalid email'));
                }
                break;
        }

    }    

    return $value;
}