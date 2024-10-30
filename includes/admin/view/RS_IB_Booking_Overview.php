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

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! function_exists( 'configureBookingOverview' ) ) {
	function configureBookingOverview() {
	    //add_appartmentBooking_head(); //nicht unbedingt ideal, aber aktuell i.O.
	    add_action("restrict_manage_posts", "add_appartmentBooking_head");
// 	    add_action("restrict_manage_posts", "indiebooking_filter_restrict_apartment_booking");
// 	    add_filter( 'parse_query', 'indiebooking_filter_restrict_apartment_booking_post_filter' );
	    add_action("manage_posts_custom_column",   "booking_custom_columns");
	    add_filter("manage_edit-rsappartment_buchung_columns", "booking_edit_columns" );
	    add_filter("manage_edit-rsappartment_buchung_sortable_columns", "booking_edit_sortable_columns" );
	    
	    add_filter("posts_join_paged", "booking_edit_join_paged",10 ,2 );
	    add_action( 'posts_search', 'ib_booking_custom_search',20 ,2 );
	    add_filter( 'posts_groupby', 'ib_booking_custom_groupby', 10 ,2 );
// 	    add_action( 'pre_get_posts', 'ib_booking_custom_search' );
	    add_filter('posts_orderby', 'ib_booking_custom_orderby', 10, 2);
// 	    add_filter('posts_orderby', 'booking_edit_posts_orderby');
	}
}

if ( ! function_exists( 'add_appartmentBooking_head' ) ) {
function add_appartmentBooking_head($post_type) {
	if (class_exists('RS_IB_Model_Appartment_Buchung')) {
	    if ($post_type == RS_IB_Model_Appartment_Buchung::RS_POSTTYPE) {
	    ?>
	        <div class="modal"></div>
	
	        <?php
	//         do_action('rs_indiebooking_show_hello_view_box');
	    }
	}
}
}

if (! function_exists('indiebooking_filter_restrict_apartment_booking')) {
	function indiebooking_filter_restrict_apartment_booking($post_type) {
		if (class_exists('RS_IB_Model_Appartment_Buchung')) {
			if ($post_type == RS_IB_Model_Appartment_Buchung::RS_POSTTYPE) {
				$values = array(
					_x('hide canceled bookings', 'booking_overview_filter', 'indiebooking') => 'hideCanceledBookings',
	// 				'label1' => 'value1',
	// 				'label2' => 'value2',
				);
				?>
				
	        <select name="INDIEBOOKING_BOOKING_OVERVIEW_ADMIN_FILTER_FIELD_VALUE">
	        <option value="">
	        	<?php _e('Filter By ', 'indiebooking'); ?>
	        </option>
	        <?php
	            $current_v = isset($_GET['INDIEBOOKING_BOOKING_OVERVIEW_ADMIN_FILTER_FIELD_VALUE'])? $_GET['INDIEBOOKING_BOOKING_OVERVIEW_ADMIN_FILTER_FIELD_VALUE']:'';
	            foreach ($values as $label => $value) {
	                printf
	                    (
	                        '<option value="%s"%s>%s</option>',
	                        $value,
	                        $value == $current_v? ' selected="selected"':'',
	                        $label
	                    );
	                }
	        ?>
	        </select>
	        <?php
			}
		}
	}
}

if (! function_exists('indiebooking_filter_restrict_apartment_booking_post_filter')) {
	function indiebooking_filter_restrict_apartment_booking_post_filter($query) {
		/*
		global $pagenow;
		$type = 'post';
		if (isset($_GET['post_type'])) {
			$type = $_GET['post_type'];
		}
		if ( RS_IB_Model_Appartment_Buchung::RS_POSTTYPE == $type && is_admin() && $pagenow=='edit.php'
				&& isset($_GET['INDIEBOOKING_BOOKING_OVERVIEW_ADMIN_FILTER_FIELD_VALUE']) && $_GET['INDIEBOOKING_BOOKING_OVERVIEW_ADMIN_FILTER_FIELD_VALUE'] != '') {
					
			$type = $type;
			$detailQuery		= $query->query;
			if (!key_exists('post_status', $detailQuery) || in_array($detailQuery['post_status'], array('all', 'publish')) ) {
// 				$queryPostStatus = ['post_status'];
				$query->query['post_status'] = 'rs_ib-storno';
			}
// 			$query->query_vars['meta_key'] = 'META_KEY';
// 			$query->query_vars['meta_value'] = $_GET['ADMIN_FILTER_FIELD_VALUE'];
		}
	*/
	}
}


/**
 * In dieser Funktion wird definiert, wie die Daten fuer die Anzeige verarbeitet werden.
 * Aufruf: add_action("manage_posts_custom_column",  array( $this, "portfolio_custom_columns"));
 * @param unknown $column
 */
/* @var $buchungKopfTbl RS_IB_Table_Buchungskopf */
/* @var $buchung RS_IB_Model_Appartment_Buchung */
if ( ! function_exists( 'booking_custom_columns' ) ) {
    function booking_custom_columns($column) {
        global $post;
        global $RSBP_DATABASE;
        
        $bookingTable       = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $buchung            = $bookingTable->getAppartmentBuchung($post->ID);
        $allReNumbers		= get_post_meta($post->ID, 'rs_indiebooking_all_renr');
        
        $buchungKopfTbl     = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
        $buchungsKopf       = $buchungKopfTbl->loadBooking($buchung->getBuchungKopfId(), true);
        $iconPath           = cRS_Indiebooking::plugin_url().'/images/booking_overview_icons/';
        $iconPath           = esc_url($iconPath);
        
        $icon_bookingBlack  = '<img class="ibui_admin_booking_staticon" src="'.$iconPath.'booking_black.png" alt="" />';
        $icon_bookingGreen  = '<img class="ibui_admin_booking_staticon" src="'.$iconPath.'booking_green.png" alt="" />';
        $icon_bookingRed    = '<img class="ibui_admin_booking_staticon" src="'.$iconPath.'booking_red.png" alt="" />';
        
        $icon_currencyBlack = '<img class="ibui_admin_booking_staticon" src="'.$iconPath.'currency_black.png" alt="" />';
        $icon_currencyGreen = '<img class="ibui_admin_booking_staticon" src="'.$iconPath.'currency_green.png" alt="" />';
        $icon_currencyRed   = '<img class="ibui_admin_booking_staticon" src="'.$iconPath.'currency_red.png" alt="" />';
        
        $icon_timeBlack     = '<img class="ibui_admin_booking_staticon" src="'.$iconPath.'time_black.png" alt="" />';
        $icon_timeGreen     = '<img class="ibui_admin_booking_staticon" src="'.$iconPath.'time_green.png" alt="" />';
        $icon_timeRed       = '<img class="ibui_admin_booking_staticon" src="'.$iconPath.'time_red.png" alt="" />';
        
        $useNewIcons			= true;
        $icon_paymentConfirmed 	= '<img class="ibui_admin_booking_staticon2" src="'.$iconPath.'pay_confirmed.png" alt="" />';
        $icon_depositConfirmed 	= '<img class="ibui_admin_booking_staticon2" src="'.$iconPath.'deposit_confirmed.png" alt="" />';
        $icon_canceled			= '<img class="ibui_admin_booking_staticon2" src="'.$iconPath.'canceled.png" alt="" />';
        $icon_outOfTime			= '<img class="ibui_admin_booking_staticon2" src="'.$iconPath.'out_of_time.png" alt="" />';
        $icon_bookedNotPayed 	= '<img class="ibui_admin_booking_staticon2" src="'.$iconPath.'booked_not_payed.png" alt="" />';
        $icon_storno			= '<img class="ibui_admin_booking_staticon2" src="'.$iconPath.'storno.png" alt="" />';
        $icon_stornoPayed		= '<img class="ibui_admin_booking_staticon2" src="'.$iconPath.'storno_payed.png" alt="" />';
        $icon_stornoNotPayed	= '<img class="ibui_admin_booking_staticon2" src="'.$iconPath.'storno_wait_for_payment.png" alt="" />';

        
        $btn_icon_paymentConfirmed 	= '<img title="'.__("Payment was confirmed", 'indiebooking').'" class="ibui_tooltip_item ibui_admin_booking_staticon2" src="'.$iconPath.'btn_pay_confirmed.png" alt="" />';
        $btn_icon_confirmPayment 	= '<img title="'.__("Confirm payment", 'indiebooking').'" class="ibui_tooltip_item ibui_admin_booking_staticon2" src="'.$iconPath.'btn_confirm_payment.png" alt="" />';
        $btn_icon_confirmDeposit 	= '<img title="'.__("Confirm deposit", 'indiebooking').'" class="ibui_tooltip_item ibui_admin_booking_staticon2" src="'.$iconPath.'btn_confirm_deposit.png" alt="" />';
        $btn_icon_cancel_Booking	= '<img title="'.__("cancel Booking", 'indiebooking').'" class="ibui_tooltip_item ibui_admin_booking_staticon2" src="'.$iconPath.'btn_cancel_booking.png" alt="" />';
        $btn_icon_cancel_BookingGrey = '<img title="'.__("Booking was cancelled", 'indiebooking').'" class="ibui_tooltip_item ibui_admin_booking_staticon2" src="'.$iconPath.'btn_cancel_booking_grey.png" alt="" />';
        
        $tooltip            	= $buchung->getPost_status_label();
        if ($buchung->getPost_status() == "rs_ib-booked" || $buchung->getPost_status() == "rs_ib-bookingcom") {
        	//buchungssymbol gruen
        	//Buchung wurde abgeschlossen
        	if ($buchungsKopf->getAnzahlungBezahlt() == 1) {
        		$tooltip		= __("Deposit confirmed", "indiebooking");
        	}
		}
        if ($buchungsKopf->getZahlungsbetrag() > 0) {
        	$debug = 1;
        }
        $paymentlData 		= get_option( 'rs_indiebooking_settings_payment');
        $depositKz			= "off";
        if ($paymentlData != false) {
        	$depositKz		= (key_exists('activedeposit_kz', $paymentlData)) ? esc_attr__( $paymentlData['activedeposit_kz'] ) : "off";
        }
        $deposit		= false;
        $deposit		= ($depositKz == "on");
//         if ($buchungsKopf->getBuchung_nr() == 1826) {
//         	$test = $buchungsKopf->getBuchung_status();
//         }
        switch ($column) {
            case "booking_description":
                the_excerpt();
                break;
            case "bookedDates":
//                 echo $buchung->getStartDate()." - ".$buchung->getEndDate();
				$buchungVon = $buchungsKopf->getBuchung_von();
				$buchungBis = $buchungsKopf->getBuchung_bis();
				if (!is_null($buchungVon)) {
					if ($buchungVon instanceof DateTime) {
						$buchungVon = $buchungVon->format("d.m.Y");
					}
				} else {
					$buchungVon = $buchung->getStartDate();
				}
				if (!is_null($buchungBis)) {
					if ($buchungBis instanceof DateTime) {
						$buchungBis = $buchungBis->format("d.m.Y");
					}
				} else {
					$buchungBis = $buchung->getEndDate();
				}
				echo $buchungVon." - ".$buchungBis;
                break;
            case "Time_of_recording";
                $startTime                  = $buchung->getStart_time();
                if (!is_null($startTime)) {
                    echo date('d.m.Y - H:i:s', $startTime);
                } else {
                    echo " - ";
                }
                break;
            case "bookingStatus":
                echo '<span class="ibui_tooltip_item" title="'.$tooltip.'">';
                if (($buchung->getPost_status() == "rs_ib-blocked")
                    || ($buchung->getPost_status() == "rs_ib-booking_info")
                    || ($buchung->getPost_status() == "rs_ib-almost_booked")
                ) {
                    //wir befinden uns im Buchungsverlauf
                    echo $icon_bookingBlack;
    //                 echo $icon_currencyBlack;
                    echo $icon_timeGreen;
                }
                if ($buchung->getPost_status() == "rs_ib-booked"
                	|| $buchung->getPost_status() == "rs_ib-bookingcom") {
                    //buchungssymbol gruen
                    //Buchung wurde abgeschlossen
                	if ($useNewIcons) {
                		if ($buchungsKopf->getAnzahlungBezahlt() == 0 || $buchungsKopf->getAnzahlungBezahlt() == 2) {
                			//0 = Anzahlung faellig aber nicht bezahlt, 2 = Anzahlung nicht faellig
                			echo $icon_bookedNotPayed;
                		} else if ($buchungsKopf->getAnzahlungBezahlt() == 1) {
                			echo $icon_depositConfirmed;
                		}
                	} else {
                    	echo $icon_bookingGreen;
                    	echo $icon_currencyBlack;
                	}
    //                 echo $icon_timeBlack;
                } elseif ($buchung->getPost_status() == "rs_ib-pay_confirmed") {
                    //buchungssymbol gruen
                    //zahlungssymbol gruen
                    //Zahlung wurde bestuetigt
                	if ($useNewIcons) {
                		echo $icon_paymentConfirmed;
                	} else {
	                    echo $icon_bookingGreen;
	                    echo $icon_currencyGreen;
                	}
    //                 echo $icon_timeBlack;
                } elseif ($buchung->getPost_status() == "rs_ib-canceled") {
                    //buchungssymbol symbol rot
                    //Buchung wurde vorzeitig abgebrochen
                	if ($useNewIcons) {
                		echo $icon_canceled;
                	} else {
                		echo $icon_bookingRed;
                	}
    //                 echo $icon_currencyBlack;
    //                 echo $icon_timeBlack;
                } elseif ($buchung->getPost_status() == "rs_ib-storno") {
                    //zahlungssymbol rot
                    //Buchung wurde storniert
                	if ($useNewIcons) {
                		if ($buchungsKopf->getZahlungsbetrag() > 0) {
//                 			echo $icon_currencyBlack;
							echo $icon_stornoNotPayed;
                		} else {
	                		echo $icon_storno;
                		}
                	} else {
	                	echo $icon_bookingRed;
	                    if ($buchungsKopf->getZahlungsbetrag() > 0) {
		                    echo $icon_currencyBlack;
	                    } else {
	// 	                    echo $icon_currencyRed;
	                    }
                	}
    //                 echo $icon_timeBlack;
                }  elseif ($buchung->getPost_status() == "rs_ib-storno_paid") {
                	if ($useNewIcons) {
                		echo $icon_stornoPayed;
                	} else {
                	//zahlungssymbol rot
                	//Buchung wurde storniert
	                	echo $icon_bookingRed;
	                	echo $icon_currencyGreen;
                	}
                	//                 echo $icon_timeBlack;
                } elseif ($buchung->getPost_status() == "rs_ib-out_of_time") {
                    //uhr symbol rot
                    //Buchung wurde abgebrochen, da die Zeit zum Buchen abgelaufen war.
                	if ($useNewIcons) {
                		echo $icon_outOfTime;
                	} else {
	                    echo $icon_bookingRed;
	    //                 echo $icon_currencyBlack;
	                    echo $icon_timeRed;
                	}
                }
                echo '</span>';
                break;
            case "bookingNr":
    //             echo $buchung->getPostId();
                echo $buchungsKopf->getBuchung_nr();
                break;
            case "invoiceNr":
            	if (isset($allReNumbers) && !is_null($allReNumbers) && sizeof($allReNumbers) > 0) {
            		$renumbers = $allReNumbers[0];
            		arsort($renumbers);
            		foreach ($renumbers as $reNumber) {
            			if (is_array($reNumber)) {
            				echo $reNumber[0];
            			}
            			echo $reNumber;
            			echo "<br />";
            		}
            	} else {
	                echo $buchungsKopf->getRechnung_nr();
            	}
                break;
            case "actions":
                echo "<div class='ibui_tooltip_container' style='display:block; width:100%;'>";
            	if ($buchungsKopf->getBcomBookingKz() == 0) {
//             		if ($buchungsKopf->getBuchung_nr() == 1917) {
//             			$debzug = 1;
//             		}
            		if ($buchung->getPost_status() !== 'rs_ib-storno_paid') {
            			//$buchung->getPost_status() !== 'rs_ib-storno' &&
	                	if ($buchung->getPost_status() == 'rs_ib-requested') {
	                		echo '<a class="btnAnfrageBestaetigen ibui_iconbtn" style="margin-right:20px;font-size:20px;" data-postId="'.
	                				$buchung->getPostId().'">
	                    		<span class="glyphicon glyphicon-bell ibui_iconbtn_green ibui_tooltip_item" title="'.
	                				__("Confirm request", 'indiebooking') . '"></span></a>';
	                	}
	                	elseif (($buchung->getPost_status() == 'rs_ib-booked' || $buchung->getPost_status() == 'rs_ib-storno') && ($buchungsKopf->getZahlungsbetrag() > 0)) {
	                		if ($buchung->getPost_status() == 'rs_ib-booked' && $buchungsKopf->getAnzahlungBezahlt() == 0) {
	                			if ($useNewIcons) {
	                				echo '<a class="btnAnzahlungBestaetigen ibui_iconbtn" style="margin-right:20px;font-size:20px;" data-postId="'.
		                				$buchung->getPostId().'">'.$btn_icon_confirmDeposit.'</a>';
	                			} else {
		                			echo '<a class="btnAnzahlungBestaetigen ibui_iconbtn" style="margin-right:20px;font-size:20px;" data-postId="'.
			                			$buchung->getPostId().'"><span class="glyphicon glyphicon-credit-card ibui_iconbtn_green ibui_tooltip_item" title="'.
			                			__("Confirm deposit", 'indiebooking') . '"></span></a>';
	                			}
	                		} else {
	                			if ($useNewIcons) {
	                				echo '<a class="btnZahlungBestaetigen ibui_iconbtn" style="margin-right:20px;font-size:20px;" data-postId="'.
		                				$buchung->getPostId().'">'.$btn_icon_confirmPayment.'</a>';
	                			} else {
			                		echo '<a class="btnZahlungBestaetigen ibui_iconbtn" style="margin-right:20px;font-size:20px;" data-postId="'.
			                                $buchung->getPostId().'"><span class="glyphicon glyphicon-credit-card ibui_iconbtn_green ibui_tooltip_item" title="'.
			                                __("Confirm payment", 'indiebooking') . '"></span></a>';
	                			}
	                		}
	                    } else {
	                    	if ($useNewIcons) {
                    			echo '<a class="ibui_iconbtn" style="margin-right:20px;font-size:20px;" data-postId="'.
	                    			$buchung->getPostId().'">'.$btn_icon_paymentConfirmed.'</a>';
	                    	} else {
		                        echo '<a class="ibui_iconbtn" style="margin-right:20px;font-size:20px;" data-postId="'.$buchung->getPostId().'">'.
		                            '<span class="glyphicon glyphicon-credit-card ibui_iconbtn_grey ibui_tooltip_item" title="'
		                                .__("Payment was confirmed", 'indiebooking')
		                            . '"></span></a>';
	                    	}
	                    }
	                    if ($buchung->getPost_status() !== 'rs_ib-canceled'
	                    	&& $buchung->getPost_status() !== 'rs_ib-storno'
	                        && $buchung->getPost_status() !== 'rs_ib-out_of_time'
	                    	&& $buchung->getPost_status() !== 'rs_ib-canc_request') {
	                    		$cancelTitle = __("cancel booking", 'indiebooking');
	                    		$cancelQuestion = __("Are you sure you want to cancel this booking", 'indiebooking');
	                    		switch ($buchung->getPost_status()) {
	                    			case 'rs_ib-requested':
		                    			$cancelTitle = _x("cancel inquiry", 'inquiry', 'indiebooking');
		                    			$cancelQuestion = _x("Are you sure you want to cancel this inquiry", 'inquiry', 'indiebooking');
	                    				break;
	                    			case 'rs_ib-booked':
	                    			case 'rs_ib-pay_confirmed':
		                    			$cancelTitle = _x("cancel booking", 'storno', 'indiebooking');
		                    			$cancelQuestion = _x("Are you sure you want to cancel this booking", 'storno', 'indiebooking');
	                    				break;
// 	                    			case 'rs_ib-booked':
// 		                    			$cancelTitle = _x("cancel booking", 'storno', 'indiebooking');
// 	                    				break;
	                    		}

	                    		if ($useNewIcons) {
	                    			$btn_icon_cancel_Booking	= '<img title="'.$cancelTitle.'" class="ibui_tooltip_item ibui_admin_booking_staticon2" src="'.$iconPath.'btn_cancel_booking.png" alt="" />';
// 	                    			$btn_icon_cancelBooking 	= '<img title="'.$cancelTitle.'" class="ibui_tooltip_item ibui_admin_booking_staticon2" src="'.$iconPath.'btn_cancel_booking.png" alt="" />';
// 	                    			echo '<a class="btnBuchungStornieren ibui_iconbtn"'.
// 	                    			' data-postId="'.$buchung->getPostId().'"'.
// 	                    			' data-cancelTitle="'.$cancelQuestion.'"'.
// 	                    			' style="font-size:20px;">'.$btn_icon_cancelBooking.'</a>';
	                    			echo '<a class="btnBuchungStornieren ibui_iconbtn"'.
		                    			' data-postId="'.$buchung->getPostId().'"'.
		                    			' data-cancelTitle="'.$cancelQuestion.'"'.
		                    			' style="font-size:20px;">'.$btn_icon_cancel_Booking.'</a>';
	                    		} else {
		                            echo '<a class="btnBuchungStornieren ibui_iconbtn"'.
		                                ' data-postId="'.$buchung->getPostId().'"'.
		                                ' data-cancelTitle="'.$cancelQuestion.'"'.
		                                ' style="font-size:20px;">'.
		                                '<span class="glyphicon glyphicon-remove-circle'.
		                                ' ibui_iconbtn_green ibui_tooltip_item"'.
		                                ' title="'.$cancelTitle.'"></span></a>';
	                    		}
	                    } else {
	                    	if ($useNewIcons) {
	                    		echo '<a class="ibui_iconbtn" data-postId="'.$buchung->getPostId().
	                    			'" style="font-size:20px;">'.$btn_icon_cancel_BookingGrey.'</span></span></a>';
	                    	} else {
		                        echo '<a class="ibui_iconbtn" data-postId="'.$buchung->getPostId().
		                                '" style="font-size:20px;"><span class="glyphicon glyphicon-remove-circle ibui_iconbtn_grey ibui_tooltip_item" title="'.
		                                __("Booking was cancelled", 'indiebooking') . '"></span></span></a>';
	                    	}
	                    }
	                    if (intval($buchungsKopf->getAdminKz()) > 0) {
	                    	$msg		= "";
	                    	$user 		= get_user_by('ID', $buchungsKopf->getAdminKz());
                    		$name 		= $user->get('display_name');
                    		$name 		= $name." (".$user->get('user_email').")";
	                    	if ($buchung->getPost_status() == 'rs_ib-canceled') {
	                    		$msg 	= sprintf(__("Booking was cancelled by %s", 'indiebooking'), $name);
	                    	} else if ($buchung->getPost_status() == 'rs_ib-pay_confirmed') {
	                    		$msg 	= sprintf(__("Payment was confirmed by %s", 'indiebooking'), $name);
	                    	} else if ($buchung->getPost_status() == "rs_ib-booked" && $buchungsKopf->getAnzahlungBezahlt() == 1) {
	                    		$msg 	= sprintf(__("Deposit was confirmed by %s", 'indiebooking'), $name);
	                    	} else if ($buchung->getPost_status() == "rs_ib-booked") {
	                    		$msg 	= sprintf(__("Booking was made by %s", 'indiebooking'), $name);
	                    	} else {
	                    		$msg 	= $name;
	                    	}
	                    	echo '<a class="ibui_iconbtn" data-postId="'.$buchung->getPostId().
	                    	'" style="font-size:20px;margin-left:20px;"><span class="glyphicon glyphicon-user ibui_iconbtn_grey ibui_tooltip_item" title="'.
	                    	$msg . '"></span></span></a>';
	                    }
	                }
            	} else {
            		if ($buchung->getPost_status() !== 'rs_ib-canceled'
            			&& $buchung->getPost_status() !== 'rs_ib-out_of_time'
            			&& $buchung->getPost_status() !== 'rs_ib-canc_request'
            			&& $buchung->getPost_status() !== 'rs_ib-pay_confirmed') {
            				
            				if ($useNewIcons) {
            					echo '<a class="btnZahlungBestaetigen ibui_iconbtn" style="margin-right:20px;font-size:20px;" data-postId="'.
             						$buchung->getPostId().'">'.$btn_icon_confirmPayment.'</a>';
            				} else {
			            		echo '<a class="btnZahlungBestaetigen ibui_iconbtn" style="margin-right:20px;font-size:20px;" data-postId="'.
			             		$buchung->getPostId().'"><span class="glyphicon glyphicon-credit-card ibui_iconbtn_green ibui_tooltip_item" title="'.
			             		__("Confirm payment", 'indiebooking') . '"></span></a>';
            				}
            		}
            		$bcomTxt = sprintf(__("Please manage via booking.com\n Reservation Id %s", "indiebooking"),
            								$buchungsKopf->getBcomReservationId());
            		$icon_Bookingcom = '<img title="'.$bcomTxt.'" class="ibui_tooltip_item ibui_admin_booking_staticon2" src="'.$iconPath.'Booking.com.png" alt="" />';
            		echo $icon_Bookingcom;
//             		echo $bcomTxt;
            	}
                echo "</div>";
                break;
        }
    }
}
/**
 * In dieser Funktion werden die Felder definiert, die in der uebersichtstabelle angezeigt werden.
 * Aufruf: add_filter("manage_edit-rsappartment_columns", array( $this, "portfolio_edit_columns" ));
 * @param unknown $columns
 * @return multitype:string
 */
if ( ! function_exists( 'booking_edit_columns' ) ) {
function booking_edit_columns($columns){
    $columns = array(
        "cb"                    => "<input type='checkbox' />",
        "bookingNr"             => __('Booking Number', 'indiebooking'),
        "invoiceNr"             => __('Invoice number', 'indiebooking'),
        "bookingStatus"         => __('Booking Status', 'indiebooking'),
        "title"                 => __('Titel', 'indiebooking'),
        "booking_description"   => __('Description', 'indiebooking'),
        "bookedDates"           => __('Booked period', 'indiebooking'),
        "Time_of_recording"     => __("Time of recording", 'indiebooking'),
        "actions"               => _x("Actions", 'Booking Overview', 'indiebooking'),
    );

    return $columns;
}

function booking_edit_join_paged($join_paged_statement, $wp_query) {
	global $RSBP_DATABASE;
	global $RSBP_TABLEPREFIX;
	global $wpdb;
	
	if ( ! is_admin() ) {
		return $join_paged_statement;
	}
// 	if ($wp_query->is_search) {
		$bkopf_table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
		$postTbl	= $wpdb->prefix . 'posts';
		$join_paged_statement .= "LEFT JOIN ".$bkopf_table_name." ibbk ON ibbk.post_id = ".$postTbl.".ID";
// 	}
	return $join_paged_statement;
}

if ( ! function_exists('ib_booking_custom_search' )) {
	function ib_booking_custom_search($search, $wp_query) {
		if ( ! is_admin() ) {
			return;
		}
		if (class_exists("RS_IB_Model_Appartment_Buchung")) {
			if ($wp_query->get("post_type") === RS_IB_Model_Appartment_Buchung::RS_POSTTYPE) {
				if ($wp_query->is_search) {
					$searchQuery = get_search_query();
					if (is_numeric($searchQuery)) {
						$search .= ' OR ibbk.'.RS_IB_Model_Buchungskopf::BUCHUNG_RECH_NR.' = '.$searchQuery;
						$search .= ' OR ibbk.'.RS_IB_Model_Buchungskopf::BUCHUNG_NR.' = '.$searchQuery;
					}
				}
			}
		}
		return $search;
	}
}

/* @var $wp_query WP_QUERY */
if ( ! function_exists('ib_booking_custom_orderby' )) {
function ib_booking_custom_orderby($orderby_statement, $wp_query ) {
	if ( ! is_admin() ) {
		return $orderby_statement;
	}
	if (class_exists('RS_IB_Model_Appartment_Buchung')) {
		if ($wp_query->get("post_type") === RS_IB_Model_Appartment_Buchung::RS_POSTTYPE) {
			$orderby = $wp_query->get( 'orderby' );
			$order	 = $wp_query->get( 'order' );
			
			if ( 'invoiceNr' == $orderby ) {
				$orderby_statement = "ibbk.rechnungNr ".$order;
			} else if ('bookingNr' == $orderby) {
				$orderby_statement = "ibbk.buchung_nr ".$order;
			}
		}
	}
	return $orderby_statement;
}
}

if ( ! function_exists('ib_booking_custom_groupby' )) {
	function ib_booking_custom_groupby($groupBy, $wp_query ) {
		global $wpdb;
		
		if (is_admin()) {
			$postTbl	= $wpdb->prefix . 'posts';
			if (strpos($groupBy, 'posts.ID')) {
				//wird bereits gruppiert
			} else {
				$groupBy = $groupBy." ".$postTbl.".ID";
			}
		}
		return $groupBy;
	}
}


if ( ! function_exists('booking_edit_sortable_columns' )) {
	function booking_edit_sortable_columns($columns) {
		if (is_admin()) {
			$columns['bookingNr'] = 'bookingNr';
			$columns['invoiceNr'] = 'invoiceNr';
		}
		return $columns;
	}
}

}