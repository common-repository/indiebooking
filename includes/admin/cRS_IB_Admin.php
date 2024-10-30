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
// $bool = class_exists('RS_IB_Admin');
// if ( ! class_exists( 'RS_IB_Admin' ) ) :
// if (class_exists( 'RS_IB_Admin' ) ) {
// 	return;
// }
class RS_IB_Admin
{
    public function __construct() {
//         if(!(defined('DOING_AJAX') && DOING_AJAX)) {
            //alle includes und hooks des Adminbereichs muessen nur ausgefuehrt werden, wenn kein AJAX ausgefuehrt wird!
            $this->includes();
            $this->init_hooks();
//         }
    }
    
    private function init_hooks() {
        $this->initAdminHooks();
    }
    
    private function includes() {
        include_once 'rs_indiebooking_wp_ajax_handler.php';
        if(!(defined('DOING_AJAX') && DOING_AJAX)) {
            include_once 'view/RS_IB_Hello_View_Box.php';
            include_once 'view/RS_IB_Settings_View.php';
            include_once 'cRS_IB_Admin_Appartment.php';
            include_once 'cRS_IB_Admin_Booking.php';
        }
        include_once 'cRS_IB_Appartmentcategory.php';
    }
    
    private function initAdminHooks() {
        add_action( 'in_admin_header', array($this, 'rs_indiebooking_add_modal_div_to_header' ));
        
        add_action( 'rs_indiebooking_admin_settings_google_informations', array($this, 'addGoogleInfos'));
        add_action( 'rs_indiebooking_apartment_settings_google_informations', array($this, 'addApartmentGoogleInfos'), 10, 1);
        add_action( 'admin_init', array($this, 'registerSettings' ));
        add_action( 'admin_menu', array($this, 'initAdminMenu'));
        add_action( "manage_posts_custom_column",   array($this, "default_custom_columns"));
        add_filter( 'parent_file', array($this, 'rs_indiebooking_set_current_menu'));
        add_filter( 'custom_menu_order', array($this,'rs_indiebooking_order_submenu') );
        
        add_action('admin_notices', array($this,'rs_ib_payment_admin_notice'));
    }
    
    public function rs_ib_payment_admin_notice() {
    	$paymentPluginActive		= false;
    	$paymentDataOk				= true;
    	$stripeData					= null;
    	$paypalData					= null;
    	if (is_plugin_active('indiebooking-stripe/indiebooking-stripe.php')) {
    		$stripeData            	= get_option( 'rs_indiebooking_settings_stripe');
    		$paymentPluginActive	= true;
    	}
    	if (is_plugin_active('indiebooking-paypal/indiebooking-paypal.php')) {
    		$paypalData         	= get_option( 'rs_indiebooking_settings_paypal');
    		$paymentPluginActive	= true;
    	}
    	
    	if ($paymentPluginActive) {
    		$paymentDataOk				= false;
    		$paymentData 				= get_option( 'rs_indiebooking_settings_payment');
    		if ($paymentData) {
    			$payperInvoiceKz		= (key_exists('payperinvoice_kz', $paymentData)) ? esc_attr__( $paymentData['payperinvoice_kz'] ) : "";
    			$invoiceMinDays			= (key_exists('invoice_availability', $paymentData)) ? esc_attr__( $paymentData['invoice_availability'] ) : 0;
    			if ($payperInvoiceKz == "on") {
    				$paymentDataOk		= (intval($invoiceMinDays) == 0);
    			}
    		}
    		if (!is_null($paypalData) && !$paymentDataOk) {
	    		$paypalKz               = (key_exists('paypal_kz', $paypalData))    ?  esc_attr__( $paypalData['paypal_kz'] )     : "";
	    		$paypalExpressKz        = (key_exists('paypal_express_kz', $paypalData)) ?  esc_attr__( $paypalData['paypal_express_kz'] ) : "";
	    		$ppInvoiceMinDays		= (key_exists('invoice_availability', $paypalData)) ?  esc_attr__( $paypalData['invoice_availability'] ) : 0;
	    		if ($paypalKz == "on" || $paypalExpressKz == "on") {
	    			$paymentDataOk		= (intval($ppInvoiceMinDays) == 0);
	    		}
    		}
    		if (!is_null($stripeData) && !$paymentDataOk) {
    			$stripeCreditCardKz		= (key_exists('stripe_kz', $stripeData)) 		?  esc_attr__( $stripeData['stripe_kz'] ) 		: "";
    			$stripeSofortKz			= (key_exists('stripe_sofort_kz', $stripeData)) ?  esc_attr__( $stripeData['stripe_sofort_kz']) : "";
    			
    			$stripeSofortMinDays	= (key_exists('sofort_availability', $stripeData)) ?  esc_attr__( $stripeData['sofort_availability']) : 0;
    			$stripeCreditCardMinDays = (key_exists('creditcard_availability', $stripeData)) ?  esc_attr__( $stripeData['creditcard_availability']) : 0;
    			
    			if ($stripeCreditCardKz == "on") {
    				$paymentDataOk		= (intval($stripeCreditCardMinDays) == 0);
    			}
    			
    			if ($stripeSofortKz == "on" && !$paymentDataOk) {
    				$paymentDataOk		= (intval($stripeSofortMinDays) == 0);
    			}
    		}
    	}
    	
    	if (!$paymentDataOk) {
    		$msg = __('please check your payment settings. In some cases, your customer may not be able to choose a payment method.', 'indiebooking');
    		printf('<div class="notice notice-warning is-dismissible"><p>%s</p></div>', $msg);
    	}
    }
    
    /**
     * Fügt das Modal-Div zu jeder Admin Ansicht hinzu, damit die Ladeanimation bei jedem Laden angezeigt werden kann.
     */
    public function rs_indiebooking_add_modal_div_to_header() {
        global $submenu_file, $current_screen, $pagenow;
        // Set the submenu as active/current while anywhere in your Custom Post Type (nwcm_news)
        $post_type = $current_screen->post_type; //klappt
        if ($post_type == RS_IB_Model_Appartment_Buchung::RS_POSTTYPE ||
            $post_type == RS_IB_Model_Appartment::RS_POSTTYPE ||
            $current_screen->id == 'indiebooking_page_rs_ib_dashboard' ||
            $current_screen->id == 'indiebooking_page_rs_einstellungen') { ?>
            <div class="modal"></div>
            <?php
            do_action('rs_indiebooking_show_hello_view_box');
        }
    }
    
    public function addGoogleInfos() {
        //do nothing
    }
    
    public function addApartmentGoogleInfos($param) {
        //do nothing
    }
    
    public function initAdminMenu() {
        $pluginFolder = cRS_Indiebooking::RS_IB_PLUGIN_FOLDER;
        
        
//         add_menu_page('Indiebooking', 'Indiebooking', 'edit_posts', 'rs_indiebooking',
//                         '', plugins_url($pluginFolder.'/images/indiebooking-logo-w-16x16.png'), 3);
        add_menu_page('Indiebooking', 'Indiebooking', 'read_indiebooking', 'rs_indiebooking',
        		'', plugins_url($pluginFolder.'/images/indiebooking-logo-w-16x16.png'), 3);
        
        //buchungsplattform_anzeigen
        
//         add_submenu_page('rs_indiebooking', __('Dashboard', 'indiebooking'), __('Dashboard', 'indiebooking'), 'edit_posts', 'rs_ib_dashboard', array($this, 'initDashboardView'));
//         add_submenu_page('rs_indiebooking', _n('Apartmentoption', 'Apartmentoptions', 2, 'indiebooking'), _n('Apartmentoption', 'Apartmentoptions', 2, 'indiebooking'), 'manage_options', 'edit-tags.php?taxonomy=rsappartmentoption&post_type=rsappartment', false);
//         add_submenu_page('rs_indiebooking', _n('Apartmentcampaign', 'Apartmentcampaigns', 2, 'indiebooking'),_n('Apartmentcampaign', 'Apartmentcampaigns', 2, 'indiebooking'), 'manage_options', 'edit-tags.php?taxonomy=rsappartmentaktion&post_type=rsappartment', false);
//         add_submenu_page('rs_indiebooking', _n('Coupon', 'Coupons', 2, 'indiebooking'),_n('Coupon', 'Coupons', 2, 'indiebooking'), 'manage_options', 'edit-tags.php?taxonomy=rsgutschein&post_type=rsappartment', false);
//         add_submenu_page('rs_indiebooking', __('Settings', 'indiebooking'),
//             __('Settings', 'indiebooking'), 'edit_posts', 'rs_ib_settings', array($this, 'initSettingsView'));
        
//         add_submenu_page('rs_indiebooking', __('Add new Apartment', 'indiebooking'),
//                 __('Add new Apartment', 'indiebooking'), 'edit_posts', 'post-new.php?post_type=rsappartment');
        
        add_submenu_page('rs_indiebooking', __('Settings', 'indiebooking'),
                __('Settings', 'indiebooking'), 'edit_posts', 'rs_einstellungen', array($this, 'initSettingsView'));
        
        //"appoptions"        => _n('Apartmentoption', 'Apartmentoptions', 2, 'indiebooking'),
//         add_submenu_page('rs_indiebooking', _n('Category', 'Categories', 2, 'indiebooking'),
//                 _n('Category', 'Categories', 2, 'indiebooking'), 'manage_options',
//                 'edit-tags.php?taxonomy=rsappartmentcategories&post_type=rsappartment', false);
		add_submenu_page('rs_indiebooking', __('Categories', 'indiebooking'),
			__('Categories', 'indiebooking'), 'manage_options', 'edit-tags.php?taxonomy=rsappartmentcategories&post_type=rsappartment', false);
//         add_submenu_page('rs_indiebooking', _n('Apartmentcategory', 'Apartmentcategories', 2, 'indiebooking'),_n('Apartmentcategory', 'Apartmentcategories', 2, 'indiebooking'), 'manage_options', 'edit-tags.php?taxonomy=rsappartmentcategories&post_type=rsappartment', false);
        
        
//         add_submenu_page('rs_indiebooking', _n('TestTax', 'TestTax', 2, 'indiebooking'),_n('TestTax', 'TestTaxes', 2, 'indiebooking'), 'manage_options', 'edit-tags.php?taxonomy=cstesttaxonomy&post_type=rsappartment', false);
    }

    
    function registerSettings() {
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings' );
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_bankdata' );
        
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_payment' );
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_filter' );
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_contact_required' );
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_google');
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_mwst' );
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_mwst_nextId' );
        
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_mail_inquiry_confirmation_txt' );
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_mail_inquiry_deny_txt' );
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_mail_booking_confirmation_txt' );
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_mail_booking_invoice_txt' );
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_mail_storno_confirmation_txt' );
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_booking_agb_txt' );
        
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_invoice_terms_of_payment_txt' );
        
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_mail_inquiry_subject');
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_mail_inquiry_deny_subject');
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_mail_confirm_subject');
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_mail_invoice_subject');
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_mail_storno_subject');
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_mail_deposit_reminder_subject');
        
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_time_to_book');
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_currency');
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_default_payment_method');
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_future_availability');
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_invoice_number_structure');
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_invoice_number_startsby');
        
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_stornobedingung_txt' );
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_storno' );
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_storno_nextId' );
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_mail_deposit_reminder_txt' );
        
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_show_welcome_kz' );
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_booking_inquiries_kz' );
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_booking_by_categorie_kz' );
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_allow_statistics_kz' );
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_admin_email' );
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_admin_email_attachment' );
        register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_datev_email' );
        
//         register_setting( 'rs_indiebooking_option_group', 'rs_indiebooking_settings_payment' );
        
        if (!is_plugin_active('wp-mail-smtp/wp_mail_smtp.php')) {
            register_setting( 'rs_indiebooking_option_group', 'mail_from');
            register_setting( 'rs_indiebooking_option_group', 'mail_from_name');
            register_setting( 'rs_indiebooking_option_group', 'smtp_host');
            register_setting( 'rs_indiebooking_option_group', 'smtp_port');
            register_setting( 'rs_indiebooking_option_group', 'smtp_user');
            register_setting( 'rs_indiebooking_option_group', 'smtp_pass');
        }
    }
    
    function rs_indiebooking_order_submenu($menu_ord) {
        global $submenu;
        
        $menuCount = 0;
        
        if (is_plugin_active('indiebooking-advanced/indiebooking_advanced.php')) {
            $dashboardPosition  = $menuCount++;
            $buchungPosition    = $menuCount++;
            $aktionPosition     = $menuCount++;
            $gutscheinPosition  = $menuCount++;
            $realgutscheinPosition  = $menuCount++;
            $apartmentPosition  = $menuCount++;
            $preispflegePosition = $menuCount++;
            $optionenPosition   = $menuCount++;
            $categoryPosition   = $menuCount++;
            $settingsPosition   = $menuCount++;
        } else {
            $buchungPosition    = $menuCount++;
            $apartmentPosition  = $menuCount++;
            $categoryPosition   = $menuCount++;
            $settingsPosition   = $menuCount++;
        }
//         $newApartmentPos    = $menuCount++;
        
        /*
         * admin.php?page=rs_ib_dashboard
         * edit.php?post_type=rsappartment
         * post-new.php?post_type=rsappartment
         * edit.php?post_type=rsappartment_buchung
         * edit-tags.php?taxonomy=rsappartmentoption&post_type=rsappartment
         * edit-tags.php?taxonomy=rsappartmentcategories&post_type=rsappartment
         * edit-tags.php?taxonomy=rsappartmentaktion&post_type=rsappartment
         * edit-tags.php?taxonomy=rsgutschein&post_type=rsappartment
         * admin.php?page=rs_einstellungen
         */
        $rs_ib_menu		= null;
        if (key_exists('rs_indiebooking', $submenu)) {
        	$rs_ib_menu = $submenu['rs_indiebooking'];
        }
        $newMenuOrder   = array();
        $newMenuOrder   = array_fill(0, $menuCount, 0);//array(0,0,0,0,0,0,0,0,0);
        if (isset($rs_ib_menu) && !is_null($rs_ib_menu)) {
	        foreach ($rs_ib_menu as $curMenu) {
	            if ($curMenu[2] == 'rs_ib_dashboard') {
	                $newMenuOrder[$dashboardPosition]   = $curMenu;
	            }
	            if ($curMenu[2] == 'edit.php?post_type=rsappartment') {
	                $newMenuOrder[$apartmentPosition]   = $curMenu;
	            }
	            if ($curMenu[2] == 'rs_preispflege') {
	                $newMenuOrder[$preispflegePosition]    = $curMenu;
	            }
	            if ($curMenu[2] == 'post-new.php?post_type=rsappartment') {
	//                 $newMenuOrder[$newApartmentPos]     = $curMenu;
	            }
	            if ($curMenu[2] == 'edit.php?post_type=rsappartment_buchung') {
	                $newMenuOrder[$buchungPosition]     = $curMenu;
	            }
	            if ($curMenu[2] == 'edit-tags.php?taxonomy=rsappartmentoption&post_type=rsappartment') {
	                $newMenuOrder[$optionenPosition]    = $curMenu;
	            }
	            if ($curMenu[2] == 'edit-tags.php?taxonomy=rsappartmentcategories&post_type=rsappartment') {
	                $newMenuOrder[$categoryPosition]    = $curMenu;
	            }
	            if ($curMenu[2] == 'edit-tags.php?taxonomy=rsappartmentaktion&post_type=rsappartment') {
	                $newMenuOrder[$aktionPosition]      = $curMenu;
	            }
	            if ($curMenu[2] == 'edit-tags.php?taxonomy=rsgutschein&post_type=rsappartment') {
	            	$newMenuOrder[$realgutscheinPosition]   = $curMenu;
	            }
	            if ($curMenu[2] == 'edit-tags.php?taxonomy=ibrealgutschein&post_type=rsappartment') {
	            	$newMenuOrder[$gutscheinPosition]   = $curMenu;
	            }
	            if ($curMenu[2] == 'rs_einstellungen') {
	                $newMenuOrder[$settingsPosition]    = $curMenu;
	            }
	        }
        }
        $submenu['rs_indiebooking'] = $newMenuOrder;
        return $menu_ord;
    }
    
    function rs_indiebooking_set_current_menu($parent_file){
        global $submenu_file, $current_screen, $pagenow;
        // Set the submenu as active/current while anywhere in your Custom Post Type (nwcm_news)
        if($current_screen->post_type == RS_IB_Appartment_post_type::POST_TYPE_NAME) {
//             if($pagenow == 'post.php'){
//                 $submenu_file = 'edit.php?post_type='.$current_screen->post_type;
//             }
            if($pagenow == 'edit-tags.php'){
//                 $submenu_file = 'edit-tags.php?taxonomy=nwcm_news_category&post_type='.$current_screen->post_type;
//                 $submenu_file = 'edit-tags.php?taxonomy=rsappartmentcategories&post_type='.$current_screen->post_type;
//                    $submenu_file = 'edit-tags.php?taxonomy=rsappartmentoption&post_type='.$current_screen->post_type;
                   $submenu_file = 'edit-tags.php?taxonomy='.$current_screen->taxonomy.'&post_type='.$current_screen->post_type;
                   
//                 edit-tags.php?taxonomy=rsappartmentcategories&post_type=rsappartment
            }
            $parent_file = 'rs_indiebooking';

        }
        return $parent_file;
    }
    
    /* @var $buchungKopfTbl RS_IB_Table_Buchungskopf */
    /* @var $stornoTable RS_IB_Table_Storno */
    /* @var $mwstTable RS_IB_Table_Mwst */
    public function initSettingsView() {
        global $RSBP_DATABASE;
        
        $mwsts          = array();
        $mwstTable      = $RSBP_DATABASE->getTable(RS_IB_Model_Mwst::RS_TABLE);
        $stornoTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Storno::RS_TABLE);
        $buchungKopfTbl = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
        $mwsts          = $mwstTable->getAllMwsts();

        $stornos        = $stornoTable->getAllStorno();
        
        $nrOfBookings   = $buchungKopfTbl->getNumberOfBookings();
        
        do_action("rs_indiebooking_createSettingsView", $mwsts, $stornos, $nrOfBookings);
//         createSettingsView($mwsts, $stornos);
    }
    
    /* @var $buchungTable RS_IB_Table_Appartment_Buchung */
//     public function initDashboardView() {
//         global $RSBP_DATABASE;
//         $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
//         $buchungTable               = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        
//         $lastBookings               = $buchungTable->loadLastBookings();
//         $outstandingPayments        = $buchungTable->loadOutstandingPayments();
        
//         createDashboardView($lastBookings, $outstandingPayments);
//     }
    
    public function default_custom_columns($column) {
        switch ($column) {
            case "description":
                the_excerpt();
                break;
        }
    }
}
new RS_IB_Admin();
// endif;