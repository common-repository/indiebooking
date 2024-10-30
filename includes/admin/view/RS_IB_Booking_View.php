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
/* @var $buchungObj RS_IB_Model_Appartment_Buchung */
/* @var $aktion RS_IB_Model_Appartmentaktion */
/* @var $position RS_IB_Model_Buchungposition */
/* @var $optionPositions RS_IB_Buchungsposition */
/* @var $aktionToCalc RS_IB_Model_Appartmentaktion */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
// if ( ! class_exists( 'RS_IB_Booking_View' ) ) :
class RS_IB_Booking_View {
    public static function showBookingHeadInformation($wp_buchungObj, RS_IB_Model_Buchungskopf $buchungsKopf) {
        $waehrung                   = rs_ib_currency_util::getCurrentCurrency();
        ?>
        <div class="ibui_postbox">
        <div class="rsib_container-fluid">
        <?php
        if ($buchungsKopf->getBuchung_status() == "rs_ib-bookingcom") {
        ?>
        	<p><?php _e("This booking was created from booking.com.
        			For more details please look at your booking.com Account.", "indiebooking");?></p>
			        			
        	<p><?php
        		$bookingcomResTxt = sprintf(__("The booking.com reservation Id is: %s", "indiebooking"), $buchungsKopf->getBcomReservationId());
        		echo $bookingcomResTxt;
        	?></p>
        <?php
        }
        ?>
        <div class="rsib_row">
        <?php
        self::showTimeRange($buchungsKopf, $wp_buchungObj);
        self::showContactData($buchungsKopf);
        self::showCustomMessage($buchungsKopf);
        ?>
        </div>
        <?php
    }
    
    public static function showBookingDocumentList($wp_buchungObj, RS_IB_Model_Buchungskopf $buchungsKopf) {
//     	$pluginPath         = cRS_Indiebooking::plugin_path();
    	$pluginFilePath     = cRS_Indiebooking::file_upload_path();
    	$pluginFileUrl		= cRS_Indiebooking::file_upload_url();
    	$filePath           = $pluginFilePath.DIRECTORY_SEPARATOR.'pdfs'.DIRECTORY_SEPARATOR.$wp_buchungObj->getPostId().'/';
    	$fileUrl			= $pluginFileUrl.DIRECTORY_SEPARATOR.'pdfs'.DIRECTORY_SEPARATOR.$wp_buchungObj->getPostId().'/';
    	if (is_dir($filePath)) {
    		// �ffnen des Verzeichnisses
    		if ( $handle = opendir($filePath) ) {
    			// einlesen der Verzeichnisses
    			echo "<ul>";
    			while (($file = readdir($handle)) !== false) {
    				if ($file !== "." && $file !== "..") {
//     					"%23"
						$filename		= $file;
    					$file			= urlencode($file);
    					$cleanFileUrl 	= $fileUrl.$file;
	    				echo "<li>";
	    				echo "<a href='".$cleanFileUrl."' target='_blank'>".$filename."</a>";
	    				echo "</li>";
    				}
    			}
    			echo "</ul>";
    			closedir($handle);
    		}
    	}
    }
    
    private static function include_edit_booking_contact_data_dialog(RS_IB_Model_Buchungskopf $buchungsKopf, $postId = 0) {
    	?>
		<div id="rs_ib_post_booking_contact_popup" data-taxonomy="rsappartment_buchung"
	        	data-dialogTitle="<?php _e("Edit booking contact data", 'indiebooking');?>"
	        	data-postId="<?php echo $postId; ?>"
	        	class="ibui_widget ui-widget rs_ib_taxonomy_popup">
	        	
	        <div id="rs_ib_admin_apartment_booking_contact_data_container">
	        	<!-- class="rs_ib_admin_apartment_contact_container" -->
	        	<?php
	        		include_once("popup/RS_IB_Apartment_Booking_Popup_Contact.php");
	        	?>
			</div>
		</div>
    	<?php
    }

    
    public static function showBooking($wp_buchungObj, RS_IB_Model_Buchungskopf $buchungsKopf) {
        $waehrung                   = rs_ib_currency_util::getCurrentCurrency();
        self::include_edit_booking_contact_data_dialog($buchungsKopf, $wp_buchungObj->getPostId());
        self::showDetailedPaymentInfo($buchungsKopf, $waehrung);
        //     showFullPriceTable($wp_buchungObj, $waehrung);
    }
    
    public static function showOberBooking($buchungKopf, $buchung, $oberbuchungObj) {
        $waehrung                   = rs_ib_currency_util::getCurrentCurrency();
        self::showDetailedPaymentInfo($buchungKopf, $waehrung, $oberbuchungObj);
    }
    
    public static function showCustomMessage(RS_IB_Model_Buchungskopf $buchungsKopf) {
    	if (strlen($buchungsKopf->getCustomText()) > 0) {
    	?>
    	<div class="rsib_col-lg-6 rsib_col-md-12 rsib_nopadding_right rsib_nopadding_md">
	    	<div class="ibui_tabitembox">
	    		<div class="ibui_h2wrap">
	    			<h2 class="ibui_h2"><?php _e('Custom message', 'indiebooking')?></h2>
	    		</div>
	    		<div class="rsib_form-group">
	        	<?php
					$text = $buchungsKopf->getCustomText();
		        	echo nl2br($text);
	        	?>
	    		</div>
			</div>
    	</div>
    	<?php
    	}
    }
    
    public static function showContactData(RS_IB_Model_Buchungskopf $buchungsKopf) {
        //     $contact = $buchung->getContact();
        ?>
        <div class="rsib_col-lg2-6 rsib_col-md2-12 rsib_nopadding_right rsib_nopadding_md rsib_nopadding_md2">
	        <div class="ibui_tabitembox">
	            <div class="ibui_h2wrap"><h2 class="ibui_h2"><?php _e('Contact Guest', 'indiebooking')?></h2></div>
	            <?php
	            if ($buchungsKopf->getBookingcomGenius() > 0) { ?>
		            <div class="rsib_form-group">
		                <label class="rsib_col-xs-12 ibui_label" style="text-align: center">
		                	<?php _e('Booker is genius', 'indiebooking');?>
		                </label>
		            </div>
	            <?php
	            }
	            if (trim($buchungsKopf->getKunde_firma()) != '') {
	            ?>
		            <div class="rsib_form-group">
		                <label class="rsib_col-xs-2 rsib_nopadding_left ibui_label"><?php _e('company', 'indiebooking');?>:</label>
		                <div class="rsib_col-xs-10 ibui_label_text">
		                	<?php echo esc_attr($buchungsKopf->getKunde_firma());
							if (strlen(trim($buchungsKopf->getKundeFirmaNr())) > 0 && $buchungsKopf->getKundeFirmaNr() != "0") {
		                	?>
			                	(
			                	<?php echo esc_attr($buchungsKopf->getKundeFirmaNrTyp());?>:
			                	<?php echo esc_attr($buchungsKopf->getKundeFirmaNr());?>
			                	)
		                	<?php } ?>
		                </div>
		            </div>
	            <?php
	            }
	            ?>
	            <?php
// 	            $kundeFirma = $buchungsKopf->getKunde_firma();
// 	            if (!empty($kundeFirma)) {
	            if (trim($buchungsKopf->getKunde_abteilung()) != '') {
	            ?>
    	            <div class="rsib_form-group">
    	                <label class="rsib_col-xs-2 rsib_nopadding_left ibui_label"><?php _e('Department', 'indiebooking');?>:</label>
    	                <div class="rsib_col-xs-10 ibui_label_text">
    	                	<?php echo esc_attr($buchungsKopf->getKunde_abteilung());?>
    	                </div>
    	            </div>
	            <?php
	            }
	            ?>
	            <div class="rsib_form-group">
	                <label class="rsib_col-xs-2 rsib_nopadding_left ibui_label"><?php _e('Name', 'indiebooking');?>:</label>
	                <div class="rsib_col-xs-10 ibui_label_text">
	                	<?php echo esc_attr($buchungsKopf->getKunde_anrede());?>
	                	<?php echo esc_attr($buchungsKopf->getKunde_title());?>
	                	<?php echo esc_attr($buchungsKopf->getKunde_vorname());?>
	                	<?php echo esc_attr($buchungsKopf->getKunde_name());//echo $contact['name'];?>
	                </div>
	            </div>
	            <div class="rsib_form-group">
	                <label class="rsib_col-xs-2 rsib_nopadding_left ibui_label"><?php _e('Street', 'indiebooking');?>:</label>
	                <div class="rsib_col-xs-10 ibui_label_text">
	                	<?php echo esc_attr($buchungsKopf->getKunde_strasse());//echo $contact['strasse'];?>
	                	<?php echo esc_attr($buchungsKopf->getKunde_strasse_nr());?>
	            	</div>
	            </div>
	            <div class="rsib_form-group">
	                <label class="rsib_col-xs-2 rsib_nopadding_left ibui_label"><?php _e('Location', 'indiebooking');?>:</label>
	                <div class="rsib_col-xs-10 ibui_label_text">
	                	<?php echo esc_attr($buchungsKopf->getKunde_plz());?>
	            		<?php echo esc_attr($buchungsKopf->getKunde_ort());//echo $contact['ort'];?>
	            	</div>
	            </div>
	            <div class="rsib_form-group">
	                <label class="rsib_col-xs-2 rsib_nopadding_left ibui_label"><?php _e('Country', 'indiebooking');?>:</label>
	                <div class="rsib_col-xs-10 ibui_label_text">
	                	<?php echo esc_attr($buchungsKopf->getKunde_land());?>
	            	</div>
	            </div>
	            <div class="rsib_form-group">
	                <label class="rsib_col-xs-2 rsib_nopadding_left ibui_label"><?php _e('E-Mail', 'indiebooking');?>:</label>
	                <div class="rsib_col-xs-10 ibui_label_text">
	                	<?php echo esc_attr($buchungsKopf->getKunde_email());//echo $contact['email'];?>
	            	</div>
	            </div>
	            <?php
	            if (!empty(trim($buchungsKopf->getKunde_telefon()))) {
	            ?>
	            <div class="rsib_form-group">
	                <label class="rsib_col-xs-2 rsib_nopadding_left ibui_label"><?php _e('Telefon', 'indiebooking');?>:</label>
	                <div class="rsib_col-xs-10 ibui_label_text">
	                	<?php echo esc_attr($buchungsKopf->getKunde_telefon());//echo $contact['telefon'];?>
	            	</div>
	            </div>
	            <?php
	            }
	            ?>
	        </div>
	        <?php
	        if ($buchungsKopf->getUseAdress2() == "1") {
	        ?>
			<div class="ibui_tabitembox">
	            <div class="ibui_h2wrap"><h2 class="ibui_h2"><?php _e('Alternative billing address', 'indiebooking')?></h2></div>
	            <?php
	            if ($buchungsKopf->getBookingcomGenius() > 0) { ?>
		            <div class="rsib_form-group">
		                <label class="rsib_col-xs-12 ibui_label" style="text-align: center">
		                	<?php _e('Booker is genius', 'indiebooking');?>
		                </label>
		            </div>
	            <?php
	            }
	            $kundeFirma2 = trim($buchungsKopf->getKunde_firma2());
	            if (!empty($kundeFirma2)) {
	            ?>
		            <div class="rsib_form-group">
		                <label class="rsib_col-xs-2 rsib_nopadding_left ibui_label"><?php _e('company', 'indiebooking');?>:</label>
		                <div class="rsib_col-xs-10 ibui_label_text">
		                	<?php echo esc_attr($buchungsKopf->getKunde_firma2());?>
		                </div>
		            </div>
	            <?php
	            }
	            ?>
	            <?php
	            if (trim($buchungsKopf->getKunde_abteilung2()) != '') {
	            ?>
    	            <div class="rsib_form-group">
    	                <label class="rsib_col-xs-2 rsib_nopadding_left ibui_label"><?php _e('Department', 'indiebooking');?>:</label>
    	                <div class="rsib_col-xs-10 ibui_label_text">
    	                	<?php echo esc_attr($buchungsKopf->getKunde_abteilung2());?>
    	                </div>
    	            </div>
	            <?php
	            }
	            ?>
				<?php
				if (!empty($buchungsKopf->getKunde_anrede2()) || !empty($buchungsKopf->getKunde_title2()) ||
				    !empty($buchungsKopf->getKunde_vorname2()) || !empty($buchungsKopf->getKunde_name2())) { ?>
	            <div class="rsib_form-group">
	                <label class="rsib_col-xs-2 rsib_nopadding_left ibui_label"><?php _e('Name', 'indiebooking');?>:</label>
	                <div class="rsib_col-xs-10 ibui_label_text">
	                	<?php echo esc_attr($buchungsKopf->getKunde_anrede2());?>
	                	<?php echo esc_attr($buchungsKopf->getKunde_title2());?>
	                	<?php echo esc_attr($buchungsKopf->getKunde_vorname2());?>
	                	<?php echo esc_attr($buchungsKopf->getKunde_name2());//echo $contact['name'];?>
	                </div>
	            </div>
	            <?php
				}
	            ?>
	            <div class="rsib_form-group">
	                <label class="rsib_col-xs-2 rsib_nopadding_left ibui_label"><?php _e('Street', 'indiebooking');?>:</label>
	                <div class="rsib_col-xs-10 ibui_label_text">
	                	<?php echo esc_attr($buchungsKopf->getKunde_strasse2());//echo $contact['strasse'];?>
	                	<?php echo esc_attr($buchungsKopf->getKunde_strasse_nr2());?>
	            	</div>
	            </div>
	            <div class="rsib_form-group">
	                <label class="rsib_col-xs-2 rsib_nopadding_left ibui_label"><?php _e('Location', 'indiebooking');?>:</label>
	                <div class="rsib_col-xs-10 ibui_label_text">
	                	<?php echo esc_attr($buchungsKopf->getKunde_plz2());?>
	            		<?php echo esc_attr($buchungsKopf->getKunde_ort2());//echo $contact['ort'];?>
	            	</div>
	            </div>
	            <div class="rsib_form-group">
	                <label class="rsib_col-xs-2 rsib_nopadding_left ibui_label"><?php _e('Country', 'indiebooking');?>:</label>
	                <div class="rsib_col-xs-10 ibui_label_text">
	                	<?php echo esc_attr($buchungsKopf->getKunde_land2());?>
	            	</div>
	            </div>
	            <?php
	            if (!empty(trim($buchungsKopf->getKunde_email2()))) {
	            ?>
	            <div class="rsib_form-group">
	                <label class="rsib_col-xs-2 rsib_nopadding_left ibui_label"><?php _e('E-Mail', 'indiebooking');?>:</label>
	                <div class="rsib_col-xs-10 ibui_label_text">
	                	<?php echo esc_attr($buchungsKopf->getKunde_email2());//echo $contact['email'];?>
	            	</div>
	            </div>
	            <?php
	            }
	            ?>
	            <?php
	            if (!empty(trim($buchungsKopf->getKunde_telefon2()))) {
	            ?>
	            <div class="rsib_form-group">
	                <label class="rsib_col-xs-2 rsib_nopadding_left ibui_label"><?php _e('Telefon', 'indiebooking');?>:</label>
	                <div class="rsib_col-xs-10 ibui_label_text">
	                	<?php echo esc_attr($buchungsKopf->getKunde_telefon2());//echo $contact['telefon'];?>
	            	</div>
	            </div>
	            <?php
	            }
	            ?>
	        </div>
	        <?php } ?>
        </div>
    	<?php wp_nonce_field('save_booking', 'save_booking_nonce_field'); ?>
    	<?php
    }
    
    public static function showTimeRange(RS_IB_Model_Buchungskopf $buchungsKopf, $wp_buchungObj) {
        $startTime  = $wp_buchungObj->getStart_time();
//         $startTime 	= $buchungsKopf->getBuchungsdatum();
        $buchungVon = $buchungsKopf->getBuchung_von();
        $buchungBis = $buchungsKopf->getBuchung_bis();
        if ($buchungVon instanceOf DateTime) {
            $buchungVon = $buchungVon->format('d.m.Y');
        }
        if ($buchungBis instanceOf DateTime) {
            $buchungBis = $buchungBis->format('d.m.Y');
        }
        ?>
    	<div class="rsib_col-lg2-6 rsib_col-md2-12 rsib_nopadding_left rsib_nopadding_md rsib_nopadding_md2">
        <div class="ibui_tabitembox">
            <div class="ibui_h2wrap"><h2 class="ibui_h2"><?php _e('Booking info', 'indiebooking')?></h2></div>
            <div id="booked_timerange_box">
                <div class="rsib_form-group">
                    <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e('Booking is on the', 'indiebooking')?>:</label>
                    <div class="rsib_col-xs-8 ibui_label_text"><?php echo date('d.m.Y - H:i:s', $startTime);?></div>
                </div>
                <div class="rsib_form-group">
                    <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e('Booking number', 'indiebooking')?>:</label>
                    <div class="rsib_col-xs-8 ibui_label_text"><?php echo esc_attr($buchungsKopf->getBuchung_nr());?></div>
                </div>
                <div class="rsib_form-group">
                    <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e('booked from', 'indiebooking')?>:</label>
                    <div class="rsib_col-xs-8 ibui_label_text"><?php echo esc_attr($buchungVon);?></div>
                </div>
                <div class="rsib_form-group">
                    <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e('booked to', 'indiebooking')?>:</label>
                    <div class="rsib_col-xs-8 ibui_label_text"><?php echo esc_attr($buchungBis);//echo $buchungObj->getEndDate();?></div>
                </div>
                <div class="rsib_form-group">
                    <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e('number of nights', 'indiebooking')?>:</label>
                    <div class="rsib_col-xs-8 ibui_label_text"><?php echo esc_attr($buchungsKopf->getAnzahl_naechte());?></div>
                </div>
                <div class="rsib_form-group">
                    <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e('payment method', 'indiebooking')?>:</label>
                    <div class="rsib_col-xs-8 ibui_label_text"><?php echo esc_attr($buchungsKopf->getHauptZahlungsart());?></div>
                </div>
            </div>
        </div>
        </div>
    <?php }
    
    /* @var $teilKopf RS_IB_Model_Teilbuchungskopf */
    /* @var $buchungKopf RS_IB_Model_Buchungskopf */
    public static function showDetailedPaymentInfo($buchungKopf, $waehrung, $oberbuchung = null) { ?>
        <div class="rsib_row">
        <div class="rsib_col-xs-12 rsib_nopadding_left rsib_nopadding_right">
        <div class="ibui_tabitembox">
            <div class="ibui_h2wrap">
            	<h2 class="ibui_h2">
            		<?php _e('Billing', 'indiebooking')?> (<?php _e('Invoice Number', 'indiebooking')?>:
        			<?php //echo esc_attr($buchungKopf->getBuchung_nr()); ?>
        			<?php echo esc_attr($buchungKopf->getRechnung_nr()); ?>
        			<?php echo ')'; ?>
    			</h2>
    		</div>
            <table id="price_display_table" class="rsib_table ibui_table">
                <thead>
                    <tr>
                        <th style="width: 120px; max-width: 120px;"><?php _e('position', 'indiebooking');?></th>
                        <th class="myTableRow ibui_text_center"><?php _e('from', 'indiebooking');?></th>
                        <th class="myTableRow ibui_text_center"><?php _e('to', 'indiebooking');?></th>
                        <th class="myTableRow ibui_text_right"><?php _e('Price', 'indiebooking');?></th>
                        <th class="myTableRow ibui_text_center"><?php _e('Number', 'indiebooking');?></th>
                        <!-- <th class="myTableRow"><?php //_e('Net', 'indiebooking');?></th>
                        <th class="myTableRow"><?php //_e('Taxes', 'indiebooking');?></th>  -->
                        <th class="myTableRow ibui_text_right"><?php _e('Gross', 'indiebooking');?></th>
                        <th class="myTableRow ibui_text_right"><?php _e('MwSt', 'indiebooking');?></th>
                        <th class="myTableRow ibui_text_right"><?php _e('CalcPosPrice', 'indiebooking');?></th>
                        <th class="myTableRow ibui_text_right"><?php _e('MwSt-Value', 'indiebooking');?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                   /* @var $buchungKopf RS_IB_Model_Buchungskopf */
                    if (!isset($waehrung)) {
                        $waehrung   = rs_ib_currency_util::getCurrentCurrency();
                    }
                    $stornoZahlungen = array();
                    $tdClass        = "";
                    $tdNumberStyle  = "style='text-align: right;'";
                    $tdvorzeichen   = "";
                    $buchungen      = array();
                    if (!is_null($oberbuchung)) {
                        $buchungen  = $oberbuchung->getBuchungen();
                    } else {
                    	array_push($buchungen, $buchungKopf);
                    }
                    $counter = 0;
                    foreach ($buchungen as $buchungKopf) {
                    if ($counter > 0) {
                        $ueberschrift = "&nbsp;";
                        if ($buchungKopf->getBuchung_status() == "rs_ib-storno" || $buchungKopf->getBuchung_status() == "rs_ib-storno_paid") {
                            $ueberschrift = "Stornogebuehren";
                        }
                        ?>
                        <tr>
                            <td colspan="9">&nbsp;</td>
                        </tr>
                        <tr style="border-top: 1px solid;">
                            <th colspan="9"><?php echo $ueberschrift; ?></th>
                        </tr>
                    <?php
                    }
                    $counter++;
                    foreach ($buchungKopf->getTeilkoepfe() as $teilKopf) { ?>
                        <tr>
                            <th colspan="9" style="background:#f8f8f8 !important;">
                            	<?php _e('booking part number', 'indiebooking')?>: <?php echo esc_attr($teilKopf->getTeilbuchung_id()); ?>
                            	<?php if (trim($teilKopf->getGastName()) != "") {
                            		echo "(".$teilKopf->getGastName().")";
                            	} ?>
                        	</th>
                        </tr>
                        <?php
                        foreach ($teilKopf->getPositionen() as $position) {
                            $preisVon   = $position->getPreis_von()->format('d.m.Y');
                            $preisBis   = $position->getPreis_bis()->format('d.m.Y');
                            //echo $position->getPosition_typ();
                            $position->calculateExpelPrice();
                            
                            $posFullPrice    	= $position->getAusschreibFullPrice();
                            $posEinzelPreis  	= $position->getAusschreibEinzelPrice();
                            $rabattEinzelpreise = $position->getRabatteEinzelPrice();
                            
                            $specialDegClass 	= "";
                            $originalPrice		= "";
                            $specialSign		= "";
                            if ($position->getHasDegression() == true || sizeof($rabattEinzelpreise) > 0) {
                            	//         				$specialCssClass = "ibui_linethrought_price";
                            	$specialDegClass = "ibui_degression_price";
                            	$specialSign	= "*";
                            	$originalPrice	= $posEinzelPreis;
                            	$originalPrice	= number_format($originalPrice, 2, ',', '.')." ".$waehrung;
                            	if ($position->getHasDegression() == true && sizeof($rabattEinzelpreise) <= 0) {
                            		$posEinzelPreis = $position->getDegressionEinzelPrice();
                            	} else if (sizeof($rabattEinzelpreise) > 0) {
                            		$lastRabatt = $rabattEinzelpreise[sizeof($rabattEinzelpreise)-1];
                            		$posEinzelPreis = $lastRabatt['price'];
                            	}
                            }
                            ?>
                            <tr>
                                <td>
                                <?php
                                	echo htmlspecialchars_decode($position->getBezeichnung());
                                ?>
                                </td>
                                <td class="<?php echo $tdClass; ?> ibui_text_center"><?php echo esc_attr($preisVon);?></td>
                                <td class="<?php echo $tdClass; ?> ibui_text_center"><?php echo esc_attr($preisBis);?></td>
                                <td class="<?php echo $tdClass; ?> <?php echo $specialDegClass;?> ibui_text_right">
                                	<?php echo esc_attr($tdvorzeichen)." "; ?>
                                	<?php //echo number_format($position->getEinzelpreis(), 2, ',', '.').$waehrung;?>
                                	<?php echo number_format($posEinzelPreis, 2, ',', '.').$waehrung.$specialSign;?>
                            	</td>
                                <td class="<?php echo $tdClass; ?> appartment_price_table_definition ibui_text_center">
                                	<?php //echo $position->getAnzahl_naechte();?>
                                	<?php echo $position->getBerechnungsAnzahlEinheit(); ?>
                            	</td>
                                <td class="<?php echo $tdClass; ?> appartment_price_table_definition ibui_text_right">
                                	<?php //echo number_format($position->getFullPrice(), 2, ',', '.').' '.$waehrung;?>
                                	<?php echo number_format($posFullPrice, 2, ',', '.').' '.$waehrung;?>
                            	</td>
                                <td class="<?php echo $tdClass; ?> appartment_price_table_definition ibui_text_right">
                                	<?php echo number_format($position->getMwst_prozent(), 2, ',', '.')."%";?>
                            	</td>
                                <td class="<?php echo $tdClass; ?> appartment_price_table_definition ibui_text_right">
                                	<?php echo number_format($position->getCalcPosPrice(), 2, ',', '.');?>
                            	</td>
                                <td class="<?php echo $tdClass; ?> appartment_price_table_definition ibui_text_right">
                                	<?php echo number_format($position->getMwst_wert(), 2, ',', '.');?>
                            	</td>
                            </tr>
                            <?php
                            /* @var $rabatt RS_IB_Model_BuchungRabatt */
                            if (is_array($position->getRabatte()) && sizeof($position->getRabatte()) > 0) {
                            	$rabattkey 		= 0;
                            	$einzelRabatte	= $position->getRabatteEinzelPrice();
                               	foreach ($position->getRabatte() as $rabatt) {
                                   $rabattVz   = "-";
                                   $rabattArt  = "";
                                   if ($rabatt->getRabatt_typ() == 1) {
                                       $wertTyp = rs_ib_currency_util::getCurrentCurrency();
                                   } elseif ($rabatt->getRabatt_typ() == 2) {
                                       $wertTyp = "%";
                                   }
                                   if ($rabatt->getRabatt_art() == RS_IB_Model_BuchungRabatt::RABATT_ART_AKTION) {
                                       $rabattArt = __('Campagne', 'indiebooking');
                                   }
                                   if ($rabatt->getRabatt_art() == RS_IB_Model_BuchungRabatt::RABATT_ART_COUPON) {
                                       $rabattArt = __('Coupon', 'indiebooking');
                                   }
                                   if ($rabatt->getRabatt_art() != RS_IB_Model_BuchungRabatt::RABATT_ART_DEGRESSION
                                   	&& $rabatt->getRabatt_art() != RS_IB_Model_BuchungRabatt::RABATT_ART_AUFSCHLAG) {
                                   		if ($rabatt->getBerechnung_art() < RS_IB_Model_BuchungRabatt::RABATT_BERECHNUNG_POSITION_PREIS) {
		                                   ?>
		                                    <tr>
		                                        <td><?php echo $rabattArt; ?></td>
		                                        <td colspan="4"><?php echo $rabatt->getBezeichnung(); ?></td>
		                                        <td class="<?php echo $tdClass; ?>" <?php echo $tdNumberStyle; ?>>
		                                        	<?php echo esc_attr($rabattVz)." "; ?>
		                                        	<?php echo number_format($rabatt->getRabatt_wert(), 2, ',', '.').$wertTyp;?>
		                                    	</td>
		                                   </tr>
		                                <?php
	                                   	} else {
	                                   		$basisPrice			= number_format($einzelRabatte[$rabattkey]['basis'], 2, ',', '.')." ".$waehrung;
	                                   		$description = "";
	                                   		if ($rabatt->getRabatt_art() == RS_IB_Model_BuchungRabatt::RABATT_ART_COUPON) {
	                                   			$description = __("Coupon", 'indiebooking');
	                                   			$description = $description . " (".$rabatt->getBezeichnung().")";
	                                   		} else {
	                                   			$description = $rabatt->getBezeichnung();
	                                   		}
	                                   		$rabattkey++;
	                                   		$priceDesc 		= $rabattVz . " " . number_format($rabatt->getRabatt_wert(), 2, ',', '.')." ".$wertTyp;
	                                   		?>
				                        <tr>
				                        	<td colspan="3">&nbsp;</td>
				    						<td colspan="6" style="font-style: italic;">
				    							*<?php printf(__("%s %s considered (before %s)", "indiebooking"), $description, $priceDesc, $basisPrice); ?>
				    						</td>
				                       	</tr>
								       	<?php
	                                   	}
                                   	} else {
	                                   	if ($rabatt->getRabatt_art() == RS_IB_Model_BuchungRabatt::RABATT_ART_AUFSCHLAG) {
	                                   		?>
					                        <tr>
					                        	<td colspan="3">&nbsp;</td>
					    						<td colspan="6" style="font-style: italic;">
					    							*<?php printf(__("Extra Charge from %s %s considered (before %s)", "indiebooking"),number_format($rabatt->getRabatt_wert(), 2, ',', '.'),  $wertTyp, $position->getEinzelpreis()); ?>
					    						</td>
					                       	</tr>
							       			<?php
	                                   	} else {
	                                   	?>
				                        <tr>
				                        	<td colspan="3">&nbsp;</td>
				    						<td colspan="6" style="font-style: italic;">
				    							*<?php printf(__("Discount according to price season from %s %s considered (before %s)", "indiebooking"),number_format($rabatt->getRabatt_wert(), 2, ',', '.'),  $wertTyp, $originalPrice); ?>
				    						</td>
				                       	</tr>
						       			<?php
	                                   	}
					       			}
                               	}
                            }
                        }
                        $specialTeilsummeClass = "teilsummenzeile text_bold";
                        if (is_array($teilKopf->getRabatte()) && sizeof($teilKopf->getRabatte()) > 0) {
                        	$specialTeilsummeClass = "rabattierteteilsummenzeile";
                        	$currentTeilsumme = $teilKopf->getOriCalcPrice();
                        } else {
                        	$currentTeilsumme = $teilKopf->getCalculatedPrice();
                        }
                        ?>
                        <tr style="border-top: 1px solid;">
                            <th colspan="3" style="background:none !important;">&nbsp;</th>
                            <th colspan="2" class="ibui_text_right" style="background:none !important;">
                            	<?php _e('Subtotal', 'indiebooking')?>
                        	</th>
                            <th class="<?php echo $tdClass; ?> appartment_price_table_definition"
                            	style="background:none !important;" <?php echo $tdNumberStyle; ?>>
                            	<?php echo number_format($currentTeilsumme, 2, ',', '.');?>
                        	</th>
                            <th colspan="3" style="background:none !important;">&nbsp;</th>
                        </tr>
                    <?php
                    if (is_array($teilKopf->getRabatte()) && sizeof($teilKopf->getRabatte()) > 0) {
                    	foreach ($teilKopf->getRabatte() as $rabatt) {
                    		$rabattVz = "-";
                    		$rabattArt  = "";
                    		if ($rabatt->getRabatt_typ() == 1) {
                    			$wertTyp = rs_ib_currency_util::getCurrentCurrency();
                    		} elseif ($rabatt->getRabatt_typ() == 2) {
                    			$wertTyp = "%";
                    		}
                    		if ($rabatt->getRabatt_art() == RS_IB_Model_BuchungRabatt::RABATT_ART_AKTION) {
                    			$rabattArt = __('Campagne', 'indiebooking');
                    		}
                    		if ($rabatt->getRabatt_art() == RS_IB_Model_BuchungRabatt::RABATT_ART_COUPON) {
                    			$rabattArt = __('Coupon', 'indiebooking');
                    		}
                    		?>
                                <tr>
                                    <td><?php echo esc_attr($rabattArt); ?></td>
                                    <td colspan="4"><?php echo esc_attr($rabatt->getBezeichnung()); ?></td>
                                    <td class="<?php echo $tdClass; ?>" <?php echo $tdNumberStyle; ?>>
                                    	<?php echo $rabattVz." "; ?><?php echo number_format($rabatt->getRabatt_wert(), 2, ',', '.').$wertTyp;?>
                                	</td>
                                    <td colspan="3">&nbsp;</td>
                               </tr>
                            <?php
                        }
                        ?>
                        <tr style="border-top: 1px solid;">
                        <th colspan="3" style="background:none !important;">&nbsp;</th>
                        <th colspan="2" class="ibui_text_right" style="background:none !important;">
                        <?php _e('Subtotal', 'indiebooking')?>
                        	</th>
                            <th class="<?php echo $tdClass; ?> appartment_price_table_definition"
                            	style="background:none !important;" <?php echo $tdNumberStyle; ?>>
                            	<?php echo number_format($teilKopf->getCalculatedPrice(), 2, ',', '.');?>
                        	</th>
                            <th colspan="3" style="background:none !important;">&nbsp;</th>
                        </tr>
                         <?php
                           
                           
                        }
                    }
                    ?>
                    <tr style="border-top: 1px solid;">
                        <th colspan="3" style="background:none !important;">&nbsp;</th>
                        <th colspan="2" class="ibui_text_right" style="background:none !important;">
                        	<?php _e('Grandtotal', 'indiebooking')?>
                    	</th>
                        <th class="<?php echo $tdClass; ?> appartment_price_table_definition"
                        	style="background:none !important;" <?php echo $tdNumberStyle; ?>>
                        	<?php echo number_format($buchungKopf->getFullPrice(), 2, ',', '.');?>
                    	</th>
                        <th colspan="3" style="background:none !important;">&nbsp;</th>
                   </tr>
                    <?php
                    if (is_array($buchungKopf->getRabatte()) && sizeof($buchungKopf->getRabatte()) > 0) {
                        foreach ($buchungKopf->getRabatte() as $rabatt) {
                            if ($rabatt->getRabatt_wert() > 0) {
                                $rabattVz = "-";
                            } else {
                                $rabattVz = "+";
                            }
                            if ($rabatt->getRabatt_typ() == 1) {
                                $wertTyp = rs_ib_currency_util::getCurrentCurrency();
                            } elseif ($rabatt->getRabatt_typ() == 2) {
                                $wertTyp = "%";
                            }
                            if ($rabatt->getRabatt_art() == RS_IB_Model_BuchungRabatt::RABATT_ART_AKTION) {
                                $rabattArt = __('Campagne', 'indiebooking');
                            }
                            if ($rabatt->getRabatt_art() == RS_IB_Model_BuchungRabatt::RABATT_ART_COUPON) {
                                $rabattArt = __('Coupon', 'indiebooking');
                            }
                            ?>
                            <tr>
                                <td style="background:none !important;"><?php echo $rabattArt; ?></td>
                                <td colspan="4" style="background:none !important;"><?php echo $rabatt->getBezeichnung(); ?></td>
                                <td class="<?php echo $tdClass; ?> ibui_text_right" style="background:none !important;" <?php echo $tdNumberStyle; ?>>
                                	<?php echo esc_attr($rabattVz)." "; ?>
                                	<?php echo number_format(abs($rabatt->getRabatt_wert()), 2, ',', '.').$wertTyp;?>
                            	</td>
                                <th colspan="3" style="background:none !important;">&nbsp;</th>
                           </tr>
                        <?php
                       }
                    }
                    ?>
                    <tr style="border-top: 1px solid;">
                        <th colspan="3" style="background:none !important;">&nbsp;</th>
                        <th class="ibui_text_right" style="background:none !important;" colspan="2">
                        	<?php _e("invoice amount", 'indiebooking'); ?>
                    	</th>
                        <th class="<?php echo $tdClass; ?> ibui_text_right"
                        	style="background:none !important;" <?php echo $tdNumberStyle; ?>>
                        	<?php echo number_format($buchungKopf->getCalculatedPrice(), 2, ',', '.');?>
                    	</th>
                        <th colspan="3" style="background:none !important;">&nbsp;</th>
                    </tr>
                    <?php
                        foreach ($buchungKopf->getFullMwstArray() as $mwstObj) {
                           ?>
                        <tr>
                            <th colspan="3" style="background:none !important;">&nbsp;</th>
                            <th class="ibui_text_right" style="background:none !important;" colspan="2">
                            	<?php //echo esc_attr($mwstObj->getMwst_prozent());?><!-- %-->
								<?php
								printf(__("incl. %s %s VAT", "indiebooking"), $mwstObj->getMwst_prozent(), '%');
								?>
                        	</th>
                            <th class="<?php echo $tdClass; ?> ibui_text_right"
                            	style="background:none !important;" <?php echo $tdNumberStyle; ?>>
                            	<?php echo number_format($mwstObj->getMwst_wert(), 2, ',', '.');?>
                        	</th>
                            <th colspan="3" style="background:none !important;">&nbsp;</th>
                        </tr>
                       <?php
                    }
                    foreach ($buchungKopf->getZahlungen() as $zahlung) {
                    	//ALWAYS TRUE!
//                     	if ($buchungKopf->getBuchung_status() != 'rs_ib-storno' || $buchungKopf->getBuchung_status() != 'rs_ib-storno_paid') {
                    	if ($buchungKopf->getBuchung_status() != 'rs_ib-storno' && $buchungKopf->getBuchung_status() != 'rs_ib-storno_paid') {
                        ?>
                        <tr>
                            <th colspan="3" style="background:none !important;">&nbsp;</th>
                            <th class="ibui_text_right" style="background:none !important;" colspan="2"><?php echo $zahlung->getBezeichnung();?></th>
                            <th class="<?php echo $tdClass; ?> ibui_text_right" style="background:none !important;" <?php echo $tdNumberStyle; ?>><?php echo number_format($zahlung->getZahlungbetrag(), 2, ',', '.');?></th>
                            <th colspan="3" style="background:none !important;">&nbsp;</th>
                        </tr>
                       <?php
                        } else {
                            array_push($stornoZahlungen, $zahlung);
                        }
                    }
                    if (is_null($oberbuchung)) {
                    ?>
                    <tr style="border-top: 1px solid;">
                        <th colspan="3" style="background:none !important;">&nbsp;</th>
                        <th class="ibui_text_right" style="background:none !important;" colspan="2">
                        	<?php _e("missing amount", 'indiebooking'); ?>
                    	</th>
                        <th class="<?php echo $tdClass; ?> ibui_text_right"
                        	style="background:none !important;" <?php echo $tdNumberStyle; ?>>
                        	<?php echo number_format($buchungKopf->getZahlungsbetrag(), 2, ',', '.');?>
                    	</th>
                        <th colspan="3" style="background:none !important;">&nbsp;</th>
                    </tr>
                    <?php } ?>
                <?php }
                if (!is_null($oberbuchung)) { ?>
                    <tr style="border-top: 5px double;"><td>&nbsp;</td></tr>
                    <?php
                    foreach ($stornoZahlungen as $stornoZahlung) {
                    	?>
                        <tr>
                            <th colspan="3" style="background:none !important;">&nbsp;</th>
                            <th class="ibui_text_right" style="background:none !important;" colspan="2">
                            	<?php echo $stornoZahlung->getBezeichnung();?>
                        	</th>
                            <th class="<?php echo $tdClass; ?> ibui_text_right"
                            	style="background:none !important;" <?php echo $tdNumberStyle; ?>>
                        		<?php
                        		echo number_format($stornoZahlung->getZahlungbetrag(), 2, ',', '.');
                        		?>
                    		</th>
                            <th colspan="3" style="background:none !important;">&nbsp;</th>
                        </tr>
                   <?php
                    }
                    if ($buchungKopf->getBuchung_status() != 'rs_ib-storno' && $buchungKopf->getBuchung_status() != 'rs_ib-storno_paid') {
                    	$endbetrag = $oberbuchung->getEndbetrag();
                    } else {
                    	$endbetrag = $buchungKopf->getFullPrice();
                    	foreach ($stornoZahlungen as $stornoZahlung) {
                    		$endbetrag = $endbetrag - $stornoZahlung->getZahlungbetrag();
                    	}
                    }
                    ?>
                    <tr>
                        <th colspan="3" style="background:none !important;">&nbsp;</th>
                        <th class="ibui_text_right" style="background:none !important;" colspan="2">ENDBETRAG</th>
                        <th class="<?php echo $tdClass; ?> ibui_text_right"
                        	style="background:none !important;" <?php echo $tdNumberStyle; ?>>
                        	<?php echo number_format($endbetrag, 2, ',', '.');?>
                    	</th>
                        <th colspan="3" style="background:none !important;">&nbsp;</th>
                    </tr>
                <?php }

                ?>
                </tbody>
            </table>
        </div></div><!--Ende row und col -->
        </div><!-- Ende ibui_tabitembox-->
    <?php
    }
}
// endif;