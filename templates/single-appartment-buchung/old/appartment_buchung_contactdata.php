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
    	<h4><?php _e("Insert your contactdata ", 'indiebooking'); ?>:</h4>
    <?php } ?>
     <?php
        $errTitle       = __("The title Data is invalid", 'indiebooking');
        $errAnrede      = __("The salutation Data is invalid", 'indiebooking');
        $errName        = __("The Name Data is invalid", 'indiebooking');
        $errFirstName   = __("The First Name Data is invalid", 'indiebooking');
        $errEmail       = __("The Email Data is invalid", 'indiebooking');
        $errStreet      = __("The Street Data is invalid", 'indiebooking');
        $errZip         = __("The Zip Data is invalid", 'indiebooking');
        $errLocation    = __("The Location Data is invalid", 'indiebooking');
        $errTelefon     = __("The Telefon Data is invalid", 'indiebooking');
        $title          = "";
        $anrede         = "";
        $lastName       = "";
        $firstName      = "";
        $email          = "";
        $street         = "";
        $zipCode        = "";
        $location       = "";
        $telefon        = "";
        if (!isset($result)) {
            $result     = "";
        }
        if (isset($contact) && !is_null($contact)) {
            if (array_key_exists('name', $contact)) {
                $lastName = $contact['name'];
            }
            if (array_key_exists('firstName', $contact)) {
                $firstName = $contact['firstName'];
            }
            if (array_key_exists('strasse', $contact)) {
                $street = $contact['strasse'];
            }
            if (array_key_exists('ort', $contact)) {
                $location = $contact['ort'];
            }
            if (array_key_exists('plz', $contact)) {
                $zipCode = $contact['plz'];
            }
            if (array_key_exists('email', $contact)) {
                $email = $contact['email'];
            }
            if (array_key_exists('telefon', $contact)) {
                $telefon = $contact['telefon'];
            }
            if (array_key_exists('title', $contact)) {
                $title = $contact['title'];
            }
            $select1 = "";
            $select2 = "";
            if (array_key_exists('anrede', $contact)) {
                $anrede = $contact['anrede'];
                if ($disabled == "") {
                    switch (trim($anrede)) {
                        case __("Mr.", 'indiebooking') :
                            $select1 = "selected = 'selected'";
                            break;
                        case __("Mrs.", 'indiebooking') :
                            $select2 = "selected = 'selected'";
                            break;
                    }
                }
            }
        }
     ?>
     <div class="row">
        <div class="form-group">
            <div class="anrede_title_box">
                <div class="col-xs-2"><label for="kontakt_anrede" class="control-label"><?php _e("Salutation", 'indiebooking'); ?><?php echo $kennzeichen; ?></label></div>
                <div class="col-xs-3">
                	<?php if ($disabled == "") { ?>
                    	<select id="kontakt_anrede" name="kontakt_anrede" class="form-control" tabindex="1">
                    		<!-- Auf keinen Fall den Wert 0 vergeben! -->
    						<option value="1" <?php echo $select1; ?>><?php _e("Mr.", 'indiebooking'); ?>
    						<option value="2" <?php echo $select2; ?>><?php _e("Mrs.", 'indiebooking'); ?>
                    	</select>
                	<?php } else { ?>
                    	<input type="text" class="form-control" id="kontakt_anrede" name="kontakt_anrede" placeholder="<?php _e("Salutation", 'indiebooking'); ?>" value="<?php echo $anrede; ?>" <?php echo $disabled; ?>>
                    <?php } ?>
                    <?php echo "<p id='err_anrede' class='text-danger'>$errAnrede</p>";?>
                </div>
                <div class="col-xs-1"><label for="kontakt_title" class="control-label"><?php _e("Title", 'indiebooking'); ?></label></div>
                <div class="col-xs-6">
                    <input type="text" class="form-control" id="kontakt_title" name="kontakt_title" placeholder="<?php _e("Title", 'indiebooking'); ?>" value="<?php echo $title; ?>" <?php echo $disabled; ?>>
                    <?php echo "<p id='err_title' class='text-danger'>$errTitle</p>";?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-2"><label for="kontakt_first_name" class="control-label"><?php _e("First Name", 'indiebooking'); ?><?php echo $kennzeichen; ?></label></div>
            <div class="col-xs-10">
                <input type="text" class="form-control" id="kontakt_first_name" name="kontakt_first_name" placeholder="<?php _e("First Name", 'indiebooking'); ?>" value="<?php echo $firstName; ?>" <?php echo $disabled; ?>>
                <?php echo "<p id='err_text_first_name' class='text-danger'>$errFirstName</p>";?>
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-2"><label for="kontakt_name" class="control-label"><?php _e("Last Name", 'indiebooking'); ?><?php echo $kennzeichen; ?></label></div>
            <div class="col-xs-10">
                <input type="text" class="form-control" id="kontakt_name" name="kontakt_name" placeholder="<?php _e("Last Name", 'indiebooking'); ?>" value="<?php echo $lastName; ?>" <?php echo $disabled; ?>>
                <?php echo "<p id='err_text_name' class='text-danger'>$errName</p>";?>
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-2"><label for="kontakt_email" class="control-label"><?php _e("E-Mail", 'indiebooking'); ?><?php echo $kennzeichen; ?></label></div>
            <div class="col-xs-10">
                <input type="email" class="form-control" id="kontakt_email" name="kontakt_email" placeholder="<?php _e("example@domain.com", 'indiebooking'); ?>" value="<?php echo $email; ?>" <?php echo $disabled; ?>>
                <?php echo "<p id='err_email' class='text-danger'>$errEmail</p>";?>
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-2"><label for="kontakt_strasse" class="control-label"><?php _e("Street", 'indiebooking'); ?><?php echo $kennzeichen; ?></label></div>
            <div class="col-xs-10">
                <input type="text" class="form-control" id="kontakt_strasse" name="kontakt_strasse" placeholder="<?php _e("Street", 'indiebooking'); ?>" value="<?php echo $street; ?>" <?php echo $disabled; ?>>
                <?php echo "<p id='err_street' class='text-danger'>$errStreet</p>";?>
            </div>
        </div>
        <div class="form-group">
            <div class="zip_ort_box">
                <div class="col-xs-2"><label for="kontakt_plz" class="control-label"><?php _e("Zip code", 'indiebooking'); ?><?php echo $kennzeichen; ?></label></div>
                <div class="col-xs-3">
                    <input type="text" class="form-control" id="kontakt_plz" name="kontakt_plz" placeholder="<?php _e("Zip code", 'indiebooking'); ?>" value="<?php echo $zipCode; ?>" <?php echo $disabled; ?>>
                    <?php echo "<p id='err_zip_code' class='text-danger'>$errZip</p>";?>
                </div>
                <div class="col-xs-1"><label for="kontakt_ort" class="control-label"><?php _e("Location", 'indiebooking'); ?><?php echo $kennzeichen; ?></label></div>
                <div class="col-xs-6">
                    <input type="text" class="form-control" id="kontakt_ort" name="kontakt_ort" placeholder="<?php _e("Location", 'indiebooking'); ?>" value="<?php echo $location; ?>" <?php echo $disabled; ?>>
                    <?php echo "<p id='err_location' class='text-danger'>$errLocation</p>";?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-2"><label for="kontakt_telefon" class="control-label"><?php _e("Telefon", 'indiebooking'); ?><?php echo $kennzeichen; ?></label></div>
            <div class="col-xs-10">
                <input type="text" class="form-control" id="kontakt_telefon" name="kontakt_telefon" placeholder="<?php _e("Telefon", 'indiebooking'); ?>" value="<?php echo $telefon; ?>" <?php echo $disabled; ?>>
                <?php echo "<p id='err_telefon' class='text-danger'>$errTelefon</p>";?>
            </div>
        </div>
        <?php if ($disabled == "") { ?>
            <div class="form-group">
            	<br />
            	<div class="col-xs-12">
            		<?php _e("Fields marked with * are required fields", 'indiebooking'); ?>
            	</div>
            	<?php //do_action("rs_indiebooking_single_rsappartment_buchung_controll_buttons"); ?>
            </div>
        <?php } ?>
    </div>
    <div class="form-group pull-right">
        <div class="col-sx-10 col-sx-offset-2">
        <?php //do_action("rs_indiebooking_single_rsappartment_buchung_controll_buttons"); ?>
    <!--         <input id="btnSaveBooking" name="submit" type="submit" value="<?php //_e("next", 'indiebooking')?>" class="btn btn-primary btn-lg btn_rewa"><!-- btn btn-primary -->
        </div>
    </div>
    <div class="form-group">
        <div class="col-sx-10 col-sx-offset-2">
            <?php echo $result; ?>
        </div>
    </div>
</div>