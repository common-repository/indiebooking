<?php
class rs_indiebooking_wp_ajax_handler {
    
    /**
     * This is a copy of the default "wp_ajax_add_tag" function
     * But this function returns a json value instead of an xml.
     */
    public static function rs_indiebooking_wp_ajax_add_tag() {
        global $wp_list_table;
        
        check_ajax_referer( 'add-tag', '_wpnonce_add-tag' );
        $taxonomy               = !empty($_POST['taxonomy']) ? $_POST['taxonomy'] : 'post_tag';
        $tax                    = get_taxonomy($taxonomy);
        
        if ( !current_user_can( $tax->cap->edit_terms ) ) {
            wp_die( -1 );
        }
        
        $x                  = new WP_Ajax_Response();
    
        $tag                = wp_insert_term($_POST['tag-name'], $taxonomy, $_POST );
    
        if ( !$tag || is_wp_error($tag) || (!$tag = get_term( $tag['term_id'], $taxonomy )) ) {
            $message        = __('An error has occurred. Please reload the page and try again.');
            if ( is_wp_error($tag) && $tag->get_error_message() ) {
                $message    = $tag->get_error_message();
            }
            $x->add( array(
                'what' => 'taxonomy',
                'data' => new WP_Error('error', $message )
            ) );
            wp_send_json_error($x);
        }
    
        $wp_list_table      = _get_list_table( 'WP_Terms_List_Table', array( 'screen' => $_POST['screen'] ) );
    
        $level = 0;
        if ( is_taxonomy_hierarchical($taxonomy) ) {
            $level          = count( get_ancestors( $tag->term_id, $taxonomy, 'taxonomy' ) );
            ob_start();
            $wp_list_table->single_row( $tag, $level );
            $noparents      = ob_get_clean();
        }
    
        ob_start();
        $wp_list_table->single_row( $tag );
        $parents            = ob_get_clean();
    
        $x->add( array(
            'what' => 'taxonomy',
            'supplemental' => compact('parents', 'noparents')
        ) );
        $x->add( array(
            'what' => 'term',
            'position' => $level,
            'supplemental' => (array) $tag
        ) );
        
        wp_send_json_success(
            $x->responses
        );
    }
}