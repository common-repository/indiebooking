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
// if ( ! class_exists( 'RS_IB_Admin_Booking' ) ) :
class RS_IB_Admin_Booking
{
    public function __construct() {
        $this->includes();
        $this->init_hooks();
    }
    
    
    private function init_hooks() {
        $this->initBookingHooks();
    }
    
    private function includes() {
        include_once 'view/RS_IB_Booking_Overview.php';
        include_once 'view/RS_IB_Booking_View.php';
//         include_once 'printBooking.php';
    }
    
    private function initBookingHooks() {
        add_action('admin_init', array($this, 'addAppartmentBuchungCustomFields'));
//         add_action('admin_init', array($this, 'addCustomHTML'));
        
        add_action( 'create_rsappartment_buchung', array($this, 'addCustomHTML'));
        //https://developer.wordpress.org/reference/hooks/edit_taxonomy/
        add_action( 'edit_rsappartment_buchung', array($this, 'addCustomHTML'));
        
        add_action('do_meta_boxes', array($this, 'remove_metaboxes'));
//         add_action('trashed_post', array($this, 'changePostBookingStatusAction'), 10, 1);
//         add_action('untrashed_post', array($this, 'changePostBookingStatusAction'), 10, 1);
        add_action('post_updated', array($this, 'changePostBookingStatusAction'), 10, 3);
        
//         add_action('restrict_manage_posts', array($this, 'addCancelDialog'));
        //TODO darf eigentlich auch nur fuer den Adminbereich gelten!
        configureBookingOverview();
    }
    
    public function addAppartmentBuchungCustomFields() {
        add_meta_box("bookedDate-meta", __('Booking details', 'indiebooking'), array($this, "booked_date"),
                        "rsappartment_buchung", "normal", "low"); //side
    }

    public function remove_metaboxes() {
        remove_meta_box('submitdiv', 'rsappartment_buchung', 'side');
        remove_meta_box('submitdiv', 'rsappartment_buchung', 'normal');
        remove_meta_box('postimagediv', 'rsappartment_buchung', 'side');
        remove_meta_box('postimagediv', 'rsappartment_buchung', 'normal');
        remove_meta_box('postcustom', 'rsappartment_buchung', 'side');
        remove_meta_box('postcustom', 'rsappartment_buchung', 'normal');
    }
    
    /* @var $buchungKopfTable RS_IB_Table_Buchungskopf */
    public function changePostBookingStatusAction($post_ID, $post_after, $post_before) {
//         global $post;
        global $RSBP_DATABASE;
        
        if ($post_after->post_type == RS_IB_Appartment_Buchung_post_type::POST_TYPE_NAME) {
            $buchungTable           = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
            $buchungKopfTable       = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
            
            $buchung                = $buchungTable->getAppartmentBuchung($post_ID);
//             RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."] "."changebookingstatus to from ".
//                                                             $post_before->post_status." to ".$post_after->post_status);
            $buchungskopf           = $buchungTable->loadBuchungskopf($buchung->getBuchungKopfId());
            $buchungskopf->setBuchung_status($post_after->post_status);
            $buchungKopfTable->saveOrUpdateBuchungskopf($buchungskopf);
        }
    }
    
    public function addCustomHTML() {
        global $pagenow;
        
        if ( 'edit.php' == $pagenow) {
            echo '<div class="modal"></div>';
            if (isset($_GET['post_type'])) {
                $posttype   = rsbp_getGetValue('post_type', '', RS_IB_Data_Validation::DATATYPE_TEXT);
                if (RS_IB_Model_Appartment_Buchung::RS_POSTTYPE == $posttype) {
                    $this->showCancelDialog();
                }
            }
        } elseif ('post.php' == $pagenow && isset($_GET['post'])) {
            echo '<div class="modal"></div>';
            $post_type      = rsbp_getGetValue('post', '', RS_IB_Data_Validation::DATATYPE_TEXT);
            if (RS_IB_Model_Appartment_Buchung::RS_POSTTYPE == $post_type) {
                $this->showCancelDialog();
            }
        }
    }
    
    private function showCancelDialog() {
        echo '<div id="cancel_booking_dialog" title="'.__("Cancel Booking", 'indiebooking').'">
                <p>'._x("Are you sure you want to cancel the booking?", "backendBooking", 'indiebooking').'</p>
            </div>';
    }
    
    /* @var $buchungKopf RS_IB_Model_Buchungskopf */
    /* @var $buchungTable RS_IB_Table_Appartment_Buchung */
    /* @var $oberbuchungTable RS_IB_Table_Oberbuchungkopf */
    public function booked_date() {
        global $post;
        global $RSBP_DATABASE;
        $buchungTable           = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $oberbuchungTable       = $RSBP_DATABASE->getTable(RS_IB_Model_Oberbuchungkopf::RS_TABLE);
        $appartmentTable        = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        $buchung                = $buchungTable->getAppartmentBuchung($post->ID);
//         $appartment             = $appartmentTable->getAppartment($buchung->getAppartment_id());
        
//         $aktionen               = $appartment->getAktionen();
        	
//         $buchung->setAktionen($aktionen);

        //TODO testen ob die nuechste Zeile wirklich weg kann oder wie sie ersetzt werden muss
//         $buchung                = $buchungTable->getPositions($buchung);

        $btnEditContactDataTxt 	= __("Edit contact data", 'indiebooking');
        $buchungKopf            = $buchungTable->loadBuchungskopf($buchung->getBuchungKopfId());
        $buchungsStatus         = get_post_status($post->ID);
        //rs_ib-blocked
        //rs_ib-booked
        if ($buchungsStatus == 'rs_ib-almost_booked') {
//             $qm                 = $appartment->getQuadratmeter();
//             $aktionen           = $appartment->getAktionen();
//             $options            = $buchung->getOptions();
//             $fromDate           = $buchung->getStartDate();
//             $toDate             = $buchung->getEndDate();
//             $bookingPrices      = $buchung->getBookingPrices();
//             $dtFromdate         = DateTime::createFromFormat("d.m.Y", $fromDate);
//             $dtTodate           = DateTime::createFromFormat("d.m.Y", $toDate);
//             $anzahlTage         = date_diff($dtFromdate, $dtTodate, true);
//             $anzahlNaechte      = intval($anzahlTage->format('%a'));
//             $coupons                    = $buchung->getCoupons();
            
//             $square             = $appartment->getQuadratmeter();
//             $yearPrices         = $bookingPrices['year'];
//             $yearlessPrices     = $bookingPrices['yearless'];
//             $qm                 = $appartment->getQuadratmeter();
            
//             $priceIsNet         = $appartment->getPriceIsNet(); //TODO nicht pro Appartment speichern
//             $allPrices          = rs_ib_price_calculation_util::calculateYearPrices($yearPrices, $priceIsNet, $dtFromdate, $dtTodate);
//             $calculatedOptions  = rs_ib_price_calculation_util::calculateOptionPrices($options, $anzahlNaechte, $priceIsNet, $qm);
//             $allPrices          = rs_ib_price_calculation_util::calculateBookingPrices($dtFromdate, $dtTodate, $priceIsNet, $yearPrices, $yearlessPrices, $coupons, $options, $aktionen, $square);
//             $calcFullPrices     = array();
        } elseif ($buchungsStatus == 'rs_ib-booked') {
            $allPrices          = $buchung->getBookingPrices();
            $calculatedOptions  = $buchung->getOptions();
            $calcFullPrices     = array(); //$buchung->getFullBookingPrices();
        } else {
            $allPrices          = array();
            $calculatedOptions  = array();
            $calcFullPrices     = array();
        }
//         showBooking($buchung);
        RS_IB_Booking_View::showBookingHeadInformation($buchung, $buchungKopf);
        
        //MUSS AUCH IN DEN STORNODRUCK!!!
        if ($buchungKopf->getBuchung_status() == "rs_ib-storno" || $buchungKopf->getBuchung_status() == "rs_ib-storno_paid") {
            $oberbuchungObj     = $oberbuchungTable->loadBookingByRechnungnr($buchungKopf->getRechnung_nr());
            RS_IB_Booking_View::showOberBooking($buchungKopf, $buchung, $oberbuchungObj);
        } else {
            RS_IB_Booking_View::showBooking($buchung, $buchungKopf);
        }
        if ($buchungKopf->getBcomBookingKz() == 0) {
	        if ($buchungsStatus !== 'rs_ib-canceled' && $buchungsStatus !== 'rs_ib-out_of_time' && $buchungsStatus !== 'rs_ib-storno'
	        		&& $buchungsStatus !== 'rs_ib-storno_paid') {
	        		
	        	$cancelBookingTxt 		= __("cancel booking", 'indiebooking');
	        	$cancelQuestion			= __("Are you sure you want to cancel the booking?", 'indiebooking');
	        	if ($buchungsStatus == 'rs_ib-booked' || $buchungsStatus == 'rs_ib-pay_confirmed') {
	        		$cancelBookingTxt 	= _x("cancel booking", "storno", 'indiebooking');
	        		$cancelQuestion		= _x("Are you sure you want to cancel the booking?", "storno", 'indiebooking');
	        	} else if ($buchungsStatus == 'rs_ib-requested') {
	        		$cancelBookingTxt 	= _x("cancel inquiry", "inquiry", 'indiebooking');
	        		$cancelQuestion		= _x("Are you sure you want to cancel the inquiry?", "inquiry", 'indiebooking');
	        	}
	            echo '<span class="right_button">
	                    <a id="btnBuchungStornierenBtn" data-postid="'.$post->ID.'" data-cancelTitle="'.$cancelQuestion.'" class="btnBuchungStornieren ibui_add_btn">'.$cancelBookingTxt. '</a>
	                  </span>';
	        }
	        if ($buchungsStatus == 'rs_ib-booked' || $buchungsStatus == 'rs_ib-pay_confirmed') {
	        	if ($buchungsStatus !== 'rs_ib-pay_confirmed' && $buchung->getPost_status() !== 'rs_ib-storno'
	        			&& $buchung->getPost_status() !== 'rs_ib-storno_paid') {
	        				if ($buchungKopf->getAnzahlungBezahlt() == 0) {
	        					echo '<span class="right_button">
				                    <a id="btnAnzahlungBestaetigenBtn" data-postid="'.$post->ID.'" class="btnAnzahlungBestaetigen ibui_add_btn">'.__("Confirm deposit", 'indiebooking') . '</a>
				                  </span>';
	        				} else {
	        					echo '<span class="right_button">
				                    <a id="btnZahlungBestaetigenBtn" data-postid="'.$post->ID.'" class="btnZahlungBestaetigen ibui_add_btn">'.__("Confirm payment", 'indiebooking') . '</a>
				                  </span>';
	        				}
	        				
	            } else {
	                echo '<span class="right_button">
	                    <a class="ibui_add_btn ibui_add_btn_disable">'.__("Confirm payment", 'indiebooking') . '</a>
	                  </span>';
	            }
	//             __("Print booking confirmation", 'indiebooking')
	//             $btnPrintBuchungsbestaetigungTxt = __("Print booking confirmation", 'indiebooking');
	            if ($buchungKopf->getRechnung_nr() != "" && $buchungKopf->getRechnung_nr() > 0) {
	            	$btnPrintBuchungsbestaetigungTxt = __("Print Bill", 'indiebooking');
	            } else {
	            	$btnPrintBuchungsbestaetigungTxt = _x("Print Bill", "ib_firstprint", 'indiebooking');
	            }
	            echo '<span class="right_button">
	                    <a id="btnPrintBuchungsbestaetigung" class="ibui_add_btn">'.$btnPrintBuchungsbestaetigungTxt. '</a>
	                  </span>';
	        } elseif($buchungsStatus === 'rs_ib-storno' || $buchungsStatus === 'rs_ib-storno_paid') {
	            $btnPrintBuchungsbestaetigungTxt 	= __("Print Storno Bill", 'indiebooking');
	            
	            echo '<span class="right_button">
	                    <a id="btnPrintBuchungsbestaetigung" class="ibui_add_btn">'.$btnPrintBuchungsbestaetigungTxt. '</a>
	                  </span>';
	        }
        } else {
        	if ($buchungsStatus !== 'rs_ib-pay_confirmed' && $buchung->getPost_status() !== 'rs_ib-storno'
        		&& $buchung->getPost_status() !== 'rs_ib-storno_paid') {
        			echo '<span class="right_button">
	                    <a id="btnZahlungBestaetigenBtn" data-postid="'.$post->ID.'" class="btnZahlungBestaetigen ibui_add_btn">'.__("Confirm payment", 'indiebooking') . '</a>
	                  </span>';
			} else {
	        	echo '<span class="right_button">
		                    <a class="ibui_add_btn ibui_add_btn_disable">'.__("Confirm payment", 'indiebooking') . '</a>
		                  </span>';
        	}
        	if ($buchungKopf->getRechnung_nr() != "" && $buchungKopf->getRechnung_nr() > 0) {
        		$btnPrintBuchungsbestaetigungTxt = __("Print Bill", 'indiebooking');
        	} else {
        		$btnPrintBuchungsbestaetigungTxt = _x("Print Bill", "ib_firstprint", 'indiebooking');
        	}
        	
        	echo '<span class="right_button">
	                    <a id="btnPrintBuchungsbestaetigung" class="ibui_add_btn">'.$btnPrintBuchungsbestaetigungTxt. '</a>
	                  </span>';
        	
        }
        if (is_plugin_active('indiebooking-advanced/indiebooking_advanced.php')) {
	        if ($buchungsStatus == 'rs_ib-booked' || $buchungsStatus == 'rs_ib-pay_confirmed' || $buchungKopf->getBcomBookingKz() != 0) {
		        if ($buchungKopf->getRechnung_nr() == 0) {
			    	echo '<span class="right_button">
				                <a id="btnEditBookingContactData" class="ibui_add_btn">'.$btnEditContactDataTxt. '</a>
				              </span>';
		        } else {
		        	$infoStorno = _x("If you change the contact information, the invoice will be canceled and a new invoice will be generated.", "admin_change_contact", 'indiebooking');
		        	echo '<span class="right_button">
				                <a id="btnEditBookingContactDataStorno" class="ibui_add_btn"' .
				                'data-infoTitle="'.$infoStorno.'">'.$btnEditContactDataTxt. '</a>
				              </span>';
		        }
	        }
        }
        echo "<br />";
        echo "<br />";
        echo "</div><!--Ende rsib_container-fluid--></div><!--Ende ibui_postbox-->";
        
        RS_IB_Booking_View::showBookingDocumentList($buchung, $buchungKopf);
//         echo '<div id="cancel_booking_dialog" title="'.__("Cancel Booking", 'indiebooking').'">
//                 <p>'.__("Are you sure you want to cancel the booking?", 'indiebooking').'</p>
//             </div>';
        //$buchung        = $appartmentBuchungsTable->getPositions($buchung);
        //printBooking($buchung);
    }
    
}
// endif;
new RS_IB_Admin_Booking();