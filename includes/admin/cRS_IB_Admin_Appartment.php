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
if ( ! defined ( 'ABSPATH' ) ) {
    exit;
} ?>
<?php
// if ( ! class_exists( 'RS_IB_Admin_Appartment' ) ) :
class RS_IB_Admin_Appartment
{
    public function __construct() {
        add_action("admin_head", array($this, 'indiebooking_apartment_on_admin_head'));
        add_action("trash_to_publish", array($this, 'checkApartmentRestore'),10,1);
        add_action("trash_to_draft", array($this, 'checkApartmentRestore'),10,1);
        add_filter('pre_get_posts', array($this, 'set_post_order_in_admin'), 5 );
        
        $this->constructAdminApartment();
    }
    
    public function set_post_order_in_admin($wp_query) {
    	global $pagenow;
    	//if (RS_IB_Model_Appartment::RS_POSTTYPE == $post_type) {
    	if ( is_admin() && 'edit.php' == $pagenow && !isset($_GET['orderby'])) {
    		$query = $wp_query->query;
    		if (key_exists('post_type', $query)) {
    			if ($query['post_type'] == RS_IB_Model_Appartment::RS_POSTTYPE) {
    				if ($wp_query->get('orderby') == "") {
			    		$wp_query->set( 'orderby', 'title' );
			    		$wp_query->set( 'order', 'ASC' );
    				}
    			}
    		}
    	}
    }
    
    public function checkApartmentRestore( $post ) {
        if (RS_IB_Model_Appartment::RS_POSTTYPE == get_post_type($post)) {
            $show   = apply_filters("rs_indiebooking_check_add_new_apartment_head_restore", $show);
            if (!$show) {
                wp_update_post(array(
                    'ID'            =>  $post->ID,
                    'post_status'   =>  'trash'
                ));
            }
        }
    }
    
    public function constructAdminApartment() {
        $this->includes();
        $this->init_hooks();
    }
    
    private function init_hooks() {
        $this->initAppartmentHooks();
        add_image_size( 'apartment-thumbnail', 100, 100, true );
    }
    
//     private function createPopups() {
//         include_once 'view/viewObjects/RS_IB_Appartmentoption_View_Settings.php';
//         include_once 'view/RS_IB_Appartment_Option_View.php';

//         global $RSBP_DATABASE;
//         global $post;
//         $mwstTable              = $RSBP_DATABASE->getTable(RS_IB_Model_Mwst::RS_TABLE);
//         $mwsts                  = $mwstTable->getAllMwsts();
        
//         $optionViewSettings     = new RS_IB_Appartmentoption_View_Settings();
        
//         $optionViewSettings->setApartmentOption($appartmentOption);
//         $optionViewSettings->setMwst($mwsts);
//         $optionViewSettings->setUebersicht(true);
//         rs_ib_createAddOptionPopup($optionViewSettings, $appartmentOption);

//         rs_ib_create_taxonomyPopups();
//         do_action("rs_indiebooking_create_admin_popup");
//     }
    
    private function includes() {
        include_once 'cRS_IB_Admin_Apartment_images.php';
        include_once 'view/RS_IB_Appartment_Overview.php';
        include_once 'view/RS_IB_Appartment_View.php';
        include_once 'view/popup/RS_IB_Popup_Add_Popups.php';
    }
       
    public function indiebooking_apartment_on_admin_head() {
        if (get_post_type() == RS_IB_Model_Appartment::RS_POSTTYPE) {
            add_action( 'edit_form_top', array('RS_IndiebookingApartmentView', 'indiebooking_add_header_before_title' ));
            add_action( 'edit_form_after_title', array('RS_IndiebookingApartmentView', 'indiebooking_add_header_after_title' ));
        }
    }
    
    private function initAppartmentHooks() {
        add_action('admin_init', array($this, 'addAppartmentCustomFields'));
        add_action('save_post', array($this, 'save_details'));
         
        add_action( 'admin_notices', array($this, 'my_admin_notices') );
        
        add_action( 'admin_menu', array($this, 'rs_ib_remove_meta_boxes'));
        add_action( 'add_meta_boxes', array($this, 'rs_ib_add_meta_box'));
        
//         add_action( 'edit_form_top', 'rs_indiebooking_add_header_before_title' );
//         add_action( 'edit_form_after_title', 'rs_indiebooking_add_header_after_title' );
        
        add_action( 'rs_indiebooking_save_apartment_extra_infos', array($this, 'saveApartmentExtraInfos'), 1, 2);
        
        // Script action for the post new page
        add_action( 'admin_print_scripts-post-new.php', array($this, 'check_add_new_apartment_head') );
        add_action( 'admin_print_footer_scripts-post-new.php', array($this, 'check_add_new_apartment_head') );
        add_filter("rs_indiebooking_check_add_new_apartment_head", array($this, "rs_indiebooking_check_add_new_apartment_head"), 10);
        add_filter("rs_indiebooking_check_add_new_apartment_head_restore", array($this, "rs_indiebooking_check_add_new_apartment_head_restore"), 10);
        
        RS_IB_Appartment_Overview::configureAppartmentOverview();
    }
    
    /**
     * prueft ob die Anzahl der Apartments die maximal Anzahl ueberschreitet.
     * $restore gibt hierbei an, ob das Apartment gerade aus dem Papierkorb genommen wird.
     *
     * @param unknown $post_type
     * @param string $restore
     * @return boolean
     */
    private function rs_indiebooking_check_apartment_count($post_type, $restore = false) {
        $answer = true;
        if (RS_IB_Model_Appartment::RS_POSTTYPE == $post_type) {
            $apartmentCount = wp_count_posts(RS_IB_Model_Appartment::RS_POSTTYPE);
            $countPublish   = $apartmentCount->publish;
            $countDraft     = $apartmentCount->draft;
            if (!$restore) {
                if (($countPublish+$countDraft) >= 3) {
                    $answer = false;
                }
            } else {
                if (($countPublish+$countDraft) > 3) {
                    return false;
                }
            }
        }
        return $answer;
    }
    
    public function rs_indiebooking_check_add_new_apartment_head_restore($show) {
        global $post_type;
    
        return $this->rs_indiebooking_check_apartment_count($post_type, true);
//         if (RS_IB_Model_Appartment::RS_POSTTYPE == $post_type) {
//             $apartmentCount = wp_count_posts(RS_IB_Model_Appartment::RS_POSTTYPE);
//             $countPublish   = $apartmentCount->publish;
//             $countDraft     = $apartmentCount->draft;
//             if (($countPublish+$countDraft) > 3) {
//                 return false;
//             }
//         }
//         return true;
    }
    
    public function rs_indiebooking_check_add_new_apartment_head($show) {
        global $post_type;
    
        return $this->rs_indiebooking_check_apartment_count($post_type, false);
//         if (RS_IB_Model_Appartment::RS_POSTTYPE == $post_type) {
//             $apartmentCount = wp_count_posts(RS_IB_Model_Appartment::RS_POSTTYPE);
//             $countPublish   = $apartmentCount->publish;
//             $countDraft     = $apartmentCount->draft;
//             if (($countPublish+$countDraft) >= 3) {
//                 return false;
//             }
//         }
//         return true;
    }
    
    public function check_add_new_apartment_head() {
        $show       = true;
        if (rs_indiebooking_is_edit_page('new')) {
            $show   = apply_filters("rs_indiebooking_check_add_new_apartment_head", $show);
        }
        if (!$show && get_post_type() == 'rsrsappartment') {
        ?>
        	<div class="ibui_admin_to_much_apartments">
        <?php
        }
    }
    
    public function check_add_new_apartment_foot() {
        $show     = true;
        if (rs_indiebooking_is_edit_page('new')) {
            $show = apply_filters("rs_indiebooking_check_add_new_apartment_head", $show);
        }
        if (!$show) {
        ?>
        	</div>
        <?php
        }
    }
    
    public function addAppartmentCustomFields() {
//         add_meta_box("year_completed-meta", __('Year Completed', 'indiebooking'), array($this, "year_completed"), "rsappartment", "normal", "low"); //side
//         add_meta_box("quadratMeter-meta", __('Squere meter', 'indiebooking'), array($this, "quadratMeter"), "rsappartment", "normal", "low"); //side
        remove_meta_box("postcustom", RS_IB_Appartment_post_type::POST_TYPE_NAME, 'normal'); //entfernt den "Benutzerdefinierte Felder"-Bereich von Wordpress
        
        add_meta_box( "default_values-meta", __('Apartment values', 'indiebooking'), array($this, "default_infos"), RS_IB_Appartment_post_type::POST_TYPE_NAME, "normal", "low");

//         add_meta_box("rs_ib_cs_test", __('Test Box', 'indiebooking'), array($this, "Test_Box"), "rsappartment", "normal", "low"); //side

        //add_meta_box( 'apartment_product_images', __( 'Appartment Gallery', 'indiebooking' ), 'RS_IB_Admin_Apartment_images::output', 'rsappartment', 'normal', 'low' );
        
        
        //add_meta_box( "rs_ib_stammdaten-meta", __('Default values', 'indiebooking'), array($this, "default_infos"), RS_IB_Appartment_post_type::POST_TYPE_NAME, "normal", "low");
    }


    /**
     * Entfernt die Metaboxen, die ich nicht im Standard-Wordpress-Layout haben muechte
     * Aktuell sind das die Kategorien und Optionen, da ich diese gerne mit Checkboxen auswuehlbar,
     * jedoch nicht hierarchisch haben muechte.
     */
    public function rs_ib_remove_meta_boxes() {
        add_action("rs_indiebooking_remove_apartment_metaboxes", array($this, "removeApartmentMetaBoxes"));
        do_action("rs_indiebooking_remove_apartment_metaboxes");
    }
    
    public function removeApartmentMetaBoxes() {
        remove_meta_box('tagsdiv-rsappartmentcategories', RS_IB_Appartment_post_type::POST_TYPE_NAME, 'normal');
    }
    
    /**
     * Fügt die Metaboxen, die ich zuvor entfernt habe wieder hinzu.
     * Allerdings mit einer eigenen Funktion, die das Template beeinflusst.
     */
    public function rs_ib_add_meta_box() {
        //nachdem alles durch Tabs ersetzt wurde, wird diese Methode aktuell nicht gebraucht.
    }
    
    /**
     * Erstellt das Template fuer die Taxonomy-Metaboxen. Bei denen ich Checkboxen haben muechte,
     * jedoch nicht muechte, dass diese Hierarchisch sind.
     */
    public static function RS_IB_mytaxonomy_metabox($post, $taxonomy) {
        global $RSBP_DATABASE;
        
        $myTaxonomy         = $taxonomy['id'];
        
        // all terms of ctax
        $all_ctax_terms     = get_terms($myTaxonomy,array('hide_empty' => 0));
        // all the terms currenly assigned to the post
        if (!is_null($post) && !(empty($post))) {
	        $all_post_terms = get_the_terms( $post->ID,$myTaxonomy );
        } else {
        	$all_post_terms	= array();
        }
        
        
        // name for each input, notice the extra []
        $name               = 'tax_input[' . $myTaxonomy . '][]';
        $dutyname           = 'duty_input[' . $myTaxonomy . '_duty][]';
        // make an array of the ids of all terms attached to the post
        $array_post_term_ids = array();
        if ($all_post_terms) {
            foreach ($all_post_terms as $post_term) {
                $post_term_id           = $post_term->term_id;
                $array_post_term_ids[]  = $post_term_id;
            }
        }
        ?>
            <div id="taxonomy-<?php echo $myTaxonomy; ?>" class="categorydiv">
            	<?php
            	do_action('rs_indiebooking_create_metadummy_'.$myTaxonomy);
            	?>
                    <input type="hidden" name="<?php echo $name; ?>" value="0" />
                    <ul>
                    <?php
                        foreach($all_ctax_terms as $key => $term) {
                            $dutychecked = "";
                            if (!strpos($term->slug, "-duty") ) {
                                if (in_array($term->term_id, $array_post_term_ids)) {
                                    $checked = "checked = 'checked'";
                                } else {
                                    $checked = "";
                                }
//                                 if (sizeof($all_ctax_terms) > ($key+1)) {
//                                     echo $all_ctax_terms[$key+1]->slug;
//                                     if (strtoupper($all_ctax_terms[$key+1]->slug) == strtoupper($term->slug."-duty")) {
//                                         $dutychecked = "checked = 'checked'";
//                                     } else {
//                                         $dutychecked = "";
//                                     }
//                                 }
                                $id = $myTaxonomy.'-'.$term->term_id;
                                echo "<li id='$id'>";
//                                 echo self::RS_IB_create_mytaxonomy_metabox_listItem($post, $myTaxonomy, $term, $checked);
                                do_action('rs_indiebooking_createApartmentMetalistItem', $post, $term, $myTaxonomy, $checked);
                               echo "</li>";
                            }
                        }
                    ?>
                    </ul>
            </div>
        <?php
    }
    
    
    public function default_infos() {
        global $post;
        global $RSBP_DATABASE;
    
//         $myPostId                   = $post->ID;
//         var_dump($post);

//         $this->createPopups();
        
//         var_dump($post->ID);
        $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $appartment                 = $appartmentTable->getAppartment($post->ID);
        $mwstTable                  = $RSBP_DATABASE->getTable(RS_IB_Model_Mwst::RS_TABLE);
        $mwsts                      = $mwstTable->getAllMwsts();
        $bookedDates                = $appartmentBuchungsTable->getBuchungszeitraeumeByAppartmentId($post->ID);
        
        showAppartmentStammdaten($post, $appartment, $mwsts, $bookedDates);
    }
    
    function compareDatesWithDate($fromTest, $toTest, $dates, $abIndex) {
//         $valid  = true;
//         for ($i = $abIndex; $i < sizeof($dates); $i++) {
//             $date   = $dates[$i];
//             $from   = $date["from"];
//             $to     = $date["to"];
//             if (($to - $from) > ($toTest - $fromTest)) {
//                 if ($fromTest < $to && $toTest > $from) {
//                     $valid = false;
//                     break;
//                 }
//             } else {
//                 if ($from < $toTest && $to > $fromTest) {
//                     $valid = false;
//                     break;
//                 }
//             }
//         }
        $valid = true;
        $overlap = rs_ib_date_util::isDateOverlap($dates, $fromTest, $toTest, $abIndex);
        if ($overlap == true) {
            $valid = false; //Wenn die Datumswerte sich ueberschneiden, sind sie nicht valide
        }
        return $valid;
    }
    
    /**
     * Prueft ob die uebergebenen Daten valide sind.
     * $dates ist ein zweidimensionales Array mit folgendem Aufbau:
     * $dates[index]["from"]
     * $dates[index]["to"]
     * @param unknown $dates
     */
    public function checkDates($dates) {
        $valid = true;
        for ($i = 0; $i < sizeof($dates); $i++) {
        	if (key_exists($i, $dates)) {
	            if ("" !== $dates[$i]["from"] && "" !== $dates[$i]["to"]) {
	                $myFrom                 = DateTime::createFromFormat("d.m.Y", $dates[$i]["from"]);
	                $myTo                   = DateTime::createFromFormat("d.m.Y", $dates[$i]["to"]);
	                if (!is_null($myFrom) && !is_null($myTo)) {
	                    $dates[$i]["from"]  = $myFrom->getTimestamp();
	                    $dates[$i]["to"]    = $myTo->getTimestamp();
	                }
	            }
        	}
        }
        for ($i = 0; $i < sizeof($dates)-1; $i++) {
        	if (key_exists($i, $dates)) {
	            $date   = $dates[$i];
	            $valid  = $this->compareDatesWithDate($date["from"], $date["to"], $dates, $i+1);
	            if (!$valid) {
	                break;
	            }
        	}
        }
        return $valid;
    }
    
    public function saveApartmentExtraInfos($taxonomy_values = null, $appartment = null) {
        //do nothing
    }
    
    public function save_details(){
        global $post;
        global $RSBP_DATABASE;
        
        $errorObj       = false;
        try {
            $save       = true;
            $save       = apply_filters("rs_indiebooking_check_add_new_apartment_head", $save);
            if (!$save) {
                if ($errorObj == false) {
                    $errorObj    = new WP_Error();
                }
                $errorObj->add(400, __("You can't generate a new Apartment", 'indiebooking'));
            }
            if (isset($post)) {
                if (RS_IB_Model_Appartment::RS_POSTTYPE === $post->post_type) {
                    if ( ! empty( $_POST ) && check_admin_referer( 'save_details', 'save_appartment_nonce_field' ) ) {
                        
                        $appartmentTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
                        $appartment         = new RS_IB_Model_Appartment();
                        $appartment->setPostId($post->ID);
                
                        $appartment->setJahr(rsbp_getPostValue("year_completed", 0, RS_IB_Data_Validation::DATATYPE_NUMBER));
                        $appartment->setPreis(rsbp_getPostValue("appartment_price", 0, RS_IB_Data_Validation::DATATYPE_NUMBER));
                        $appartment->setMwstId(rsbp_getPostValue("appartment_price_mwst", 0, RS_IB_Data_Validation::DATATYPE_INTEGER));
                        $appartment->setQuadratmeter(rsbp_getPostValue("appartment_square_meter", 0, RS_IB_Data_Validation::DATATYPE_NUMBER));
                        $appartment->setMinDateRange(rsbp_getPostValue("appartment_min_date_range", 0, RS_IB_Data_Validation::DATATYPE_INTEGER));
                        $appartment->setArrivalDays(rsbp_getPostValue("chk_gloabal_weekday_group", array()));

                        $appartment->setLocationDescription(rsbp_getPostValue("appartment_locationdescription", "", RS_IB_Data_Validation::DATATYPE_TEXT));
                        $appartment->setShowOnStartPage(rsbp_getPostValue("showOnStartPageKz", "off", RS_IB_Data_Validation::DATATYPE_TEXT));
                        $appartment->setOnlyInquire(rsbp_getPostValue("apartmentOnlyInquireKz", "off", RS_IB_Data_Validation::DATATYPE_TEXT));
                        $appartment->setLocation(rsbp_getPostValue("appartment_location", "", RS_IB_Data_Validation::DATATYPE_TEXT));
                        $appartment->setStreet(rsbp_getPostValue("appartment_street", "", RS_IB_Data_Validation::DATATYPE_TEXT));
                        $appartment->setZipCode(rsbp_getPostValue("appartment_zip_code", "", RS_IB_Data_Validation::DATATYPE_TEXT));
                        
                        $appartment->setAnzahlEinzelBetten(rsbp_getPostValue("appartment_anzahl_einzel_betten",0, RS_IB_Data_Validation::DATATYPE_INTEGER));
                        $appartment->setAnzahlDoppelBetten(rsbp_getPostValue("appartment_anzahl_doppel_betten",0, RS_IB_Data_Validation::DATATYPE_INTEGER));
                        $appartment->setAnzahlBetten(rsbp_getPostValue("appartment_anzahl_betten", 0, RS_IB_Data_Validation::DATATYPE_INTEGER));
                        $appartment->setAnzahlPersonen(rsbp_getPostValue("appartment_anzahl_personen", 0, RS_IB_Data_Validation::DATATYPE_INTEGER));
                        $appartment->setAnzahlPersonenVorbelegung(rsbp_getPostValue("appartment_anzahl_personen_vorbelegung", 0, RS_IB_Data_Validation::DATATYPE_INTEGER));
                        $appartment->setAnzahlZimmer(rsbp_getPostValue("appartment_anzahl_zimmer", 0, RS_IB_Data_Validation::DATATYPE_INTEGER));
                             
                        $appartment->setBookingComHotelId(rsbp_getPostValue("rs_appartment_bookingcom_hotel_id", 0, RS_IB_Data_Validation::DATATYPE_INTEGER));
                        $appartment->setBookingComRoomId(rsbp_getPostValue("rs_appartment_bookingcom_room_id", 0, RS_IB_Data_Validation::DATATYPE_INTEGER));
                        $appartment->setBookingComDefaultRateId(rsbp_getPostValue("default_bookingcom_rateid", 0, RS_IB_Data_Validation::DATATYPE_INTEGER));
                        $appartment->setBookingComAufschlag(rsbp_getPostValue("rs_bookingcom_appartment_extra_charge", 0, RS_IB_Data_Validation::DATATYPE_NUMBER));
                        $appartment->setBookingComAufschlagTyp(rsbp_getPostValue("appartment_bookingcom_extra_charge_typ", 0, RS_IB_Data_Validation::DATATYPE_NUMBER));
                        
                        
//                         $appartment->setPriceIsNet(rsbp_getPostValue("cb_appartment_price_netto_kz"));
                        $appartment->setAllowVaryingPrice(rsbp_getPostValue("activate_varying_price"));
                        $appartment->setShortDescription(rsbp_getPostValue("appartment_shortdescription", "", RS_IB_Data_Validation::DATATYPE_TEXT));
                        
                        $appartment->setLat(rsbp_getPostValue("appartment_latitude", 0, RS_IB_Data_Validation::DATATYPE_NUMBER));
                        $appartment->setLng(rsbp_getPostValue("appartment_longitude", 0, RS_IB_Data_Validation::DATATYPE_NUMBER));
                        
                        $appartment->setSecurityDeposit(rsbp_getPostValue("rs_appartment_security_deposit", 0, RS_IB_Data_Validation::DATATYPE_NUMBER));
                        
                        $notbookableFroms   = rsbp_getPostValue("rs_ib_not_bookable_periods_from", array());
                        $notbookableTos     = rsbp_getPostValue("rs_ib_not_bookable_periods_to", array());
                        $notbookableDates   = array();
                        for ($i=0; $i < sizeof($notbookableFroms); $i++) {
                            $notbookableDates[$i]["from"]  = RS_IB_Data_Validation::check_with_whitelist($notbookableFroms[$i],
                                                                                                        RS_IB_Data_Validation::DATATYPE_DATUM);
                            $notbookableDates[$i]["to"]    = RS_IB_Data_Validation::check_with_whitelist($notbookableTos[$i],
                                                                                                        RS_IB_Data_Validation::DATATYPE_DATUM);
                        }
                        if ($this->checkDates($notbookableDates)) {
                            $appartment->setNotbookableDates($notbookableDates);
                        } else {
                           $appartment->setNotbookableDates(null);
                           if ($errorObj == false) {
                               $errorObj    = new WP_Error();
                           }
                           $errorObj->add(500, __("Your not bookable dates are not valid", 'indiebooking'));
                        }
//                         $datesValid         = $this->checkDates($bookableDates);
//                         if (!$datescalid) {
//                             throw new Exception("Datumswerte nicht korrekt", 1);
//                         } else {
                            
                            //price_date_from = Saison Datum von
                            
                       	/*
                       	 * **********************************************************
                       	 * ******** SAISON PREISE ERMITTELN SOFERN VORHANDEN ********
                       	 * **********************************************************
                       	 */
                        $priceFromDates     = rsbp_getPostValue('price_date_from', array());
                        $priceToDates       = rsbp_getPostValue('price_date_to', array());
                        $pricePrices        = rsbp_getPostValue('price_date_price', array());
                        $validFromDates     = rsbp_getPostValue('price_date_valid_from', array());
                        $automaticKzs       = rsbp_getPostValue('price_date_automatic_kz', array());
                        $validToYears       = rsbp_getPostValue('price_date_valid_to', array());
                        $priceDates         = array();

                        $checkDateArray     = array();
                        $saisonIndex		= 0;
                        for ($i=0; $i < sizeof($priceFromDates); $i++) {
                        	$cloneTo						= null;
                        	$checkClonedDate				= false;
                        	$priceDates[$saisonIndex]['from']         = RS_IB_Data_Validation::check_with_whitelist($priceFromDates[$i],
                                                                                                        RS_IB_Data_Validation::DATATYPE_DATUM);
                        	$priceDates[$saisonIndex]['to']           = RS_IB_Data_Validation::check_with_whitelist($priceToDates[$i],
                                                                                                        RS_IB_Data_Validation::DATATYPE_DATUM);
                        	$priceDates[$saisonIndex]['price']        = RS_IB_Data_Validation::check_with_whitelist($pricePrices[$i],
                                                                                                        RS_IB_Data_Validation::DATATYPE_NUMBER);
                        	$priceDates[$saisonIndex]['valid']        = RS_IB_Data_Validation::check_with_whitelist($validFromDates[$i],
                                                                                                        RS_IB_Data_Validation::DATATYPE_DATUM);
                        	$priceDates[$saisonIndex]['automatic']    = $automaticKzs[$i];
                        	$priceDates[$saisonIndex]['validto']      = $validToYears[$i];
                            
                        	if ($priceDates[$saisonIndex]['from'] != "") {
	                            $monthFrom 					= substr($priceDates[$i]['from'], 3);
	                            $monthTo 					= substr($priceDates[$i]['to'], 3);
	                            $valid						= $priceDates[$i]['valid'];
	                            $validTo					= $priceDates[$i]['validto'];
	                            if ((intval($monthFrom) > intval($monthTo)) && ($validTo == "" || $validTo == 0 || $validTo > $valid)) {
	                            	$cloneTo				= $priceDates[$i]['to'];
	                            	$priceDates[$saisonIndex]['to'] 	= '31.12';
	                            }
                            }
                            if ($priceFromDates[$i] != "" && $priceToDates[$i] != "" && $validFromDates[$i] != ""
                            		&& $automaticKzs[$i] == 0) {
                            			
                            	$checkDateArray[$saisonIndex]['from'] = $priceFromDates[$i].".".$validFromDates[$i];
                            	$checkDateArray[$saisonIndex]['to']   = $priceToDates[$i].".".$validFromDates[$i];
                                if (!is_null($cloneTo)) {
                                	$checkClonedDate		= true;
                                }
                            }
                            if (!is_null($cloneTo)) {
                            	$saisonIndex++;
//                             	$anzSaison++;
                            	
                            	$priceDates[$saisonIndex]['from']         = '01.01';
                            	$priceDates[$saisonIndex]['to']           = $cloneTo;
                            	$priceDates[$saisonIndex]['price']        = RS_IB_Data_Validation::check_with_whitelist($pricePrices[$i],
                            													RS_IB_Data_Validation::DATATYPE_NUMBER);
                            	$priceDates[$saisonIndex]['valid']        = RS_IB_Data_Validation::check_with_whitelist($validFromDates[$i],
                            													RS_IB_Data_Validation::DATATYPE_DATUM);
                            	$priceDates[$saisonIndex]['automatic']    = $automaticKzs[$i];
                            	$priceDates[$saisonIndex]['validto']      = $validToYears[$i];
                            	
                            	
                            	if ($checkClonedDate) {
                            		$checkDateArray[$saisonIndex]['from'] = $priceFromDates[$i].".".$validFromDates[$i];
                            		$checkDateArray[$saisonIndex]['to']   = $priceToDates[$i].".".$validFromDates[$i];
                            	}
                            	
                            }
                            $saisonIndex++;
                        }
                        $checkDateArray = array_values($checkDateArray);
                        if ($this->checkDates($checkDateArray)) {
                            $appartment->setYearlessPriceDates($priceDates);
                        } else {
                            $appartment->setYearlessPriceDates(null);
	                        if ($errorObj == false) {
	                            $errorObj    = new WP_Error();
	                        }
                        	$errorObj->add(500, __('Your saison dates are not valid', 'indiebooking'));
                        }
                        /*
                         * **********************************************************
                         * **********************************************************
                         * **********************************************************
                         */
                        
                        /*
                         * **********************************************************
                         * ********** DEGRESSION ERMITTELN SOFERN VORHANDEN *********
                         * **********************************************************
                         */
                        $degressionCondCount	= rsbp_getPostValue('degression_condition_count', array());
                        $degressionCondRange	= rsbp_getPostValue('degression_condition_range', array());
                        $degressionReduction	= rsbp_getPostValue('degression_reduction', array());
                        $degressionReductionTyp = rsbp_getPostValue('appartment_degression_typ', array());
                        $degresBookingRateId = null;
                        if (is_plugin_active('indiebooking-booking.com/indiebooking-booking.com.php')) {
                        	$degresBookingRateId = rsbp_getPostValue('degression_bookingcom_rateid', array());
                        }
                        
                        $degressionData         = array();
                        for ($i=0; $i < sizeof($degressionCondCount); $i++) {
                        	$reduction							= RS_IB_Data_Validation::check_with_whitelist($degressionReduction[$i],
                        											RS_IB_Data_Validation::DATATYPE_NUMBER);
                        	if ($reduction != '') {
	                        	$degressionData[$i]["count"]	= RS_IB_Data_Validation::check_with_whitelist($degressionCondCount[$i],
		                        									RS_IB_Data_Validation::DATATYPE_INTEGER);
	                        	$degressionData[$i]["range"]	= RS_IB_Data_Validation::check_with_whitelist($degressionCondRange[$i],
	                        										RS_IB_Data_Validation::DATATYPE_TEXT);
	                        	$degressionData[$i]["reduction"] = $reduction;
	                        	$degressionData[$i]["reductionTyp"] = RS_IB_Data_Validation::check_with_whitelist($degressionReductionTyp[$i],
		                        									RS_IB_Data_Validation::DATATYPE_INTEGER);
	                        	
	                        	if (!is_null($degresBookingRateId)) {
	                        		$degressionData[$i]['bookingcomRateId'] = RS_IB_Data_Validation::check_with_whitelist($degresBookingRateId[$i],
	                        															RS_IB_Data_Validation::DATATYPE_INTEGER);
	                        	} else {
	                        		$degressionData[$i]['bookingcomRateId'] = "";
	                        	}
                        	}
                        }
                        $appartment->setDegression($degressionData);
                        /*
                         * **********************************************************
                         * **********************************************************
                         * **********************************************************
                         */
                        
                        /*
                         * **********************************************************
                         * ******************** APARTMENT FEATURES*******************
                         * **********************************************************
                         */
                        $feature_values = rsbp_getPostValue("apartment_feature", array());
                        $appartment->setFeatures($feature_values);
                        
                        /*
                         * **********************************************************
                         * ********** AUFSCHLAG ERMITTELN SOFERN VORHANDEN **********
                         * **********************************************************
                         */
                        $extraChargeCondCount		= rsbp_getPostValue('extra_charge_condition_count', array());
                        $extraChargeCondRange		= rsbp_getPostValue('extra_charge_condition_range', array());
                        $extraChargePrice			= rsbp_getPostValue('extra_charge_price', array());
                        $extraChargeReductionTyp 	= rsbp_getPostValue('appartment_extra_charge_typ', array());
                        $extraChargeBookingRateId	= rsbp_getPostValue('extra_charge_bookingcom_rateid', array());
                        
                        $extraChargeData         	= array();
                        for ($i=0; $i < sizeof($extraChargeCondCount); $i++) {
                        	$extraCharge						= RS_IB_Data_Validation::check_with_whitelist($extraChargePrice[$i],
                        			RS_IB_Data_Validation::DATATYPE_NUMBER);
                        	if ($extraCharge != '') {
                        		$extraChargeData[$i]["count"]	= RS_IB_Data_Validation::check_with_whitelist($extraChargeCondCount[$i],
                        				RS_IB_Data_Validation::DATATYPE_INTEGER);
                        		$extraChargeData[$i]["range"]	= RS_IB_Data_Validation::check_with_whitelist($extraChargeCondRange[$i],
                        				RS_IB_Data_Validation::DATATYPE_TEXT);
                        		$extraChargeData[$i]["extraCharge"] = $extraCharge;
                        		$extraChargeData[$i]["extraChargeTyp"] = RS_IB_Data_Validation::check_with_whitelist($extraChargeReductionTyp[$i],
                        				RS_IB_Data_Validation::DATATYPE_INTEGER);
                        		
                        		if (isset($extraChargeBookingRateId) && key_exists($i, $extraChargeBookingRateId)) {
	                        		$extraChargeData[$i]["extraChargeBookingComId"] = RS_IB_Data_Validation::check_with_whitelist($extraChargeBookingRateId[$i],
	                        			RS_IB_Data_Validation::DATATYPE_INTEGER);
                        		}
                        	}
                        }
                        $appartment->setExtraCharge($extraChargeData);
                        /*
                         * **********************************************************
                         * **********************************************************
                         * **********************************************************
                         */
                        
                        do_action("rs_indiebooking_admin_saveOrUpdateApartment", $appartment);
//                             $appartmentTable->saveOrUpdateAppartment($appartment);
                            
                        if (class_exists("RS_IB_Admin_Apartment_images")) {
                            RS_IB_Admin_Apartment_images::saveGallery($post->ID, $post);
                        }
                            
                        $taxonomy_values            = rsbp_getPostValue("tax_input", array());
                            
                        
                        do_action( 'rs_indiebooking_save_apartment_extra_infos', $taxonomy_values, $appartment);

                        if (has_action('rs_indiebooking_synchronize_apartmentdata')) {
                        	do_action( 'rs_indiebooking_synchronize_apartmentdata');
                        }
                        if ($errorObj != false) {
                                
                        }
                    }
                }
            }
        } catch (Exception $e) {
            add_filter( 'redirect_post_location', array( $this, 'add_notice_query_var' ), 99, "", $errorObj );
        }
    }

    public function my_admin_notices() {
        //workaround da die Popups plötzlich nicht mehr funktioniert haben!
        if (get_post_type() == RS_IB_Model_Appartment::RS_POSTTYPE) {
            rs_ib_create_taxonomyPopups();
            do_action("rs_indiebooking_create_admin_popup");
            ?>
            <script id="rs_indiebooking_workaround_poups">
            	rs_indiebooking_initial_ApartmentView_Popups();
            </script>
            <?php
            if ( ! isset( $_GET['ERROR_QUERY_VAR'] ) ) {
                return;
                }?>
                <div class="error">
                    <p><?php esc_html_e( 'Wrong Data! Appartment has not been saved', 'indiebooking' ); ?></p>
                </div>
            <?php
        }
    }
    
    public function add_notice_query_var($location, $errorObj) {
        remove_filter( 'redirect_post_location', array( $this, 'add_notice_query_var' ), 99 );
        return add_query_arg( array( 'ERROR_QUERY_VAR' => 'ID' ), $location );
    }
    
}
// endif;
new RS_IB_Admin_Appartment();