<!-- class="ibui_newbookingtable_contantdata" -->
<?php
	/* @var $buchungsKopf RS_IB_Model_Buchungskopf */
	$bookingNumber		= $buchungsKopf->getBuchung_nr();
?>
<div id="edit_booking_contact_data_box" class="ibui_newbookingtable_contantdata" data-bookingnumber="<?php echo $bookingNumber; ?>">
	<?php
	$firma          = $buchungsKopf->getKunde_firma();
	$anrede         = $buchungsKopf->getKunde_anrede();
	$title          = $buchungsKopf->getKunde_title();
	$lastName       = $buchungsKopf->getKunde_name();
	$firstName      = $buchungsKopf->getKunde_vorname();
	$email          = $buchungsKopf->getKunde_email();
	$street         = $buchungsKopf->getKunde_strasse();
	$zipCode        = $buchungsKopf->getKunde_plz();
	$location       = $buchungsKopf->getKunde_ort();
	$telefon        = $buchungsKopf->getKunde_telefon();
	$country        = $buchungsKopf->getKunde_land();
	$strasseNr      = $buchungsKopf->getKunde_strasse_nr();
	$department		= $buchungsKopf->getKunde_abteilung();
	
	$useAdress2		= $buchungsKopf->getUseAdress2();
	
	$firma2     	= $buchungsKopf->getKunde_firma2();
	$anrede2     	= $buchungsKopf->getKunde_anrede2();
	$title2     	= $buchungsKopf->getKunde_title2();
	$lastName2     	= $buchungsKopf->getKunde_name2();
	$firstName2     = $buchungsKopf->getKunde_vorname2();
	$email2     	= $buchungsKopf->getKunde_email2();
	$street2     	= $buchungsKopf->getKunde_strasse2();
	$zipCode2     	= $buchungsKopf->getKunde_plz2();
	$location2     	= $buchungsKopf->getKunde_ort2();
	$telefon2     	= $buchungsKopf->getKunde_telefon2();
	$country2     	= $buchungsKopf->getKunde_land2();
	$strasseNr2     = $buchungsKopf->getKunde_strasse_nr2();
	$department2	= $buchungsKopf->getKunde_abteilung2();
	
	
	$select3		= "";
	if (!isset($errTitle)) {
		$errTitle   = __("The title Data is invalid", 'indiebooking');
	}
	if (!isset($errAnrede)) {
		$errAnrede  = __("The salutation Data is invalid", 'indiebooking');
	}
	if (!isset($errName)) {
		$errName    = __("The Name Data is invalid", 'indiebooking');
	}
	if (!isset($errFirstName)) {
		$errFirstName   = __("The First Name Data is invalid", 'indiebooking');
	}
	if (!isset($errEmail)) {
		$errEmail       = __("The Email Data is invalid", 'indiebooking');
	}
	if (!isset($errStreet)) {
		$errStreet      = __("The Street Data is invalid", 'indiebooking');
	}
	if (!isset($errZip)) {
		$errZip         = __("The Zip Data is invalid", 'indiebooking');
	}
	if (!isset($errLocation)) {
		$errLocation    = __("The Location Data is invalid", 'indiebooking');
	}
	if (!isset($errTelefon)) {
		$errTelefon     = __("The Telefon Data is invalid", 'indiebooking');
	}
	if (!isset($errHausnr)) {
		$errHausnr      = __("The Nr is invalid (max 10 character)", 'indiebooking');
	}
	if (!isset($errCountry)) {
		$errCountry     = __("The Country is invalid", 'indiebooking');
	}
	if (!isset($firma)) {
		$firma          = "";
	}
	if (!isset($title)) {
		$title          = "";
	}
	if (!isset($anrede)) {
		$anrede         = "";
	}
	if (!isset($lastName)) {
		$lastName       = "";
	}
	if (!isset($firstName)) {
		$firstName      = "";
	}
	if (!isset($email)) {
		$email          = "";
	}
	if (!isset($street)) {
		$street         = "";
	}
	if (!isset($zipCode)) {
		$zipCode        = "";
	}
	if (!isset($location)) {
		$location       = "";
	}
	if (!isset($$telefon)) {
		$telefon        = "";
	}
	if (!isset($country)) {
		$country        = "";
	}
	if (!isset($strasseNr)) {
		$strasseNr      = "";
	}
	if (!isset($department)) {
		$department 	= "";
	}
	
	if (!isset($useAdress2)) {
		$useAdress2     = 0;
	}
	if (!isset($firma2)) {
		$firma2         = "";
	}
	if (!isset($title2)) {
		$title2         = "";
	}
	if (!isset($anrede2)) {
		$anrede2        = "";
	}
	if (!isset($lastName2)) {
		$lastName2      = "";
	}
	if (!isset($firstName2)) {
		$firstName2     = "";
	}
	if (!isset($email2)) {
		$email2         = "";
	}
	if (!isset($street2)) {
		$street2        = "";
	}
	if (!isset($zipCode2)) {
		$zipCode2       = "";
	}
	if (!isset($location2)) {
		$location2      = "";
	}
	if (!isset($telefon2)) {
		$telefon2       = "";
	}
	if (!isset($country2)) {
		$country2       = "";
	}
	if (!isset($strasseNr2)) {
		$strasseNr2     = "";
	}
	if (!isset($checkAdr2)) {
		$checkAdr2 		= "";
	}
	
	if (!isset($select1)) {
		$select1		= "";
	}
	if (!isset($select2)) {
		$select2		= "";
	}
	if (!isset($select21)) {
		$select21		= "";
	}
	if (!isset($select22)) {
		$select22		= "";
	}
	if (!isset($select23)) {
		$select23		= "";
	}
	if (!isset($select24)) {
		$select24		= "";
	}
	if (!isset($result)) {
		$result			= "";
	}
	if (!isset($department2)) {
		$department2 	= "";
	}
	
	switch (trim($anrede)) {
		case __("Mr.", 'indiebooking') :
			$select1 = "selected = 'selected'";
			break;
		case __("Mrs.", 'indiebooking') :
			$select2 = "selected = 'selected'";
			break;
		default:
			$select3 = "selected = 'selected'";
			break;
	}
	
	switch (trim($anrede2)) {
		case __("Mr.", 'indiebooking') :
			$select21 = "selected = 'selected'";
			break;
		case __("Mrs.", 'indiebooking') :
			$select22 = "selected = 'selected'";
			break;
	}
	
	if ($useAdress2 == true) {
		$checkAdr2 = "checked='checked'";
	}
	
	if ($select21 == "" && $select22 == "" && $select23 == "" && $select24 == "") {
		$select23 		= 'selected = "selected"';
	}
	
	$kennzeichen       	= "";
	if (!isset($disabled)) {
	    $disabled   	= "";
	}
	if ($disabled == "") {
	    $kennzeichen    = "*";
    ?>
	    <div class="ibui_h2wrap">
	        <h2 class="ibui_h2"><?php _e("Insert your contactdata", 'indiebooking'); ?>:</h2>
	    </div>
    <?php } ?>
     <div class="row">
        <div class="form-group">
            <label for="kontakt_firma" class="rsib_col-md-2 col-sm-3 hidden-xs control-label ibui_label"><?php _e("Company", 'indiebooking'); ?></label>
            <div class="rsib_col-md-10 col-sm-9">
                <input type="text" class="form-control ibui_input" id="kontakt_firma" name="kontakt_firma"
                		placeholder="<?php esc_attr_e("Company", 'indiebooking'); ?>"
                		value="<?php echo esc_attr($firma); ?>" <?php echo esc_attr($disabled); ?>>
                <?php echo "<p id='err_firma' class='text-danger'></p>";?>
            </div>
        </div>
        <div class="form-group">
            <label for="kontakt_abteilung" class="rsib_col-md-2 col-sm-3 hidden-xs control-label ibui_label"><?php _e("Department", 'indiebooking'); ?></label>
            <div class="rsib_col-md-10 col-sm-9">
                <input type="text" class="form-control ibui_input" id="kontakt_abteilung" name="kontakt_abteilung"
                		placeholder="<?php esc_attr_e("Department", 'indiebooking'); ?>"
                		value="<?php echo esc_attr($department); ?>" <?php echo esc_attr($disabled); ?>>
            </div>
        </div>
        <div class="form-group">
            <label for="kontakt_anrede" class="rsib_col-md-2 col-sm-3 hidden-xs control-label ibui_label">
            		<?php _e("Salutation", 'indiebooking'); ?>
            		<?php echo $kennzeichen; ?>
    		</label>
            <div class="rsib_col-md-10 col-sm-9">
                <?php if ($disabled == "") { ?>
                	<select id="kontakt_anrede" name="kontakt_anrede" class="form-control ibui_select" tabindex="1">
                		<!-- Auf keinen Fall den Wert 0 vergeben! -->
    					<option value="1" <?php echo $select1; ?>><?php _e("Mr.", 'indiebooking'); ?>
						<option value="2" <?php echo $select2; ?>><?php _e("Mrs.", 'indiebooking'); ?>
                		<option value="3" <?php echo $select3; ?>><?php echo ""; ?>
                    </select>
                <?php } else { ?>
                	<input type="text" class="form-control" id="kontakt_anrede" name="kontakt_anrede"
                			placeholder="<?php esc_attr_e("Salutation", 'indiebooking'); ?>"
                			value="<?php echo esc_attr($anrede); ?>" <?php echo esc_attr($disabled); ?>>
                <?php } ?>
                <?php echo "<p id='err_anrede' class='text-danger'>$errAnrede</p>";?>
            </div>
            <br class="clear" />
        </div>
        <div class="form-group">
            <label for="kontakt_title" class="rsib_col-md-2 col-sm-3 hidden-xs control-label ibui_label"><?php _e("Title", 'indiebooking'); ?></label>
            <div class="rsib_col-md-10 col-sm-9">
                <input type="text" class="form-control ibui_input" id="kontakt_title" name="kontakt_title"
                		placeholder="<?php esc_attr_e("Title", 'indiebooking'); ?>" value="<?php echo esc_attr($title); ?>"
                		<?php echo $disabled; ?>>
                <?php echo "<p id='err_title' class='text-danger'>$errTitle</p>";?>
            </div>
            <br class="clear" />
        </div>
        <div class="form-group">
            <label for="kontakt_first_name" class="rsib_col-md-2 col-sm-3 hidden-xs control-label ibui_label">
            	<?php _e("First Name", 'indiebooking'); ?>
            	<?php echo $kennzeichen; ?>
        	</label>
            <div class="rsib_col-md-10 col-sm-9">
                <input type="text" class="form-control rs_ib_pasteable_field ibui_input" id="kontakt_first_name" name="kontakt_first_name"
                		placeholder="<?php esc_attr_e("First Name", 'indiebooking'); ?>"
                		value="<?php echo esc_attr($firstName); ?>"
                		autocomplete="on"
                		<?php echo $disabled; ?>>
                <?php echo "<p id='err_text_first_name' class='text-danger'>$errFirstName</p>";?>
            </div>
            <br class="clear" />
        </div>
        <div class="form-group">
            <label for="kontakt_name" class="rsib_col-md-2 col-sm-3 hidden-xs control-label ibui_label">
            	<?php _e("Last Name", 'indiebooking'); ?>
            	<?php echo $kennzeichen; ?>
        	</label>
            <div class="rsib_col-md-10 col-sm-9">
                <input type="text" class="form-control ibui_input" id="kontakt_name" name="kontakt_name"
                		placeholder="<?php esc_attr_e("Last Name", 'indiebooking'); ?>"
                		value="<?php echo esc_attr($lastName); ?>"
                		autocomplete="on"
                		<?php echo $disabled; ?>>
                <?php echo "<p id='err_text_name' class='text-danger'>$errName</p>";?>
            </div>
            <br class="clear" />
        </div>
        <div class="form-group">
            <label for="kontakt_email" class="rsib_col-md-2 col-sm-3 hidden-xs control-label ibui_label">
            	<?php _e("E-Mail", 'indiebooking'); ?>
            	<?php echo $kennzeichen; ?>
        	</label>
            <div class="rsib_col-md-10 col-sm-9">
                <input type="email" class="form-control ibui_input" id="kontakt_email" name="kontakt_email"
                		placeholder="<?php esc_attr_e("example@domain.com", 'indiebooking'); ?>"
                		value="<?php echo esc_attr($email); ?>"
                		<?php echo $disabled; ?>>
                <?php echo "<p id='err_email' class='text-danger'>$errEmail</p>";?>
            </div>
            <br class="clear" />
        </div>
        <div class="row margin-no">
            <div class="form-group">
                <label for="kontakt_strasse" class="rsib_col-md-2 col-sm-3 hidden-xs control-label ibui_label">
                	<?php esc_attr_e("Street", 'indiebooking'); ?>
                	<?php echo $kennzeichen; ?>
            	</label>
                <div class="rsib_col-md-7 col-sm-6">
                    <input type="text" class="form-control ibui_input" id="route" name="kontakt_strasse"
                    		placeholder="<?php esc_attr_e("Street", 'indiebooking'); ?>"
                    		value="<?php echo esc_attr($street); ?>"
                    		autocomplete="on"
                    		<?php echo $disabled; ?>>
                    <?php echo "<p id='err_street' class='text-danger'>$errStreet</p>";?>
                </div>
                <div class="rsib_col-md-3 col-sm-3">
                    <input type="text" class="form-control ibui_input" id="street_number" name="kontakt_strasse_nr"
                    		placeholder="<?php esc_attr_e("Street Nr.", 'indiebooking'); ?>"
                    		value="<?php echo esc_attr($strasseNr); ?>"
                    		autocomplete="on"
                    		<?php echo $disabled; ?>>
                    <?php echo "<p id='err_hausnr' class='text-danger'>$errHausnr</p>";?>
                </div>
                <br class="clear" />
            </div>
        </div>
        <div class="form-group">
            <label for="kontakt_plz" class="rsib_col-md-2 col-sm-3 hidden-xs control-label ibui_label">
        		<?php esc_attr_e("Zip code", 'indiebooking'); ?>
        		<?php echo $kennzeichen; ?>
    		</label>
            <div class="rsib_col-md-10 col-sm-9">
                <input type="text" class="form-control ibui_input" id="postal_code" name="kontakt_plz"
                	placeholder="<?php esc_attr_e("Zip code", 'indiebooking'); ?>"
                	autocomplete="on"
                	value="<?php echo esc_attr($zipCode); ?>"
                	<?php echo $disabled; ?>>
                <?php echo "<p id='err_zip_code' class='text-danger'>$errZip</p>";?>
            </div>
            <br class="clear" />
        </div>
        <div class="form-group">
            <label for="kontakt_ort" class="rsib_col-md-2 col-sm-3 hidden-xs control-label ibui_label">
            	<?php esc_attr_e("Location", 'indiebooking'); ?>
            	<?php echo $kennzeichen; ?>
        	</label>
            <div class="rsib_col-md-10 col-sm-9">
                <input type="text" class="form-control ibui_input" id="locality" name="kontakt_ort"
                		placeholder="<?php esc_attr_e("Location", 'indiebooking'); ?>"
                		autocomplete="on"
            			value="<?php echo esc_attr($location); ?>"
            			<?php echo $disabled; ?>>
                <?php echo "<p id='err_location' class='text-danger'>$errLocation</p>";?>
            </div>
            <br class="clear" />
        </div>
        <div class="form-group">
            <label for="kontakt_land" class="rsib_col-md-2 col-sm-3 hidden-xs control-label ibui_label">
            	<?php esc_attr_e("Country", 'indiebooking'); ?>
            	<?php echo $kennzeichen; ?>
        	</label>
            <div class="rsib_col-md-10 col-sm-9">
                <input type="text" class="form-control ibui_input" id="country" name="kontakt_land"
                		placeholder="<?php esc_attr_e("Country", 'indiebooking'); ?>"
                		autocomplete="on"
                		value="<?php echo esc_attr($country); ?>"
                		<?php echo $disabled; ?>>
                <?php echo "<p id='err_country' class='text-danger'>$errCountry</p>";?>
            </div>
            <br class="clear" />
        </div>
        <div class="form-group">
            <label for="kontakt_telefon" class="rsib_col-md-2 col-sm-3 hidden-xs control-label ibui_label">
            	<?php esc_attr_e("Telefon", 'indiebooking'); ?>
            	<?php //echo $kennzeichen; ?>
        	</label>
            <div class="rsib_col-md-10 col-sm-9">
                <input type="text" class="form-control ibui_input" id="kontakt_telefon" name="kontakt_telefon"
                		placeholder="<?php esc_attr_e("Telefon", 'indiebooking'); ?>"
                		autocomplete="on"
                		value="<?php echo esc_attr($telefon); ?>"
                		<?php echo $disabled; ?>>
                <?php echo "<p id='err_telefon' class='text-danger'>$errTelefon</p>";?>
            </div>
            <br class="clear" />
        </div>
        <?php if ($disabled == "") { ?>
            <div class="form-group">
            	<div class="col-xs-12 rsib_col-md-10 rsib_col-md-offset-2 notice_pflichtfelder">
            		<?php esc_attr_e("Fields marked with * are required fields", 'indiebooking'); ?>
            	</div>
            	<br class="clear" />
            </div>
        <?php } ?>
    </div>
<!-- Alternative Rechnungsadresse -->
    <div class="row">
    	<div class="form-group">
        	<div class="toggleLink rsib_col-md-1 ibui_toggleLink_rechnungsadresse">
                <div class="ibui_switchbtn" style="float:right;">
                   <input id="useAlternateBillingAdressCb" class="ibui_switchbtn_input ibfc_switchbtn_input"
                   			type="checkbox"
                   			value=""
                            <?php echo esc_attr($checkAdr2); ?> />
                   <label for="useAlternateBillingAdressCb"></label>
                </div>
        	</div>
            <div class="toggleLink rsib_col-md-11 ibui_toggleLink_rechnungsadresse" style="padding-left: 10px; padding-top: 7px;">
            	<!--
    			<input id="useAlternateBillingAdressCb_alt" class="rs_ib_toggleBtn" type="checkbox" <?php //echo esc_attr($checkAdr2); ?>>
    			 -->
    			<?php __("alternative billing address", 'indiebooking'); ?>
    			<?php esc_attr_e("alternative billing address", 'indiebooking');?>
        	</div>
        	<br class="clear" />
    	</div>
    	<?php
    	$displaySecondAdress = 'display: none;';
    	if ($useAdress2 == true) {
    		$displaySecondAdress = "";
    	}
    	?>
		<div class="toggle_item" style="<?php echo $displaySecondAdress; ?>">
	        <div class="row">
	            <div class="form-group">
	                <label for="kontakt_firma2" class="rsib_col-md-2 col-sm-3 hidden-xs control-label ibui_label">
	                	<?php _e("Company", 'indiebooking'); ?>
	            	</label>
	                <div class="rsib_col-md-10 col-sm-9">
	                    <input type="text" class="form-control ibui_input" id="kontakt_firma2" name="kontakt_firma2"
	                    		placeholder="<?php esc_attr_e("Company", 'indiebooking'); ?>"
	                    		value="<?php echo esc_attr($firma2); ?>"
	                    		<?php echo $disabled; ?>>
	                    <?php echo "<p id='err_firma2' class='text-danger'></p>";?>
	                </div>
	                <br class="clear" />
	            </div>
		        <div class="form-group">
		            <label for="kontakt_abteilung2" class="rsib_col-md-2 col-sm-3 hidden-xs control-label ibui_label"><?php _e("Department", 'indiebooking'); ?></label>
		            <div class="rsib_col-md-10 col-sm-9">
		                <input type="text" class="form-control ibui_input" id="kontakt_abteilung2" name="kontakt_abteilung2"
		                		placeholder="<?php esc_attr_e("Department", 'indiebooking'); ?>"
		                		value="<?php echo esc_attr($department2); ?>" <?php echo esc_attr($disabled); ?>>
		            </div>
		        </div>
	            <div class="form-group">
	                <label for="kontakt_anrede2" class="rsib_col-md-2 col-sm-3 hidden-xs control-label ibui_label">
	                	<?php _e("Salutation", 'indiebooking'); ?>
	                	<?php //echo $kennzeichen; ?>
	            	</label>
	                <div class="rsib_col-md-10 col-sm-9">
	                    <?php if ($disabled == "") { ?>
	                    	<select id="kontakt_anrede2" name="kontakt_anrede2" class="form-control ibui_select" tabindex="1">
	                    		<!-- Auf keinen Fall den Wert 0 vergeben! -->
	        					<option value="1" <?php echo $select21; ?>><?php _e("Mr.", 'indiebooking'); ?>
	    						<option value="2" <?php echo $select22; ?>><?php _e("Mrs.", 'indiebooking'); ?>
	    						<option value="3" <?php echo $select23; ?>><?php echo ""; ?>
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
	                <br class="clear" />
	            </div>
	            <div class="form-group">
	                <label for="kontakt_title2" class="rsib_col-md-2 col-sm-3 hidden-xs control-label ibui_label">
	                	<?php _e("Title", 'indiebooking'); ?>
	            	</label>
	                <div class="rsib_col-md-10 col-sm-9">
	                    <input type="text" class="form-control ibui_input" id="kontakt_title2" name="kontakt_title2"
	                    		placeholder="<?php esc_attr_e("Title", 'indiebooking'); ?>"
	                    		value="<?php echo esc_attr($title2); ?>"
	                    		<?php echo $disabled; ?>>
	                    <?php echo "<p id='err_title2' class='text-danger'>$errTitle</p>";?>
	                </div>
	                <br class="clear" />
	            </div>
	            <div class="form-group">
	                <label for="kontakt_first_name2" class="rsib_col-md-2 col-sm-3 hidden-xs control-label ibui_label">
	                	<?php esc_attr_e("First Name", 'indiebooking'); ?>
	                	<?php //echo $kennzeichen; ?>
	            	</label>
	                <div class="rsib_col-md-10 col-sm-9">
	                    <input type="text" class="form-control ibui_input" id="kontakt_first_name2" name="kontakt_first_name2"
	                    		placeholder="<?php esc_attr_e("First Name", 'indiebooking'); ?>"
	                    		value="<?php echo esc_attr($firstName2); ?>"
	                    		<?php echo $disabled; ?>>
	                    <?php echo "<p id='err_text_first_name2' class='text-danger'>$errFirstName</p>";?>
	                </div>
	                <br class="clear" />
	            </div>
	            <div class="form-group">
	                <label for="kontakt_name2" class="rsib_col-md-2 col-sm-3 hidden-xs control-label ibui_label">
	                	<?php esc_attr_e("Last Name", 'indiebooking'); ?>
	                	<?php //echo $kennzeichen; ?>
	            	</label>
	                <div class="rsib_col-md-10 col-sm-9">
	                    <input type="text" class="form-control ibui_input" id="kontakt_name2" name="kontakt_name2"
	                    		placeholder="<?php esc_attr_e("Last Name", 'indiebooking'); ?>"
	                    		value="<?php echo esc_attr($lastName2); ?>"
	                    		<?php echo $disabled; ?>>
	                    <?php echo "<p id='err_text_name2' class='text-danger'>$errName</p>";?>
	                </div>
	                <br class="clear" />
	            </div>
				<!--
				 email2 wird versteckt, da dies im Rechnungsformular erst mal nicht mehr gebraucht wird.
				 -->
	            <div class="form-group" hidden="hidden">
	                <label for="kontakt_email2" class="rsib_col-md-2 col-sm-3 hidden-xs control-label ibui_label" >
	                	<?php esc_attr_e("E-Mail", 'indiebooking'); ?>
	                	<?php //echo $kennzeichen; ?>
	            	</label>
	                <div class="rsib_col-md-10 col-sm-9">
	                    <input type="email" class="form-control ibui_input" id="kontakt_email2" name="kontakt_email2"
	                    		placeholder="<?php esc_attr_e("example@domain.com", 'indiebooking'); ?>"
	                    		value="<?php echo esc_attr($email2); ?>"
	                    		<?php echo $disabled; ?>>
	                    <?php echo "<p id='err_email2' class='text-danger'>$errEmail</p>";?>
	                </div>
	                <br class="clear" />
	            </div>
	            <div class="row margin-no">
	                <div class="form-group">
	                    <label for="kontakt_strasse2" class="rsib_col-md-2 col-sm-3 hidden-xs control-label ibui_label">
	                    	<?php esc_attr_e("Street", 'indiebooking'); ?>
	                    	<?php echo $kennzeichen; ?>
	                	</label>
	                    <div class="rsib_col-md-7 col-sm-6">
	                        <input type="text" class="form-control ibui_input" id="route2" name="kontakt_strasse2"
	                        		placeholder="<?php esc_attr_e("Street", 'indiebooking'); ?>"
	                        		value="<?php echo esc_attr($street2); ?>"
	                        		<?php echo $disabled; ?>>
	                        <?php echo "<p id='err_street2' class='text-danger'>$errStreet</p>";?>
	                    </div>
	                    <div class="rsib_col-md-3 col-sm-3">
	                        <input type="text" class="form-control ibui_input" id="street_number2" name="kontakt_strasse2_nr"
	                        		placeholder="<?php esc_attr_e("Street Nr.", 'indiebooking'); ?>"
	                        		value="<?php echo esc_attr($strasseNr2); ?>"
	                        		<?php echo $disabled; ?>>
	                        <?php echo "<p id='err_street2' class='text-danger'></p>";?>
	                    </div>
	                    <br class="clear" />
	                </div>
	            </div>
	            <div class="form-group">
	                <label for="kontakt_plz2" class="rsib_col-md-2 col-sm-3 hidden-xs control-label ibui_label">
	                	<?php _e("Zip code", 'indiebooking'); ?><?php echo $kennzeichen; ?>
	            	</label>
	                <div class="rsib_col-md-10 col-sm-9">
	                    <input type="text" class="form-control ibui_input" id="postal_code2" name="kontakt_plz2"
	                    		placeholder="<?php esc_attr_e("Zip code", 'indiebooking'); ?>"
	                    		value="<?php echo esc_attr($zipCode2); ?>"
	                    		<?php echo $disabled; ?>>
	                    <?php echo "<p id='err_zip_code2' class='text-danger'>$errZip</p>";?>
	                </div>
	                <br class="clear" />
	            </div>
	            <div class="form-group">
	                <label for="kontakt_ort2" class="rsib_col-md-2 col-sm-3 hidden-xs control-label ibui_label">
	                	<?php esc_attr_e("Location", 'indiebooking'); ?>
	                	<?php echo $kennzeichen; ?>
	            	</label>
	                <div class="rsib_col-md-10 col-sm-9">
	                    <input type="text" class="form-control ibui_input" id="locality2" name="kontakt_ort2"
	                    		placeholder="<?php esc_attr_e("Location", 'indiebooking'); ?>"
	                    		value="<?php echo esc_attr($location2); ?>"
	                    		<?php echo $disabled; ?>>
	                    <?php echo "<p id='err_location2' class='text-danger'>$errLocation</p>";?>
	                </div>
	                <br class="clear" />
	            </div>
	            <div class="form-group">
	                <label for="kontakt_land2" class="rsib_col-md-2 col-sm-3 hidden-xs control-label ibui_label">
	                	<?php esc_attr_e("Country", 'indiebooking'); ?>
	                	<?php echo $kennzeichen; ?>
	            	</label>
	                <div class="rsib_col-md-10 col-sm-9">
	                    <input type="text" class="form-control ibui_input" id="country2" name="kontakt_land2"
	                    		placeholder="<?php esc_attr_e("Country", 'indiebooking'); ?>"
	                    		value="<?php echo esc_attr($country2); ?>"
	                    		<?php echo $disabled; ?>>
	                </div>
	                <br class="clear" />
	            </div>
				<!--
				 telefon2 wird versteckt, da dies im Rechnungsformular erst mal nicht mehr gebraucht wird.
				 -->
	            <div class="form-group" hidden="hidden">
	                <label for="kontakt_telefon2" class="rsib_col-md-2 col-sm-3 hidden-xs control-label ibui_label">
	                	<?php esc_attr_e("Telefon", 'indiebooking'); ?>
	                	<?php echo $kennzeichen; ?>
	            	</label>
	                <div class="rsib_col-md-10 col-sm-9">
	                    <input type="text" class="form-control ibui_input" id="kontakt_telefon2" name="kontakt_telefon2"
	                        	placeholder="<?php esc_attr_e("Telefon", 'indiebooking'); ?>"
	                        	value="<?php echo esc_attr($telefon2); ?>"
	                        	<?php echo $disabled; ?>>
	                    <?php echo "<p id='err_telefon2' class='text-danger'>$errTelefon</p>";?>
	                </div>
	                <br class="clear" />
	            </div>
	            <?php if ($disabled == "") { ?>
	                <div class="form-group">
	                	<div class="rsib_col-xs-12 rsib_col-md-10 rsib_col-md-offset-2 notice_pflichtfelder">
	                		<?php _e("Fields marked with * are required fields", 'indiebooking'); ?>
	                	</div>
	                	<br class="clear" />
	                </div>
	            <?php } ?>
	        </div>
	    </div>
	    <div class="form-group">
	        <div class="col-xs-10 col-xs-offset-2">
	            <?php echo $result; ?>
	        </div>
	    </div>
 	</div>
</div>