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
// if ( ! class_exists( 'RS_IB_Appartment_Uebersicht_Controller' ) ) :
/**
 * @author schmitt
 *
 */
class RS_IB_Appartment_Uebersicht_Controller
{
    public function __construct() {
//     	add_filter('posts_orderby', array($this, 'ib_apartment_overview_custom_orderby'), 10, 2);
    	add_filter( 'posts_clauses', array($this, 'ib_apartment_overview_custom_clauses'), 10, 2 );
    }
    
    public function getAllAppartments() {
        global $RSBP_DATABASE;
        
        $type                   = RS_IB_Model_Appartment::RS_POSTTYPE;
        $args = array(
            'post_type'         => $type,
            'post_status'       => 'publish',
            'posts_per_page'    => -1,
            'ignore_sticky_posts' => 1,
            'orderby'           => 'title',
            'order'             => 'ASC',
        	'suppress_filters' 	=> false
        );
        $my_query               = new WP_Query($args);
        $this->showAppartments($my_query);
        wp_reset_query();  // Restore global post data stomped by the_post().
    }
    
    
    public function getAllAppartmentsByCategory() {
    	global $RSBP_DATABASE;
    	
    	$type                   = RS_IB_Model_Appartment::RS_POSTTYPE;
    	$args = array(
    		'post_type'         => $type,
    		'post_status'       => 'publish',
    		'posts_per_page'    => -1,
    		'ignore_sticky_posts' => 1,
    		'orderby'           => 'title',
    		'order'             => 'ASC',
    		'suppress_filters' 	=> false
    	);
    	$my_query               = new WP_Query($args);
    	$this->showAppartmentsByCategory($my_query);
    	wp_reset_query();  // Restore global post data stomped by the_post().
    }
    
//     public function ib_apartment_overview_custom_orderby($orderby_statement, $wp_query) {
//     	if (!is_admin()) {
//     		if ($wp_query->get("post_type") === RS_IB_Model_Appartment::RS_POSTTYPE) {
//     			$bookingByCategorieKz		= get_option('rs_indiebooking_settings_booking_by_categorie_kz');
//     			$bookingByCategorieKz		= ($bookingByCategorieKz == "on");
//     			if ($bookingByCategorieKz) {
    				
//     			}
//     		}
//     	}
//     	return $orderby_statement;
//     }
    
    
    public function ib_apartment_overview_custom_clauses($clauses, $wp_query) {
//     	global $RSBP_DATABASE;
    	
    	if (!is_admin()) {
    		if ($wp_query->get("post_type") === RS_IB_Model_Appartment::RS_POSTTYPE) {
    			$bookingByCategorieKz		= get_option('rs_indiebooking_settings_booking_by_categorie_kz');
    			$bookingByCategorieKz		= ($bookingByCategorieKz == "on");
    			if ($bookingByCategorieKz) {
			    	global $RSBP_TABLEPREFIX;
			    	global $wpdb;
			    	
			    	$termTable				= $wpdb->prefix.'terms';
			    	$termTaxTable			= $wpdb->prefix.'term_taxonomy';
			    	$relationTaxTable		= $wpdb->prefix.'term_relationships';
    				
// 			    	$clauses['join'] .= 'JOIN '.$termTaxTable.' ib_rs_prfx_tax ON '.$relationTaxTable.'.term_taxonomy_id = ib_rs_prfx_tax.term_taxonomy_id';
//     				$clauses['orderby'] = 'ib_rs_prfx_tax.description DESC, '.$clauses['orderby'];
			    	$clauses['join'] 	.= 'JOIN '.$termTaxTable.' ib_rs_prfx_tax ON '.$relationTaxTable.'.term_taxonomy_id = ib_rs_prfx_tax.term_taxonomy_id';
			    	$clauses['join'] 	.= ' JOIN '.$termTable.' ib_rs_prfx_terms ON ib_rs_prfx_tax.term_id = ib_rs_prfx_terms.term_id';
			    	$clauses['orderby'] = 'ib_rs_prfx_terms.name ASC, '.$clauses['orderby'];
    			}
    		}
    	}
    	return $clauses;
    }
    
    /* @var $appartment RS_IB_Model_Appartment */
    /* @var $category_term WP_Term */
    private function showAppartmentsByCategory($my_query) {
    	global $RSBP_DATABASE;
    	
    	$count 							= 0;
    	$apartmentCategoryArray			= array();
    	if( $my_query->have_posts() ) {
    		$appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
    		$appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
    		$templatePartFound          = false;
    		$templatePart               = locate_template('appartmentliste.php');
    		if ($templatePart != '') {
    			$templatePartFound  = true;
    		}
    		
    		while( $my_query->have_posts() ): $my_query->the_post();
	    		$postId                     = get_the_ID();
	    		$apartmentIstBuchbar        = true;
	    		$appartment                 = $appartmentTable->getAppartment($postId);
	
	    		$bookedDates                = $appartmentBuchungsTable->getBuchungszeitraeumeByAppartmentId($postId);
	    		$bookableDates              = json_encode($appartment->getBookableDates());
	    		$bookedDates                = json_encode($bookedDates);
	    		$arrivalDays                = json_encode($appartment->getArrivalDays());
	    		$apartmentIstBuchbar        = $appartment->isApartmentBookable();
	    		$appartment_post            = get_post($postId);
	    		$description                = get_the_excerpt($appartment_post);
	
	    		$bookPrice                  = $appartment->getSmalestPrice();
	    		if ($bookPrice != "") {
	    			$bookPrice              = str_replace(".", ",", number_format($bookPrice, 2));
	    		}

	    		if ($templatePartFound) {
	    			//                     include($templatePart);
	    		} else {
	    			$args = array(
	    				'bookPrice'     => $bookPrice,
	    				'bookableDates' => $bookableDates,
	    				'bookedDates'   => $bookedDates,
	    				'arrivalDays'   => $arrivalDays,
	    				'kurztext'      => $appartment->getShortDescription(),
	    				'langtext'      => $description,
	    				'isBookable'    => $apartmentIstBuchbar,
	    				'aktionen'      => $appartment->getAktionen(),
	    				'minAufenthalt' => $appartment->getMinDateRange(),
	    				'apartmentData' => $appartment,
	    			);

	    			$all_post_category_terms    = get_the_terms($postId , 'rsappartmentcategories' );
	    			$categories                 = "";
	    			if ($all_post_category_terms !== false) {
	    				foreach ($all_post_category_terms as $category_term) {
	    					$apartmentCategoryArray[$category_term->term_id]['categoryterm'] = $category_term;
		    				$apartmentCategoryArray[$category_term->term_id][$appartment->getPostId()] = $args;
// 	    					if (!key_exists($category_term->term_id, $categoryTerms)) {
	    						
// 	    					}
// 	    					if ($categories !== "") {
// 	    						$categories             = $categories . ", " . $category_term->name;
// 	    					} else {
// 	    						$categories             = $categories . $category_term->name;
// 	    					}
	    				}
	    			}
	    			
// 	    			cRS_Template_Loader::rs_ib_get_template('appartmentlist.php', $args);
	    		}
	    		$count++;
    		endwhile;
    		
    		if (sizeof($apartmentCategoryArray) > 0) {
    			$listArgs = array(
    				'showByCategory'		=> true,
    				'categorieApartments' 	=> $apartmentCategoryArray,
    			);
    			cRS_Template_Loader::rs_ib_get_template('appartmentlist.php', $listArgs);
    		}
    		
    		/* Restore original Post Data */
    		//             wp_reset_postdata();
    	}
    }
    
    
    /* @var $appartment RS_IB_Model_Appartment */
    private function showAppartments($my_query) {
        global $RSBP_DATABASE;
        
        $count = 0;
        if( $my_query->have_posts() ) {
//         	$points3 = $my_query->found_posts;
            $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
            $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
            $templatePartFound          = false;
            $templatePart               = locate_template('appartmentliste.php');
            $bookingByCategorieKz		= get_option('rs_indiebooking_settings_booking_by_categorie_kz');
            $bookingByCategorieKz		= ($bookingByCategorieKz == "on");
            if ($templatePart != '') {
                $templatePartFound  = true;
            }
            while( $my_query->have_posts() ): $my_query->the_post();
                $postId                     = get_the_ID();
                $apartmentIstBuchbar        = true;
                $appartment                 = $appartmentTable->getAppartment($postId);
//                 $buchungen                  = $appartmentBuchungsTable->getBuchungenByAppartmentid($postId);
//                 $bookedDates                = array();
//                 for ($i = 0; $i < sizeof($buchungen); $i++) {
//                     $dates = unserialize($buchungen[$i]->meta_value);
//                     $bookedDates[$i]["from"]    = $dates[0];
//                     $bookedDates[$i]["to"]      = $dates[1];
//                 }
                $bookedDates                = $appartmentBuchungsTable->getBuchungszeitraeumeByAppartmentId($postId);
                $bookableDates              = json_encode($appartment->getBookableDates());
                $bookedDates                = json_encode($bookedDates);
                $arrivalDays                = json_encode($appartment->getArrivalDays());
                $apartmentIstBuchbar        = $appartment->isApartmentBookable();
                $appartment_post            = get_post($postId);
                $description                = get_the_excerpt($appartment_post);
//                 if ($appartment->getPreis() == "" || $appartment->getPreis() <= 0) {
//                     //kein defaultpreis gepflegt
//                     if (sizeof($appartment->getYearlessPriceDates()) <= 0) {
//                         $apartmentIstBuchbar    = false;
//                     }
//                 }
//                 if (sizeof($appartment->getOffenZeitraumeDB()) <= 0) {
//                     $apartmentIstBuchbar    = false;
//                 }

//                 $zeitraumeDB                = json_encode($appartment->getZeitraumeDB());
//                 var_dump($zeitraumeDB);

                //TODO auslagern --> derzeit nicht buchbar!?
                $firstCategoryName			= "";
                $bookPrice                  = $appartment->getSmalestPrice();
                if ($bookPrice != "") {
                    $bookPrice              = str_replace(".", ",", number_format($bookPrice, 2));
                }
                $firstCategoryName			= $appartmentTable->getApartmentFirstCategoryName($postId);
//                 $all_post_category_terms    = get_the_terms($postId , 'rsappartmentcategories' );
//                 $categories                 = "";
//                 if ($all_post_category_terms !== false) {
//                     foreach ($all_post_category_terms as $category_term) {
//                     	if ($firstCategoryName == "") {
//                     		$firstCategoryName = $category_term->name;
//                     	}
//                         if ($categories !== "") {
//                             $categories             = $categories . ", " . $category_term->name;
//                         } else {
//                             $categories             = $categories . $category_term->name;
//                         }
//                     }
//                 }
                if ($templatePartFound) {
//                     include($templatePart);
                } else {
                    $args = array(
                        'bookPrice'     => $bookPrice,
                        'bookableDates' => $bookableDates,
                        'bookedDates'   => $bookedDates,
                        'arrivalDays'   => $arrivalDays,
                        'kurztext'      => $appartment->getShortDescription(),
                        'langtext'      => $description,
                        'isBookable'    => $apartmentIstBuchbar,
                        'aktionen'      => $appartment->getAktionen(),
                    	'minAufenthalt' => $appartment->getMinDateRange(),
                    	'showCategoryAsName' => $bookingByCategorieKz,
                    	'firstCategoryName' => $firstCategoryName,
                    );
                    cRS_Template_Loader::rs_ib_get_template('appartmentlist.php', $args);
                }
                $count++;
            endwhile;
            /* Restore original Post Data */
//             wp_reset_postdata();
        }
//         $i = 1;
    }
    
    public function searchAppartment(RS_IB_SearchData $searchData) {
        global $RSBP_DATABASE;
        
        $my_query                   = null;
        $found                      = false;
        $categorySearchQuery        = null;
        $numberOfBedsSearchQuery    = null;
        $numberOfRoomsSearchQuery   = null;
        $numberOfGuestsSearchQuery  = null;
        
//         var_dump($searchData);
        
        //TODO - muss in eine Option gewandelt werden.
        //wurde gemacht, die Anzeige passt so allerdings noch nicht
        //option ist:
        $bookingByCategorieKz		= get_option('rs_indiebooking_settings_booking_by_categorie_kz');
		$bookingByCategorieKz		= ($bookingByCategorieKz == "on");
        $showApartmentByCategory	= false; //diese Variable bestimmt die Anzeige
        
        $categorie                  = $searchData->getCategorie();
        $features					= $searchData->getFeatures();
        $pIds                       = $this->searchAppartmentIdsByDate($searchData->getDateFrom(),
                                                                        $searchData->getDateTo());
        if (sizeof($pIds) > 0) {
            $found                  = true;
            $tax_query              = array('relation' => 'AND');
//             if (!is_null($categorie) && trim($categorie) != '0' && trim($categorie) != '') {
            
            if ($bookingByCategorieKz) {
            	/* @var $categoryTable RS_IB_Table_Appartmentkategorie */
            	$categoryTable		= $RSBP_DATABASE->getTable(RS_IB_Model_Appartmentkategorie::RS_TABLE);
            	$allCategories		= $categoryTable->loadAllCategorieIds();
            	
            	$categoryTaxQuery = array(
            		'taxonomy' => RS_IB_Model_Appartmentkategorie::RS_TAXONOMY,
            		'field'		=> 'term_id',
            		'terms'		=> $allCategories,
            	);
            	array_push($tax_query, $categoryTaxQuery);
            }
            
            if (!is_null($categorie) && sizeof($categorie) > 0 && $categorie != '' && $categorie != 'null') {
                $categorySearchQuery = array(
                    'taxonomy' => RS_IB_Model_Appartmentkategorie::RS_TAXONOMY,
                    'field'    => 'name',
                    'terms'    => $categorie,
                    'operator' => 'IN',
                );
                array_push($tax_query, $categorySearchQuery);
            }
            if (!is_null($searchData->getOptions()) && sizeof($searchData->getOptions()) > 0) {
                foreach ($searchData->getOptions() as $optionId) {
                    $optionSearchQuery = array(
                        'taxonomy' => RS_IB_Model_Appartmentoption::RS_TAXONOMY,
                        'field'    => 'term_id',
                        'terms'    => $optionId,
                    );
                    array_push($tax_query, $optionSearchQuery);
                }
            }
            $meta_query             = array();
            if (!is_null($searchData->getNumberOfBeds())) {
                if (is_array($searchData->getNumberOfBeds())) {
                    if (sizeof($searchData->getNumberOfBeds()) > 0 && $searchData->getNumberOfBeds() !== ''
                        && $searchData->getNumberOfBeds() !== 'null') {
                            $numberOfBedsSearchQuery = array(
                                'key'   => RS_IB_Model_Appartment::APPARTMENT_ANZAHL_BETTEN,
                                'value' => $searchData->getNumberOfBeds(),
                                'compare' => 'IN',
                            );
                            array_push($meta_query, $numberOfBedsSearchQuery);
                    }
                } else {
                    if ($searchData->getNumberOfBeds() !== '' && $searchData->getNumberOfBeds() !== 'null'
                        && intval($searchData->getNumberOfBeds()) > 0) {
                            $numberOfBedsSearchQuery = array(
                                'key'   => RS_IB_Model_Appartment::APPARTMENT_ANZAHL_BETTEN,
                                'value' => $searchData->getNumberOfBeds(),
                                'compare' => '>=',
                            );
                            array_push($meta_query, $numberOfBedsSearchQuery);
                    }
                }
//                 }
//             if (!is_null($searchData->getNumberOfBeds()) && trim($searchData->getNumberOfBeds()) != '0' && trim($searchData->getNumberOfBeds()) != '') {
            }
            
            if (!is_null($searchData->getNumberOfRooms())) {
                if (is_array($searchData->getNumberOfRooms())) {
                    if (sizeof($searchData->getNumberOfRooms()) > 0 && $searchData->getNumberOfRooms() !== ''
                        && $searchData->getNumberOfRooms() !== 'null') {
                            $numberOfRoomsSearchQuery = array(
                                'key'   => RS_IB_Model_Appartment::APPARTMENT_ANZAHL_ZIMMER,
                                'value' => $searchData->getNumberOfRooms(),
                                'compare' => 'IN',
                            );
                            array_push($meta_query, $numberOfRoomsSearchQuery);
                        }
                } else {
                    if ($searchData->getNumberOfRooms() !== '' && $searchData->getNumberOfRooms() !== 'null'
                        && intval($searchData->getNumberOfRooms()) > 0) {
                            $numberOfRoomsSearchQuery = array(
                                'key'   => RS_IB_Model_Appartment::APPARTMENT_ANZAHL_ZIMMER,
                                'value' => $searchData->getNumberOfRooms(),
                                'compare' => '>=',
                            );
                            array_push($meta_query, $numberOfRoomsSearchQuery);
                        }
                }
            }
            
            if (!is_null($searchData->getLocation())) {
                if ($searchData->getLocation() !== '' && $searchData->getLocation() !== 'null') {
                    $locationSearchQuery = array(
                        'key'   => RS_IB_Model_Appartment::APPARTMENT_LOCATION_DESC,
                        'value' => $searchData->getLocation(),
                        'compare' => '=',
                    );
                    array_push($meta_query, $locationSearchQuery);
                }
            }
            if (isset($features) && !is_null($features) && $features != '' && $features != 'null' && sizeof($features) > 0) {
            	if (is_string($features)) {
            		$features = explode(",",$features);
            	}
				$searchFeature = "";
				$featureQueryArgs = array();
				$featureQueryArgs['relation'] = "AND";
				foreach ($features as $key => $f) {
					$featureQuery = array(
							'key' => RS_IB_Model_Appartment::APPARTMENT_FEATURES,
							'value' => $f,
							//                     'value' => $features,
					       'compare' => '=',
					);
					array_push($featureQueryArgs, $featureQuery);
// 					if ($key > 0) {
// 						$searchFeature = $searchFeature.",";
// 					}
// 					$searchFeature = $searchFeature.$f;
				}
//             	$featureQuery = array(
//             		'key' => RS_IB_Model_Appartment::APPARTMENT_FEATURES,
//             		'value' => $searchFeature,
// //                     'value' => $features,
// //                     'compare' => '=',
//             	);
            	array_push($meta_query, $featureQueryArgs);
            }
            if (!is_null($searchData->getNumberOfGuests())) {
                if (is_array($searchData->getNumberOfGuests())) {
                    if (sizeof($searchData->getNumberOfGuests()) > 0 && $searchData->getNumberOfGuests() !== ''
                        && $searchData->getNumberOfGuests() !== 'null') {
                            $numberOfGuestsSearchQuery = array(
                                'key'   => RS_IB_Model_Appartment::APPARTMENT_ANZAHL_PERSONEN,
                                'value' => intval($searchData->getNumberOfGuests()),
                                'compare' => 'IN',
                            );
                            array_push($meta_query, $numberOfGuestsSearchQuery);
                    }
                } else {
                    if ($searchData->getNumberOfGuests() !== '' && $searchData->getNumberOfGuests() !== 'null'
                        && intval($searchData->getNumberOfGuests()) > 0) {
                            $numberOfGuestsSearchQuery = array(
                                'key'   => RS_IB_Model_Appartment::APPARTMENT_ANZAHL_PERSONEN,
                                'value' => intval($searchData->getNumberOfGuests()),
                                'compare' => '>=',
                            );
                            array_push($meta_query, $numberOfGuestsSearchQuery);
                    }
                }
            }
            $type                   = RS_IB_Model_Appartment::RS_POSTTYPE;
            $suppress_filters		= false;
            
            if ( function_exists('icl_object_id') ) {
            	global $sitepress;
            	
//             	$my_current_lang 	= $sitepress->get_current_language();
//             	do_action( 'wpml_switch_language', $my_current_lang );
//             	$defaultLanguage	= $sitepress->get_default_language();
//             	if ($my_current_lang != $defaultLanguage) {
//             	$suppress_filters	= true; //sorgt dafuer, dass auch anderssprachige Apartments gefunden werden.
//             	}
//             	$currentlanguage	= apply_filters( 'wpml_current_language', NULL );
//             	$args = array(
// 	            	'post_type'         => $type,
// 	            	'post_status'       => 'publish',
// 	            	'posts_per_page'    => -1,
// 	            	'ignore_sticky_posts' => 1,
// 	            	'orderby'           => 'title',
// 	            	'order'             => 'ASC',
// 	            	'suppress_filters' 	=> false
//             	);
            }
//             else {
	                //             'caller_get_posts'  => 1,
//             'ignore_custom_sort' => true,
	            $args = array(
	                'post_type'         => $type,
	                'tax_query'         => $tax_query,
	                'meta_query'        => $meta_query,
	                'post_status'       => 'publish',
	                'posts_per_page'    => -1,
	                'ignore_sticky_posts' => 1,
	                'post__in'          => $pIds,
	                'orderby'           => 'title',
	                'order'             => 'ASC',
	                'suppress_filters' 	=> false
	            );
//             }
            $my_query               = new WP_Query($args);
            $queryStr = $my_query->request;
//             echo $queryStr;
//             $queryStr = $queryStr." ORDER BY rs_ib_posts.title DESC";
//             $my_query->request = $queryStr;
            if( $my_query->have_posts() ) {
            	if (!$showApartmentByCategory) {
                	$this->showAppartments($my_query);
            	} else {
            		$this->showAppartmentsByCategory($my_query);
            	}
            } else {
                $found              = false;
            }
            
//             $currentlanguage	= apply_filters( 'wpml_current_language', NULL );
//             $type                   = RS_IB_Model_Appartment::RS_POSTTYPE;
//             $args = array(
//             	'post_type'         => $type,
//             	'post_status'       => 'publish',
//             	'posts_per_page'    => -1,
//             	'ignore_sticky_posts' => 1,
//             	'orderby'           => 'title',
//             	'order'             => 'ASC',
//             	'suppress_filters' 	=> false
//             );
//             $my_query               = new WP_Query($args);
//             $this->showAppartments($my_query);
            
            
        }
        if (!$found) {
            $templatePart           = locate_template('appartmentliste_not_found.php');
            $templatePartFound      = false;
            if ($templatePart != '') {
                $templatePartFound  = true;
            }
            if ($templatePartFound) {
                include($templatePart);
            } else {
                include cRS_Indiebooking::plugin_path().'/templates/appartmentlist_not_found.php';
            }
        }
        wp_reset_query();  // Restore global post data stomped by the_post().
        return $my_query;
    }
    
    public function searchAppartmentByDate($dtSuche_von, $dtSuche_bis) {
        global $RSBP_DATABASE;
        $my_query               = null;
        
        $pIds                   = $this->searchAppartmentIdsByDate($dtSuche_von, $dtSuche_bis);
        if (sizeof($pIds) > 0) {
//         	if ( function_exists('icl_object_id') ) {
//         		global $sitepress;
        		
// 				$my_current_lang 	= $sitepress->get_current_language();
// 				do_action( 'wpml_switch_language', $my_current_lang );
//         	}
            $type                   = RS_IB_Model_Appartment::RS_POSTTYPE;
    //             'caller_get_posts'  => 1,
            $args = array(
                'post_type'         => $type,
                'post_status'       => 'publish',
                'posts_per_page'    => -1,
                'ignore_sticky_posts' => 1,
                'post__in'          => $pIds,
                'orderby'           => 'title',
                'order'             => 'ASC',
            	'suppress_filters' 	=> true
            );
            $my_query               = new WP_Query($args);
            $this->showAppartments($my_query);
        } else {
            $templatePart       = locate_template('appartmentliste_not_found.php');
            $templatePartFound  = false;
            if ($templatePart != '') {
                $templatePartFound  = true;
            }
            if ($templatePartFound) {
                include($templatePart);
            } else {
                include cRS_Indiebooking::plugin_path().'/templates/appartmentlist_not_found.php';
            }
        }
        wp_reset_query();  // Restore global post data stomped by the_post().
        return $my_query;
    }
    
    /* @var $buchungTable RS_IB_Table_Appartment_Buchung */
    private function searchAppartmentIdsByDate($dtSuche_von, $dtSuche_bis) {
        global $RSBP_DATABASE;
        $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        $buchungTable               = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        
        if (empty($dtSuche_von) || empty($dtSuche_bis)) {
        	$apartments				= $buchungTable->getAllAppartments();
        } else {
        	$apartments             = $buchungTable->getAvailableAppartments($dtSuche_von, $dtSuche_bis);
        }
//         $pIds                       = array();
//         var_dump($apartments);
//         foreach ($apartments as $apId) {
//             $pIds[]                 = $apId//$apIds->post_id;
//         }
        
        return $apartments;
    }
    
    /* @var $buchungTable RS_IB_Table_Appartment_Buchung */
//     private function searchAppartmentIds($dtSuche_von, $dtSuche_bis, $categorie) {
//         global $RSBP_DATABASE;
//         $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
//         $buchungTable               = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
    
//         $apartments                 = $buchungTable->getAvailableAppartments($dtSuche_von, $dtSuche_bis);
//         //         $pIds                       = array();
//         //         var_dump($apartments);
//         //         foreach ($apartments as $apId) {
//         //             $pIds[]                 = $apId//$apIds->post_id;
//         //         }
    
//         return $apartments;
//     }
}
// endif;
