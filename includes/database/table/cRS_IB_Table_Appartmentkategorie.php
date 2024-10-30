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
} ?>
<?php
// if ( ! class_exists( 'RS_IB_Table_Appartmentkategorie' ) ) :
/**
 * @author schmitt
 *
 */
/*
 * Bsp SQL:
    SELECT * FROM `wp_term_taxonomy` as a
    INNER JOIN `wp_rewabp_termmeta`as b
    ON a.term_taxonomy_id = b.rewabp_term_id
    where a.`taxonomy` = 'rsappartmentaktion'
 */
class RS_IB_Table_Appartmentkategorie extends RS_IB_Table_Termmeta
{
    private static $_instance = null;
    
    /* Diese Klasse ist ein Singleton --> Objekterstellung ueber instance() */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    private function __construct() {
        
    }
    
    public function loadAllCategories() {
        return get_terms(RS_IB_Model_Appartmentkategorie::RS_TAXONOMY);
    }
    
    public function loadAllCategorieIds() {
    	return get_terms(array(
    		'fields' 		=> 'ids',
    		'taxonomy' 		=> RS_IB_Model_Appartmentkategorie::RS_TAXONOMY,
    		'orderby' 		=> 'term_order',
    		'hide_empty' 	=> true,
    	));
    }
    
    /**
     * Gibt ein Array mit allen Appartmentkategorien zu dem zugehuerigen Post / Appartment zurueck.
     * @param unknown $post_id
     * @return multitype:RS_IB_Model_Appartmentaktion
     */
    public function getPostAppartmentActions($post_id) {
        $terms                  = wp_get_object_terms($post_id, RS_IB_Model_Appartmentkategorie::RS_TAXONOMY); //gibt die Aktionen des Appartments zurueck
        $categories             = array();
        foreach ($terms as $term) {
            $categories[]       = $this->getAppartmentKategorie( $term );
        }
        return $categories;
    }
    
    
    public function getCategoryApartments($termIds = array()) {
    	global $wpdb;
    	
    	//         var_dump($termIds);
    	$selectedTaxonomies = array();
    	if (!is_null($termIds) && sizeof($termIds) > 0) {
    		if (sizeof($termIds) > 1) {
    			//                 var_dump($termIds);
    			$termIdstr  =  implode(', ',$termIds );
    		} else {
    			$termIdstr  = $termIds[0];
    		}
    		
    		$tbl_taxonomy   = $wpdb->prefix . 'term_taxonomy';
    		$tbl_term_rel   = $wpdb->prefix . 'term_relationships';
    		$tbl_posts      = $wpdb->prefix . 'posts';
    		$sql            = $wpdb->prepare(
    			"SELECT p.ID FROM $tbl_taxonomy t".
    			" INNER JOIN $tbl_term_rel r".
    			" ON t.term_id = r.term_taxonomy_id".
    			" INNER JOIN $tbl_posts p".
    			" ON r.object_id = p.ID AND p.post_type = %s".
    			" WHERE t.`taxonomy` = %s AND t.`term_id` IN (".$termIdstr.")" .
    			" GROUP BY p.ID",
    			array(
    				'rsappartment',
    				RS_IB_Model_Appartmentkategorie::RS_TAXONOMY
    			)
    			);
    		$results        = $wpdb->get_results( $sql , ARRAY_A );
    		foreach ($results as $result) {
    			$selectedTaxonomies[] = intval($result['ID']);
    		}
    	}
    	return $selectedTaxonomies;
    }
    
    /**
     * Gibt eine Appartmentkategorie zurueck
     * @param stdClass | array $term
     * @return RS_IB_Model_Appartmentkategorie
     */
    public function getAppartmentKategorie( $term ) {
        $modelKategorie         = new RS_IB_Model_Appartmentkategorie($term);
//         $term_id                = $modelKategorie->getTermId();
//         $meta_type              = $modelKategorie->getMetaType();
        return $modelKategorie;
    }
}
// endif;