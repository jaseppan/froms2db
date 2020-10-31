<?php 

function get_form_data( $args = array() ) {
    if( current_user_can( 'edit_posts' ) ) {

        global $wpdb;

        $conditions = '';
        $conditions .= ( isset($args['form_id']) || isset($args['post_id']) || isset($args['user_id']) || isset($args['content']) ) ? " WHERE " : "";
        $conditions .= ( isset( $args['form_id'] ) ) ? $args['form_id'] . " = i%" : '';
        $conditions .= ( isset( $args['post_id'] ) ) ? $args['post_id'] . " = i%" : '';
        $conditions .= ( isset( $args['user_id'] ) ) ? $args['user_id'] . " = i%" : '';
        $conditions .= ( isset( $args['content'] ) ) ? $args['content'] . " LIKE s%" : '';
        $conditions .= ( isset( $args['order_by'] ) ) ? $args['order_by'] . " ORDER BY s%" : '';

        $sql = "SELECT * FROM " . $wpdb->prefix . "forms2fb_data"  . $conditions;
        $result = $wpdb->get_results($wpdb->prepare($sql), $args);

        return $result;
        
    }
}
