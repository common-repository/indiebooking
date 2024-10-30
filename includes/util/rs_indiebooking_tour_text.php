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
// if ( ! class_exists( 'rs_indiebooking_tour_text' ) ) :
/**
 * @author schmitt
 *
 */
class rs_indiebooking_tour_text
{
    public static function getTourTexts()  {
        
        $ibui_indiebooking_tour_text = array(
            //------------------Einstellungen--------------------------
            'welcomeTitle'      => __('Welcome', 'indiebooking'),
            'welcomeTxt'        => __('This tour will show you the functionality of Indiebooking. You Are able to restart the Tour By clicking "start Tour"',
                                       'indiebooking'),
            
            'generalTitle'      => __('General', 'indiebooking'),
            'generalTxt'        => __('In the general settings you are able to set all of youre company data and default values for your site.', 'indiebooking'),

            'generalDefaultSettingsTitle'   => __('General Options', 'indiebooking'),
            'generalDefaultSettingsTxt'      => __('Here you can allow us to catch anonymous data to make Indiebooking better and better. You can enable e.g. disable the Welcome-Banner. And you can set the time, the user have to complete the booking.',
                                                   'indiebooking'),
            
            'filterTitle'       => __('Filter', 'indiebooking'),
            'filterTxt'         => __('In the Filter section you are able to choose the filter possibilities that the user will have in the frontend', 'indiebooking'),

            'paymentTitle'      => __("Payment", 'indiebooking'),
            'paymentTxt'        => __("In the payment section you are able to choose the payment methods the user can choose from", 'indiebooking'),

            'cancellationTitle' => __("Cancellation", 'indiebooking'),
            'cancellationTxt'   => __("In the cancellation section you are able to write youre storno-texts.", 'indiebooking'),

            'taxesTitle'        => __('Taxes', 'indiebooking'),
            'taxesTxt'          => __('In the taxes tab you are able to add the taxes you need. This taxes are available in the complete Indiebooking administration',
                                       'indiebooking'),

            'printingsTitle'    => __('Printings', 'indiebooking'),
            'printingsTxt'      => __('In the printing section you are able to choose the Logo which get\'s shown at youre bill', 'indiebooking'),

            'mailTitle'         => __("Mail", 'indiebooking'),
            'mailTxt'           => __("In the E-Mail section you can add the E-Mail Texts which get\'s send to the user", 'indiebooking'),

        	'mailAdminTitle'    => __("Info Mail and attachments", 'indiebooking'),
        	'mailAdminTxt'      => __("In this section you can define the E-Mail adress which get get\'s an email after a booking was done. You can also define attachments that will be sent to the customer with each e-mail.", 'indiebooking'),
        	
        	
            'termsConditionTitle' => __("Terms and conditions", 'indiebooking'),
            'termsConditionTxt' => __("In the Terms and conditions sections you are able to write your conditions which the user have to accept before the booking.",
                                        'indiebooking'),
        	'indiebookingTxt'		=> __("Under this point you are always be able to start the tour again", 'indiebooking'),
            
        	//------------------Google--------------------------------
        	'googleTitle'       => __("Google Maps & Analytics", 'indiebooking'),
        	'googleTxt'         => __("Under this section, you can define your Google-Api-Key for using Google Maps. Also you are able to set your google Analytics Tracking-ID.", 'indiebooking'),
        	
        	'requiredContactDataTitle' 	=> __("required contact data", 'indiebooking'),
        	'requiredContactDataTxt'	=> __("Also you can define, which contact data are required in the booking process.", 'indiebooking'),
            //------------------Kategorie------------------------------
            'categoryTitle'         => __("Category", 'indiebooking'),
            'categoryTxt'           => __("In the category view you are able to add categories for your apartments. Categories can help the user to find the right apartment by filtering.",
                                        'indiebooking'),
            'addCategoryTitle'     => __("Add category", 'indiebooking'),
            'addCategoryTxt'       => __("Please click now 'add category'", 'indiebooking'),
            
            'addCategoryDlgTitle'  => __("Create or add category", 'indiebooking'),
            'addCategoryDlgTxt'    => __("In this dialog you are able to add a category. Please add a category by entering Name and description and click 'save'",
                                            'indiebooking'),
            
            'addedCategoryTitle'   => __("Congratulations", 'indiebooking'),
            'addedCategoryTxt'      => __("You have a new category. Let's see how to add an apartment", 'indiebooking'),
            
            //------------------Apartmentuebersicht---------------------
            'apartmentuebersichtTitle'   => __("Apartment overview", 'indiebooking'),
            'apartmentuebersichtTxt'     => __("On this page you see all your apartments.", 'indiebooking'),
            
            'addApartmentBtnTitle'   => __("Add new apartment", 'indiebooking'),
            'addApartmentBtnTxt'     => __("Please click now 'add new apartment' for adding a new apartment.", 'indiebooking'),
            
            //------------------Apartment------------------------------
            'apartmentNameTitle'   => __("Apartment name", 'indiebooking'),
            'apartmentNameTxt'     => __("Pleaser enter here the name of the apartment", 'indiebooking'),
            
            'apartmentDescTitle'   => __("Apartment description", 'indiebooking'),
            'apartmentDescTxt'     => __("Pleaser enter here the desciption of the apartment. This discription is shown in the apartment view in the frontend.",
                                            'indiebooking'),
            
            'apartmentBasicTitle'   => __("Apartment basic data", 'indiebooking'),
            'apartmentBasicTxt'     => __("In this section you are able to enter the basic data of the apartment. ".
                                            "Please enter now the basic data of the apartment and then click next",
                                            'indiebooking'),
            
            'apartmentBasic2Title'   => _x("Basic data 2", 'Tour text', 'indiebooking'),
            'apartmentBasic2Txt'     => __("In this section you are able to put the apartment in one ore more categories ",
                                            'indiebooking'),
            
            'apartmentPaymentTitle'   => __("Payment info", 'indiebooking'),
            'apartmentPaymentTxt'     => __("In this section you are able to do alle Settings for the payment like taxes and prices.",
                                            'indiebooking'),
            
            'apartmentBookingTitle' => __("Booking info", 'indiebooking'),
            'apartmentBookingTxt'   => __("In this section you are able to set the minimal booking period, the arrival days and the not bookable periods",
                                            'indiebooking'),
            
            'apartmentGalleryTitle' => __("Appartment Gallery", 'indiebooking'),
            'apartmentGalleryTxt'   => __("In this section you are able to add pictures to the Apartment. This pictures are getting shown in the Apartment frontend",
                                            'indiebooking'),
            
            'apartmentPublishTitle' => __("Publish your Apartment", 'indiebooking'),
            'apartmentPublishTxt'   => __("By clicking on publish, the apartment gets published on youre page. Youe have to publish your Apartment once.".
                                         "After publish, all settings you are editing will be shown directly in the frontend",
                                        'indiebooking'),
            
        	'apartmentCampaignTitle' => __("campaign & coupons", 'indiebooking'),
        	'apartmentCampaignTxt'   => __("Under this section you ca define, which campaigns and which coupons are valid for this apartment", 'indiebooking'),
        	
        	'apartmentSpecialFeaturesTitle' => __("special features", 'indiebooking'),
        	'apartmentSpecialFeaturesTxt'   => __("Here you can define what special features the apartment has", 'indiebooking'),
        	
        	
        	'apartmentPrice1Title' => __("default price", 'indiebooking'),
        	'apartmentPrice1Txt'   => __("The default price defined the price of the apartment, if no other price (e.g. saison price) is defined for the specific time period.", 'indiebooking'),
        	
        	'apartmentPrice2Title' => __("extra charge", 'indiebooking'),
        	'apartmentPrice2Txt'   => __("Here you can define the extra charge for bookings below a certain number of days.", 'indiebooking'),
        	
        	'apartmentPrice3Title' => __("degression", 'indiebooking'),
        	'apartmentPrice3Txt'   => __("Under this point you can define from how many days there should be a discount.", 'indiebooking'),
        	
        	'apartmentPrice4Title' => __("season prices", 'indiebooking'),
        	'apartmentPrice4Txt'   => __("Below this point you can set different seasons and the price for them.", 'indiebooking'),
        	
        	
        	//------------------Aktionen---------------------------
        	'campaignOverviewTitle' => __("campaign", 'indiebooking'),
        	'campaignOverviewTxt' 	=> __("Under this section you can add campaigns for your site.", 'indiebooking'),
        	
        	'campaignOverview2Title' => __("add campaign", 'indiebooking'),
        	'campaignOverview2Txt' 	=> __("Click this button for add a new campaign", 'indiebooking'),
        	
        	'campaignAdd1Title' => __("add campaign", 'indiebooking'),
        	'campaignAdd1Txt' 	=> __("in this popup you can define the name and description of the campaign", 'indiebooking'),
        	
        	'campaignAdd2Title' => __("add campaign", 'indiebooking'),
        	'campaignAdd2Txt' 	=> __("you can also define the value, the calculation (EUR or %) and the type (discount or extra charge) of the campaign", 'indiebooking'),
        	
        	'campaignAdd3Title' => __("campaign settings", 'indiebooking'),
        	'campaignAdd3Txt' 	=> __("under settings you can define differend settings of how the campaign should work. ", 'indiebooking'),
        	
        	'campaignAdd4Title' => __("activation conditions", 'indiebooking'),
        	'campaignAdd4Txt' 	=> __("Here you can define under which conditions the campaign should get active", 'indiebooking'),
        	
        	'campaignAdd5Title' => __("valid periods", 'indiebooking'),
        	'campaignAdd5Txt' 	=> __("Here you can define on which periods the campaign should be active. And if the campaign is active or not.", 'indiebooking'),
        	
        	'campaignAdd6Title' => __("valid appartments", 'indiebooking'),
        	'campaignAdd6Txt' 	=> __("Here you can define for which apartments the campaign should be active.", 'indiebooking'),
        	
        	'campaignAdd7Title' => __("save campaign", 'indiebooking'),
        	'campaignAdd7Txt' 	=> __("By click on the save button, the campaign will be saved", 'indiebooking'),
        	
        	//------------------Gutscheine-----------------------------
        	'voucherAdd1Title'	=> __("voucher", 'indiebooking'),
        	'voucherAdd1Txt' 	=> __("Under this section you can add vouchers for your site.", 'indiebooking'),
        	
        	'voucherAdd2Title'	=> __("add voucher", 'indiebooking'),
        	'voucherAdd2Txt' 	=> __("Click this button for add a new voucher.", 'indiebooking'),
        	
        	'voucherAdd3Title'	=> __("add voucher", 'indiebooking'),
        	'voucherAdd3Txt' 	=> __("in this popup you can define the name and description of the voucher.", 'indiebooking'),
        	
        	'voucherAdd4Title'	=> __("count of voucher & voucher code", 'indiebooking'),
        	'voucherAdd4Txt' 	=> __("Here you can define how often the voucher can be used and the voucher code.", 'indiebooking'),
        	
//         	'voucherAdd5Title'	=> __("value of the voucher", 'indiebooking'),
//         	'voucherAdd5Txt' 	=> __("Here you can define the value of the voucher.", 'indiebooking'),
        	
        	'voucherAdd5Title'	=> __("valid periods", 'indiebooking'),
        	'voucherAdd5Txt' 	=> __("Here you can define on which periods the voucher should be active. And if the voucher is active or not.", 'indiebooking'),
        	
        	'voucherAdd6Title'	=> __("voucher value", 'indiebooking'),
        	'voucherAdd6Txt' 	=> __("Here you can define the value of the voucher.", 'indiebooking'),
        	
        	'voucherAdd7Title'	=> __("save voucher", 'indiebooking'),
        	'voucherAdd7Txt' 	=> __("By click on the save button, the voucher will be saved.", 'indiebooking'),
        	
        	//------------------Coupons-----------------------------
        	'couponsAdd1Title'	=> __("Coupons", 'indiebooking'),
        	'couponsAdd1Txt' 	=> __("Under this section you can add coupons for your site.", 'indiebooking'),
        	
        	'couponsAdd2Title'	=> __("add coupon", 'indiebooking'),
        	'couponsAdd2Txt' 	=> __("Click this button for add a new coupon.", 'indiebooking'),
        	
        	'couponsAdd3Title'	=> __("add coupon", 'indiebooking'),
        	'couponsAdd3Txt' 	=> __("in this popup you can define the name and description of the coupon.", 'indiebooking'),
        	
        	'couponsAdd4Title'	=> __("count of coupon, coupon code & settings", 'indiebooking'),
        	'couponsAdd4Txt' 	=> __("Here you can define how often the coupon can be used and the coupon code."
        								." Also you can define if the coupon is cobineable with other coupons or campaigns.", 'indiebooking'),
        	
        	'couponsAdd5Title'	=> __("values of the coupon", 'indiebooking'),
        	'couponsAdd5Txt' 	=> __("Here you can define the values of the coupon. A coupon can have one or more debits with different activation conditions", 'indiebooking'),
        	
        	'couponsAdd6Title'	=> __("coupon options", 'indiebooking'),
        	'couponsAdd6Txt' 	=> __("Here you can define if the coupon should depit one or more options.", 'indiebooking'),
        	
        	'couponsAdd7Title'	=> __("valid periods", 'indiebooking'),
        	'couponsAdd7Txt' 	=> __("Here you can define on which periods the coupon should be active. And if the coupon is active or not.", 'indiebooking'),
        	
        	'couponsAdd8Title'	=>  __("valid appartments", 'indiebooking'),
        	'couponsAdd8Txt' 	=>  __("Here you can define for which apartments the campaign should be active.", 'indiebooking'),
        	
        	'voucherAdd9Title'	=> __("save coupon", 'indiebooking'),
        	'voucherAdd9Txt' 	=> __("By click on the save button, the coupon will be saved.", 'indiebooking'),
        	
            //------------------Buchungen------------------------------
            'bookingoverviewTitle' => __("Booking overview", 'indiebooking'),
            'bookingoverviewTxt'   => __("In this section you see all of your bookings.",
                                        'indiebooking'),
            
            'tourCompleteTitle' => __("Let's go", 'indiebooking'),
            'tourCompleteTxt'   => __("Now you have an insight into the areas of Indiebooking. We wish you lots of fun and success with the use of Indiebooking.",
                'indiebooking'),
            
        );
        return $ibui_indiebooking_tour_text;
    }
}
// endif;