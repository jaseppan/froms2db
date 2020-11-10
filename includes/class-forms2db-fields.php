<?php

/**
 * The functionality to create form fields.
 * 
 * @package    Forms2db
 * @subpackage Forms2db/includes
 * @author     Janne Seppänen <j.v.seppanen@gmail.com>
 */
class Forms2db_Fields {

    /**
     * Create field
     * 
     * Available arguments:
     * type
     * attribites
     * class
     * id
     * label
     * name
     * options
     * value
     */

    public function add_field($args) {

        if( !is_array($args) )
            return;

        // Set defauts
        $defaults = array(
            'field-type'            => 'input',
            'value'                 => '',
            'id'                    => '',
            'class'                 => '',
            'label'                 => '',
            'name'                  => '',
            'attributes'            => '',
        );

        $ərgs = array_merge($defaults, $args);
        
        // Types for input fields
        $inputs = ['text', 'number', 'email', 'hidden'];
        
        // Checkboxes and radio buttons requires options and they are handled like select field
        $checkboxes = ['checkbox', 'radio', 'select'];    
        
        // Add label
        $field = isset( $args['label'] ) ? sprintf( '<label for="%s" class="froms2db-main-label">%s</label>', $args['id'], $args['label'] ) : '';
        switch ($args['field-type']) {
            case "select" :
                if(!isset($args['options']))
                    return __("Options required for this kind of field");
                $field .= sprintf('<select name="%s" id="%s" class="%s" %s>', 
                    esc_attr($args['name']), 
                    esc_attr($args['id']), 
                    esc_attr($args['class']), 
                    esc_attr($args['attributes'])
                );
                $field .= implode(' ', array_map(array($this, "add_select_option"), 
                        $args['options'] , 
                        array_fill(0, count($args['options']), esc_attr($args['value']))
                    )
                );
                $field .= '</select>';
                break;
            case in_array( $args['field-type'], $inputs ) :
                $field .= sprintf('<input type="%s" name="%s" id="%s" class="%s" value="%s">', 
                    esc_attr($args['field-type']), 
                    esc_attr($args['name']), 
                    esc_attr($args['id']), 
                    esc_attr($args['class']), 
                    esc_attr($args['value'])
                );
                break;    
            case in_array( $args['field-type'], $checkboxes ) :
                if(!isset($args['options']))
                    return __("Options required for this kind of field");
                $options = esc_html($args['options']);
                $options_count = count($args['options']);
                $value = array_fill(0, $options_count, esc_attr( $args['value']) );
                $name = array_fill(0, $options_count, esc_attr( $args['name']) );
                $type = array_fill(0, $options_count, esc_attr( $args['field-type']) );
                $id = array_fill( 0, $options_count, esc_attr( $args['id'] ) );
                $id_numbers = array_fill(0, $options_count, esc_attr( $args['id']) );
                $class = array( 0, $options_count, esc_attr( $args['class']) );
                $field = implode(' ', array_map(array($this, "add_checkbox"), 
                    $name, 
                    $options, 
                    $value, 
                    $type, 
                    $id, 
                    $id_numbers, 
                    $class
                    )
                );
                break;
            case "textarea" :
                $field .= sprintf('<textarea name="%s" id="%s" class="%s">%s</textarea>', 
                    esc_attr($args['name']), 
                    esc_attr($args['id']), 
                    esc_attr($args['class']), 
                    esc_attr($args['value'])
                );
                break;
            case "file" :
                $size = isset($args['size']) ? esc_attr($args['size']) : '20';
                $accept = isset($args['accept']) ? esc_attr($args['accept']) : 'image/*';
                $field .= sprintf('<input type="%s" name="%s" id="%s" class="%s" size="%s" accept="%s">', 
                    esc_attr($args['field-type']), 
                    esc_attr($args['name']), 
                    esc_attr($args['id']), 
                    esc_attr($args['class']), 
                    $size,
                    $accept
                );
                break;
        }
        
        return $field;

    }

    function add_select_option($option, $value) {

        $selected = ($option['value'] == $value) ? 'selected' : ''; 

        return sprintf('<option value="%s" %s>%s</option>', 
            esc_attr($option['value']), 
            esc_attr($selected), 
            esc_attr($option['text'])
        );

    }

    function add_checkbox( $name, $option, $value, $type, $id, $id_number, $class ) {

        $checked = ($option['value'] == $value) ? 'checked' : '';
        $item_id = $id . '-' . $id_number;

        if($type == 'checkbox')
            $name .= '[]';
        
        return sprintf('<input type="%s" name="%s" id="%s" class="%s" value="%s" %s><label for="%s" class="froms2db-checkbox-label">%s</label>', 
            esc_attr($type), 
            esc_attr($name), 
            esc_attr($item_id), 
            esc_attr($class), 
            esc_attr($option['value']), 
            esc_attr($checked), 
            esc_attr($item_id), 
            esc_attr($option['text'])
        );
    }
}
?>