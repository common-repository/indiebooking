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
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// if ( ! class_exists( 'RS_INDIEBOOKING_TEMPLATE_FKT' ) ) :
class RS_INDIEBOOKING_TEMPLATE_FKT {
    
    public static function include_global_javascript_translation_variables() {
//         include_once 'template_methods/javascript_translation_texts.php';
    }
    
    public static function show_booking_control_button($postId) {
        //do nothing
        //diese Methode ist ein Platzhalter fuer bspw. das Payment Plugin, welches durch die Action
        //rs_indiebooking_show_booking_control_buttons die Mueglichkeit hat noch weiter Buttons
        //hinzuzufuegen.
    }
    
    public static function show_default_booking_header_data($buchung) {
        global $RSBP_DATABASE;
        
        $appartmentId = $buchung->getAppartment_id();
        ?>
		<input type="hidden" id="appartmentPostId" name="appartmentPostId" value="<?php echo $appartmentId; ?>">
        <?php
    }
    
    public static function single_appartment_header_data() {
        global $RSBP_DATABASE;
        $allMwst                    = array();
    
        $appartmentMwst             = "";
        $postId                     = get_the_ID();
        $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        $appartment                 = $appartmentTable->getAppartment($postId);
        $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $mwstTable                  = $RSBP_DATABASE->getTable(RS_IB_Model_Mwst::RS_TABLE);
        $allMwst                    = $mwstTable->getAllMwsts();
        //         $buchungen                  = $appartmentBuchungsTable->getBuchungenByAppartmentid($postId);
        //         $bookedDates                = array();
        //         for ($i = 0; $i < sizeof($buchungen); $i++) {
        //             $dates = unserialize($buchungen[$i]->meta_value);
        //             $bookedDates[$i]["from"]    = $dates[0];
        //             $bookedDates[$i]["to"]      = $dates[1];
        //         }
        $bookedDates                = $appartmentBuchungsTable->getBuchungszeitraeumeByAppartmentId($postId);
        $notBookableDates           = $appartment->getNotbookableDates();
        $datesAndPrices             = $appartment->getBookableDatesWithCalculatedPrices($allMwst);
        foreach ($allMwst as $appMwSt) {
            if ($appMwSt->getMwstId() == $appartment->getMwstId()) {
                $appartmentMwst = $appMwSt->getMwstValue();
                break;
            }
        }
        ?>
        <script type="text/javascript">
        		/* var bookableDates 	= <?php //echo json_encode($appartment->getBookableDates()); ?>;*/
                /* var bookedDates   	= <?php //echo json_encode($bookedDates); ?>; */
                var datesAndPrices 	= <?php echo json_encode($datesAndPrices);?>;
        </script>
        <?php
            $bookableDates      = json_encode($appartment->getBookableDates());
            $arrivalDays        = json_encode($appartment->getArrivalDays());
            $bookedDates        = json_encode($bookedDates);
            $notBookableDates   = json_encode($notBookableDates);
        ?>
        <input id='rs_zabuto-calendar_data' type="hidden" data-appartmentId='<?php the_ID(); ?>'
        		data-bookable='<?php echo $bookableDates; ?>' data-booked='<?php echo $bookedDates; ?>'
        		data-notbookabledates='<?php echo $notBookableDates; ?>'
        		data-arrivaldays='<?php echo $arrivalDays; ?>' />
            
        <input id="price_per_night" data-isNet="<?php echo $appartment->getPriceIsNet();?>"
        		data-mwst="<?php echo $appartmentMwst;?>" type="hidden"
        		value="<?php echo $appartment->getPreis();?>" />
        		
        <input id="apartemtn_square_meter" type="hidden" value="<?php echo $appartment->getQuadratmeter();?>" />
        <?php
    }
    
    public static function show_single_apartment_header($apartmentId, $booking = false) {
    	global $RSBP_DATABASE;
    	
    	if (!$booking) {
        	self::single_appartment_header_data();
    	}
        
    	$apartment_title				= get_the_title($apartmentId);
        $bookingByCategorieKz			= get_option('rs_indiebooking_settings_booking_by_categorie_kz');
        $bookingByCategorieKz			= ($bookingByCategorieKz == "on");
        $firstCategoryName				= "";
        if ($bookingByCategorieKz) {
        	$appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        	$firstCategoryName			= $appartmentTable->getApartmentFirstCategoryName($apartmentId);
        	$apartment_title			= $firstCategoryName;
//         	$all_post_category_terms    = get_the_terms(get_the_ID() , 'rsappartmentcategories' );
//         	$categories                 = "";
//         	if ($all_post_category_terms !== false) {
//         		foreach ($all_post_category_terms as $category_term) {
//         			if ($firstCategoryName == "") {
//         				$firstCategoryName = $category_term->name;
//         				$apartment_title	= $firstCategoryName;
//         				break;
//         			}
//         		}
//         	}
        }
        $args = array(
            'apartmentTitle' 		=> $apartment_title,
        	'showCategoryAsName' 	=> $bookingByCategorieKz,
        	'firstCategoryName' 	=> $firstCategoryName,
        );
        if ($booking) {
            cRS_Template_Loader::rs_ib_get_template('single-appartment-buchung/appartment_buchung_appartment_header.php', $args);
        } else {
            cRS_Template_Loader::rs_ib_get_template('single-appartment/appartment_header.php', $args);
        }
    }
    
    public static function rs_indiebooking_single_rsappartment_buchung_appartment_header($apartmentId = 0) {
    	self::show_single_apartment_header($apartmentId, true);
    }
    
    public static function rs_indiebooking_template_single_appartment_header($apartmentId = 0) {
    	self::show_single_apartment_header($apartmentId, false);
    }
    
    /* @var $buchungskopf RS_IB_Model_Buchungskopf */
    public static function show_payment_boxes($zahlart = "", $paypalPlusKz = "", $buchungPostId = 0) {
    	global $RSBP_DATABASE;
    	
    	$checked 					= "";
    	$checkedClass				= "";
    	$invoiceKz					= "";
    	$invoiceLoggedKz			= "";
    	$paymentPluginsActive		= false;
    	// 	if (is_plugin_active('indiebooking_payment/indiebooking_payment.php')) {
    	if (is_plugin_active('indiebooking-paypal/indiebooking-paypal.php')) {
    		$paymentPluginsActive	= true;
    		$paypalData             = get_option( 'rs_indiebooking_settings_paypal');
    		if ($paypalData) {
    			$testpaypalKz       = (key_exists('testpaypal_kz', $paypalData)) ? esc_attr__( $paypalData['testpaypal_kz'] ) : "";
    			$paypalPlusKz		= ""; //(key_exists('paypal_plus_kz', $paypalData)) ?  esc_attr__( $paypalData['paypal_plus_kz'] ) : "";
    			if ($testpaypalKz != "on" || ($testpaypalKz == "on" && current_user_can('administrator'))) {
    				//do default
    			} else {
    				$paypalPlusKz	= "";
    			}
    		}
    	}
    	if (is_plugin_active('indiebooking-stripe/indiebooking-stripe.php')) {
    		$paymentPluginsActive	= true;
    	}
    	$invoiceMinDays				= 0;
    	$showInvoiceMethod			= true;
    	$paymentData 				= get_option( 'rs_indiebooking_settings_payment');
    	if ($paymentData) {
    		$payperInvoiceKz		= (key_exists('payperinvoice_kz', $paymentData)) ? esc_attr__( $paymentData['payperinvoice_kz'] ) : "on";
    		$invoiceMinDays			= (key_exists('invoice_availability', $paymentData)) ? esc_attr__( $paymentData['invoice_availability'] ) : 0;
    		$invoiceLoggedKz		= (key_exists('invoice_loggeduser_kz', $paymentData)) ? esc_attr__( $paymentData['invoice_loggeduser_kz'] ) : "";
    	} else {
    		$payperInvoiceKz 		= "on";
    	}
    	
    	if ($payperInvoiceKz == "on" && $paypalPlusKz != "on") {
    		if ($invoiceMinDays > 0) {
	    		$buchungTable    		= $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
	    		$buchungKopfTable		= $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
	    		$buchung         		= $buchungTable->getAppartmentBuchung($buchungPostId);
	    		$buchungskopf    		= $buchungTable->loadBuchungskopf($buchung->getBuchungKopfId());
	    		
	    		$today					= new DateTime("now");
	    		$bookingFrom			= $buchungskopf->getBuchung_von();
	    		if (!$bookingFrom instanceof DateTime) {
	    			$bookingFrom		= new DateTime($bookingFrom);
	    		}
	    		$differenz 				= date_diff($today, $bookingFrom);
	    		$differenz				= $differenz->format('%a');
	    		if ((intval($differenz) < $invoiceMinDays) && $paymentPluginsActive) {
	    			$showInvoiceMethod	= false;
	    		}
    		}
    		$userloggedin				= is_user_logged_in();
    		if ($invoiceLoggedKz == "on" && !$userloggedin) {
    			$showInvoiceMethod		= false;
    		}
	        if ($zahlart == "" || $zahlart == "INVOICE") {
	            $checked = 'checked="checked"';
	            $checkedClass = "ibui_payment_radio_button_box_checked";
	        }
	        if ($showInvoiceMethod) {
	        ?>
				<div class="row ibui_payment_row">
	<!-- 	    		<div class="col-md-2 col-sm-3"></div> -->
			    	<label class="col-md-12 col-sm-12 ibui_payment_radio_button_box <?php echo $checkedClass; ?>">
			    		<div class="col-md-1 col-sm-1"></div>
						<div class="col-md-2 col-sm-2 ibui_payment_icon ibui_payment_invoice_icon"></div>
						<label class="col-md-9 col-sm-9 ibui_payment_radio_button_box_label">
					        <input type="radio" id="rs_ib_pay_invoice" class="ibfc_default_payment_methode ibui_payment_radio_button"
					        		 name="rs_ib_Zahlmethode" value="Invoice" <?php echo $checked; ?>>
					    	<label for="rs_ib_pay_invoice" class="invoce_payment_logo btn" >
					    		<?php _e("pay per invoice", 'indiebooking');?>
							</label>
							<br>
						</label>
					</label>
				</div>
    	<?php
			}
    	}
    }
    
    public static function show_buchung_controll_buttons($postId, $buttonKz=0) {
        $args = array(
            'btnKz' => $buttonKz,
            'postId' => $postId,
        );
        cRS_Template_Loader::rs_ib_get_template('single-appartment-buchung/appartment_buchung_controll_buttons.php', $args);
    }
}//end of class
// endif;



/**
 * Leite auf die Homepage weiter, wenn der Nutzer, der sich angemeldet hat
 * keine lesenden Rechte besitzt.
 */
if ( ! function_exists( 'rs_indiebooking_themeblvd_redirect_admin' )) {
	function rs_indiebooking_themeblvd_redirect_admin(){
	    if ( ! defined('DOING_AJAX') && !current_user_can('read') ) {
	        wp_redirect( site_url() );
	        exit;
	    }
	}
}
add_action( 'admin_init', 'rs_indiebooking_themeblvd_redirect_admin' );


/**
 * ACTIONS
 */

if ( ! function_exists( 'rs_indiebooking_rsappartment_show_first_page_apartments' )) {
    
    /* @var $appartment RS_IB_Model_Appartment */
    function rs_indiebooking_rsappartment_show_first_page_apartments() {
        global $RSBP_DATABASE;
        $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        
        $inquiry					= false;
        $apInquiry					= false;
        $bookingInquiriesKz			= get_option('rs_indiebooking_settings_booking_inquiries_kz');
        if (isset($bookingInquiriesKz) && !is_null($bookingInquiriesKz) && $bookingInquiriesKz == "on") {
        	$inquiry				= true;
        }
        $meta_query = array();
        $firstPageApartments = array(
            'key'   => RS_IB_Model_Appartment::APPARTMENT_SHOWONSTART,
            'value' => 'on',
        );
        array_push($meta_query, $firstPageApartments);
        $type                   = RS_IB_Model_Appartment::RS_POSTTYPE;
        $args = array(
            'post_type'         => $type,
            'meta_query'        => $meta_query,
            'post_status'       => 'publish',
            'posts_per_page'    => -1,
            'ignore_sticky_posts' => 1,
            'orderby'           => 'title',
            'order'             => 'ASC',
        );
        $my_query               = new WP_Query($args);
        while( $my_query->have_posts() ): $my_query->the_post();
            $postId             	= get_the_ID();
            $appartment         	= $appartmentTable->getAppartment($postId, true);
            $bookingByCategorieKz	= get_option('rs_indiebooking_settings_booking_by_categorie_kz');
            $bookingByCategorieKz	= ($bookingByCategorieKz == "on");
            if (!$inquiry) {
            	$apartmentIsInquiry	= $appartment->getOnlyInquire();
            	if (isset($apartmentIsInquiry) && !is_null($apartmentIsInquiry) && $apartmentIsInquiry == "on") {
            		$apInquiry		= true;
            	}
            }
            
            $firstCategoryName		= "";
            if ($bookingByCategorieKz) {
            	$firstCategoryName	= $appartmentTable->getApartmentFirstCategoryName($postId);
            }
            
            $bookPrice          = $appartment->getSmalestPrice();
            if ($bookPrice != "") {
                $bookPrice      = str_replace(".", ",", number_format($bookPrice, 2));
            }
            $showAction         = false;
            $aktionen           = $appartment->getAktionen();
            if (sizeof($aktionen) > 0) {
                foreach ($aktionen as $aktion) {
                    //Sobald eine Aktion gefunden wurde, die ausgeschrieben werden soll
                    //soll auch das Aktionsfaehnchen dargestellt werden
                    if ($aktion->getExpelPriceKz() == 'on') {
                        $showAction = true;
                        break;
                    }
                }
            }
            if ($inquiry == true) {
            	$apInquiry		= true;
            }
            
            $arguments          = array(
                'price'         => $bookPrice,
                'waehrung'      => rs_ib_currency_util::getCurrentCurrency(),
                'kurztext'      => $appartment->getShortDescription(),
                'aktionen'      => $aktionen,
                'showAction'    => $showAction,
            	'inquiry'		=> $apInquiry,
            	'minAufenthalt' => $appartment->getMinDateRange(),
            	'showCategoryAsName' 	=> $bookingByCategorieKz,
            	'firstCategoryName' 	=> $firstCategoryName,
            );
            cRS_Template_Loader::rs_ib_get_template('firstpageapartmentitem.php', $arguments);
            $apInquiry = false;
        endwhile;
    }
}


/* @var $buchung RS_IB_Model_Appartment_Buchung */
/*
if ( ! function_exists( 'default_booking_header_data' ) ) {
    function default_booking_header_data($buchung) {
        global $RSBP_DATABASE;
        
        $appartmentId = $buchung->getAppartment_id();
        ?>
		<input type="hidden" id="appartmentPostId" name="appartmentPostId" value="<?php echo $appartmentId; ?>">
        <?php
    }
}
*/

if ( !function_exists( 'rs_indiebooking_single_rsappartment_smallesprice' ) ) {
    function rs_indiebooking_single_rsappartment_smallesprice($apartmentId) {
        if (is_null($apartmentId) || $apartmentId == '') {
            $apartmentId = get_the_ID();
        }
        RS_IB_Template_Buchungsanzeige::show_appartment_smalles_price($apartmentId);
    }
}

if ( ! function_exists( 'rs_indiebooking_show_navbar' ) ) {
    function rs_indiebooking_show_navbar($seite = 1) {
        $args = array(
            'seite' => $seite,
        );
        cRS_Template_Loader::rs_ib_get_template('indiebooking_navbar.php', $args);
    }
}

if ( ! function_exists( 'rs_indiebooking_template_single_appartment_gallery' ) ) {
    function rs_indiebooking_template_single_appartment_gallery($apartmentId = null, $showAction = false) {
        if (!is_plugin_active('indiebooking-advanced/indiebooking_advanced.php')) {
            do_action('rs_indiebooking_single_appartment_profile_picture', null);
        }
    }
}

if ( ! function_exists( 'rs_indiebooking_template_appartment_list_gallery' ) ) {
    function rs_indiebooking_template_appartment_list_gallery($appartmentId) {
        RS_IB_Template_Buchungsanzeige::show_appartment_gallery($appartmentId);
    }
}


if ( ! function_exists( 'rs_indiebooking_single_rsappartment_from_to_dates' ) ) {
    function rs_indiebooking_single_rsappartment_from_to_dates($buchungVon = '', $buchungBis = '') {
        RS_IB_Template_Buchungsanzeige::show_single_appartment_from_to_dates(get_the_ID(), $buchungVon, $buchungBis);
    }
}

if ( ! function_exists( 'rs_indiebooking_list_rsappartment_from_to_dates' ) ) {
    function rs_indiebooking_list_rsappartment_from_to_dates($appartment_id) {
        RS_IB_Template_Buchungsanzeige::show_appartment_from_to_dates($appartment_id);
    }
}


if ( ! function_exists( 'rs_indiebooking_template_single_appartment_prices' )) {
    function rs_indiebooking_template_single_appartment_prices($apartmentId = 0) {
        //do nothing - wird in extension plugin benuetigt
    }
}

// if (!function_exists('rs_indiebooking_template_list_appartment_short_description')) {
//     function rs_indiebooking_template_list_appartment_short_description($appartmentId = 0) {
//         if ($apartmentId == 0) {
//             $apartmentId = get_the_ID();
//         }
//         RS_IB_Template_Buchungsanzeige::show_appartment_short_description($appartmentId);
//     }
// }

/* @var $apartmentBuchungTbl RS_IB_Table_Appartment_Buchung */
/* @var $apartmentTbl RS_IB_Table_Appartment */
if (!function_exists('rs_indiebooking_template_getSearchBoxItemData')) {
    function rs_indiebooking_template_getSearchBoxItemData($big = true) {
        global $RSBP_DATABASE;
        
        $postId         = get_the_ID();
        $from           = "";
        $to             = "";
        $anzPerson      = 0;
        $anzBetten      = 0;
        $selCategory    = 0;
        $selFeatures	= 0;
        $selAnzBed      = 0;
        $selNrGuest     = 0;
        $selAnzRoom     = 0;
        $searchLoc      = "";
        
        $from           = rsbp_getPostValue('search_booking_date_from', "", RS_IB_Data_Validation::DATATYPE_DATUM);
        $to             = rsbp_getPostValue('search_booking_date_to', "", RS_IB_Data_Validation::DATATYPE_DATUM);
        $selCategory    = rsbp_getPostValue('search_category', "");
        $selFeatures    = rsbp_getPostValue('search_features', "");
        $selAnzBed      = rsbp_getPostValue('search_nr_of_beds', 0, RS_IB_Data_Validation::DATATYPE_INTEGER);
        $selNrGuest     = rsbp_getPostValue('search_nr_of_guest', 0, RS_IB_Data_Validation::DATATYPE_INTEGER);
        $searchLoc      = rsbp_getPostValue('search_location', "", RS_IB_Data_Validation::DATATYPE_TEXT);
        $selAnzRoom     = rsbp_getPostValue('search_nr_of_rooms', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
        
        $allOptions                     = array();
        $kategorieTbl                   = $RSBP_DATABASE->getTable(RS_IB_Model_Appartmentkategorie::RS_TABLE);
        $apartmentTbl                   = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        //TODO auslagern!
        if (class_exists("RS_IB_Table_Appartmentoption")) {
            $optionTable                = RS_IB_Table_Appartmentoption::instance();
            $allOptions                 = $optionTable->getAllOptions();
        }
        
        $apartmentBuchungTbl            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $zeitraumTable                  = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Zeitraeume::RS_TABLE);
        $fullyBookedDaysObjs            = $apartmentBuchungTbl->getFullBookedDays();
        $fullyBookedDays                = array();
        $freeBookingRanges              = array();
        if ($big) {
            foreach ($fullyBookedDaysObjs as $fullyBookedDaysObj) {
                $datetimeobj                = new DateTime($fullyBookedDaysObj->datum);
                if ($datetimeobj instanceof DateTime) {
                    array_push($fullyBookedDays, $datetimeobj->format('Y-m-d'));
                }
            }
            $freeBookingRanges          = $zeitraumTable->loadGlobalVerfuegbareZeitraueme();
        }
        
        /*
         * ermittle die verfuegbaren zeitraueme aller apartments
         */
        $minDate 			= new DateTime("now");
        $allApartments 		= $apartmentBuchungTbl->getAllAppartments();
        $apartmentDates 	= array();
        $checkedApartments 	= array();
        foreach ($allApartments as $apartmentPostId) {
        	$appartment_id	= apply_filters('rs_indiebooking_get_original_apartment_id_from_wpml', $apartmentPostId);
        	if (!in_array($appartment_id, $checkedApartments)) {
        		$availableDates = $apartmentTbl->getAvailableDates($appartment_id, $minDate);
	        	$apartmentDates[$apartmentPostId] = $availableDates;
	        	array_push($checkedApartments, $appartment_id);
        	}
        }
        
        $filterData                     = get_option( 'rs_indiebooking_settings_filter');
        $options                        = get_option( 'rs_indiebooking_settings' );
        if ($options) {
            $icon_image_id              = (key_exists('icon_image_id', $options)) ?  esc_attr__( $options['icon_image_id'] ) : "";
        } else {
            $icon_image_id              = "";
        }
        $icon_image_url                 = "";
        if (!is_null($icon_image_id) && $icon_image_id !== "" && $icon_image_id > 0) {
            $icon_image                 = wp_get_attachment_image_src( $icon_image_id, 'small' );
            $icon_image_url             = $icon_image[0];
        }
        $settingsFilterCategoryKz       = "";
        $settingsFilterAnzBettenKz      = "";
        $settingsFilterAnzPersonenKz    = "";
        $settingsFilterOptions          = "";
        $hiddenFilterCategory           = "";
        $hiddenFilterAnzBetten          = "";
        $hiddenFilterAnzZimmer          = "";
        $hiddenFilterAnzPersonen        = "";
        $hiddenFilterOptions            = "";
        $hiddenFilterRegion             = "";
        $settingsFilterAnzZimmerKz		= "";
        $settingsFilterRegionKz			= "";
        $hiddenFilterFeatures			= "";
        if ($filterData) {
            $settingsFilterCategoryKz    = (key_exists('category_kz', $filterData))        ?  esc_attr__( $filterData['category_kz'] )        : "";
            $settingsFilterFeaturesKz    = (key_exists('features_kz', $filterData))        ?  esc_attr__( $filterData['features_kz'] )        : "";
            $settingsFilterAnzBettenKz   = (key_exists('anzahl_betten_kz', $filterData))   ?  esc_attr__( $filterData['anzahl_betten_kz'] )   : "";
            $settingsFilterAnzPersonenKz = (key_exists('anzahl_personen_kz', $filterData)) ?  esc_attr__( $filterData['anzahl_personen_kz'] ) : "";
            $settingsFilterOptions       = (key_exists('options_kz', $filterData))         ?  esc_attr__( $filterData['options_kz'] )         : "";
            $settingsFilterAnzZimmerKz   = (key_exists('rooms_kz', $filterData))           ?  esc_attr__( $filterData['rooms_kz'] )         : "";
            $settingsFilterRegionKz      = (key_exists('region_kz', $filterData))          ?  esc_attr__( $filterData['region_kz'] )         : "";
        }
        $allCategories  = $kategorieTbl->loadAllCategories();
        $maxAnzBetten   = $apartmentTbl->loadMaxAnzahlBetten();
        $maxAnzPersonen = $apartmentTbl->loadMaxAnzahlPersonen();
        $maxAnzZimmer   = $apartmentTbl->loadMaxAnzahlZimmer();
        $locationdesc   = $apartmentTbl->loadAllLocations();
        
        $allFeatures	= array();
        if (class_exists("indiebooking_apartment_features")) {
	        $features		= $apartmentTbl->loadAllFeatures();
	        foreach ($features as $feat) {
	        	$allFeatures[$feat] = indiebooking_apartment_features::get_feature_tooltip($feat);
	        }
        } else {
        	$settingsFilterFeaturesKz 	= "off";
        	$features 					= array();
        }
        if ($settingsFilterCategoryKz === "off"|| (is_null($allCategories)) || sizeof($allCategories) <= 0) {
            $hiddenFilterCategory        = 'hidden = "hidden"';
        }
        if ($settingsFilterFeaturesKz === "off"|| (is_null($allFeatures)) || sizeof($allFeatures) <= 0) {
        	$hiddenFilterFeatures        = 'hidden = "hidden"';
        }
        if ($settingsFilterAnzBettenKz === "off") {
            $hiddenFilterAnzBetten       = 'hidden = "hidden"';
        }
        if ($settingsFilterAnzZimmerKz === "off" || $settingsFilterAnzZimmerKz === "") {
            $hiddenFilterAnzZimmer       = 'hidden = "hidden"';
        }
        if ($settingsFilterAnzPersonenKz === "off" || (is_null($maxAnzPersonen)) || $maxAnzPersonen <= 0) {
            $hiddenFilterAnzPersonen     = 'hidden = "hidden"';
        }
        if ($settingsFilterOptions === "off") {
            $hiddenFilterOptions         = 'hidden = "hidden"';
        }
        if ($settingsFilterRegionKz === "off" || $settingsFilterRegionKz === "" || (is_null($locationdesc)) || sizeof($locationdesc) <= 0) {
            $hiddenFilterRegion         = 'hidden = "hidden"';
        }
        
        //         var_dump($allCategories);
        $categories     = array();
        array_push($categories, 0);
        foreach ($allCategories as $categorieTerm) {
            array_push($categories, $categorieTerm->name);
        }
        if (!is_null($selAnzBed) && $selAnzBed !== '') {
            $selAnzBed  = explode(",", $selAnzBed);
        } else {
        	$selAnzBed  = array();
        }
        if (!is_null($selAnzRoom) && $selAnzRoom !== '') {
            $selAnzRoom  = explode(",", $selAnzRoom);
        } else {
        	$selAnzRoom  = array();
        }
        if (!is_null($selCategory) && $selCategory !== '') {
            $selCategory  = explode(",", $selCategory);
        } else {
        	$selCategory  = array();
        }
        if (!is_null($selFeatures) && $selFeatures !== '') {
        	$selFeatures  = explode(",", $selFeatures);
        } else {
        	$selFeatures  = array();
        }
        $args = array(
            'postId'            => $postId,
            'from'              => $from,
            'to'                => $to,
            'categories'        => $categories,
            'maxAnzBetten'      => $maxAnzBetten,
            'maxAnzZimmer'      => $maxAnzZimmer,
            'maxAnzPersonen'    => $maxAnzPersonen,
            'hiddenCategory'    => $hiddenFilterCategory,
            'hiddenAnzBetten'   => $hiddenFilterAnzBetten,
            'hiddenAnzZimmer'   => $hiddenFilterAnzZimmer,
            'hiddenAnzPersonen' => $hiddenFilterAnzPersonen,
        	'hiddenFilterFeatures' => $hiddenFilterFeatures,
            'hiddenOptions'     => $hiddenFilterOptions,
            'hiddenRegion'      => $hiddenFilterRegion,
            'allOptions'        => $allOptions,
            'selCategory'       => $selCategory,
        	'selFeatures'		=> $selFeatures,
            'selAnzBed'         => $selAnzBed,
            'selAnzRoom'        => $selAnzRoom,
            'selNrGuest'        => $selNrGuest,
            'fullyBookedDays'   => $fullyBookedDays,
            'freeBookingRanges' => $freeBookingRanges,
            'locationdesc'      => $locationdesc,
        	'features'			=> $allFeatures,
            'searchLoc'         => $searchLoc,
            'icon_image_url'    => $icon_image_url,
        	'apavailabledates'  => $apartmentDates,
        );
        return $args;
    }
}

/* @var $categorieTerm WP_TERM */
/* @var $apartmentTbl RS_IB_Table_Appartment */
if ( ! function_exists( 'rs_indiebooking_template_search_appartment_box' ) ) {
    function rs_indiebooking_template_search_appartment_box() {
        $args = rs_indiebooking_template_getSearchBoxItemData(true);
        $args['startpagenumber'] = 1;
        cRS_Template_Loader::rs_ib_get_template('appartment_suche/appartment_suche_box.php', $args);
    }
}

if ( ! function_exists( 'rs_indiebooking_template_search_appartment_box_2' ) ) {
	function rs_indiebooking_template_search_appartment_box_2() {
		$args = rs_indiebooking_template_getSearchBoxItemData(true);
		$args['startpagenumber'] = 2;
		cRS_Template_Loader::rs_ib_get_template('appartment_suche/appartment_suche_box.php', $args);
	}
}

if ( ! function_exists( 'rs_indiebooking_template_search_appartment_box_small' ) ) {
    function rs_indiebooking_template_search_appartment_box_small() {
        $args = rs_indiebooking_template_getSearchBoxItemData(true);
        cRS_Template_Loader::rs_ib_get_template('appartment_suche/appartment_suche_box_small.php', $args);
    }
}

if ( ! function_exists( 'rs_indiebooking_template_single_appartment_buchung_contact' ) ) {
    function rs_indiebooking_template_single_appartment_buchung_contact($buchungId = null, $disabled = "") {
        RS_IB_Template_Buchungsanzeige::show_buchung_contact($buchungId, $disabled);
    }
}

if ( ! function_exists( 'rs_indiebooking_template_single_appartment_buchung_zahlungsart' ) ) {
    function rs_indiebooking_template_single_appartment_buchung_zahlungsart($buchungId = null) {
        RS_IB_Template_Buchungsanzeige::show_buchung_zahlungsarten($buchungId);
    }
}

if ( ! function_exists( 'rs_indiebooking_template_single_appartment_buchung_payment_button' )) {
    function rs_indiebooking_template_single_appartment_buchung_payment_button($buchungId = null) {
        RS_IB_Template_Buchungsanzeige::show_buchung_zahlungsartbutton($buchungId);
    }
}


if ( ! function_exists( 'rs_indiebooking_single_rsappartment_buchung_header' ) ) {
    function rs_indiebooking_single_rsappartment_buchung_header($pagekz, $postId = null) {
//         default_header_data();
		/*
		 * Update Carsten Schmitt 01.10.2018
		 * Damit beim aktualisieren der Seite (F5) auch der Heartbeat aktualisiert wird, pruefe
		 * ich an dieser Stelle den Status der Buchung und aktualisieren den Heartbeat so oder so.
		 * Ansonsten kann es dazu kommen, dass eine Buchung, die im falschen Moment aktualisiert wird
		 * (oder oefter hintereinander) keine Moeglichkeit hat den Heartbeat korrekt zu senden
		 * und somit abgebrochen wird, obwohl der Kunde noch am buchen ist.
		 */
		$postStatus = get_post_status($postId);
		if ($postStatus != "rs_ib-canceled" && $postStatus != "rs_ib-out_of_time") {
	    	update_post_meta($postId, RS_IB_Model_Appartment_Buchung::BUCHUNG_CURRENT_PAGEKZ, $pagekz);
// 	    	$doHeartbeat = get_post_meta($postId, RS_IB_Model_Appartment_Buchung::BUCHUNG_DO_HEARTBEAT, true);
// 	    	if ($doHeartbeat == 0) {
// 	    		RS_Indiebooking_Log_Controller::write_log("reset / activate heartbeat ".$postId);
	    		update_post_meta($postId, RS_IB_Model_Appartment_Buchung::BUCHUNG_LAST_HEARTBEAT, time());
	    		update_post_meta($postId, RS_IB_Model_Appartment_Buchung::BUCHUNG_DO_HEARTBEAT, 1);
// 	    	}
		}
        $active1 = '';
        $active2 = '';
        $active3 = '';
        $active4 = '';
        $biggestPageKz = 0;
        if (!is_null($postId)) {
            $biggestPageKz = get_post_meta($postId, RS_IB_Model_Appartment_Buchung::BUCHUNG_BIGGEST_PAGEKZ, true);
        } else {
            $biggestPageKz = 0;
        }
        if ($pagekz == 1) {
            $active1 = 'active';
        } elseif ($pagekz == 2) {
            $active2 = 'active';
        } elseif ($pagekz == 3) {
            $active3 = 'active';
        }  elseif ($pagekz == 4) {
            $active4 = 'active';
        }

        $args = array(
            'pagekz'        => $pagekz,
            'biggestPagekz' => $biggestPageKz,
            'active1'       => $active1,
            'active2'       => $active2,
            'active3'       => $active3,
            'active4'       => $active4,
        );
        cRS_Template_Loader::rs_ib_get_template('single-appartment-buchung/appartment_buchung_header.php', $args);
    }
}

if ( ! function_exists( 'rs_indiebooking_single_rsappartment_buchung_start' ) ) {
    function rs_indiebooking_single_rsappartment_buchung_start($postId, $returnedFromPayPal = false) {
        global $RSBP_DATABASE;
        
        if (!isset($postId) || $postId == 0) {
            $postId                 = get_the_ID();
        }
        if ($returnedFromPayPal) {
        	//wird jetzt in content-single-rsappartment-buchung.php gemacht
        	//nicht optimal. bleibt aber erst mal so
        	
//             $myPost         = array(
//                 "ID"        => $postId,
//                 "post_status" => 'rs_ib-booking_info', //'rs_ib-blocked',
//             );
//             if (!is_null($myPost)) {
//                 wp_update_post($myPost);
//             }
//             update_post_meta($postId, RS_IB_Model_Appartment_Buchung::BUCHUNG_BIGGEST_PAGEKZ, 1);
        }
        $buchungTable               = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        
        $buchung                    = $buchungTable->getAppartmentBuchung($postId);
//         $buchung                    = $buchungTable->getPositions($buchung);
        $buchungKopf                = $buchungTable->loadBuchungskopf($buchung->getBuchungKopfId());
        $appartment                 = $appartmentTable->getAppartment($buchung->getAppartment_id());
        
        $args = array(
            'appartment'    => $appartment,
            'buchung'       => $buchung,
            'buchungKopf'   => $buchungKopf,
        );
        
//         default_booking_header_data($buchung);
        do_action("rs_indiebooking_show_default_booking_header_data", $buchung);
        cRS_Template_Loader::rs_ib_get_template('single-appartment-buchung/content-single-rsappartment_buchung_start.php', $args);
    }
}

if (! function_exists("rs_indiebooking_template_single_appartment_buchung_contact_data")) {
    function rs_indiebooking_template_single_appartment_buchung_contact_data($postId, $disabled) {
        RS_IB_Template_Buchungsanzeige::show_buchung_contact_data($postId, $disabled);
    }
}

if ( ! function_exists( 'rs_indiebooking_template_single_appartment_buchung_appartment_list' ) ) {
    function rs_indiebooking_template_single_appartment_buchung_appartment_list($buchungId = null, $disabled = "") {
        if (is_null($buchungId) || $buchungId == "") {
            $postType       = get_post_type( get_the_ID() );
            if ($postType == "rsappartment_buchung") {
                $buchungId  = get_the_ID();
            }
        }
        RS_IB_Template_Buchungsanzeige::show_buchung_appartment_list($buchungId, $disabled);
    }
}

if ( ! function_exists( 'rs_indiebooking_single_rsappartment_buchung_almost_booked' ) ) {
    function rs_indiebooking_single_rsappartment_buchung_almost_booked($buchungId = null) {
        global $RSBP_DATABASE;
        
        if (!is_null($buchungId)) {
            $postId                 = $buchungId;
        } else {
            $postId                 = get_the_ID();
        }
        $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $buchung                    = $appartmentBuchungsTable->getAppartmentBuchung($postId);
//         $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
//         $appartment                 = $appartmentTable->getAppartment($buchung->getAppartment_id());
//         $aktionen                   = $appartment->getAktionen();
//         $buchung->setAktionen($aktionen);
//         $buchung                    = $appartmentBuchungsTable->getPositions($buchung);
        $args = array(
//             'appartment'    => $appartment,
            'buchung'       => $buchung,
        );
//         default_booking_header_data($buchung);
        cRS_Template_Loader::rs_ib_get_template('single-appartment-buchung/content-single-rsappartment_buchung_uebersicht.php', $args);
    }
}

if ( ! function_exists( 'rs_indiebooking_single_rsappartment_buchung_final' ) ) {
	/* @var $buchungsKopf RS_IB_Model_Buchungskopf */
    function rs_indiebooking_single_rsappartment_buchung_final($buchungId = null) {
        global $RSBP_DATABASE;
        if (!is_null($buchungId)) {
            $postId                 = $buchungId;
        } else {
            $postId                 = get_the_ID();
        }
        $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $buchung                    = $appartmentBuchungsTable->getAppartmentBuchung($postId);
        $buchungKopfId              = $buchung->getBuchungKopfId();
        
        $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        $buchungKopfTbl     		= $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
        $buchungsKopf       		= $buchungKopfTbl->loadBooking($buchung->getBuchungKopfId(), false);
        $appartment                 = $appartmentTable->getAppartment($buchung->getAppartment_id());
        $aktionen                   = $appartment->getAktionen();

        $inquiry					= false;
        $inquiry					= ($buchungsKopf->getBuchung_status() == "rs_ib-requested");
        $buchung->setAktionen($aktionen);
        $buchung                    = $appartmentBuchungsTable->getPositions($buchung);
        $args = array(
            'appartment'    	=> $appartment,
            'buchung'       	=> $buchung,
            'buchungNr'     	=> $buchungKopfId,
        	'inquiry'			=> $inquiry,
        );
//         default_booking_header_data($buchung);
        do_action("rs_indiebooking_show_default_booking_header_data", $buchung);
        cRS_Template_Loader::rs_ib_get_template('single-appartment-buchung/content-single-rsappartment_buchung_final.php', $args);
    }
}

// if ( ! function_exists( 'rs_indiebooking_template_single_appartment_buchung_options' ) ) {
//     function rs_indiebooking_template_single_appartment_buchung_options($buchungPostId) {
//         global $RSBP_DATABASE;
//         if (!isset($buchungPostId)) {
//             $buchungPostId          = get_the_ID();
//         }
// //         $postId                     = get_the_ID();
//         $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
//         $buchung                    = $appartmentBuchungsTable->getAppartmentBuchung($buchungPostId);
//         $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
//         $appartment                 = $appartmentTable->getAppartment($buchung->getAppartment_id());
//         $aktionen                   = $appartment->getAktionen();

//         $allMwst                    = array();
//         $mwstTable                  = $RSBP_DATABASE->getTable(RS_IB_Model_Mwst::RS_TABLE);
//         $allMwst                    = $mwstTable->getAllMwsts();

//         $buchung->setAktionen($aktionen);
//         $buchung                    = $appartmentBuchungsTable->getPositions($buchung);
//         $args = array(
//             'allMwst'       => $allMwst,
//             'appartment'    => $appartment,
//             'buchung'       => $buchung,
//         );
//         cRS_Template_Loader::rs_ib_get_template('single-appartment-buchung/appartment_buchung_options.php', $args);
//     }
// }

if (! function_exists( 'rs_indiebooking_single_rsappartment_buchung_not_found' ) ) {
    function rs_indiebooking_single_rsappartment_buchung_not_found() {
        $args = array(
        	'reloaddirect' => 0,
        );
        cRS_Template_Loader::rs_ib_get_template('single-appartment-buchung/appartment_buchung_not_found.php', $args);
    }
}

if (! function_exists( 'rs_indiebooking_single_rsappartment_buchung_not_found2' ) ) {
	function rs_indiebooking_single_rsappartment_buchung_not_found2() {
		$args = array(
			'reloaddirect' => 1,
		);
		cRS_Template_Loader::rs_ib_get_template('single-appartment-buchung/appartment_buchung_not_found2.php', $args);
	}
}

if ( ! function_exists( 'rs_indiebooking_template_single_appartment_buchung_countdown' ) ) {
    function rs_indiebooking_template_single_appartment_buchung_countdown($buchungPostId) {
        global $RSBP_DATABASE;
        
        if (!isset($buchungPostId)) {
            $buchungPostId          = get_the_ID();
        }
        $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $buchung                    = $appartmentBuchungsTable->getAppartmentBuchung($buchungPostId);
        $remainingTime              = $buchung->getRemainingtTime();
        $args = array(
            'remainingTime' => $remainingTime,
        );
        
        cRS_Template_Loader::rs_ib_get_template('single-appartment-buchung/appartment_buchung_countdown.php', $args);
    }
}

if ( ! function_exists( 'rs_indiebooking_single_rsappartment_buchung_time_range' ) ) {
    function rs_indiebooking_single_rsappartment_buchung_time_range($postId) {
        RS_IB_Template_Buchungsanzeige::show_buchung_appartment_zeitraeume($postId);
    }
}

if ( ! function_exists( 'rs_indiebooking_rsappartment_show_first_page_welcome' ) ) {
    function rs_indiebooking_rsappartment_show_first_page_welcome() {
        $args = array();
        cRS_Template_Loader::rs_ib_get_template('indiebooking_first_page_welcome.php', $args);
    }
}

if ( ! function_exists( 'rs_indiebooking_single_rsappartment_buchung_detail_payment' ) ) {
    function rs_indiebooking_single_rsappartment_buchung_detail_payment($postId) {
        RS_IB_Template_Buchungsanzeige::rs_indiebooking_single_rsappartment_buchung_detail_payment($postId);
    }
}

if ( ! function_exists( 'rs_indiebooking_single_rsappartment_buchung_full_prices' ) ) {
    /* @var $buchungKopfTbl RS_IB_Table_Buchungskopf */
    /* @var $buchungKopf    RS_IB_Model_Buchungskopf */
    function rs_indiebooking_single_rsappartment_buchung_full_prices($postId) {
        global $RSBP_DATABASE;
        
        if (!isset($postId)) {
            $postId                 = get_the_ID();
        }
        $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $buchungKopfTbl             = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
//         $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        
        //$postId != buchungnr
        $buchung                    = $appartmentBuchungsTable->getAppartmentBuchung($postId);
//         $buchung                    = $appartmentBuchungsTable->getPositions($buchung);
        $buchungKopf                = $buchungKopfTbl->loadBooking($buchung->getBuchungKopfId());
//         $appartment                 = $appartmentTable->getAppartment($buchung->getAppartment_id());
        $args = array(
//             'appartment'    => $appartment,
            'buchungObj'    => $buchung,
            'buchungKopf'   => $buchungKopf,
        );
        cRS_Template_Loader::rs_ib_get_template('single-appartment-buchung/appartment_buchung_full_prices.php', $args);
    }
}

if (! function_exists(('rs_indiebooking_changePostBookingStatusAction'))) {
	/* @var $buchungTable RS_IB_Table_Buchungskopf */
    function rs_indiebooking_changePostBookingStatusAction($post_ID, $post_after, $post_before) {
        global $RSBP_DATABASE;
        
        if ($post_after->post_type == RS_IB_Appartment_Buchung_post_type::POST_TYPE_NAME) {
            $buchungTable           = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
            $buchungKopfTable       = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
            
            $buchung                = $buchungTable->getAppartmentBuchung($post_ID);
            RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."] "."changebookingstatus frontend to from ".$post_before->post_status." to ".$post_after->post_status);
            $buchungskopf           = $buchungTable->loadBuchungskopf($buchung->getBuchungKopfId());
            $buchungskopf->setBuchung_status($post_after->post_status);
            $buchungKopfTable->saveOrUpdateBuchungskopf($buchungskopf);
        }
    }
}

if (!function_exists('rs_indiebooking_is_apartment_bookable')) {
    function rs_indiebooking_is_apartment_bookable($post_ID) {
        global $RSBP_DATABASE;
        
        $isBookable                 = true;
        $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        $appartment                 = $appartmentTable->getAppartment($post_ID);
        $isBookable                 = $appartment->isApartmentBookable();

        return $isBookable;
    }
}