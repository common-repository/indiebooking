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
// if ( ! class_exists( 'cRS_Indiebooking' ) ) :
/**
 * Basisklasse fuer Indiebooking.
 * Hier wird alles initialisiert.
 * Es wird die Version verwaltet (die an die Javascript und CSS Dateien dran gehangen wird)
 * und die Datenbankversucht.
 * RS_IB_START_TOUR ist fuer die Hopscotchtour, die noch entwickelt werden soll.
 *
 * @author schmitt
 *
 */
class cRS_Indiebooking
{
    const RS_IB_VER                         = '0.2.4-dev';
    const RS_IB_DB_VER                      = 24;
//     const RS_IB_START_TOUR      = 1;
    const RS_IB_PLUGIN_FOLDER               = 'indiebooking';
    const RS_INDIEBOOKING_SETTINGS_PAGE_ID  = "indiebooking_page_rs_einstellungen";
    
    protected static $_instance = null;
    

    /**
     * Function: register_admin_scripts
     * Parameters:
     * 		$hook - beinhaltet Informationen, auf welcher Seite wir uns befinden
     *
     * Returns:
     *
     * Diese Methode soll aufgerufen werden, wenn Admin-CSS oder Admin-Skripte eingebunden werden sollen.
     * Indiebooking Javascripts und CSS's sollen nur im Bereich von indiebooking selbst verfuegbar sein.
     * @param String $hook
     */
    public static function isIndiebookingPage($hook) {
    	$isIndiebookingPage 	= true; //zur absicherung, lieber mal zu viel wie zu wenig laden.
    	if (isset($hook) && !is_null($hook) & "" != $hook) {
	    	$isIndiebookingPage = false;
	    	$allowedHooks = array(
	    			'indiebooking_page_rs_einstellungen',
	    			'indiebooking_page_rs_ib_dashboard',
	    			'edit.php',
	    			'edit-tags.php',
	    			'post.php',
	    			'post-new.php'
	    	);
	    	$isIndiebookingPage = in_array($hook, $allowedHooks);
	    	if ($isIndiebookingPage) {
	    		if ('post.php' == $hook || 'edit.php' == $hook || 'post-new.php' == $hook) {
	    			$postType 		= get_post_type();
	    			if ($postType == false) {
	    				$screen     = get_current_screen();
	    				$postType 	= $screen->post_type;
	    			}
	    			/*
	    			 * Die Posttypes stehen zwar auch in Ihren entsprechenden Model-Klassen
	    			 * um die Methode jedoch auch fuer alle Plugins von indiebooking nutzen zu koennen,
	    			 * werden hier alle als String aufgefuert.
	    			 */
	    			$allowedPostTypes = array(
	    					'rsappartment',
	    					'rsappartment_buchung',
	    					'rsappartment_zeitraeume'
	    			);
	    			$isIndiebookingPage  = in_array($postType, $allowedPostTypes);
	    		} elseif ('edit-tags.php' == $hook) {
	    			$screen     = get_current_screen();
	    			/*
	    			 * Die Taxonomies stehen zwar auch in Ihren entsprechenden Model-Klassen
	    			 * um die Methode jedoch auch fuer alle Plugins von indiebooking nutzen zu koennen,
	    			 * werden hier alle als String aufgefuert.
	    			 */
	    			$allowedTaxonomies = array(
	    					'rsappartmentoption',
	    					'rsappartmentaktion',
	    					'rsgutschein',
	    					'ibrealgutschein',
	    					'rsappartmentcategories'
	    			);
	    			$isIndiebookingPage  = in_array($screen->taxonomy, $allowedTaxonomies);
	    		}
	    	}
    	}
    	return $isIndiebookingPage;
    }
    
    /**
     * registriert alle Skripte, die in der Administration geladen werden sollen.
     * @param String $hook
     */
    public static function register_admin_scripts($hook) {
        global $RS_IB_SCRIPT_VERSION;

        $test = __("net amount", 'indiebooking');
        $isIndiebookingPage = self::isIndiebookingPage($hook);
        if ($isIndiebookingPage) {
	        $screen         = get_current_screen();
	        $jsPath         = self::plugin_url() . '/assets/js/';
	        $adminJsPath    = $jsPath.'admin/';
	
	//         rs_ib_date_util::testSaisonpreisermittlung();
	        add_filter('rs_indiebooking_images_limitimages', array('cRS_Indiebooking', 'indiebookingLimitImages'), 5);
	        
	        wp_register_script(
	            'rs_indiebooking_appartment_admin',
	            $adminJsPath . 'rsbp_appartment_admin.js',
	            array( 'jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-ui-datepicker',
	            		'jquery-ui-sortable', 'jquery-ui-tooltip', 'rs_indiebooking_util'),
	            $RS_IB_SCRIPT_VERSION
	        );
	        
	        wp_register_script(
	            'rs_indiebooking_jquery_ui_extensions',
	            $jsPath . 'jquery-ui-extensions/jquery.ui.spinner.js',
	            array( 'jquery', 'jquery-ui-core', 'jquery-ui-spinner' ),
	            $RS_IB_SCRIPT_VERSION
	        );
	        
	        wp_register_script(
	            'rs_indiebooking_jquery_tooltipster',
	            $jsPath . 'tooltipster/tooltipster.bundle.min.js',
	            array( 'jquery', 'jquery-ui-core' ),
	            $RS_IB_SCRIPT_VERSION
	        );
	        
	        
	        wp_register_script(
	            'rs_indiebooking_bootstraptour',
	            $jsPath . 'bootstrap_tour/bootstrap-tour-standalone.min.js',
	            array( 'jquery', 'jquery-ui-core' ),
	            $RS_IB_SCRIPT_VERSION
	        );
	        
	        wp_register_script(
	            'rs_indiebooking_tabulator_script',
	            $jsPath . 'tabulator/dist/js/tabulator.min.js',
	            array( 'jquery', 'jquery-ui-core' ),
	            $RS_IB_SCRIPT_VERSION
            );
	        
	        $ibui_indiebooking_tour_text    = rs_indiebooking_tour_text::getTourTexts();
	        $ibui_indiebooking_base_url     = array(
	            "baseurl"   => get_site_url(),
	            "adminurl"  => get_admin_url(),
	        );
	        wp_localize_script( 'rs_indiebooking_bootstraptour', 'ibuiTourTextObj', $ibui_indiebooking_tour_text);
	        wp_localize_script( 'rs_indiebooking_bootstraptour', 'ibuiBaseData', $ibui_indiebooking_base_url);
	        
	        
	        
	        if (!is_plugin_active('indiebooking-advanced/indiebooking_advanced.php')) {
		        /*
		         * Nutze die Tour des Free-Plugins
		         */
	        	wp_register_script(
		            'rs_indiebooking_bootstraptour_admin_script',
		            $jsPath . 'admin/rs_bp_indiebooking_admin_bootstraptour.js',
		            array( 'rs_indiebooking_bootstraptour', 'rs_indiebooking_appartment_admin' ),
		            $RS_IB_SCRIPT_VERSION
		        );
	        } else {
	        	wp_register_script(
	        		'rs_indiebooking_bootstraptour_admin_script',
	        		$jsPath . 'admin/rs_bp_indiebooking_admin_bootstraptour_advanced.js',
	        		array( 'rs_indiebooking_bootstraptour', 'rs_indiebooking_appartment_admin' ),
	        		$RS_IB_SCRIPT_VERSION
        		);
	        }
	        
	        
	        if ($screen->id == self::RS_INDIEBOOKING_SETTINGS_PAGE_ID) {
	            wp_register_script(
	                'rs_indiebooking_settings',
	                $adminJsPath . 'rsbp_indiebooking_settings.js',
	                array('rs_indiebooking_util'),
	                $RS_IB_SCRIPT_VERSION
	            );
	        }
	        
	        wp_register_script(
	            'rs_indiebooking_popup',
	            $adminJsPath . 'rsbp_indiebooking_popup.js',
	            array(),
	            $RS_IB_SCRIPT_VERSION
	        );
        }
    }
    
    public static function indiebookingLimitImages($limit) {
        return true;
    }
    
    public function include_backend_scripts() {
        global $RS_IB_VERSION;
        
        $screen         = get_current_screen();
        $limitImages    = false;
        $limitImages    = apply_filters("rs_indiebooking_images_limitimages", $limitImages);
        
        $ibui_indiebooking_base_url     = array(
            "baseurl"   => get_site_url(),
            "adminurl"  => get_admin_url(),
            "apartmentuebersichturl" => get_admin_url()."/edit.php?post_type=rsappartment",
        );
        
        $adminTextArray = array(
            'nomoreimagestxt'   		=> __("You can only add 3 images in this version of indiebooking", 'indiebooking'),
            'nomoreapartmentstitle' 	=> __("You can't generate a new Apartment", 'indiebooking'),
            'nomoreapartmentscontent' 	=> __("To enable more Apartments you need to buy the extension pack", 'indiebooking'),
            'attention' 				=> __("Attention", 'indiebooking'),
            'confirmdeletebooking' 		=> __("Are you sure you want to delete this booking?", 'indiebooking'),
        	'confirmstornobooking' 		=> _x("Are you sure you want to cancel this booking?", 'storno', 'indiebooking'),
        	'confirmcancelinquiry' 		=> _x("Are you sure you want to cancel this inquiry?", 'inquiry', 'indiebooking'),
        	'confirmcancel' 			=> __("Are you sure you want to cancel?", 'indiebooking'),
            'confirmpayment' 			=> __("Are you sure you want to confirm the payment?", 'indiebooking'),
        	'confirmdepositpayment' 	=> __("Are you sure you want to confirm the deposit?", 'indiebooking'),
        	'confirminquiry'			=> __("Are you sure you want to confirm the inquiry?", 'indiebooking'),
            'limitimages'       		=> $limitImages,
            'maximagecount'     		=> 3,
        	'btnCancel' 				=> __('Cancel', 'indiebooking'),
        );
        
        $adminBookingContactTexts = array(
        	'btnSave' 		=> __('Save', 'indiebooking'),
        	'btnCancel' 	=> __('Cancel', 'indiebooking'),
        	'btnNext' 		=> __('Next', 'indiebooking'),
        	'btnClose'		=> __('Close', 'indiebooking'),
        	'btnShowPrices'	=> __('show prices', 'indiebooking'),
        	'btnFinalize'	=> __('finalize booking', 'indiebooking'),
        	'errCheckData' 	=> __('please check your data', 'indiebooking'),
        );
        
        $ajaxObject = array(
            'ajaxURL' => admin_url('admin-ajax.php'),
        	'ib_ajax_nonce' => wp_create_nonce('rs-indiebooking-admin-ajax-nonce'),
        );
        wp_localize_script('rs_indiebooking_appartment_admin', 'indiebooking_admin_ajaxObject', $ajaxObject);
        wp_localize_script('rs_indiebooking_appartment_admin', 'global_admin_apartment_texts', $adminTextArray);
        wp_localize_script('rs_indiebooking_appartment_admin', 'admin_booking_contact_popup_data', $adminBookingContactTexts);
        wp_localize_script('rs_indiebooking_appartment_admin', 'global_admin_links', $ibui_indiebooking_base_url);
        
        wp_enqueue_script( 'rs_indiebooking_appartment_admin');
        
        if ($screen->id == self::RS_INDIEBOOKING_SETTINGS_PAGE_ID) {
	        wp_localize_script('rs_indiebooking_settings', 'indiebooking_admin_settings_ajaxObject', $ajaxObject);
            wp_enqueue_script( 'rs_indiebooking_tabulator_script' );
            wp_enqueue_script( 'rs_indiebooking_settings');
        }
        
        $popupTxtArray = array(
        		'btnSave' 	=> __('Save', 'indiebooking'),
        		'btnCancel' => __('Cancel', 'indiebooking'),
        		'btnNext' 	=> __('Next', 'indiebooking'),
        );
        wp_localize_script('rs_indiebooking_popup', 'indiebooking_admin_popup_ajaxObject', $ajaxObject);
        wp_localize_script('rs_indiebooking_popup', 'indiebooking_popup_txt', $popupTxtArray);
        
        wp_enqueue_script( 'rs_indiebooking_popup');
        wp_enqueue_script( 'rs_indiebooking_zabuto_pick_admin');

        
        wp_localize_script('rsbp_appartment_admin', 'indiebooking_admin_ajaxObject', $ajaxObject);
        wp_localize_script('rs_indiebooking_popup', 'indiebooking_admin_popup_ajaxObject', $ajaxObject);
    
        //         wp_enqueue_script( 'rs_zabuto_calendar', $this->plugin_url() . '/assets/js/default/' . 'rs_zabuto.js'.$RS_IB_VERSION );
        //         wp_enqueue_script( 'rsbp_dataTable', $this->plugin_url() . '/assets/js/' . 'datatables.min.js'.$RS_IB_VERSION );
        //         wp_enqueue_script( 'bootstrap', $this->plugin_url() . '/assets/bootstrap/js/bootstrap.min.js', array( 'jquery' ), '2.2.2', true );
    }
    
    
    public static function register_default_scripts() {
    	global $RS_IB_SCRIPT_VERSION;
    	
    	wp_register_script(
    		'rs_indiebooking_util',
    		self::plugin_url() . '/assets/js/default/indiebooking_util.js',
    		array('jquery', 'jquery-ui-core', 'jquery-effects-core'),
    		$RS_IB_SCRIPT_VERSION
    	);
    }
    
    public static function register_default_frontend_scripts() {
    	global $RS_IB_SCRIPT_VERSION;
    	
    	if (!wp_script_is('rs_indiebooking_bootstrap_multiselect_script', 'registered')) {
	    	wp_register_script(
	    		'rs_indiebooking_bootstrap_multiselect_script',
	    		self::plugin_url() . '/assets/bootstrap_multiselect/bootstrap-multiselect.js',
	    		array('jquery', 'jquery-ui-core', 'jquery-ui-widget'),
	    		$RS_IB_SCRIPT_VERSION
			);
    	}
    }
    
    
    
    public static function register_frontend_scripts() {
        global $RS_IB_SCRIPT_VERSION;
        
        $frontendPath   = self::plugin_url() . '/assets/js/frontend/';
        
        wp_register_script(
            'rs_indiebooking_translation_script',
            $frontendPath . 'rs_ib_translation.js',
            array(),
            $RS_IB_SCRIPT_VERSION
        );
        
        wp_register_script(
            'rs_indiebooking_custom_autocomplete_box', $frontendPath . 'autocompletebox.js',
            array('jquery', 'jquery-ui-core', 'jquery-ui-widget'),
            $RS_IB_SCRIPT_VERSION
        );
        
        wp_register_script(
            'rs_indiebooking_custom_booking', $frontendPath . 'appartment_buchung.js',
            array('jquery', 'jquery-ui-core', 'jquery-ui-autocomplete', 'rs_indiebooking_util'),
            $RS_IB_SCRIPT_VERSION
        );
        
        wp_register_script( 'rs_indiebooking_google_autocomplete_script', $frontendPath . 'google_autocomplete_adress.js', array(), $RS_IB_SCRIPT_VERSION );
    }
    
    
    
    public function include_frontend_scripts() {
        global $RS_IB_VERSION;
        
        $my_current_lang	= get_locale();
        if ( function_exists('icl_object_id') ) {
        	$my_current_lang = apply_filters( 'wpml_current_language', NULL );
        }
        $languageArray = array(
        	'language' => $my_current_lang,
        );
        
        $ajaxObject = array(
            'ajaxURL'       => admin_url('admin-ajax.php'),
            'ib_ajax_nonce'	=> wp_create_nonce('rs-indiebooking-frontend-ajax-nonce'),
        );
        $googleData         = get_option( 'rs_indiebooking_settings_google');
        
        $googleApiKey       = "";
        if ($googleData) {
            $googleApiKey   = (key_exists('ib_google_api_key', $googleData)) ?  esc_attr__( $googleData['ib_google_api_key']) : "";
        }

        $googleKeys = array(
            'apikey'        => $googleApiKey,
        );
        
        wp_enqueue_script("jquery-ui-widget");
        wp_enqueue_script("jquery-ui-autocomplete");
        
        $ibTranslationArray = array(
        		'allSelected' 		=> __("All selected", 'indiebooking'),
        		'keineKategorie'	=> __("Category", 'indiebooking'),
        		'keineFeatures'		=> __("Features", 'indiebooking'),
        		'sicherAbfrage'	 	=> __("Are you sure?", 'indiebooking'),
        		'btnYes' 			=> __("Yes", 'indiebooking'),
        		'btnNo' 			=> __("No", 'indiebooking'),
        		'btnClose' 			=> __("Close", 'indiebooking'),
        		'Error' 			=> __("Error", 'indiebooking'),
        		'attention'			=> __('Attention', 'indiebooking'),
        		'minnaechte' 		=> __("below minimum number of nights", 'indiebooking'),
        		'almostOutOfTime'	=> __("You booking time has almost expired, would you like to renew it? ", 'indiebooking'),
        		'sicherAbfrage_CancelBooking' => __("Are you sure you want to cancel the booking?", 'cancel', 'indiebooking'),
        );
        wp_localize_script('rs_indiebooking_translation_script', 'rs_indiebooking_translation_txt', $ibTranslationArray);
        wp_enqueue_script( 'rs_indiebooking_translation_script');
        

        wp_enqueue_script('rs_indiebooking_custom_autocomplete_box');
        
        $ibBookingTxtArray = array(
        		'no_valid_date' 	=> __("Please select a valid date before you book", 'indiebooking')
        );
        wp_localize_script('rs_indiebooking_custom_booking', 'ib_google_api', $googleKeys);
        wp_localize_script('rs_indiebooking_custom_booking', 'indiebooking_frontend_ajaxObject', $ajaxObject);
        wp_localize_script('rs_indiebooking_custom_booking', 'indiebooking_language', $languageArray);
        wp_localize_script('rs_indiebooking_custom_booking', 'rs_indiebooking_booking_txt', $ibBookingTxtArray);
        wp_enqueue_script('rs_indiebooking_custom_booking');
//         wp_enqueue_script('heartbeat');
        
        wp_enqueue_script('rs_indiebooking_bootstrap_multiselect_script');
        
        wp_localize_script('rs_indiebooking_google_autocomplete_script', 'ib_google_api', $googleKeys);
        wp_enqueue_script( 'rs_indiebooking_google_autocomplete_script');
        
        $postType = get_post_type( get_the_ID() );
        if ($postType == "rsappartment" || $postType == "rsappartment_buchung") {
            wp_enqueue_script( 'ib_rs_appartment_slider', $this->plugin_url() . '/assets/js/frontend/' . 'appartment_buchung_slider.js'.$RS_IB_VERSION );
            wp_enqueue_script( 'ib_rs_jssor_slider',
                                $this->plugin_url() . '/assets/js/frontend/jssor_slider/' . 'jssor.slider.mini.js'.$RS_IB_VERSION );
        }

//         wp_localize_script('ib_rs_custom_booking', 'ajaxObject', $ajaxObject);
    }

    
    public function include_default_scripts() {
        global $RS_IB_VERSION;
        global $RS_IB_SCRIPT_VERSION;
        
        $jsPath      = self::plugin_url() . '/assets/js/';
        
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script("jquery-ui-core");
    
        wp_enqueue_script("jquery-ui-dialog");
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script("jquery-ui-draggable");
        wp_enqueue_script("jquery-ui-sortable");
        wp_enqueue_script("jquery-ui-droppable");
        wp_enqueue_script("jquery-ui-selectable");
        wp_enqueue_script("jquery-ui-slider");
        wp_enqueue_script("jquery-ui-spinner");
        wp_enqueue_script("jquery-ui-tooltip");
        
        wp_enqueue_script("rs_indiebooking_bootstraptour");
        wp_enqueue_script("rs_indiebooking_bootstraptour_admin_script");
        wp_enqueue_script("rs_indiebooking_jquery_ui_extensions");
        wp_enqueue_script("rs_indiebooking_jquery_tooltipster");
        
        wp_enqueue_script("rs_indiebooking_util");

//         wp_enqueue_script( 'jqueryUi1121', $this->plugin_url() . '/assets/js/jquery-ui-1.12.1/' . 'jquery-ui.js'.$RS_IB_VERSION );
        //         wp_enqueue_script('jqueryUi1121_online',  "https://code.jquery.com/ui/1.12.1/jquery-ui.js");

//         wp_enqueue_script( 'rs_indiebooking_indiebooking_calendar_base',
//         		$jsPath.'zabuto_calendar/' . 'indiebooking_calendar.js',
//         		array( 'jquery' ),
//         		$RS_IB_SCRIPT_VERSION );
        wp_register_script( 'rs_indiebooking_indiebooking_calendar_base',
		        $jsPath.'zabuto_calendar/' . 'indiebooking_calendar.js',
		        array( 'jquery' ),
		        $RS_IB_SCRIPT_VERSION );
        
        wp_register_script( 'rs_indiebooking_zabuto_calendar',
                                $jsPath.'zabuto_calendar/indiebooking_calendar_functions.js',
                                array('rs_indiebooking_indiebooking_calendar_base'),
                                $RS_IB_SCRIPT_VERSION );
        
        $curLanguage	= get_locale();
        $curLanguage	= substr($curLanguage, 0,2);
        if ( function_exists('icl_object_id') ) {
        	$curLanguage = 'ibwpml';
        }
        $monthLabelArray = array(
        	__("January",'indiebooking'),
        	__("February",'indiebooking'),
        	__("March",'indiebooking'),
        	__("April",'indiebooking'),
        	__("May",'indiebooking'),
        	__("June",'indiebooking'),
        	__("July",'indiebooking'),
        	__("August",'indiebooking'),
        	__("September",'indiebooking'),
        	__("October",'indiebooking'),
        	__("November",'indiebooking'),
        	__("December",'indiebooking')
        );
        
        $dowLabelArray = array(
        	__("Mon",'indiebooking'),
        	__("Tue",'indiebooking'),
        	__("Wed",'indiebooking'),
        	__("Thu",'indiebooking'),
        	__("Fri",'indiebooking'),
        	__("Sat",'indiebooking'),
        	__("Sun",'indiebooking')
        );
        
        $localLabels = array(
        	'months' => $monthLabelArray,
        	'dow'	=> $dowLabelArray
        );
        
        $zabutoTexts 	= array(
            'bookedTxt'         => __("booked", 'indiebooking'),
            'freeTxt'           => __("free", 'indiebooking'),
            'notbookableTxt'    => __("not bookable", 'indiebooking'),
            'arrivaldayTxt'     => __("arrival day", 'indiebooking'),
        	'mindaterange'		=> __("minimum range", "indiebooking"),
        	'language'			=> $curLanguage,
        );
        
        wp_localize_script( 'rs_indiebooking_indiebooking_calendar_base', 'ibui_local_labels', $localLabels);
        wp_enqueue_script( 'rs_indiebooking_indiebooking_calendar_base');
        
        wp_localize_script( 'rs_indiebooking_zabuto_calendar', 'ibui_zabutotxt', $zabutoTexts);
        wp_enqueue_script( 'rs_indiebooking_zabuto_calendar');
    }
    
    /**
     * Get the plugin path.
     * @return string
     */
    public static function plugin_path() {
        return untrailingslashit( plugin_dir_path( __FILE__ ) );
    }
    
    public static function file_upload_path() {
        $upload_dir     = wp_upload_dir();
        $fileUploadDir  = $upload_dir['basedir'].DIRECTORY_SEPARATOR.'indiebooking_uploads';
        if (wp_mkdir_p($fileUploadDir)) {
            return $fileUploadDir;
        } else {
            return new WP_Error( 'broke', __( "indiebooking fileupload directory could not be create", "indiebooking" ) );
        }
        return $fileUploadDir;
    }
    
    public static function file_upload_url() {
    	$uploadDir			= wp_upload_dir();
    	$baseUploadDir		= $uploadDir['baseurl'].DIRECTORY_SEPARATOR.'indiebooking_uploads';
    	return $baseUploadDir;
    }
    
    /**
     * Get the plugin url.
     * @return string
     */
    public static function plugin_url() {
        return untrailingslashit( plugins_url( '/', __FILE__ ) );
    }
    
    /*
     * Diese Klasse ist ein Singleton --> Objekterstellung ueber instance()
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
//         register_activation_hook( __FILE__, array( 'this', 'activate' ) );
        //$this->define_constants();
        	$timezonestring = get_option('timezone_string');
        	if (!is_null($timezonestring) && $timezonestring != "") {
            	date_default_timezone_set(get_option('timezone_string'));
        	}

            $this->defaultIncludes();
            $this->includes();
            $this->frontend_includes();
            
            
//             RS_Indiebooking_Log_Controller::send_log_to_server();
//             _x( 'Appartment Overview', 'Page title', 'indiebooking' );
//             $this->test_include();

//         apply_filters("rs_indiebooking", $value)
//         $this->init_hooks();
    }
    
    /**
     * Die Methode wurde in die rewasoft_indiebooking.php verschoben
     */
    public function activate() {
    }
    
    public function init_options() {
        update_option('rs_indiebooking_version', self::RS_IB_VER);
//         add_option('rs_indiebooking_db_version', self::RS_IB_DB_VER);
    }
    
    public function translate() {
    }
    
    /**
     * Get the template path.
     * @return string
     */
    public function template_path() {
        return apply_filters( 'rs_indiebooking_template_path', 'indiebooking/' );
    }
    
    public function include_backend_styles($hook) {
        global $RS_IB_VERSION;

        $isIndiebookingPage = self::isIndiebookingPage($hook);
        if ($isIndiebookingPage) {
	        wp_enqueue_style( 'appartment_style', $this->plugin_url() . '/assets/css/' . 'admin_appartment.css'.$RS_IB_VERSION );
	        wp_enqueue_style( 'admin_element_style', $this->plugin_url() . '/assets/css/' . 'admin_elements.css'.$RS_IB_VERSION );
	        wp_enqueue_style( 'admin_columns', $this->plugin_url() . '/assets/css/' . 'admin_columns.css'.$RS_IB_VERSION );
	
	        wp_enqueue_style( 'rs_indiebooking_tooltipster_style_core',
	                $this->plugin_url() . '/assets/js/tooltipster/tooltipster.core.css',
	                array()
	        );
	        wp_enqueue_style( 'rs_indiebooking_tooltipster_style_bundle',
	            $this->plugin_url() . '/assets/js/tooltipster/tooltipster.bundle.css',
	            array()
	        );
	        wp_enqueue_style( 'rs_indiebooking_tooltipster_style_borderless',
	            $this->plugin_url() . '/assets/js/tooltipster/tooltipster-sideTip-borderless.min.css',
	            array()
	        );
	        
	        wp_enqueue_style( 'rs_indiebooking_bootstrap_tour_style',
	            $this->plugin_url() . '/assets/js/bootstrap_tour/bootstrap-tour-standalone.min.css',
	            array()
	        );
	        
	        wp_enqueue_style( 'rs_indiebooking_bootstrap',
	            $this->plugin_url() . '/assets/css/ib_bootstrap.css',
	            array(),
	            $RS_IB_VERSION
	        );
	        
	        wp_enqueue_style( 'rs_indiebooking_ui_elements',
	            $this->plugin_url() . '/assets/css/ib_ui_elements.css',
	            array('RS_IB_default_styles', 'rs_indiebooking_bootstrap'),
	            $RS_IB_VERSION
	        );
	        
	        wp_enqueue_style( 'rs_indiebooking_admin_settings',
	            $this->plugin_url() . '/assets/css/admin_settings.css',
	            array('rs_indiebooking_ui_elements'),
	            $RS_IB_VERSION
	        );
	        
	        wp_enqueue_style( 'admin_indiebooking_taxonomies',
	            $this->plugin_url() . '/assets/css/admin_taxonomies.css',
	            array(),
	            $RS_IB_VERSION
	        );
        
	        wp_enqueue_style( 'admin_indiebooking_tabulator',
	            $this->plugin_url() . '/assets/js/tabulator/dist/css/tabulator.min.css',
	            array(),
	            $RS_IB_VERSION
            );
	        
//         wp_enqueue_style( 'admin_settings', $this->plugin_url() . '/assets/css/' . 'admin_settings.css'.$RS_IB_VERSION );
//         wp_enqueue_style( 'admin_indiebooking_taxonomies', $this->plugin_url() . '/assets/css/' . 'admin_taxonomies.css'.$RS_IB_VERSION );
        	wp_enqueue_style( 'genericons', $this->plugin_url() . '/assets/genericons/genericons.css', array(), '3.03' );
        }
    }
    
    public function show_apartment_overview() {
    	$template 	= "";
    	$ausgabe	= "";
    	if (!is_admin()) {
	    	$file 			= 'appartmentuebersicht.php';
	    	$find[] 		= $file;
	    	$find[] 		= 'templates/' . $file;
	    	$template       = locate_template( array_unique( $find ) );
	    	if ( ! $template) {
	    		$template = RS_INDIEBOOKING_INIT()->plugin_path() . '/templates/shortcodes/' . $file;
	    	}
	    	/*
	    	 * Update Carsten Schmitt 04.10.2018
	    	 * die Ausgabe des shortcodes muss als return-Wert erfolgen und darf nicht eine eigene
	    	 * Ausgabe generieren. Daher habe ich die Ausgabe nun mittels ob_start abgefangen und speichere
	    	 * diese in $ausgabe. Das scheint zu funktionieren.
	    	 */
	    	ob_start();
			include_once($template);
			$ausgabe    = ob_get_contents();
			@ob_end_clean();
    	}
    	return $ausgabe;
    }
    
    public function defaultIncludes() {
        add_filter( 'admin_body_class', array($this, 'ibui_add_indiebooking_body_class' ));
        
        add_action( 'admin_enqueue_scripts', array( $this, 'include_default_styles' ));
        add_action( 'wp_enqueue_scripts', array( $this, 'include_default_styles' ));
        
        add_action( 'admin_enqueue_scripts', array( $this, 'include_default_scripts' ));
        add_action( 'wp_enqueue_scripts', array( $this, 'include_default_scripts' ));
        
        add_action( 'rs_ib_check_booking_event', array($this, 'checkBooking'), 10, 1 );
        
        add_shortcode('rs_ib_show_apartment_overview', array($this, 'show_apartment_overview'));
		
		
        include_once( $this->plugin_path().'/includes/rs_ib_template_print_functions.php' );
        include_once( $this->plugin_path().'/includes/util/rs_ib_data_validation.php');
        
        include_once( $this->plugin_path().'/includes/widgets/rs_indiebooking_widget_controller.php');
        
        //Widget wieder deaktiviert. Lasse es vorerst doch bei der vorherigen Loesung
// 		include_once( $this->plugin_path().'/includes/rs_ib_apartment_widget.php' );
// 		add_action( 'widgets_init',  array($this, 'register_indiebooking_widget') );
    }
    
//     public function register_indiebooking_widget() {
//     	register_widget( 'rs_ib_apartment_widget' );
//     }
    
    /**
     * Fügt dem Body-Element eine Klasse hinzu, damit per css darauf verwiesen werden kann.
     * Diese Klasse soll aber nur auf den Indiebookingseiten eingebunden werden.
     * @param unknown $classes
     * @return string
     */
    public function ibui_add_indiebooking_body_class($classes) {
        if (get_post_type()) {
            $postType = get_post_type();
            if ($postType == "rsappartment" || $postType == "rsappartment_buchung") {
                $classes .= ' ibui_body_wrapper';
            }
        } else {
            $screen     = get_current_screen();
            if ($screen->taxonomy == RS_IB_Model_Appartmentkategorie::RS_TAXONOMY) {
                $classes .= ' ibui_body_wrapper';
            }
            if ($screen->id == "indiebooking_page_rs_einstellungen") {
                $classes .= ' ibui_body_wrapper';
            }
        }
        return $classes;
    }
    
    /* @var $buchungTable RS_IB_Table_Appartment_Buchung */
    /* @var $buchungKopfTable RS_IB_Table_Buchungskopf */
    public function checkBooking($bookingId) {
        global $RSBP_DATABASE;
        
        RS_Indiebooking_Log_Controller::write_log('checkBooking '.$bookingId, __LINE__, __CLASS__, RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_INFO);
        
        $buchungTable       = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $buchungKopfTable   = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
        
//         $buchungKopfTable->loadBooking($buchungsNr)
        
        $modelBuchung		= $buchungTable->checkBookingTime($bookingId);
        /*
         * Update Carsten Schmitt 21.08.2018
         * Sofern die Verbleibende Zeit ungleich NULL und größer 0 ist, soll ein erneuetes Event geplant werden,
         * damit auch nach ablauf dieser Zeit geprueft wird, ob eine Buchung abgebrochen werden muss.
         * Ist die RemainingTime NULL, bedeutet es, dass die Buchung nicht mehr am laufen ist und somit auch keine erneute Prüfung
         * durchgefürht werden muss.
         * Der initiale Aufruf dieser Buchung geschieht im appartment_buchung_controller.php, hier jedoch nur, wenn die
         * Buchung nicht aus der Administration aufgerufen wird.
         * Demnach muss an dieser Stelle nicht nochmal geprüft werden, ob die Buchung eine Admin-Buchung ist oder nicht
         */
        if (!is_null($modelBuchung->getRemainingtTime()) && $modelBuchung->getRemainingtTime() > 0) {
//         	$timestamp = wp_next_scheduled( 'rs_ib_check_booking_event', array( $bookingId ) );
//         	wp_unschedule_event( $timestamp, 'rs_ib_check_booking_event', array( $bookingId ) );
        	wp_schedule_single_event( time() + $modelBuchung->getRemainingtTime(), 'rs_ib_check_booking_event', array( $bookingId ) );
//         	$timestamp = wp_next_scheduled( 'rs_ib_check_booking_event' );
        }
    }
    
    /* @var $theme WP_Theme */
    public function include_default_styles() {
        global $RS_IB_SCRIPT_VERSION;
        
        $cssPath    = self::plugin_url() . '/assets/css/';
        $jsPath     = self::plugin_url() . '/assets/js/';
        
        wp_enqueue_style( 'rs_ib_zabuto_kalendar_style_default', $cssPath . 'zabuto_kalender.css', array(), $RS_IB_SCRIPT_VERSION );
        wp_enqueue_style( 'RS_IB_glyphicons_styles', $cssPath . 'bootstrap-glyphicons.css', array(), $RS_IB_SCRIPT_VERSION );
        wp_enqueue_style( 'RS_IB_default_styles', $cssPath . 'style.css', array(), $RS_IB_SCRIPT_VERSION );
        wp_enqueue_style( 'zabuto_calendar_style', $jsPath.'zabuto_calendar/zabuto_calendar.css', array(), $RS_IB_SCRIPT_VERSION );
        
        wp_enqueue_style( 'rs_indiebooking_customized_jquery-ui-spinner',
                            $jsPath .'jquery-ui-extensions/ext-jquery-ui.css',
                            array(),
                            $RS_IB_SCRIPT_VERSION );
        
        $theme      = wp_get_theme('indiebooking_rr_cs');
        if ($theme->exists() && is_admin()) {
            wp_enqueue_style( 'zabuto_calendar_style_theme',
                                $theme->get_template_directory_uri() . '/js/zabuto_calendar.css',
                                array(),
                                $RS_IB_SCRIPT_VERSION );
        }
    }
    
    /*
     * Inkludiert alle benuetigten Dateien.
     */
    public function includes() {
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        
        add_action('admin_head', array( $this, 'gavickpro_add_my_tc_button'));
        
        include_once( $this->plugin_path().'/includes/controller/parent_controller/rs_indiebooking_log_controller.php' );
        include_once( $this->plugin_path().'/includes/database/cRS_IB_DatabaseController.php' );
        include_once( $this->plugin_path().'/includes/util/rs_ib_print_util.php');
        include_once( $this->plugin_path().'/includes/util/rs_ib_print_util_data_object.php');
        include_once( $this->plugin_path().'/includes/util/rs_ib_date_util.php' );
        include_once( $this->plugin_path().'/includes/util/rs_ib_currency_util.php' );
//         include_once( $this->plugin_path().'/includes/util/rs_ib_price_calculation_util.php' );
        include_once( $this->plugin_path().'/includes/data_container/cRS_IB_Customer.php');
        include_once( $this->plugin_path().'/includes/cRS_IB_post_type_loader.php' ); //laed / registriert die Post types
        include_once( $this->plugin_path().'/includes/admin/cRS_IB_Admin.php' ); //ist fuer die Erstellung des Backendbereiches zustaendig
        include_once( $this->plugin_path().'/includes/controller/indiebooking_settings_controller.php' );
        include_once( $this->plugin_path().'/includes/controller/rs_ib_backend_controller_wp_ajax.php');
        include_once( $this->plugin_path().'/includes/controller/rs_ib_appartment_uebersicht_controller_wp_ajax.php');
        include_once( $this->plugin_path().'/includes/controller/rs_ib_indiebooking_basic_taxonomy_controller_wp_ajax.php');
        include_once( $this->plugin_path().'/includes/admin/view/RS_IB_Booking_View.php' );
        
        include_once( $this->plugin_path().'/includes/data_container/cRS_IB_SearchData.php');
        include_once( $this->plugin_path().'/includes/controller/parent_controller/rs_ib_mail_controller.php' );
        
        if (!is_admin() || ((defined('DOING_AJAX') && DOING_AJAX))) {
            include_once( 'includes/apartment/frontend/indiebooking_apartment_frontend_helper.php' );
        }
        
        if(!(defined('DOING_AJAX') && DOING_AJAX)) {
            add_action( 'admin_enqueue_scripts', array( $this, 'include_backend_scripts' ), 15); //15 damit ggf. das pro plugin zuerst greift.
            add_action( 'admin_enqueue_scripts', array( $this, 'include_backend_styles' ));
        }
        
        if (!is_plugin_active('wp-mail-smtp/wp_mail_smtp.php')) {
            add_action( 'phpmailer_init', array( $this, 'wpse8170_phpmailer_init' ));
        }
    }
    
    public function gavickpro_add_my_tc_button() {
        global $typenow;
        // check user permissions
        if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
            return;
        }
        // verify the post type
//         if( ! in_array( $typenow, array( 'post', 'page' ) ) )
//             return;
        // check if WYSIWYG is enabled
        if ( get_user_option('rich_editing') == 'true') {
            add_filter("mce_external_plugins", array( $this, "gavickpro_add_tinymce_plugin"));
            add_filter('mce_buttons', array( $this, 'gavickpro_register_my_tc_button'));
        }
    }
    
    public function gavickpro_add_tinymce_plugin($plugin_array) {
        $plugin_array['rs_indiebooking_tc_button'] = plugins_url( '/assets/js/text-button.js', __FILE__ ); // CHANGE THE BUTTON SCRIPT HERE
        return $plugin_array;
    }
    
    public function gavickpro_register_my_tc_button($buttons) {
//         array_push($buttons, "gavickpro_tc_button");
//         $mybuttons  = array();
//         $mybuttons[0]["name"]   = "rs_indiebooking_tc_button";
//         $mybuttons[0]["text"]   = __("Add Customer Name", 'indiebooking');
        
//         $mybuttons[1]["name"]   = "rs_indiebooking_tc_button_2";
//         $mybuttons[1]["text"]   = __("Add Booking Number", 'indiebooking');
        
//         $mybuttons[2]["name"]   = "rs_indiebooking_tc_button_3";
//         $mybuttons[2]["text"]   = __("Add saluation", 'indiebooking');
        
//         $mybuttons[3]["name"]   = "rs_indiebooking_tc_button_4";
//         $mybuttons[3]["text"]   = __("Add title", 'indiebooking');
//         array_push($buttons, $mybuttons);
        array_push($buttons, "rs_indiebooking_tc_button");
        array_push($buttons, "rs_indiebooking_tc_button_2");
        array_push($buttons, "rs_indiebooking_tc_button_3");
        array_push($buttons, "rs_indiebooking_tc_button_4");
        return $buttons;
    }
     
    public function wpse8170_phpmailer_init( PHPMailer $phpmailer ) {
        $mailFrom               = get_option( 'mail_from' );
        $mailFromName           = get_option( 'mail_from_name' );
        $smtp_host              = get_option( 'smtp_host' );
        $smtp_port              = get_option( 'smtp_port' );
        $smtp_user              = get_option( 'smtp_user' );
        $smtp_pass              = get_option( 'smtp_pass' );

        $phpmailer->IsSMTP();
        $phpmailer->From        = $mailFromName;
        $phpmailer->Sender      = $mailFrom;
        $phpmailer->Host        = $smtp_host;
        $phpmailer->Port        = $smtp_port;
        $phpmailer->SMTPAuth    = true; // if required
        $phpmailer->Password    = $smtp_pass; // if required
        $phpmailer->Username    = $smtp_user; // if required
//         $phpmailer->SMTPSECURE  = 'SSL'; // ENABLE IF REQUIRED, 'TLS' IS ANOTHER POSSIBLE VALUE
    }
    
    public function include_frontend_styles() {
        wp_enqueue_style( 'rs_ib_slider_styles', $this->plugin_url() . '/assets/css/' . 'appartment_gallery_slider.css' );
        wp_enqueue_style( 'rs_ib_bootstrap_multiselect',
                            $this->plugin_url() . '/assets/bootstrap_multiselect/bootstrap-multiselect.css' );
    }
    
    public function frontend_includes() {
//         include_once( $this->plugin_path(). "/includes/template_methods/javascript_translation_texts.php" );
        
        include_once( $this->plugin_path().'/includes/template_methods/rs_ib_template_buchungsanzeige.php' );
        include_once( $this->plugin_path().'/includes/rs_ib_template_functions.php' );
        include_once( $this->plugin_path().'/includes/rs_ib_template_hooks.php' );
        
        include_once( $this->plugin_path().'/includes/rs_ib_hook_mail_functions.php' );
        include_once( $this->plugin_path().'/includes/rs_ib_hooks.php' );
        
        include_once( $this->plugin_path().'/includes/cRS_Template_loader.php' );  // Template Loader
        
        include_once( $this->plugin_path().'/includes/controller/appartment_buchung_controller_wp_ajax.php');
        
        if ( $this->is_active_theme( 'twentyseventeen' ) ) {
//         	include_once( $this->plugin_path(). '/includes/theme-support/rs_indiebooking_twenty_seventeen.php' );
        }
        if ( $this->is_active_theme( 'twentysixteen' ) ) {
//         	include_once( $this->plugin_path(). '/includes/theme-support/rs_indiebooking_twenty_sixteen.php' );
        }
        add_action( 'wp_enqueue_scripts', array( $this, 'include_frontend_scripts' ));
        add_action( 'wp_enqueue_scripts', array( $this, 'include_frontend_styles' ));
    }
    
    /**
     * Check the active theme.
     */
    private function is_active_theme( $theme ) {
    	return get_template() === $theme;
    }
    
    public static function init_rewa_booking() {
        register_setting('rewa_booking_gruppe', 'rewa_booking_options', 'rewa_booking_validate');
    }
    
    public static function rewa_booking_validate($input) {
        return $input;
    }
}
// endif;