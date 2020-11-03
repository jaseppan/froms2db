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

        $sql = "SELECT * FROM " . $wpdb->prefix . "forms2db_data"  . $conditions;
        $result = $wpdb->get_results($wpdb->prepare($sql), $args);

        return $result;
        
    }
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