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

$settingsFilterCategoryChecked    	= 'checked="checked"';
$settingsFilterAnzZimmerChecked 	= 'checked="checked"';
$settingsFilterRegionChecked 		= 'checked="checked"';
$settingsFilterAnzBettenChecked   	= 'checked="checked"';
$settingsFilterAnzPersonenChecked 	= 'checked="checked"';
$settingsFilterOptionsChecked 		= 'checked="checked"';
$settingsFilterFeaturesChecked		= 'checked="checked"';

/* Required Field control*/
$settingsContactRequiredFirmaChecked 		= 'checked="checked"';
$settingsContactRequiredAbteilungChecked 	= 'checked="checked"';
$settingsContactRequiredAnredeChecked		= 'checked="checked"';
$settingsContactRequiredVornameChecked		= 'checked="checked"';
$settingsContactRequiredNachnameChecked		= 'checked="checked"';
$settingsContactRequiredMailChecked			= 'checked="checked"';
$settingsContactRequiredAdressChecked		= 'checked="checked"';
$settingsContactRequiredTelefonChecked		= 'checked="checked"';

$filterData                      	= get_option( 'rs_indiebooking_settings_filter');
if ($filterData) {
	$settingsFilterCategoryKz    	= (key_exists('category_kz', $filterData))        ?  esc_attr__( $filterData['category_kz'] )        : "";
	$settingsFilterAnzBettenKz   	= (key_exists('anzahl_betten_kz', $filterData))   ?  esc_attr__( $filterData['anzahl_betten_kz'] )   : "";
	$settingsFilterAnzPersonenKz 	= (key_exists('anzahl_personen_kz', $filterData)) ?  esc_attr__( $filterData['anzahl_personen_kz'] ) : "";
	$settingsFilterOptionsKz     	= (key_exists('options_kz', $filterData))         ?  esc_attr__( $filterData['options_kz'] )         : "";
	$settingsFilterAnzZimmerKz   	= (key_exists('rooms_kz', $filterData))           ?  esc_attr__( $filterData['rooms_kz'] )           : "";
	$settingsFilterRegionKz      	= (key_exists('region_kz', $filterData))          ?  esc_attr__( $filterData['region_kz'] )          : "";
	$settingsFilterFeaturesKz      	= (key_exists('features_kz', $filterData))        ?  esc_attr__( $filterData['features_kz'] )        : "";
	if ($settingsFilterCategoryKz == "off") {
		$settingsFilterCategoryChecked = '';
	}
	
	if ($settingsFilterAnzZimmerKz == "off") {
		$settingsFilterAnzZimmerChecked = '';
	}
	
	if ($settingsFilterRegionKz == "off") {
		$settingsFilterRegionChecked = '';
	}
	
	if ($settingsFilterAnzBettenKz == "off") {
		$settingsFilterAnzBettenChecked = '';
	}
	
	if ($settingsFilterAnzPersonenKz == "off") {
		$settingsFilterAnzPersonenChecked = '';
	}
	if ($settingsFilterOptionsKz == "off") {
		$settingsFilterOptionsChecked = '';
	}
	if ($settingsFilterFeaturesKz == "off") {
		$settingsFilterFeaturesChecked = '';
	}
} else {
	$settingsFilterCategoryKz 		= "on";
	$settingsFilterAnzZimmerKz 		= "on";
	$settingsFilterRegionKz 		= "on";
	$settingsFilterAnzBettenKz 		= "on";
	$settingsFilterAnzPersonenKz 	= "on";
	$settingsFilterOptionsKz 		= "on";
	$settingsFilterFeaturesKz 		= "on";
}

$requiredFilterData = get_option( 'rs_indiebooking_settings_contact_required');
if ($requiredFilterData) {
	$settingsContactRequiredFirmaKz		= (key_exists('firma', $requiredFilterData))        ?  esc_attr__( $requiredFilterData['firma'] )       : "";
	$settingsContactRequiredAbteilungKz	= (key_exists('abteilung', $requiredFilterData))   	?  esc_attr__( $requiredFilterData['abteilung'] )   : "";
	$settingsContactRequiredAnredeKz	= (key_exists('anrede', $requiredFilterData)) 		?  esc_attr__( $requiredFilterData['anrede'] ) 		: "";
	$settingsContactRequiredVornameKz	= (key_exists('vorname', $requiredFilterData))      ?  esc_attr__( $requiredFilterData['vorname'] )     : "";
	$settingsContactRequiredNachnameKz	= (key_exists('nachname', $requiredFilterData))     ?  esc_attr__( $requiredFilterData['nachname'] )    : "";
	$settingsContactRequiredMailKz		= (key_exists('mail', $requiredFilterData))         ?  esc_attr__( $requiredFilterData['mail'] )        : "";
	$settingsContactRequiredAdressKz	= (key_exists('address', $requiredFilterData))      ?  esc_attr__( $requiredFilterData['address'] )		: "";
	$settingsContactRequiredTelefonKz	= (key_exists('telefon', $requiredFilterData))      ?  esc_attr__( $requiredFilterData['telefon'] )		: "";
	
	if ($settingsContactRequiredFirmaKz		== "off") {
		$settingsContactRequiredFirmaChecked		= "";
	}
	if ($settingsContactRequiredAbteilungKz	== "off") {
		$settingsContactRequiredAbteilungChecked  	= "";
	}
	if ($settingsContactRequiredAnredeKz	== "off") {
		$settingsContactRequiredAnredeChecked     	= "";
	}
	if ($settingsContactRequiredVornameKz	== "off") {
		$settingsContactRequiredVornameChecked    	= "";
	}
	if ($settingsContactRequiredNachnameKz	== "off") {
		$settingsContactRequiredNachnameChecked   	= "";
	}
	if ($settingsContactRequiredMailKz		== "off") {
		$settingsContactRequiredMailChecked       	= "";
	}
	if ($settingsContactRequiredAdressKz	== "off") {
		$settingsContactRequiredAdressChecked     	= "";
	}
	if ($settingsContactRequiredTelefonKz	== "off") {
		$settingsContactRequiredTelefonChecked	  	= "";
	}
} else {
	$settingsContactRequiredFirmaKz		= "on";
	$settingsContactRequiredAbteilungKz	= "on";
	$settingsContactRequiredAnredeKz	= "on";
	$settingsContactRequiredVornameKz	= "on";
	$settingsContactRequiredNachnameKz	= "on";
	$settingsContactRequiredMailKz		= "on";
	$settingsContactRequiredAdressKz	= "on";
	$settingsContactRequiredTelefonKz	= "on";
}

?>
<div class="rsib_container-fluid">
    <div class="rsib_row">
<!--         <div class="rsib_col-sm-12 rsib_col-xs-12 rsib_nopadding_left rsib_nopadding_md2"> -->
		<div class="rsib_col-lg2-6 rsib_col-md2-12 rsib_nopadding_left rsib_nopadding_md2">
            <div class="ibui_tabitembox">
                <div class="ibui_h2wrap"><h2 class="ibui_h2"><?php _e('Possible filter', 'indiebooking'); ?></h2></div>
                <input id="rs_indiebooking_setting_filter_category" name="cb_setting_filter_category"
                		class="ibui_checkbox tooltipItem ibfc_switchbtn_input" type="checkbox"
                		title="" <?php echo $settingsFilterCategoryChecked; ?> >
                <label for="rs_indiebooking_setting_filter_category"><?php _e('Filter by category', 'indiebooking'); ?></label>
                <input id='rs_indiebooking_setting_filter_category_kz' type='hidden' name='rs_indiebooking_settings_filter[category_kz]' value="<?php echo $settingsFilterCategoryKz; ?>" >
                <br />
				<input id="cb_setting_filter_anzahl_zimmer" name="cb_setting_filter_anzahl_zimmer"
					class="ibui_checkbox tooltipItem ibfc_switchbtn_input" type="checkbox" title=""
					<?php echo $settingsFilterAnzZimmerChecked; ?> >
                <label for="cb_setting_filter_anzahl_zimmer">
                	<?php _e('Filter by number of rooms', 'indiebooking'); ?>
            	</label>
            	<input id='cb_setting_filter_anzahl_zimmer_kz' type='hidden' name='rs_indiebooking_settings_filter[rooms_kz]'
            			value="<?php echo esc_attr($settingsFilterAnzZimmerKz); ?>" >
        		<br />
				<input id="rs_indiebooking_setting_filter_region" name="cb_setting_filter_region"
					class="ibui_checkbox tooltipItem ibfc_switchbtn_input" type="checkbox" title=""
					<?php echo $settingsFilterRegionChecked; ?> >
                <label for="rs_indiebooking_setting_filter_region">
                	<?php _e('Filter by region', 'indiebooking'); ?>
            	</label>
            	<input id='rs_indiebooking_setting_filter_region_kz' type='hidden' name='rs_indiebooking_settings_filter[region_kz]'
            			value="<?php echo esc_attr($settingsFilterRegionKz); ?>" >
        		<br />
                <!--
                <input id="cb_setting_filter_anzahl_betten" name="cb_setting_filter_anzahl_betten"
                		class="ibui_checkbox tooltipItem ibfc_switchbtn_input" type="checkbox"
                		title="" <?php //echo $settingsFilterAnzBettenChecked; ?> >
                <label for="cb_setting_filter_anzahl_betten"><?php //_e('Filter by number of beds', 'indiebooking'); ?></label>
                <input id='rs_indiebooking_setting_filter_anzahl_betten_kz' type='hidden' name='rs_indiebooking_settings_filter[anzahl_betten_kz]' value="<?php //echo $settingsFilterAnzBettenKz; ?>" >
                <br />
                -->
                <input id="rs_indiebooking_setting_filter_anzahl_personen" name="cb_setting_filter_anzahl_personen"
                		class="ibui_checkbox tooltipItem ibfc_switchbtn_input"
                		type="checkbox" title="" <?php echo $settingsFilterAnzPersonenChecked; ?> >
                <label for="rs_indiebooking_setting_filter_anzahl_personen"><?php _e('Filter by number of guests', 'indiebooking'); ?></label>
                <input id='rs_indiebooking_setting_filter_anzahl_personen_kz' type='hidden'
                		name='rs_indiebooking_settings_filter[anzahl_personen_kz]'
                		value="<?php echo esc_attr($settingsFilterAnzPersonenKz); ?>" >
                <br />
                
                <?php
                if (is_plugin_active('indiebooking-advanced/indiebooking_advanced.php')) {
                ?>
	                <input id="rs_indiebooking_setting_filter_features" name="cb_setting_filter_features"
	                		class="ibui_checkbox tooltipItem ibfc_switchbtn_input" type="checkbox"
	                		title="" <?php echo $settingsFilterFeaturesChecked; ?> >
	                <label for="rs_indiebooking_setting_filter_features"><?php _e('Filter by features', 'indiebooking'); ?></label>
	                <input id='rs_indiebooking_setting_filter_features_kz' type='hidden' name='rs_indiebooking_settings_filter[features_kz]' value="<?php echo $settingsFilterFeaturesKz; ?>" >
	                <br />
                <?php
                } ?>
                <!--
                <input id="cb_setting_filter_options" name="cb_setting_filter_options" data-indiebooking-value-fieldid="rs_indiebooking_setting_filter_options_kz" class="ibui_checkbox tooltipItem" type="checkbox" title="" <?php echo $settingsFilterOptionsChecked; ?> >
                <label for="cb_setting_filter_options"><?php //_e('Filter by ', 'indiebooking'); ?></label>
                <input id='rs_indiebooking_setting_filter_options_kz' type='hidden' name='rs_indiebooking_settings_filter[options_kz]' value="<?php echo $settingsFilterOptionsKz; ?>" >
                 -->
            </div>
        </div>
		<div class="rsib_col-lg2-6 rsib_col-md2-12 rsib_nopadding_left rsib_nopadding_md2">
			<div id="rs_indiebooking_settings_required_contact_data_tour" class="ibui_tabitembox">
                <div class="ibui_h2wrap"><h2 class="ibui_h2">
                	<?php _e('Contact Information Required field Settings', 'indiebooking'); ?></h2>
                </div>
                
                <!-- Firma -->
                <input id="rs_indiebooking_contact_required_firma" name="rs_indiebooking_settings_contact_required_firma"
                		class="ibui_checkbox tooltipItem ibfc_switchbtn_input" type="checkbox"
                		title="" <?php echo $settingsContactRequiredFirmaChecked; ?> >
                <label for="rs_indiebooking_contact_required_firma">
                	<?php _e('Company', 'indiebooking'); ?>
                </label>
                <input id='rs_indiebooking_contact_required_firma_kz' type='hidden'
                		name='rs_indiebooking_settings_contact_required[firma]' value="<?php echo $settingsContactRequiredFirmaKz; ?>" >
                <br />
                
                <!-- Abteilung -->
                <input id="rs_indiebooking_contact_required_department" name="rs_indiebooking_settings_contact_required_abteilung"
                		class="ibui_checkbox tooltipItem ibfc_switchbtn_input" type="checkbox"
                		title="" <?php echo $settingsContactRequiredAbteilungChecked; ?> >
                <label for="rs_indiebooking_contact_required_department">
                	<?php _e('Department', 'indiebooking'); ?>
                </label>
                <input id='rs_indiebooking_contact_required_department_kz' type='hidden'
                		name='rs_indiebooking_settings_contact_required[abteilung]' value="<?php echo $settingsContactRequiredAbteilungKz; ?>" >
                <br />
                
				<!-- Anrede -->
                <input id="rs_indiebooking_contact_required_anrede" name="rs_indiebooking_settings_contact_required_anrede"
                		class="ibui_checkbox tooltipItem ibfc_switchbtn_input" type="checkbox"
                		title="" <?php echo $settingsContactRequiredAnredeChecked; ?> >
                <label for="rs_indiebooking_contact_required_anrede">
                	<?php _e('Salutation', 'indiebooking'); ?>
                </label>
                <input id='rs_indiebooking_contact_required_anrede_kz' type='hidden'
                		name='rs_indiebooking_settings_contact_required[anrede]' value="<?php echo $settingsContactRequiredAnredeKz; ?>" >
                <br />

				<!-- Vorname -->
                <input id="rs_indiebooking_contact_required_vorname" name="rs_indiebooking_settings_contact_required_vorname"
                		class="ibui_checkbox tooltipItem ibfc_switchbtn_input" type="checkbox"
                		title="" <?php echo $settingsContactRequiredVornameChecked; ?> >
                <label for="rs_indiebooking_contact_required_vorname">
                	<?php _e('first name', 'indiebooking'); ?>
                </label>
                <input id='rs_indiebooking_contact_required_vorname_kz' type='hidden'
                		name='rs_indiebooking_settings_contact_required[vorname]' value="<?php echo $settingsContactRequiredVornameKz; ?>" >
                <br />

				<!-- Nachname -->
                <input id="rs_indiebooking_contact_required_nachname" name="rs_indiebooking_settings_contact_required_nachname"
                		class="ibui_checkbox tooltipItem ibfc_switchbtn_input" type="checkbox"
                		title="" <?php echo $settingsContactRequiredNachnameChecked; ?> >
                <label for="rs_indiebooking_contact_required_nachname">
                	<?php _e('name', 'indiebooking'); ?>
                </label>
                <input id='rs_indiebooking_contact_required_nachname_kz' type='hidden'
                		name='rs_indiebooking_settings_contact_required[nachname]' value="<?php echo $settingsContactRequiredNachnameKz; ?>" >
                <br />

				<!-- E-Mail -->
                <input id="rs_indiebooking_contact_required_mail" name="rs_indiebooking_settings_contact_required_mail"
                		class="ibui_checkbox tooltipItem ibfc_switchbtn_input" type="checkbox"
                		title="" <?php echo $settingsContactRequiredMailChecked; ?> >
                <label for="rs_indiebooking_contact_required_mail">
                	<?php _e('E-Mail', 'indiebooking'); ?>
                </label>
                <input id='rs_indiebooking_contact_required_mail_kz' type='hidden'
                		name='rs_indiebooking_settings_contact_required[mail]' value="<?php echo $settingsContactRequiredMailKz; ?>" >
                <br />
                
				<!-- Adresse-->
                <input id="rs_indiebooking_contact_required_address" name="rs_indiebooking_settings_contact_required_address"
                		class="ibui_checkbox tooltipItem ibfc_switchbtn_input" type="checkbox"
                		title="" <?php echo $settingsContactRequiredAdressChecked; ?> >
                <label for="rs_indiebooking_contact_required_address">
                	<?php _e('Adress', 'indiebooking'); ?>
                </label>
                <input id='rs_indiebooking_contact_required_address_kz' type='hidden'
                		name='rs_indiebooking_settings_contact_required[address]' value="<?php echo $settingsContactRequiredAdressKz; ?>" >
                <br />

				<!-- Telefon -->
                <input id="rs_indiebooking_contact_required_telefon" name="rs_indiebooking_settings_contact_required_telefon"
                		class="ibui_checkbox tooltipItem ibfc_switchbtn_input" type="checkbox"
                		title="" <?php echo $settingsContactRequiredTelefonChecked; ?> >
                <label for="rs_indiebooking_contact_required_telefon">
                	<?php _e('Telefon', 'indiebooking'); ?>
                </label>
                <input id='rs_indiebooking_contact_required_telefon_kz' type='hidden'
                		name='rs_indiebooking_settings_contact_required[telefon]' value="<?php echo $settingsContactRequiredTelefonKz; ?>" >
                <br />

        	</div>
        </div>
    </div>
</div>