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
                    $args['name'], 
                    $args['id'], 
                    $args['class'], 
                    $args['attributes']
                );
                $field .= implode(' ', array_map(array($this, "add_select_option"), 
                        $args['options'] , 
                        array_fill(0, count($args['options']), $args['value'])
                    )
                );
                $field .= '</select>';
                break;
            case in_array( $args['field-type'], $inputs ) :
                $field .= sprintf('<input type="%s" name="%s" id="%s" class="%s" value="%s">', 
                    $args['field-type'], 
                    $args['name'], 
                    $args['id'], 
                    $args['class'], 
                    $args['value']
                );
                break;    
            case in_array( $args['field-type'], $checkboxes ) :
                if(!isset($args['options']))
                    return __("Options required for this kind of field");
                $options = $args['options'];
                $options_count = count($args['options']);
                $value = array_fill(0, $options_count, $args['value']);
                $name = array_fill(0, $options_count, $args['name']);
                $type = array_fill(0, $options_count, $args['field-type']);
                $id = array_fill( 0, $options_count, $args['id'] );
                $id_numbers = array_fill(0, $options_count, $args['id']);
                $class = array( 0, $options_count, $args['class']);
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
                    $args['name'], 
                    $args['id'], 
                    $args['class'], 
                    $args['value']
                );
                break;
            case "file" :
                $size = isset($args['size']) ? $args['size'] : '20';
                $accept = isset($args['accept']) ? $args['accept'] : 'image/*';
                $field .= sprintf('<input type="%s" name="%s" id="%s" class="%s" size="%s" accept="%s">', 
                    $args['field-type'], 
                    $args['name'], 
                    $args['id'], 
                    $args['class'], 
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
            $option['value'], 
            $selected, 
            $option['text']
        );

    }

    function add_checkbox( $name, $option, $value, $type, $id, $id_number, $class ) {

        $checked = ($option['value'] == $value) ? 'checked' : '';
        $item_id = $id . '-' . $id_number;

        if($type == 'checkbox')
            $name .= '[]';
        
        return sprintf('<input type="%s" name="%s" id="%s" class="%s" value="%s" %s><label for="%s" class="froms2db-checkbox-label">%s</label>', 
            $type, 
            $name, 
            $item_id, 
            $class, 
            $option['value'], 
            $checked, 
            $item_id, 
            $option['text']
        );
    }
}
?>