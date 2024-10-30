<?php

class rs_indiebooking_apartment_frontend_helper {
    public function __construct() {
        add_action( 'rs_indiebooking_single_rsappartment_summary',
            array($this, 'rs_indiebooking_template_single_appartment_gallery'), 10 );
        
        add_action( 'rs_indiebooking_single_rsappartment_summary',
            array($this, 'rs_indiebooking_template_single_appartment_dates'), 15 );
        
        add_action( 'rs_indiebooking_single_rsappartment_summary',
            array($this, 'rs_indiebooking_template_single_appartment_description'), 15 );
        
        
        add_action( 'rs_indiebooking_single_rsappartment_dates',
            array($this, 'rs_indiebooking_template_single_appartment_dates'), 15 );
        
        add_action( 'rs_indiebooking_single_rsappartment_description',
            array($this, 'rs_indiebooking_template_single_appartment_description'), 15 );
        
        //Pruefen ob die action noch gebraucht wird
//         add_action( 'rs_indiebooking_single_rsappartment_summary',
//             array($this, 'rs_indiebooking_template_single_appartment_options'), 15 );
        
        
        add_action( 'rs_indiebooking_single_appartment_profile_picture',
            array($this, 'show_appartment_profile_picture'), 10, 2 );
        
        add_action( 'rs_indiebooking_single_rsappartment_gallery',
            array($this, 'show_appartment_gallery'), 5, 2 );
        
        add_action( 'rs_indiebooking_list_rsappartment_gallery',
            array($this, 'show_appartment_list_gallery'), 5, 2 );
        
        add_action( 'rs_indiebooking_list_rsappartment_dates',
            array($this, 'rs_indiebooking_template_list_appartment_dates'), 15, 2 );
        
        add_action( 'rs_indiebooking_list_rsappartment_dates_by_category',
        	array($this, 'rs_indiebooking_template_list_appartment_dates_by_category'), 15, 2 );
        
        add_action( 'rs_indiebooking_show_apartment_booking_extra_info',
            array($this, 'show_apartment_booking_person_count'), 10, 1);
        
        add_action( 'rs_indiebooking_show_single_apartment_side_navigation',
            array($this, 'show_apartment_side_navigation'),10,1);
        
//         add_action( 'rs_indiebooking_show_apartment_booking_person_count',
//             array($this, 'show_apartment_booking_person_count'), 10, 1);

        add_action( 'rs_indiebooking_single_apartment_side_navigation',
            array($this, 'include_single_apartment_side_navigation'), 10, 1);
        
        add_action( 'rs_indiebooking_show_apartment_list_extra_infos',
            array($this, 'show_apartment_langtext_info'), 10, 1);
    }
    
    public function show_apartment_langtext_info() {
        if (!is_plugin_active('indiebooking-advanced/indiebooking_advanced.php')) {
            $description                = get_the_excerpt(get_the_ID());
            echo $description;
        }
    }
    
    /* @var $appartment RS_IB_Model_Appartment */
    public function include_single_apartment_side_navigation($appartmentId) {
    	global $RSBP_DATABASE;
    	
        $showDetailLink 	= false;
        
        $appartmentTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        $appartment         = $appartmentTable->getAppartment($appartmentId);
        $anzahlZimmer		= $appartment->getAnzahlZimmer();
        $maxAnzPers			= $appartment->getAnzahlPersonen();
        $wohnflaeche		= $appartment->getQuadratmeter();
        $anzahlDoppelBetten	= $appartment->getAnzahlDoppelBetten();
        $anzahlEinzelBetten	= $appartment->getAnzahlEinzelBetten();
        $features			= $appartment->getFeatures();
        
        if (isset($maxAnzPers) && intval($maxAnzPers) > 0
        		|| isset($wohnflaeche) && intval($wohnflaeche) > 0
        		|| isset($anzahlZimmer) && intval($anzahlZimmer) > 0
        		|| ((isset($anzahlDoppelBetten) && intval($anzahlDoppelBetten) > 0)
        		|| (isset($anzahlEinzelBetten) && intval($anzahlEinzelBetten) > 0))
        		|| isset($features) && sizeof($features) > 0) {
        		
        	$showDetailLink = true;
		}
        
		$args 				= array(
				'showDetail' => $showDetailLink,
				
		);
		
        cRS_Template_Loader::rs_ib_get_template('single-appartment/rs_ib_appartment_side_navigation.php', $args);
    }
    
    public function show_apartment_side_navigation() {
        "rs_ib_appartment_side_navigation.php";
    }
    
    public function rs_indiebooking_template_list_appartment_dates_by_category($apartmentIds, $class="item_kalender") {
    	$fromDate   = rsbp_getPostValue("searchDateFrom", "");
    	$toDate     = rsbp_getPostValue("searchDateTo", "");
    	if ($fromDate == "") {
    		$fromDate   = rsbp_getPostValue("search_booking_date_from", "");
    	}
    	if ($toDate == "") {
    		$toDate   = rsbp_getPostValue("search_booking_date_to", "");
    	}
    	$this->show_appartment_calendar_by_category($apartmentIds, $class, $fromDate, $toDate);
    }
    
    public function rs_indiebooking_template_list_appartment_dates($appartment_id, $class="item_kalender") {
        $fromDate   = rsbp_getPostValue("searchDateFrom", "");
        $toDate     = rsbp_getPostValue("searchDateTo", "");
        if ($fromDate == "") {
            $fromDate   = rsbp_getPostValue("search_booking_date_from", "");
        }
        if ($toDate == "") {
            $toDate   = rsbp_getPostValue("search_booking_date_to", "");
        }
        $this->show_appartment_calendar($appartment_id, $class, $fromDate, $toDate);
    }
    
    public function rs_indiebooking_template_single_appartment_description($appartment_id = 0) {
        global $RSBP_DATABASE;
        if (is_null($appartment_id) || "" == $appartment_id) {
            $apartmentId = get_the_ID();
        }
        
        $allMwst                    = array();
        $mwstTable                  = $RSBP_DATABASE->getTable(RS_IB_Model_Mwst::RS_TABLE);
        $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        
        $allMwst                    = $mwstTable->getAllMwsts();
        $appartment                 = $appartmentTable->getAppartment($appartment_id);
        $appartment_post            = get_post($appartment_id);
        $description                = $appartment_post->post_content;
        
        $args = array (
            'allMwst'               => $allMwst,
            'appartment'            => $appartment,
            'description'           => $description,
            'maxAnzPers'            => $appartment->getAnzahlPersonen(),
            'wohnflaeche'           => $appartment->getQuadratmeter(),
            'anzahlBetten'          => $appartment->getAnzahlBetten(),
            'anzahlZimmer'          => $appartment->getAnzahlZimmer(),
            'anzahlEinzelBetten'    => $appartment->getAnzahlEinzelBetten(),
            'anzahlDoppelBetten'    => $appartment->getAnzahlDoppelBetten(),
        );
        
        cRS_Template_Loader::rs_ib_get_template('single-appartment/appartment_description.php', $args);
    }
    
    public function rs_indiebooking_template_single_appartment_dates() {
        $this->show_appartment_calendar(get_the_ID());
    }
    
    public function show_appartment_list_gallery($appartment_id, $showAction = false) {
        if (is_plugin_active('indiebooking-advanced/indiebooking_advanced.php')) {
            $this->show_appartment_gallery($appartment_id, $showAction);
        } else {
           $this->show_appartment_profile_picture($appartment_id, $showAction);
        }
    }
    
    public function show_appartment_gallery($appartment_id, $showAction = false) {
        if (is_null($appartment_id) || "" == $appartment_id) {
            $appartment_id = get_the_ID();
        }
//         RS_IB_Template_Buchungsanzeige::show_appartment_gallery(get_the_ID(), $showAction);
        if ( metadata_exists( 'post', $appartment_id, 'rs_apartment_image_gallery' ) ) {
            $apartment_image_gallery    = get_post_meta( $appartment_id, 'rs_apartment_image_gallery', true );
        } else {
            // Backwards compat
            $attachment_ids             = get_posts( 'post_parent=' . $appartment_id . '&numberposts=-1&post_type=attachment&orderby=menu_order&order=ASC&post_mime_type=image&fields=ids&meta_key=rs_apartment_image_gallery&meta_value=0' );
            //             $attachment_ids             = array_diff( $attachment_ids, array( get_post_thumbnail_id() ) );
            $apartment_image_gallery    = implode( ',', $attachment_ids );
        }
        $attachments                    = array_filter( explode( ',', $apartment_image_gallery ) );
        $loadingImage                   = cRS_Indiebooking::plugin_url().'/assets/slider_control_images/loading.gif';
        $args = array(
            'attachments'       => $attachments,
            'loadingImage'      => $loadingImage,
            'sliderImagePath'   => cRS_Indiebooking::plugin_url().'/assets/slider_control_images/',
            'appartmentId'      => $appartment_id,
            'showAction'        => $showAction,
        );
        cRS_Template_Loader::rs_ib_get_template('single-appartment/appartment_gallery.php', $args);
    }
    
    public function show_appartment_profile_picture($appartment_id, $showAction = false) {
        if (is_null($appartment_id) || "" == $appartment_id) {
            $apartmentId = get_the_ID();
        }
        $profilePicture         = wp_get_attachment_image_src(get_post_thumbnail_id($appartment_id), 'full');
        $image                  = "";
        if (sizeof($profilePicture) > 0) {
            $image              = $profilePicture[0];
        }
        $args = array(
            'profileImage'      => $image,
            'appartmentId'      => $appartment_id,
        );
        cRS_Template_Loader::rs_ib_get_template('single-appartment/appartment_profile_picture.php', $args);
    }
    
    
    /* @var $buchungTeilKopf RS_IB_Model_Buchungskopf */
    /* @var $appartmentTable RS_IB_Table_Appartment */
    /* @var $buchungTable RS_IB_Table_Appartment_Buchung */
    private function show_appartment_calendar($appartment_id, $class="item_kalender", $fromDate = "", $toDate = "") {
        global $RSBP_DATABASE;
    
        $buchungNr					= 0;
        $buchungVon                 = $fromDate;
        $buchungBis                 = $toDate;
        $buchungVonEng              = $fromDate;
        $buchungBisEng              = $toDate;
        $buchungTable               = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
    
        $appartment                 = $appartmentTable->getAppartment($appartment_id);
        $minnaechte                 = $appartment->getMinDateRange();
        //         var_dump($appartment->getZeitraumeDB());
    
        //         $buchungen                  = $buchungTable->getBuchungenByAppartmentid($appartment_id);
        //         $bookedDates                = array();
        //         for ($i = 0; $i < sizeof($buchungen); $i++) {
        //             $dates = unserialize($buchungen[$i]->meta_value);
        //             $bookedDates[$i]["from"]    = $dates[0];
        //             $bookedDates[$i]["to"]      = $dates[1];
        //         }
//         $bookedDates                = $buchungTable->getBuchungszeitraeumeByAppartmentId($appartment_id);
//         $bookedDates                = json_encode($bookedDates);
        $bookableDates              = json_encode($appartment->getBookableDates());
        $bookableDatesEng           = json_encode($appartment->getZeitraumeDB());
        $arrivalDays                = json_encode($appartment->getArrivalDays());
        $notBookableDates           = json_encode($appartment->getNotbookableDates());
        
        $futureAvailabilityYear		= get_option("rs_indiebooking_settings_future_availability");
        if (!$futureAvailabilityYear) {
        	$futureAvailabilityYear	= 2;
        }
        $curMaxDate					= new DateTime("now");
        $addYears					= "P".$futureAvailabilityYear."Y";
        $curMaxDate->add(new DateInterval($addYears));
        $curMaxDate					= $curMaxDate->format("Y-m-d");
        //         $buchungTable
        //         loadBookingPartHeader
        $postType                   = get_post_type( get_the_ID() );
        if ($postType == "rsappartment_buchung") {
            $buchungId              = get_the_ID();
            $buchungTable           = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
            $buchungKopfTable       = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
            $teilBuchungKopfTbl     = $RSBP_DATABASE->getTable(RS_IB_Model_Teilbuchungskopf::RS_TABLE);
            $buchung                = $buchungTable->getAppartmentBuchung($buchungId);
            //             $buchungKopf            = $buchungTable->loadBuchungskopf($buchung->getBuchungKopfId());
            $buchungTeilKopf        = $teilBuchungKopfTbl->loadBookingPartHeader($buchung->getBuchungKopfId(), $appartment_id);
            $buchungNr				= $buchungTeilKopf[0]->getBuchung_nr();
            //             echo $buchung->getBuchungKopfId();
            //             echo "<br />";
            //             echo $appartment_id;
            //             var_dump($buchungTeilKopf);
            $buchungVon             = $buchungTeilKopf[0]->getTeilbuchung_von();
            $buchungBis             = $buchungTeilKopf[0]->getTeilbuchung_bis();
            if (!$buchungVon instanceof DateTime) {
                $buchungVon         = (new Datetime($buchungTeilKopf[0]->getTeilbuchung_von()));
                $buchungBis         = (new Datetime($buchungTeilKopf[0]->getTeilbuchung_bis()));
            }
            $buchungVonEng          = $buchungVon->format("Y-m-d");
            $buchungBisEng          = $buchungBis->format("Y-m-d");
            $buchungVon             = $buchungVon->format("d.m.Y");
            $buchungBis             = $buchungBis->format("d.m.Y");
        } else {
            $buchungVonEng          = rs_ib_date_util::convertDateValueToDateTime($buchungVonEng);
            $buchungBisEng          = rs_ib_date_util::convertDateValueToDateTime($buchungBisEng);
            if ($buchungVonEng) {
                $buchungVonEng      = $buchungVonEng->format("Y-m-d");
            }
            if ($buchungBisEng) {
                $buchungBisEng      = $buchungBisEng->format("Y-m-d");
            }
        }
        $bookedDates                = $buchungTable->getBuchungszeitraeumeByAppartmentId($appartment_id, false, $buchungNr);
        $bookedDates                = json_encode($bookedDates);
        $args = array(
            'appartment_id'     => $appartment_id,
            'bookableDates'     => $bookableDates,
            'bookableDatesEng'  => $bookableDatesEng,
            'bookedDates'       => $bookedDates,
            'buchungVon'        => $buchungVon,
            'buchungBis'        => $buchungBis,
            'buchungVonEng'     => $buchungVonEng,
            'buchungBisEng'     => $buchungBisEng,
            'arrivalDays'       => $arrivalDays,
            'myClass'           => $class,
            'minnaechte'        => $minnaechte,
            'notBookableDates'  => $notBookableDates,
        	'maxDate'			=> $curMaxDate,
        );
        cRS_Template_Loader::rs_ib_get_template('single-appartment/appartment_dates.php', $args);
    }
    
    /* @var $buchungTeilKopf RS_IB_Model_Buchungskopf */
    /* @var $appartmentTable RS_IB_Table_Appartment */
    /* @var $buchungTable RS_IB_Table_Appartment_Buchung */
    private function show_appartment_calendar_by_category($appartment_ids, $class="item_kalender", $fromDate = "", $toDate = "") {
    	global $RSBP_DATABASE;
    	
    	$buchungNr					= 0;
    	$buchungVon                 = $fromDate;
    	$buchungBis                 = $toDate;
    	$buchungVonEng              = $fromDate;
    	$buchungBisEng              = $toDate;
    	$buchungTable               = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
    	$appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
    	
    	
    	$minnaechte					= 0;
    	
    	/*
    	$appartment                 = $appartmentTable->getAppartment($appartment_id);
    	$minnaechte                 = $appartment->getMinDateRange();

    	$bookableDates              = json_encode($appartment->getBookableDates());
    	$bookableDatesEng           = json_encode($appartment->getZeitraumeDB());
    	$arrivalDays                = json_encode($appartment->getArrivalDays());
    	$notBookableDates           = json_encode($appartment->getNotbookableDates());
    	*/
    	
    	$futureAvailabilityYear		= get_option("rs_indiebooking_settings_future_availability");
    	if (!$futureAvailabilityYear) {
    		$futureAvailabilityYear	= 2;
    	}
    	$curMaxDate					= new DateTime("now");
    	$addYears					= "P".$futureAvailabilityYear."Y";
    	$curMaxDate->add(new DateInterval($addYears));
    	$curMaxDate					= $curMaxDate->format("Y-m-d");
    	//         $buchungTable
    	//         loadBookingPartHeader
    	$postType                   = get_post_type( get_the_ID() );
    	if ($postType == "rsappartment_buchung") {
    		$buchungId              = get_the_ID();
    		$buchungTable           = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
    		$buchungKopfTable       = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
    		$teilBuchungKopfTbl     = $RSBP_DATABASE->getTable(RS_IB_Model_Teilbuchungskopf::RS_TABLE);
    		$buchung                = $buchungTable->getAppartmentBuchung($buchungId);
    		//             $buchungKopf            = $buchungTable->loadBuchungskopf($buchung->getBuchungKopfId());
    		$buchungTeilKopf        = $teilBuchungKopfTbl->loadBookingPartHeader($buchung->getBuchungKopfId(), $appartment_id);
    		$buchungNr				= $buchungTeilKopf[0]->getBuchung_nr();
    		//             echo $buchung->getBuchungKopfId();
    		//             echo "<br />";
    		//             echo $appartment_id;
    		//             var_dump($buchungTeilKopf);
    		$buchungVon             = $buchungTeilKopf[0]->getTeilbuchung_von();
    		$buchungBis             = $buchungTeilKopf[0]->getTeilbuchung_bis();
    		if (!$buchungVon instanceof DateTime) {
    			$buchungVon         = (new Datetime($buchungTeilKopf[0]->getTeilbuchung_von()));
    			$buchungBis         = (new Datetime($buchungTeilKopf[0]->getTeilbuchung_bis()));
    		}
    		$buchungVonEng          = $buchungVon->format("Y-m-d");
    		$buchungBisEng          = $buchungBis->format("Y-m-d");
    		$buchungVon             = $buchungVon->format("d.m.Y");
    		$buchungBis             = $buchungBis->format("d.m.Y");
    	} else {
    		$buchungVonEng          = rs_ib_date_util::convertDateValueToDateTime($buchungVonEng);
    		$buchungBisEng          = rs_ib_date_util::convertDateValueToDateTime($buchungBisEng);
    		if ($buchungVonEng) {
    			$buchungVonEng      = $buchungVonEng->format("Y-m-d");
    		}
    		if ($buchungBisEng) {
    			$buchungBisEng      = $buchungBisEng->format("Y-m-d");
    		}
    	}
    	$bookedDates				= $buchungTable->getBuchungszeitraeumeByAppartmentId_new($appartment_ids);
//     	$bookedDates                = $buchungTable->getBuchungszeitraeumeByAppartmentId($appartment_id, false, $buchungNr);
    	$bookedDates                = json_encode($bookedDates);
    	
    	
    	$appartment_id 		= 0;
    	$bookableDates 		= json_encode(array());
    	$bookableDatesEng	= json_encode(array());
    	$arrivalDays		= json_encode(array());
    	$notBookableDates	= json_encode(array());
    	
    	$args = array(
    		'appartment_id'     => $appartment_id,
    		'bookableDates'     => $bookableDates,
    		'bookableDatesEng'  => $bookableDatesEng,
    		'bookedDates'       => $bookedDates,
    		'buchungVon'        => $buchungVon,
    		'buchungBis'        => $buchungBis,
    		'buchungVonEng'     => $buchungVonEng,
    		'buchungBisEng'     => $buchungBisEng,
    		'arrivalDays'       => $arrivalDays,
    		'myClass'           => $class,
    		'minnaechte'        => $minnaechte,
    		'notBookableDates'  => $notBookableDates,
    		'maxDate'			=> $curMaxDate,
    	);
    	cRS_Template_Loader::rs_ib_get_template('single-appartment/appartment_dates.php', $args);
    }
    
    
    /* @var $teilbuchungsKopfTbl RS_IB_Table_Teilbuchungskopf */
    public function show_apartment_booking_person_count($apartmentId) {
        global $RSBP_DATABASE;
        $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        $appartment                 = $appartmentTable->getAppartment($apartmentId);
        $maxAnzPersonen             = $appartment->getAnzahlPersonen();
        $vorbelegungAnzPersonen		= $appartment->getAnzahlPersonenVorbelegung();
        $curAnzPersonen             = $vorbelegungAnzPersonen;
    
        $postId                     = get_the_ID();
        $post                       = get_post($postId);
        if ($post->post_type == RS_IB_Model_Appartment_Buchung::RS_POSTTYPE) {
            $buchungTable           = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
            $buchung                = $buchungTable->getAppartmentBuchung($post->ID);
            $teilbuchungsKopfTbl    = $RSBP_DATABASE->getTable(RS_IB_Model_Teilbuchungskopf::RS_TABLE);
            //             $teilBuchungsKopf       = $teilbuchungsKopfTbl->loadBookingPartHeader($postId, $apartmentId, false, false);
            $teilbuchungsKopf       = $teilbuchungsKopfTbl->getTeilbuchungskopf($buchung->getBuchungKopfId(), $apartmentId);
            $curAnzPersonen         = $teilbuchungsKopf->getAnzahlPersonen();
        }
        if (is_null($curAnzPersonen) || $curAnzPersonen <= 0) {
            $curAnzPersonen         = $vorbelegungAnzPersonen;
        }
        $arguments          = array(
            'curAnzPersonen' => $curAnzPersonen,
            'maxAnzPersonen' => $maxAnzPersonen,
        );
        cRS_Template_Loader::rs_ib_get_template('single-appartment/appartment_person_count.php', $arguments);
    }
    
    
}
new rs_indiebooking_apartment_frontend_helper();