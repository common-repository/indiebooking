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
// if ( ! class_exists( 'RS_IB_Model_Meta' ) ) :
abstract class RS_IB_Model_Meta
{

	public static function getAllIdsFromWPMLId($wpmlId, $element_type) {
		global $wpdb;
		global $RSBP_DATABASE;
		global $RSBP_TABLEPREFIX;
		
		$useWpml = true;
		
		$allIds = array();
		$allActiveLanguages = array();
		if (!$useWpml) {
			array_push($allIds, $wpmlId);
		}
		if ($useWpml && function_exists('icl_object_id') ) {
			$wpmlId 			= intval($wpmlId);
			$trid				= apply_filters( 'wpml_element_trid', NULL, $wpmlId, $element_type);
			//$originalId			= self::getOriginalIdFromWPMLId($wpmlId);
			//     		$allActiveLanguages	= apply_filters( 'wpml_active_languages', NULL);
			$trids = apply_filters('wpml_get_element_translations', NULL, $trid, $element_type);
			
			foreach ($trids as $foundTrIds) {
				//     			$langKurz	= $languages['language_code'];
				//     			$originalId = apply_filters( 'wpml_object_id', $wpmlId, 'post', TRUE, $langKurz );
				if (!is_null($foundTrIds)) {
					$foundedId = $foundTrIds->element_id;
					if (!in_array($foundedId, $allIds)) {
						array_push($allIds, $foundedId);
					}
				}
			}
			if (sizeof($allIds) <= 0) {
				array_push($allIds, $wpmlId);
			}
		} else {
			array_push($allIds, $wpmlId);
		}
		return $allIds;
	}
	
	
	public static function getOriginalIdFromWPMLId($wpmlId, $elementType = 'post') {
		global $wpdb;
		global $RSBP_DATABASE;
		global $RSBP_TABLEPREFIX;
		
		
		if ( function_exists('icl_object_id') ) {
			$wpmlId 			= intval($wpmlId);
			$my_default_lang 	= apply_filters('wpml_default_language', NULL );
			$originalId 		= apply_filters( 'wpml_object_id', $wpmlId, $elementType , TRUE, $my_default_lang );
		} else {
			$originalId	= $wpmlId;
		}
		return $originalId;
	}
	
    public function __construct($data = array()) {
        $this->exchangeArray($data);
    }
    
    public function exchangeArray($data) {
        //doNothing
    }
    
}
// endif;