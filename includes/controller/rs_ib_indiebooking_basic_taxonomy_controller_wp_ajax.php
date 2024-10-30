<?php
/*
 * Indiebooking - the Booking Software for your Homepage!
 * Copyright (C) 2016  ReWa Soft GmbH
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
 */
?>
<?php if ( ! defined ( 'ABSPATH' ) ) {
    exit;
}
// if ( ! class_exists( 'RS_IB_Indiebooking_Basic_Taxonomy_Controller_WP_AJAX' ) ) :
/**
 * @author schmitt
 *
 */
class RS_IB_Indiebooking_Basic_Taxonomy_Controller_WP_AJAX
{
    
    private function save_new_term($myTaxonomy, $update = false) {
        $description    = rsbp_getPostValue('description', "", RS_IB_Data_Validation::DATATYPE_TEXT);
        $tagName        = rsbp_getPostValue('tag-name', "", RS_IB_Data_Validation::DATATYPE_TEXT);
        if ($update == false) {
            $termArgs = array(
                'description'   => $description,
            );
            $answer     = wp_insert_term($tagName, $myTaxonomy, $termArgs);
        } else {
            $termId     = rsbp_getPostValue("tag_ID");
            $termArgs   = array(
                'description'   => $description,
                'name'          => $tagName,
            );
            $answer     = wp_update_term($termId, $myTaxonomy, $termArgs);
        }
        $answer['description']  = $description;
        $answer['tagName']      = $tagName;
        return $answer;
    }
    
    public function __construct() {
        add_action( 'wp_ajax_rs_ib_save_new_taxonomy', array($this, 'save_new_taxonomy') );
        add_action( 'wp_ajax_rs_ib_save_new_taxonomy_admin', array($this, 'save_new_taxonomy_wp_add_tag') );
        add_action( 'wp_ajax_rs_ib_edit_taxonomy', array($this, 'edit_taxonomy') );
        
        add_action( 'wp_ajax_rs_ib_load_taxonomy', array($this, 'load_taxonomy') );
    }
    
    public function edit_taxonomy() {
        $taxonomy   = rsbp_getPostValue('taxonomy', "");
        switch ($taxonomy) {
            case RS_IB_Model_Appartmentkategorie::RS_TAXONOMY:
                $this->edit_category();
                break;
        }
    }

    public function save_new_taxonomy_wp_add_tag() {
        $taxonomy   = rsbp_getPostValue('taxonomy', "");
        switch ($taxonomy) {
            case RS_IB_Model_Appartmentkategorie::RS_TAXONOMY:
                $result = rs_indiebooking_wp_ajax_handler::rs_indiebooking_wp_ajax_add_tag();
                break;
        }
    }
    
    public function save_new_taxonomy() {
        $taxonomy   = rsbp_getPostValue('taxonomy', "");
        switch ($taxonomy) {
            case RS_IB_Model_Appartmentkategorie::RS_TAXONOMY:
                $this->save_new_category();
                break;
        }
    }
    
    public function save_new_category($update = false) {
        $answer = $this->save_new_term(RS_IB_Model_Appartmentkategorie::RS_TAXONOMY, $update);
        if (is_wp_error($answer)) {
            $error_string       = $answer->get_error_message();
            wp_send_json_error($error_string);
        } else {
            $catArray = array(
                "termId"        => $answer["term_id"],
                "name"          => rsbp_getPostValue('tag-name', '', RS_IB_Data_Validation::DATATYPE_TEXT),
            );
            wp_send_json_success($catArray);
        }
    }
    
    public function load_taxonomy() {
        global $RSBP_DATABASE;

        $answer                     = array();
        $termId                     = rsbp_getPostValue('termId', 0);
        $taxonomy                   = rsbp_getPostValue('taxonomy', "");
        if ($termId !== 0 && $taxonomy !== "") {
//             $answer = get_term($termId, $taxonomy, ARRAY_A);
            switch ($taxonomy) {
                case RS_IB_Model_Appartmentkategorie::RS_TAXONOMY:
                    $answer         = get_term($termId, $taxonomy, ARRAY_A);
                    $returnTax      = null;
                    break;
            }
            if (!is_null($returnTax)) {
                $answer             = $returnTax->object_to_array();
            }
        }
        wp_send_json_success($answer);
    }
    
    public function edit_category() {
        $answer         = $this->save_new_term(RS_IB_Model_Appartmentkategorie::RS_TAXONOMY, true);
        if (is_wp_error($answer)) {
            $error_string       = $answer->get_error_message();
            wp_send_json_error($error_string);
        } else {
            $catArray = array(
                "termId"        => $answer["term_id"],
                "name"          => $answer['tagName'],
                "description"   => $answer['description']
//                 "name"          => rsbp_getPostValue('tag-name', '', RS_IB_Data_Validation::DATATYPE_TEXT),
            );
            wp_send_json_success($catArray);
        }
    }
}
// endif;
new RS_IB_Indiebooking_Basic_Taxonomy_Controller_WP_AJAX();