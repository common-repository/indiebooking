<?php
/*
* Indiebooking - die Buchungssoftware fuer Ihre Homepage!
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
?>
<div id="contact_data_box">
	<?php
	$kennzeichen       = "";
	if (!isset($disabled)) {
	    $disabled   = "";
	}
	if ($disabled == "") {
	    $kennzeichen    = "*";
	    ?>
    	<h2><?php _e("Insert your contactdata ", 'indiebooking'); ?>:</h2>
    <?php
	}
	$onFocus = "";
    if ($useGoogleFeatures) {
    	$onFocus = 'onFocus="geolocate()"';
    }
    ?>
<!--
<iframe id="indiebooking_remember_contact" name="indiebooking_remember_contact" style="display: none;" src="/content/blank"></iframe>
<form target="indiebooking_remember_contact" method="post" action="/content/blank" autocomplete="on">
<button id="indiebooking_remember_contact_btn" type="submit" style="display: none;"></button>
Test: <input type="text" name="ib_autocomplete_test_name">
-->
     <div class="row">
        <div class="form-group">
            <label for="kontakt_firma" class="col-md-2 col-sm-3 hidden-xs control-label">
            	<?php _e("Company", 'indiebooking'); ?>
            	<?php
            	if ($contactRequired['companyRequiredKz']) {
            		echo $kennzeichen;
            	}
            	?>
            </label>
            <div class="col-md-10 col-sm-9">
                <input type="text" class="form-control fkt_contact_field" id="kontakt_firma" name="kontakt_firma"
                		data-errorname="firma" data-required="<?php echo $contactRequired['companyRequiredKz']; ?>"
                		data-settingsname="firma"
                		placeholder="<?php esc_attr_e("Company", 'indiebooking'); ?>"
                		value="<?php echo esc_attr($firma); ?>" <?php echo esc_attr($disabled); ?>>
                <?php echo "<p id='err_firma' class='text-danger'>".$errFirma."</p>";?>
            </div>
        </div>
        <div class="form-group">
            <label for="kontakt_abteilung" class="col-md-2 col-sm-3 hidden-xs control-label">
            	<?php _e("Department", 'indiebooking'); ?>
				<?php
					if ($contactRequired['departmentRequiredKz']) {
						echo $kennzeichen;
				    }
				?>
            </label>
            <div class="col-md-10 col-sm-9">
                <input type="text" class="form-control fkt_contact_field" id="kontakt_abteilung" name="kontakt_abteilung"
                		data-errorname="department" data-required="<?php echo $contactRequired['departmentRequiredKz']; ?>"
                		data-settingsname="department"
                		placeholder="<?php esc_attr_e("Department", 'indiebooking'); ?>"
                		value="<?php echo esc_attr($department); ?>" <?php echo esc_attr($disabled); ?>>
				<?php echo "<p id='err_abteilung' class='text-danger'>".$errAbteilung."</p>";?>
            </div>
        </div>
        <div class="form-group">
            <label for="kontakt_anrede" class="col-md-2 col-sm-3 hidden-xs control-label">
            	<?php _e("Salutation", 'indiebooking'); ?>
				<?php
					if ($contactRequired['salutationRequiredKz']) {
						echo $kennzeichen;
				    }
				?>
    		</label>
            <div class="col-md-10 col-sm-9">
                <?php if ($disabled == "") { ?>
                	<select id="kontakt_anrede" name="kontakt_anrede"
                			class="form-control fkt_contact_field" data-settingsname="anrede"
                			tabindex="1">
                		<!-- Auf keinen Fall den Wert 0 vergeben! -->
    					<option value="1" <?php echo $select1; ?>><?php _e("Mr.", 'indiebooking'); ?>
						<option value="2" <?php echo $select2; ?>><?php _e("Mrs.", 'indiebooking'); ?>
                    </select>
                <?php } else { ?>
                	<input type="text" class="form-control fkt_contact_field" id="kontakt_anrede" name="kontakt_anrede"
                			data-errorname="anrede" data-required="<?php echo $contactRequired['salutationRequiredKz']; ?>"
                			data-settingsname="anrede"
                			placeholder="<?php esc_attr_e("Salutation", 'indiebooking'); ?>"
                			value="<?php echo esc_attr($anrede); ?>" <?php echo esc_attr($disabled); ?>>
                <?php } ?>
                <?php echo "<p id='err_anrede' class='text-danger'>$errAnrede</p>";?>
            </div>
        </div>
        <div class="form-group">
            <label for="kontakt_title" class="col-md-2 col-sm-3 hidden-xs control-label">
            	<?php _e("Title", 'indiebooking'); ?>
            </label>
            <div class="col-md-10 col-sm-9">
                <input type="text" class="form-control fkt_contact_field" id="kontakt_title" name="kontakt_title"
                		placeholder="<?php esc_attr_e("Title", 'indiebooking'); ?>" value="<?php echo esc_attr($title); ?>"
                		data-settingsname="titel"
                		<?php echo $disabled; ?>>
                <?php echo "<p id='err_title' class='text-danger'>$errTitle</p>";?>
            </div>
        </div>
        <div class="form-group">
            <label for="kontakt_first_name" class="col-md-2 col-sm-3 hidden-xs control-label">
            	<?php _e("First Name", 'indiebooking'); ?>
            	<?php
            	if ($contactRequired['firstnameRequiredKz']) {
            		echo $kennzeichen;
            	}
            	?>
        	</label>
            <div class="col-md-10 col-sm-9">
                <input type="text" class="form-control fkt_contact_field" id="kontakt_first_name" name="kontakt_first_name"
                		data-errorname="firstName" data-required="<?php echo $contactRequired['firstnameRequiredKz']; ?>"
                		data-settingsname="firstName"
                		placeholder="<?php esc_attr_e("First Name", 'indiebooking'); ?>"
                		value="<?php echo esc_attr($firstName); ?>"
                		autocomplete="on"
                		<?php echo $disabled; ?>>
                <?php echo "<p id='err_text_first_name' class='text-danger'>$errFirstName</p>";?>
            </div>
        </div>
        <div class="form-group">
            <label for="kontakt_name" class="col-md-2 col-sm-3 hidden-xs control-label">
            	<?php _e("Last Name", 'indiebooking'); ?>
				<?php
					if ($contactRequired['nameRequiredKz']) {
						echo $kennzeichen;
				    }
				?>
        	</label>
            <div class="col-md-10 col-sm-9">
                <input type="text" class="form-control fkt_contact_field" id="kontakt_name" name="kontakt_name"
                		data-errorname="name" data-required="<?php echo $contactRequired['nameRequiredKz']; ?>"
                		data-settingsname="name"
                		placeholder="<?php esc_attr_e("Last Name", 'indiebooking'); ?>"
                		value="<?php echo esc_attr($lastName); ?>"
                		autocomplete="on"
                		<?php echo $disabled; ?>>
                <?php echo "<p id='err_text_name' class='text-danger'>$errName</p>";?>
            </div>
        </div>
        <div class="form-group">
            <label for="kontakt_email" class="col-md-2 col-sm-3 hidden-xs control-label">
            	<?php _e("E-Mail", 'indiebooking'); ?>
				<?php
					if ($contactRequired['mailRequiredKz']) {
						echo $kennzeichen;
				    }
				?>
        	</label>
            <div class="col-md-10 col-sm-9">
                <input type="email" class="form-control fkt_contact_field" id="kontakt_email" name="kontakt_email"
                		data-errorname="email" data-required="<?php echo $contactRequired['mailRequiredKz']; ?>"
                		data-settingsname="email"
                		placeholder="<?php esc_attr_e("example@domain.com", 'indiebooking'); ?>"
                		value="<?php echo esc_attr($email); ?>"
                		<?php echo $disabled; ?>>
                <?php echo "<p id='err_email' class='text-danger'>$errEmail</p>";?>
            </div>
        </div>
        <div class="row margin-no">
            <div class="form-group">
                <label for="kontakt_strasse" class="col-md-2 col-sm-3 hidden-xs control-label">
                	<?php esc_attr_e("Street", 'indiebooking'); ?>
					<?php
						if ($contactRequired['addressRequiredKz']) {
							echo $kennzeichen;
					    }
					?>
            	</label>
                <div class="col-md-7 col-sm-6">
                    <input type="text" class="form-control fkt_contact_field" id="route" name="kontakt_strasse" <?php echo $onFocus;?>
                    		data-errorname="strasse" data-required="<?php echo $contactRequired['addressRequiredKz']; ?>"
                    		data-settingsname="strasse"
                    		placeholder="<?php esc_attr_e("Street", 'indiebooking'); ?>"
                    		value="<?php echo esc_attr($street); ?>"
                    		autocomplete="on"
                    		<?php echo $disabled; ?>>
                    <?php echo "<p id='err_street' class='text-danger'>$errStreet</p>";?>
                </div>
                <div class="col-md-3 col-sm-3">
                    <input type="text" class="form-control fkt_contact_field" id="street_number" name="kontakt_strasse_nr"
                    		data-errorname="strasseNr" data-required="<?php echo $contactRequired['addressRequiredKz']; ?>"
                    		data-settingsname="strasseNr"
                    		placeholder="<?php esc_attr_e("Street Nr.", 'indiebooking'); ?>"
                    		value="<?php echo esc_attr($strasseNr); ?>"
                    		autocomplete="on"
                    		<?php echo $disabled; ?>>
                    <?php echo "<p id='err_hausnr' class='text-danger'>$errHausnr</p>";?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="kontakt_plz" class="col-md-2 col-sm-3 hidden-xs control-label">
        		<?php esc_attr_e("Zip code", 'indiebooking'); ?>
				<?php
					if ($contactRequired['addressRequiredKz']) {
						echo $kennzeichen;
				    }
				?>
    		</label>
            <div class="col-md-10 col-sm-9">
                <input type="text" class="form-control fkt_contact_field" id="postal_code" name="kontakt_plz"
                	data-errorname="plz" data-required="<?php echo $contactRequired['addressRequiredKz']; ?>"
                	data-settingsname="plz"
                	placeholder="<?php esc_attr_e("Zip code", 'indiebooking'); ?>"
                	autocomplete="on"
                	value="<?php echo esc_attr($zipCode); ?>"
                	<?php echo $disabled; ?>>
                <?php echo "<p id='err_zip_code' class='text-danger'>$errZip</p>";?>
            </div>
        </div>
        <div class="form-group">
            <label for="kontakt_ort" class="col-md-2 col-sm-3 hidden-xs control-label">
            	<?php esc_attr_e("Location", 'indiebooking'); ?>
				<?php
					if ($contactRequired['addressRequiredKz']) {
						echo $kennzeichen;
				    }
				?>
        	</label>
            <div class="col-md-10 col-sm-9">
                <input type="text" class="form-control fkt_contact_field" id="locality" name="kontakt_ort"
                		data-errorname="ort" data-required="<?php echo $contactRequired['addressRequiredKz']; ?>"
                		data-settingsname="ort"
                		placeholder="<?php esc_attr_e("Location", 'indiebooking'); ?>"
                		autocomplete="on"
            			value="<?php echo esc_attr($location); ?>"
            			<?php echo $disabled; ?>>
                <?php echo "<p id='err_location' class='text-danger'>$errLocation</p>";?>
            </div>
        </div>
        <div class="form-group">
            <label for="kontakt_land" class="col-md-2 col-sm-3 hidden-xs control-label">
            	<?php esc_attr_e("Country", 'indiebooking'); ?>
				<?php
					if ($contactRequired['addressRequiredKz']) {
						echo $kennzeichen;
				    }
				?>
        	</label>
            <div class="col-md-10 col-sm-9">
                <input type="text" class="form-control fkt_contact_field" id="country" name="kontakt_land"
                		data-errorname="country" data-required="<?php echo $contactRequired['addressRequiredKz']; ?>"
                		data-settingsname="country"
                		placeholder="<?php esc_attr_e("Country", 'indiebooking'); ?>"
                		autocomplete="on"
                		value="<?php echo esc_attr($country); ?>"
                		<?php echo $disabled; ?>>
                <?php echo "<p id='err_country' class='text-danger'>$errCountry</p>";?>
            </div>
        </div>
        <div class="form-group">
            <label for="kontakt_telefon" class="col-md-2 col-sm-3 hidden-xs control-label">
            	<?php esc_attr_e("Telefon", 'indiebooking'); ?>
				<?php
					if ($contactRequired['telefonRequiredKz']) {
						echo $kennzeichen;
				    }
				?>
        	</label>
            <div class="col-md-10 col-sm-9">
                <input type="text" class="form-control fkt_contact_field" id="kontakt_telefon" name="kontakt_telefon"
                		data-errorname="telefon" data-required="<?php echo $contactRequired['telefonRequiredKz']; ?>"
                		data-settingsname="telefon"
                		placeholder="<?php esc_attr_e("Telefon", 'indiebooking'); ?>"
                		autocomplete="on"
                		value="<?php echo esc_attr($telefon); ?>"
                		<?php echo $disabled; ?>>
                <?php echo "<p id='err_telefon' class='text-danger'>$errTelefon</p>";?>
            </div>
        </div>
        <?php if ($disabled == "") { ?>
            <div class="form-group">
            	<div class="col-xs-12 notice_pflichtfelder">
            		<?php esc_attr_e("Fields marked with * are required fields", 'indiebooking'); ?>
            	</div>
            </div>
        <?php } ?>
    </div>
    <!-- Alternative Rechnungsadresse -->
    <?php if ($buchungKopf->getBuchung_status() !== "rs_ib-almost_booked") {
        $checkAdr2 = "";
        if ($useAdress2 == 1) {
            $checkAdr2 = "checked='checked'";
        }
    ?>
    <div class="row">
    	<div class="form-group">
        	<div class="toggleLink col-md-1">
        		&nbsp;
        	</div>
            <div class="toggleLink col-md-11">
    			<input id="useAlternateBillingAdressCb" class="rs_ib_toggleBtn" type="checkbox" <?php echo esc_attr($checkAdr2); ?>>
    			<?php __("alternative billing address", 'indiebooking'); ?>
    			<?php esc_attr_e("alternative billing address", 'indiebooking');?>
        	</div>
    	</div>
	</div>
    <?php
	    }
        $alternateAdressClass = "rs_indiebooking_do_not_show";
        if (isset($useAdress2)) {
            if ($useAdress2 == 1 || $useAdress2 === "1") {
                $alternateAdressClass = "";
            }
        }
    ?>
    <?php
    if ($useAdress2 == 1 || $useAdress2 === "1") { ?>
    <div class="row">
    	<div class="form-group">
    		<label class="col-md-4 col-sm-4 hidden-xs control-label">
    			<?php esc_attr_e("alternative billing address", 'indiebooking');?>
			</label>
    	</div>
	</div>
    <?php } ?>
	<div class="toggle_item  <?php echo esc_attr($alternateAdressClass); ?>">
         <div class="row">
            <div class="form-group">
                <label for="kontakt_firma2" class="col-md-2 col-sm-3 hidden-xs control-label">
                	<?php _e("Company", 'indiebooking'); ?>
            	</label>
                <div class="col-md-10 col-sm-9">
                    <input type="text" class="form-control" id="kontakt_firma2" name="kontakt_firma2"
                    		placeholder="<?php esc_attr_e("Company", 'indiebooking'); ?>"
                    		value="<?php echo esc_attr($firma2); ?>"
                    		<?php echo $disabled; ?>>
                    <?php echo "<p id='err_firma2' class='text-danger'></p>";?>
                </div>
            </div>
	        <div class="form-group">
	            <label for="kontakt_abteilung2" class="col-md-2 col-sm-3 hidden-xs control-label"><?php _e("Department", 'indiebooking'); ?></label>
	            <div class="col-md-10 col-sm-9">
	                <input type="text" class="form-control" id="kontakt_abteilung2" name="kontakt_abteilung2"
	                		placeholder="<?php esc_attr_e("Department", 'indiebooking'); ?>"
	                		value="<?php echo esc_attr($department2); ?>" <?php echo esc_attr($disabled); ?>>
	                <?php echo "<p id='err_firma' class='text-danger'></p>";?>
	            </div>
	        </div>
            <div class="form-group">
                <label for="kontakt_anrede2" class="col-md-2 col-sm-3 hidden-xs control-label">
                	<?php _e("Salutation", 'indiebooking'); ?>
                	<?php //echo $kennzeichen; ?>
            	</label>
                <div class="col-md-10 col-sm-9">
                    <?php if ($disabled == "") { ?>
                    	<select id="kontakt_anrede2" name="kontakt_anrede2" class="form-control" tabindex="1">
                    		<!-- Auf keinen Fall den Wert 0 vergeben! -->
        					<option value="1" <?php echo $select21; ?>><?php _e("Mr.", 'indiebooking'); ?>
    						<option value="2" <?php echo $select22; ?>><?php _e("Mrs.", 'indiebooking'); ?>
    						<option value="3" <?php echo $select23; ?>><?php _e("", 'indiebooking'); ?>
    						<option value="4" <?php echo $select24; ?>><?php _e("Department", 'indiebooking'); ?>
                        </select>
                    <?php } else { ?>
                    	<input type="text" class="form-control" id="kontakt_anrede2" name="kontakt_anrede2"
                    			placeholder="<?php esc_attr_e("Salutation", 'indiebooking'); ?>"
                    			value="<?php echo esc_attr($anrede2); ?>"
                    			<?php echo $disabled; ?>>
                    <?php } ?>
                    <?php echo "<p id='err_anrede2' class='text-danger'>$errAnrede</p>";?>
                </div>
            </div>
            <div class="form-group">
                <label for="kontakt_title2" class="col-md-2 col-sm-3 hidden-xs control-label">
                	<?php _e("Title", 'indiebooking'); ?>
            	</label>
                <div class="col-md-10 col-sm-9">
                    <input type="text" class="form-control" id="kontakt_title2" name="kontakt_title2"
                    		placeholder="<?php esc_attr_e("Title", 'indiebooking'); ?>"
                    		value="<?php echo esc_attr($title2); ?>"
                    		<?php echo $disabled; ?>>
                    <?php echo "<p id='err_title2' class='text-danger'>$errTitle</p>";?>
                </div>
            </div>
            <div class="form-group">
                <label for="kontakt_first_name2" class="col-md-2 col-sm-3 hidden-xs control-label">
                	<?php esc_attr_e("First Name", 'indiebooking'); ?>
                	<?php //echo $kennzeichen; ?>
            	</label>
                <div class="col-md-10 col-sm-9">
                    <input type="text" class="form-control" id="kontakt_first_name2" name="kontakt_first_name2"
                    		placeholder="<?php esc_attr_e("First Name", 'indiebooking'); ?>"
                    		value="<?php echo esc_attr($firstName2); ?>"
                    		<?php echo $disabled; ?>>
                    <?php echo "<p id='err_text_first_name2' class='text-danger'>$errFirstName</p>";?>
                </div>
            </div>
            <div class="form-group">
                <label for="kontakt_name2" class="col-md-2 col-sm-3 hidden-xs control-label">
                	<?php esc_attr_e("Last Name", 'indiebooking'); ?>
                	<?php //echo $kennzeichen; ?>
            	</label>
                <div class="col-md-10 col-sm-9">
                    <input type="text" class="form-control" id="kontakt_name2" name="kontakt_name2"
                    		placeholder="<?php esc_attr_e("Last Name", 'indiebooking'); ?>"
                    		value="<?php echo esc_attr($lastName2); ?>"
                    		<?php echo $disabled; ?>>
                    <?php echo "<p id='err_text_name2' class='text-danger'>$errName</p>";?>
                </div>
            </div>
            <div class="form-group" hidden="hiddden">
                <label for="kontakt_email2" class="col-md-2 col-sm-3 hidden-xs control-label">
                	<?php esc_attr_e("E-Mail", 'indiebooking'); ?>
                	<?php echo $kennzeichen; ?>
            	</label>
                <div class="col-md-10 col-sm-9">
                    <input type="email" class="form-control" id="kontakt_email2" name="kontakt_email2"
                    		placeholder="<?php esc_attr_e("example@domain.com", 'indiebooking'); ?>"
                    		value="<?php echo esc_attr($email2); ?>"
                    		<?php echo $disabled; ?>>
                    <?php echo "<p id='err_email2' class='text-danger'>$errEmail</p>";?>
                </div>
            </div>
            <div class="row margin-no">
                <div class="form-group">
                    <label for="kontakt_strasse2" class="col-md-2 col-sm-3 hidden-xs control-label">
                    	<?php esc_attr_e("Street", 'indiebooking'); ?>
                    	<?php echo $kennzeichen; ?>
                	</label>
                    <div class="col-md-7 col-sm-6">
                        <input type="text" class="form-control" id="route2" name="kontakt_strasse2" <?php echo $onFocus; ?>
                        		placeholder="<?php esc_attr_e("Street", 'indiebooking'); ?>"
                        		value="<?php echo esc_attr($street2); ?>"
                        		<?php echo $disabled; ?>>
                        <?php echo "<p id='err_street2' class='text-danger'>$errStreet</p>";?>
                    </div>
                    <div class="col-md-3 col-sm-3">
                        <input type="text" class="form-control" id="street_number2" name="kontakt_strasse2_nr"
                        		placeholder="<?php esc_attr_e("Street Nr.", 'indiebooking'); ?>"
                        		value="<?php echo esc_attr($strasseNr2); ?>"
                        		<?php echo $disabled; ?>>
                        <?php echo "<p id='err_hausnr2' class='text-danger'>$errHausnr</p>";?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="kontakt_plz2" class="col-md-2 col-sm-3 hidden-xs control-label">
                	<?php _e("Zip code", 'indiebooking'); ?><?php echo $kennzeichen; ?>
            	</label>
                <div class="col-md-10 col-sm-9">
                    <input type="text" class="form-control" id="postal_code2" name="kontakt_plz2"
                    		placeholder="<?php esc_attr_e("Zip code", 'indiebooking'); ?>"
                    		value="<?php echo esc_attr($zipCode2); ?>"
                    		<?php echo $disabled; ?>>
                    <?php echo "<p id='err_zip_code2' class='text-danger'>$errZip</p>";?>
                </div>
            </div>
            <div class="form-group">
                <label for="kontakt_ort2" class="col-md-2 col-sm-3 hidden-xs control-label">
                	<?php esc_attr_e("Location", 'indiebooking'); ?>
                	<?php echo $kennzeichen; ?>
            	</label>
                <div class="col-md-10 col-sm-9">
                    <input type="text" class="form-control" id="locality2" name="kontakt_ort2"
                    		placeholder="<?php esc_attr_e("Location", 'indiebooking'); ?>"
                    		value="<?php echo esc_attr($location2); ?>"
                    		<?php echo $disabled; ?>>
                    <?php echo "<p id='err_location2' class='text-danger'>$errLocation</p>";?>
                </div>
            </div>
            <div class="form-group">
                <label for="kontakt_land2" class="col-md-2 col-sm-3 hidden-xs control-label">
                	<?php esc_attr_e("Country", 'indiebooking'); ?>
                	<?php echo $kennzeichen; ?>
            	</label>
                <div class="col-md-10 col-sm-9">
                    <input type="text" class="form-control" id="country2" name="kontakt_land2"
                    		placeholder="<?php esc_attr_e("Country", 'indiebooking'); ?>"
                    		value="<?php echo esc_attr($country2); ?>"
                    		<?php echo $disabled; ?>>
                    <?php echo "<p id='err_country2' class='text-danger'>$errCountry</p>";?>
                </div>
            </div>
            <div class="form-group" hidden="hidden">
                <label for="kontakt_telefon2" class="col-md-2 col-sm-3 hidden-xs control-label">
                	<?php esc_attr_e("Telefon", 'indiebooking'); ?>
                	<?php echo $kennzeichen; ?>
            	</label>
                <div class="col-md-10 col-sm-9">
                    <input type="text" class="form-control" id="kontakt_telefon2" name="kontakt_telefon2"
                        	placeholder="<?php esc_attr_e("Telefon", 'indiebooking'); ?>"
                        	value="<?php echo esc_attr($telefon2); ?>"
                        	<?php echo $disabled; ?>>
                    <?php echo "<p id='err_telefon2' class='text-danger'>$errTelefon</p>";?>
                </div>
            </div>
            <?php if ($disabled == "") { ?>
                <div class="form-group">
                	<div class="col-xs-12 notice_pflichtfelder">
                		<?php _e("Fields marked with * are required fields", 'indiebooking'); ?>
                	</div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sx-10 col-sx-offset-2">
            <?php echo $result; ?>
        </div>
    </div>
<!-- </form> -->
</div>