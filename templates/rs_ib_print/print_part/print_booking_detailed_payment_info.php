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
/* @var $buchungObj RS_IB_Model_Appartment_Buchung */
/* @var $aktion RS_IB_Model_Appartmentaktion */
/* @var $position RS_IB_Model_Buchungposition */
/* @var $optionPositions RS_IB_Buchungsposition */
/* @var $aktionToCalc RS_IB_Model_Appartmentaktion */
/* @var $buchungKopf RS_IB_Model_Buchungskopf */
$waehrung = rs_ib_currency_util::getCurrentCurrency();
?>
<style>
.beschreibungzeile {
	/*
	width: 5.6cm;
	min-width: 5.6cm;
	max-width: 5.6cm;
	*/
}

.zeitraumzeile {
	width: 3.8cm;
	min-width: 3.8cm;
	max-width: 3.8cm;
	text-align: center;
}

.nachtzeile {
/*
	width: 1.3cm;
	min-width: 1.3cm;
	max-width: 1.3cm;
	*/
	text-align: center;
}

/* .personenzeile { */
/* 	width: 1.6cm; */
/* 	max-width: 1.6cm; */
/* 	text-align: center; */
/* } */

.mwstzeile {
	width: 1.8cm;
	min-width: 1.8cm;
	max-width: 1.8cm;
}

.einzelpreiszeile {
	width: 2.5cm;
	min-width: 2.5cm;
	max-width: 2.5cm;
}

.gesamtpreiszeile {
	width: 3.0cm;
	min-width: 3.0cm;
	max-width: 3.0cm;
}

.rabattbezeichnung {
	padding-left: 0.5cm;
}

.ueberschrift th {
	border-bottom: 1px solid black;
	padding-top: 5px;
}

.summenbeschreibung {
	text-align: left;
}

.summebetrag {
	border-top: 1px solid black;
}

.rechnungsbetrag {
	border-top: 1px solid black;
}

.zahlungsbetrag {
	border-top: 1px solid black;
}

.bereitsgezahlt {
	border-top: 1px solid black;
}

.mwstzeile {
	text-align: left;
	padding-left: 0.3cm;
}

.buchungsdatum {
	text-align: right;
}

.ihrebuchung {
	padding-bottom: 0.3cm;
}

tr.ueberschrift th {
	text-align: center;
}

.ibui_linethrought_price {
	text-decoration: line-through;
}

.ibui_degression_price {
	color: red;
}

table {
	font-size: 11px;
}

.defaultprinttext {
	font-size: 11px;
}

</style>
<?php
    $stornoZahlungen    = array();
    $tdNumberStyle      = "style='text-align: right;'";
    $tdvorzeichen       = "";
    $document_header    = "";
    $stornoKennzeichen  = false;
    $cancellationFees	= false;
    $buchungen          = array();
    if (!isset($waehrung)) {
        $waehrung       = rs_ib_currency_util::getCurrentCurrency();
    }
    $counter            = 0;
    $tdClass            = "";
    $tdvorzeichen       = "";
    if (!is_null($oberbuchung)) {
        $buchungen      = $oberbuchung->getBuchungen();
    } else {
        array_push($buchungen, $buchungKopf);
    }
    $stornoueberschrift 	= "";
    $counter            	= 0;
    $summeNetto				= array();
    $documentDate			= null;
    $bookingIsInquiry		= false;
    $bookingByCategorieKz	= get_option('rs_indiebooking_settings_booking_by_categorie_kz');
    $bookingByCategorieKz	= ($bookingByCategorieKz == "on");
    foreach ($buchungen as $buchungKopf) {
    	if (is_null($documentDate)) {
//     		$documentDate = $buchungKopf->getBuchungsdatum()->format('d.m.Y');
    		$documentDate = $buchungKopf->getRechnungsdatum()->format('d.m.Y');
    		if ($documentDate == '01.01.1970') {
    			/*
    			 * Update Carsten Schmitt 25.06.2018
    			 * Das kann passieren, wenn eine aeltere Rechnung nachgedruckt wird.
    			 * Dann soll hier das Buchungsdatum greifen.
    			 */
    			$documentDate = $buchungKopf->getBuchungsdatum()->format('d.m.Y');
    		}
    	}
        if ($buchungKopf->getBuchung_status() == "rs_ib-booked") {
        	if (isset($dataUeberschrift) && $dataUeberschrift !== "") {
        		$document_header 	= $dataUeberschrift;
        	}
            if ($document_header == "") {
//                 $document_header = _e("Invoice / Booking confirmation", 'indiebooking');
            	$document_header = __("Invoice", 'indiebooking');
            }
        } elseif ($buchungKopf->getBuchung_status() == "rs_ib-pay_confirmed") {
            if ($document_header == "") {
            	if ($buchungKopf->getAdminKz() == 0 || $firstBillPrint) {
            		/*
            		 * Wenn kein AdminKz vorhanden ist, wurde die Buchung via Stripe o.ae bezahlt
            		 * Daher wurde dem Kunden noch nie eine Rechnung ausgestellt und demnach soll
            		 * er einen Ausdruck mit der Rechnungsueberschrift anstatt der
            		 * Zahlungsbestaetigungsueberschrift erhalten.
            		 */
            		$document_header 	= __("Invoice", 'indiebooking');
            	} else {
            		if (isset($dataUeberschrift) && $dataUeberschrift !== "") {
            			$document_header 	= $dataUeberschrift;
            		} else {
            			$document_header 	= __("Payment confirmation", 'indiebooking');
            			$documentDate		= date("d.m.y");
            		}
            		if (is_null($documentDate) || $documentDate == '01.01.1970') {
            			$documentDate		= date("d.m.y");
            		}
//             		if (isset($dataUeberschrift) && $dataUeberschrift !== "") {
//             			$document_header 	= $dataUeberschrift;
//             		} else {
//             			$document_header 	= __("Payment confirmation", 'indiebooking');
//             		}
//             		$documentDate		= date("d.m.y");
            	}
            }
        } elseif ($buchungKopf->getBuchung_status() == "rs_ib-requested") {
        	$bookingIsInquiry 	= true;
        	$documentDate		= date("d.m.y");
            if ($document_header == "") {
                $document_header = __("Inquiery confirmation", 'indiebooking');
            }
        } elseif ($buchungKopf->getBuchung_status() == "rs_ib-canc_request") {
        	$bookingIsInquiry 	= true;
        	$documentDate		= date("d.m.y");
        	if ($document_header == "") {
        		$document_header = __("Inquiery rejected", 'indiebooking');
        	}
        }
        if ($counter == 0 && $buchungKopf->getBuchung_status() == "rs_ib-canceled") {
            $stornoueberschrift = __("cancellation", 'indiebooking');
            $stornoKennzeichen  = true;
        }
        if (sizeof($buchungKopf->getZahlungen()) > 0) {
            foreach ($buchungKopf->getZahlungen() as $zahlung) {
            	if ($buchungKopf->getBuchung_status() != 'rs_ib-storno' && $buchungKopf->getBuchung_status() != 'rs_ib-canceled' && $buchungKopf->getBuchung_status() != 'rs_ib-storno_paid') {
                    //wird unten angedruckt
            	} elseif($buchungKopf->getBuchung_status() == 'rs_ib-storno' || $buchungKopf->getBuchung_status() == 'rs_ib-storno_paid') {
                    if ($document_header == "") {
//                         $document_header 	= __("Storno confirmation", 'indiebooking');
                    	$document_header	= __("invoice adjustment", 'indiebooking');
                        $documentDate		= date("d.m.y");
                    }
                    array_push($stornoZahlungen, $zahlung);
                }
            }
        } elseif($buchungKopf->getBuchung_status() == 'rs_ib-storno' || $buchungKopf->getBuchung_status() == 'rs_ib-storno_paid') {
            if ($document_header == "") {
//                 $document_header = __("Storno confirmation", 'indiebooking');
            	$document_header	= __("invoice adjustment", 'indiebooking');
            }
            $zeroZahlung = new RS_IB_Model_BuchungZahlung();
            $zeroZahlung->setBezeichnung("");
            $zeroZahlung->setZahlungbetrag(0);
            array_push($stornoZahlungen, $zeroZahlung);
        }
        $counter++;
    }
    $counter = 0;
?>
<?php
    echo $document_header;
?>
<table id="price_display_table" style="width: 100%; border: none;">
	<!-- Durch thead wird gewahrleistet, dass bei einem Seitenwechsel, der Header auf der neuen Seite ebenfalls angedruckt wird -->
    <thead>
    	<tr>
    		<!--<th style="text-align: left;"><?php //_e("Booking number", 'indiebooking');?>:&nbsp;<?php //echo $buchungKopf->getBuchung_nr(); ?></th>-->
    		<th style="text-align: left;">
    			<?php if (!$bookingIsInquiry) { ?>
    				<?php _e("Invoice number", 'indiebooking');?>:&nbsp;<?php echo $buchungKopf->getRechnung_nr(); ?>
    			<?php } else {
    				_e("Your inquiry from", 'indiebooking');
    				?>&nbsp;<?php
    					echo $buchungKopf->getBuchungsdatum()->format('d.m.Y');
    				}
    				?>
    		</th>
    		<td colspan="4">&nbsp;</td>
    		<td colspan="3" class="buchungsdatum">
    			<?php echo $companyLocation.", ". $documentDate; //date("d.m.y") ?>
    		</td>
    	</tr>
    	<?php
		if (!$bookingIsInquiry) {
		?>
		<tr>
    		<td colspan="5" class="ihrebuchung">
    			<?php
				_e("Your booking from", 'indiebooking');
    			?>&nbsp;<?php echo $buchungKopf->getBuchungsdatum()->format('d.m.Y');?>
    		</td>
    	</tr>
    	<?php
    	}
        if (sizeof($stornoZahlungen) > 0) {
            foreach ($stornoZahlungen as $stornoZahlung) { ?>
               	<tr>
               		<th colspan="2"><?php _e("already made payments", 'indiebooking'); ?></th>
    				<th style="text-align: left;" colspan="2">
    					<?php echo $stornoZahlung->getBezeichnung();?>
    				</th>
                   	<th colspan="2" class="<?php echo $tdClass; ?>" <?php echo $tdNumberStyle; ?>>
                   		<?php echo number_format($stornoZahlung->getZahlungbetrag(), 2, ',', '.')." ".$waehrung;?>
                   	</th>
<!--                    	<td> &nbsp;</td> -->
               	</tr>
       	   <?php
           	}
//            	$stornoZahlungen = array();
        }
        if ($stornoueberschrift !== "") { ?>
            <tr>
            	<td colspan="6">&nbsp;</td>
            </tr>
            <tr style="border-top: 1px solid;">
            	<th colspan="6"><?php echo $stornoueberschrift; ?></th>
        	</tr>
    	<?php
        }
        ?>
        <tr class="ueberschrift">
        	<th class="beschreibungzeile"><?php _e("Description", 'indiebooking');?></th>
            <th class="zeitraumzeile"><?php _e('Period', 'indiebooking');?></th>
            <th class="nachtzeile"><?php _e('Number', 'indiebooking');?></th>
            <th class="einzelpreiszeile"><?php _e('Price', 'indiebooking');?></th>
            <th class="mwstzeile"><?php _e('MwSt', 'indiebooking');?></th>
            <th class="gesamtpreiszeile"><?php _e('Gross', 'indiebooking');?></th>
        </tr>
    </thead>
    <tbody>
    <?php
    foreach ($buchungen as $buchungKopf) {
        if ($counter > 0) {
            $ueberschrift = "&nbsp;";
            if ($buchungKopf->getBuchung_status() == "rs_ib-storno" || $buchungKopf->getBuchung_status() == "rs_ib-storno_paid") {
            	$summeNetto 	= array();
            	$ueberschrift 	= __("cancellation fees", 'indiebooking'); //"Stornogebuehren";
				//<pagebreak>
            }
            if (sizeof($buchungKopf->getTeilkoepfe()) > 0) {
            	$cancellationFees = true;
            ?>
            <tr>
            	<td colspan="10">&nbsp;</td>
            </tr>
            <tr style="border-top: 1px solid;">
            	<th colspan="10"><?php echo $ueberschrift; ?></th>
            </tr>
        <?php
            }
        }
        $counter++;
    	/* @var $teilKopf RS_IB_Model_Teilbuchungskopf */
        foreach ($buchungKopf->getTeilkoepfe() as $teilKopf) {
        	
            ?>
            <?php
                if (sizeof($buchungKopf->getTeilkoepfe()) > 1 || $buchungKopf->getBookingType() == 2) {
                	if (sizeof($buchungKopf->getTeilkoepfe()) > 1) {
            ?>
			            <tr>
			            	<th colspan="8"><?php _e("booking part", 'indiebooking');?>&nbsp;-&nbsp;<?php echo $teilKopf->getTeilbuchung_id(); ?>
			            	&nbsp;-&nbsp;<?php
			            	if (!$bookingByCategorieKz) {
			            		echo $teilKopf->getAppartment_name();
			            	} else {
			            		echo $teilKopf->getAppartment_category_name();
			            	}
			            	?>
			            	</th>
			            </tr>
            <?php
	                } else {
	                	?>
	                	<tr>
	                		<th colspan="8"><?php
	                		if (!$bookingByCategorieKz) {
	                			echo $teilKopf->getAppartment_name();
	                		} else {
	                			echo $teilKopf->getAppartment_category_name();
	                		}
	                		?></th>
		            	</tr>
		            	<?php
	                }
               	}
            ?>
            	<?php
                    foreach ($teilKopf->getPositionen() as $position) {
                        $preisVon   		= $position->getPreis_von()->format('d.m.Y');
                        $preisBis   		= $position->getPreis_bis()->format('d.m.Y');
                        $zeitraum   		= $preisVon." - ".$preisBis;

                        $position->calculateExpelPrice();
                        $posFullPrice    	= $position->getAusschreibFullPrice();
                        $posEinzelPreis  	= $position->getAusschreibEinzelPrice();
                        $rabattEinzelpreise = $position->getRabatteEinzelPrice();
                        $kommentar       	= $position->getKommentar();
                        
                        $mwstProz = floatval($position->getMwst_prozent()) * 100;
                        $mwstProz = intval($mwstProz);
                        if (key_exists($mwstProz, $summeNetto)) {
                        	$summeNetto[$mwstProz] += $position->getNettoBetrag();
                        } else {
                        	$summeNetto[$mwstProz] = $position->getNettoBetrag();
                        }
                        $specialDegClass 	= "";
                        $originalPrice		= "";
                        $specialSign		= "";
                        if ($position->getHasDegression() == true  || sizeof($rabattEinzelpreise) > 0) {
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
//                         	$posEinzelPreis = $position->getDegressionEinzelPrice();
                        }
                ?>
                <tr>
                	<td class="beschreibungzeile">
                		<?php
	                		if (!$bookingByCategorieKz || $position->getPosition_typ() != "appartment_price") {
	                			$bezeichnung = $position->getBezeichnung();
	                		} else if ($bookingByCategorieKz && $position->getPosition_typ() == "appartment_price") {
	                			$bezeichnung = $teilKopf->getAppartment_category_name();
	                		}
// 							$bezeichnung = htmlspecialchars_decode($position->getBezeichnung(), ENT_QUOTES);
// 							$bezeichnung	= html_entity_decode($bezeichnung, ENT_QUOTES);
                			echo $bezeichnung;
                		?>
                		<?php if ($position->getPosition_typ() == "appartment_price") {?>
                		<?php printf(__("(%s persons)", "indiebooking"),$position->getAnzahlPersonen()); ?>
                		<?php } ?>
                	</td>
                    <td class="zeitraumzeile"><?php echo $zeitraum;?></td>
                    <td class="nachtzeile">
                    	<?php
                    	echo $position->getBerechnungsAnzahlEinheit();
//                     	if ($position->getPosition_typ() == "appartment_price") {
//                     		echo $position->getAnzahl_naechte();
//                     	}
                    	?>
                    </td>
                    <td class="<?php echo $specialDegClass; ?> einzelpreiszeile appartment_price_table_definition">
                    	<?php echo $tdvorzeichen." "; ?><?php echo number_format($posEinzelPreis, 2, ',', '.')." ".$waehrung.$specialSign;?>
                    </td>
                    <td class="mwstzeile appartment_price_table_definition">
                    	<?php echo number_format($position->getMwst_prozent() * 100, 2, ',', '.')." %";?>
                    </td>
                    <!--
                    <td class="einheit">
                    	<?php //echo $position->getBerechnungstypEinheit();?>
                    	<?php //echo $kommentar; ?>
                	</td>
                	 -->
                    <td class="gesamtpreiszeile appartment_price_table_definition">
                    	<?php echo number_format($posFullPrice, 2, ',', '.')." ".$waehrung;?>
                    </td>
                    <!--<td class="<?php //echo $tdClass; ?> appartment_price_table_definition"><?php //echo number_format($position->getCalcPosPrice(), 2, ',', '.')." ".$waehrung;?></td>-->
                    <!--<td class="<?php //echo $tdClass; ?> appartment_price_table_definition"><?php //echo number_format($position->getMwst_wert(), 2, ',', '.')." ".$waehrung;?></td>   -->
                </tr>
    			<?php
    			if (is_array($position->getRabatte()) && sizeof($position->getRabatte()) > 0) {
    				$rabattkey 		= 0;
    				$einzelRabatte	= $position->getRabatteEinzelPrice();
    			   	foreach ($position->getRabatte() as $rabatt) {
						$rabattVz = "-";
                      	if ($rabatt->getPlus_minus_kz() == 2) {
                      		$rabattVz = "";
                      	}
        			    if ($rabatt->getRabatt_typ() == 1) {
        			    	$wertTyp = rs_ib_currency_util::getCurrentCurrency();
        			    } elseif ($rabatt->getRabatt_typ() == 2) {
        			    	$wertTyp = "%";
        			    }
        			    if ($rabatt->getRabatt_art() != 3) {
	                       	if ($rabatt->getRabatt_ausschreiben_kz() == 1) {
	                       		if ($rabatt->getBerechnung_art() < RS_IB_Model_BuchungRabatt::RABATT_BERECHNUNG_POSITION_PREIS) {
		    			       	?>
		                       	<tr>
		    						<td colspan="5" class="rabattbezeichnung"><?php echo $rabatt->getBezeichnung(); ?></td>
		                           	<td class="<?php echo $tdClass; ?> appartment_price_table_definition">
		                           		<?php echo $rabattVz." "; ?>
		                           		<?php echo number_format($rabatt->getRabatt_wert(), 2, ',', '.')." ".$wertTyp;?>
		                           	</td>
		                           	<td>&nbsp;</td>
		                       </tr>
		    				<?php
		                       	} else {
		                       		$basisPrice			= number_format($einzelRabatte[$rabattkey]['basis'], 2, ',', '.')." ".$waehrung;
		                       		$description 		= "";
		                       		if ($rabatt->getRabatt_art() == RS_IB_Model_BuchungRabatt::RABATT_ART_COUPON) {
		                       			$description 	= __("Coupon", 'indiebooking');
		                       			$description 	= $description . " (".$rabatt->getBezeichnung().")";
		                       		} else {
		                       			$description 	= $rabatt->getBezeichnung();
		                       		}
		                       		$rabattkey++;
		                       		$priceDesc 			= $rabattVz . " " . number_format($rabatt->getRabatt_wert(), 2, ',', '.')." ".$wertTyp;
		                       		?>
			                        <tr>
			    						<td colspan="5" style="font-style: italic;">
			    							*<?php printf(__("%s %s considered (before %s)", "indiebooking"), $description, $priceDesc, $basisPrice); ?>
			    						</td>
			                       	</tr>
							       	<?php
		                       	}
	                       	}
        			    } else { ?>
	                        <tr>
	    						<td colspan="5" style="font-style: italic;">
	    							*<?php printf(__("Discount according to price season from %s %s considered (before %s)", "indiebooking"),number_format($rabatt->getRabatt_wert(), 2, ',', '.'),  $wertTyp, $originalPrice); ?>
	    						</td>
	                    	</tr>
					    <?php
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
            <?php
            if ((sizeof($buchungKopf->getTeilkoepfe()) > 1) || (is_array($teilKopf->getRabatte()) && sizeof($teilKopf->getRabatte()) > 0)) {
            ?>
	           	<tr>
	    			<th class="summenbeschreibung" colspan="5"><?php _e("subtotal", 'indiebooking');?></th>
	               	<th class="<?php echo $tdClass; ?> appartment_price_table_definition">
	               		<?php echo number_format($currentTeilsumme, 2, ',', '.')." ".$waehrung;?>
	               	</th>
	           </tr>
           <?php
            }
            if (is_array($teilKopf->getRabatte()) && sizeof($teilKopf->getRabatte()) > 0) {
            	foreach ($teilKopf->getRabatte() as $rabatt) {
            		if ($rabatt->getRabatt_ausschreiben_kz() == 1) {
            			$rabattVz = "-";
            			if ($rabatt->getPlus_minus_kz() == 2) {
            				$rabattVz = "";
            			}
            			if ($rabatt->getRabatt_typ() == 1) {
            				$wertTyp = rs_ib_currency_util::getCurrentCurrency();
            			} elseif ($rabatt->getRabatt_typ() == 2) {
            				$wertTyp = "%";
            			}
            			$description 		= "";
            			if ($rabatt->getRabatt_art() == RS_IB_Model_BuchungRabatt::RABATT_ART_COUPON) {
            				$description 	= __("Coupon", 'indiebooking');
            				$description 	= $description . " (".$rabatt->getBezeichnung().")";
            			} else {
            				$description 	= $rabatt->getBezeichnung();
            			}
            			?>
                       	<tr>
    						<td colspan="5" class="rabattbezeichnung"><?php echo $description; ?></td>
                           	<td class="<?php echo $tdClass; ?> appartment_price_table_definition">
                           		<?php echo $rabattVz." "; ?>
                           		<?php echo number_format($rabatt->getRabatt_wert(), 2, ',', '.')." ".$wertTyp;?>
                           	</td>
                           	<td>&nbsp;</td>
                       </tr>
    			<?php
                    }
    		   }
    		   ?>
	           	<tr>
	    			<th class="summenbeschreibung" colspan="5"><?php _e("subtotal", 'indiebooking');?></th>
	               	<th class="<?php echo $tdClass; ?> appartment_price_table_definition">
	               		<?php echo number_format($teilKopf->getCalculatedPrice(), 2, ',', '.')." ".$waehrung;?>
	               	</th>
	           </tr>
           <?php
    		   
    		}
           ?>
    	<?php
        }
        if (sizeof($buchungKopf->getTeilkoepfe()) > 0) {
        ?>
	       	<tr>
	    		<th class="summenbeschreibung summebetrag" colspan="5"><?php _e("sum", 'indiebooking');?></th>
	           	<th class="<?php echo $tdClass; ?> summebetrag appartment_price_table_definition">
	           		<?php echo number_format($buchungKopf->getFullPrice(), 2, ',', '.')." ".$waehrung;?>
	           	</th>
	       </tr>
        <?php
        }
        /* @var $rabatt RS_IB_Model_BuchungRabatt */
        if (is_array($buchungKopf->getRabatte()) && sizeof($buchungKopf->getRabatte()) > 0) {
            foreach ($buchungKopf->getRabatte() as $rabatt) {
                if ($rabatt->getRabatt_ausschreiben_kz() == 1) {
                    $rabattVz = "-";
                    if ($rabatt->getPlus_minus_kz() == 2) {
                        $rabattVz = "";
                    }
                    if ($rabatt->getRabatt_typ() == 1) {
                        $wertTyp = rs_ib_currency_util::getCurrentCurrency();
                    } elseif ($rabatt->getRabatt_typ() == 2) {
                        $wertTyp = "%";
                    }
                    $description 		= "";
                    if ($rabatt->getRabatt_art() == RS_IB_Model_BuchungRabatt::RABATT_ART_COUPON) {
                    	$description 	= __("Coupon", 'indiebooking');
                    	$description 	= $description . " (".$rabatt->getBezeichnung().")";
                    } else {
                    	$description 	= $rabatt->getBezeichnung();
                    }
                ?>
               	<tr>
		       		<td class="beschreibungzeile">&nbsp;</td>
		       		<td class="zeitraumzeile">&nbsp;</td>
		       		<td class="nachtzeile">&nbsp;</td>
    				<!-- <td colspan="2" style="padding-left: 5px">-->
    				<td colspan="2">
    					<?php echo $description; ?>
    				</td>
                   	<td style="width: 3.0cm; max-width: 3.0cm;" class="<?php echo $tdClass; ?> appartment_price_table_definition">
                   		<?php echo $rabattVz." "; ?><?php echo number_format($rabatt->getRabatt_wert(), 2, ',', '.')." ".$wertTyp;?>
                   	</td>
                   	<td> &nbsp;</td>
               </tr>
    		<?php
                }
    	   }
    	}
    	$firstnetto = true;
    	if (is_array($summeNetto) && sizeof($summeNetto) > 0) {
    		foreach ($summeNetto as $mwstKey => $nettoBetrag) {
    			$pricetablerowclass = "";
    			if ($firstnetto) {
    				$pricetablerowclass = "rechnungsbetrag";
    				$firstnetto = false;
    			}
    			?>
		       	<tr>
		       		<td class="beschreibungzeile">&nbsp;</td>
		       		<td class="zeitraumzeile">&nbsp;</td>
		       		<td class="nachtzeile">&nbsp;</td>
		    		<th class="summenbeschreibung <?php echo $pricetablerowclass; ?>" colspan="2">
		    			<?php
		    				_e("net amount", 'indiebooking');
		    				echo " ".$mwstKey."%";
		    			?>
		    		</th>
		           	<th class="<?php echo $tdClass; ?><?php echo $pricetablerowclass; ?> appartment_price_table_definition">
		           		<?php echo number_format($nettoBetrag, 2, ',', '.')." ".$waehrung;?>
		           	</th>
		       	</tr>
    			<?php
    		}
    	}
       	foreach ($buchungKopf->getFullMwstArray() as $mwstObj) {
       		if (!is_null($mwstObj->getMwst_prozent()) && $mwstObj->getMwst_prozent() > 0) {
       			?>
           	<tr>
           		<td class="beschreibungzeile">&nbsp;</td>
           		<td class="zeitraumzeile">&nbsp;</td>
           		<td class="nachtzeile">&nbsp;</td>
				<!--
    			<th class="mwstzeile" colspan="3"><?php //_e("incl.", 'indiebooking');?>&nbsp;
    				<?php //echo $mwstObj->getMwst_prozent();?>%
    			</th>
    			 -->
    			 <!-- <th class="mwstzeile" colspan="2"> -->
    			 <th class="summenbeschreibung" colspan="2">
    			 <?php
    			 printf(__("VAT %s %s", "indiebooking"), $mwstObj->getMwst_prozent(), '%');
    			 ?>
    			 </th>
               	<th class="<?php echo $tdClass; ?> appartment_price_table_definition">
               		<?php echo number_format($mwstObj->getMwst_wert(), 2, ',', '.')." ".$waehrung;?>
               	</th>
               	<td> &nbsp;</td>
           	</tr>
       	   <?php
           	}
       	}
       	if ($buchungKopf->getBuchung_status() != 'rs_ib-storno' ||
       		($buchungKopf->getBuchung_status() == 'rs_ib-storno' && $cancellationFees)) {
       	?>
       	<tr>
       		<td class="beschreibungzeile">&nbsp;</td>
       		<td class="zeitraumzeile">&nbsp;</td>
       		<td class="nachtzeile">&nbsp;</td>
    		<th class="summenbeschreibung rechnungsbetrag" colspan="2"><?php _e("invoice amount", 'indiebooking'); ?></th>
           	<th class="<?php echo $tdClass; ?> rechnungsbetrag appartment_price_table_definition">
           		<?php echo number_format($buchungKopf->getCalculatedPrice(), 2, ',', '.')." ".$waehrung;?>
           	</th>
       	</tr>
       	<?php
    	}
       	if ($buchungKopf->getAnzahlungsbetrag() > -1 && $buchungKopf->getBuchung_status() != 'rs_ib-storno') {
       		?>
			<tr>
	       		<td class="beschreibungzeile">&nbsp;</td>
	       		<td class="zeitraumzeile">&nbsp;</td>
	       		<td class="nachtzeile">&nbsp;</td>
	    		<th class="summenbeschreibung rechnungsbetrag" colspan="2"><?php _ex("deposit", 'anzahlung', 'indiebooking'); ?></th>
	           	<th class="<?php echo $tdClass; ?> rechnungsbetrag appartment_price_table_definition">
	           		<?php echo number_format($buchungKopf->getAnzahlungsbetrag(), 2, ',', '.')." ".$waehrung;?>
	           	</th>
	       	</tr>
       		<?php
       	}
       	/* @var $zahlung RS_IB_Model_BuchungZahlung */
       	foreach ($buchungKopf->getZahlungen() as $zahlung) {
           	    if ($buchungKopf->getBuchung_status() != 'rs_ib-storno'
           	    		&& $buchungKopf->getBuchung_status() != 'rs_ib-canceled'
           	    		&& $buchungKopf->getBuchung_status() != 'rs_ib-storno_paid') {
       	    ?>
           	<tr>
           		<td class="beschreibungzeile">&nbsp;</td>
           		<td class="zeitraumzeile">&nbsp;</td>
           		<td class="nachtzeile">&nbsp;</td>
    			<th class="bereitsgezahlt summenbeschreibung" colspan="2">
    				<?php
    				if ($zahlung->getZahlungart() == RS_IB_Model_BuchungZahlung::ZAHLART_GUTSCHEIN) {
						_e("voucher", 'indiebooking');
	    				echo " (".$zahlung->getBezeichnung().")";
    				} else {
    					echo $zahlung->getBezeichnung();
    				}
    				?>
    			</th>
               	<th class="<?php echo $tdClass; ?> bereitsgezahlt appartment_price_table_definition">
               		<?php echo "- ".number_format($zahlung->getZahlungbetrag(), 2, ',', '.')." ".$waehrung;?>
               	</th>
               	<td> &nbsp;</td>
           	</tr>
       	   <?php
           	    		} elseif($buchungKopf->getBuchung_status() == 'rs_ib-storno'
           	    			|| $buchungKopf->getBuchung_status() == 'rs_ib-storno_paid') {
       	       //array_push($stornoZahlungen, $zahlung);
       	   }
       	}
       	if (is_null($oberbuchung)) { ?>
       	<tr>
       		<td class="beschreibungzeile">&nbsp;</td>
       		<td class="zeitraumzeile">&nbsp;</td>
       		<td class="nachtzeile">&nbsp;</td>
    		<th class="summenbeschreibung zahlungsbetrag" colspan="2">
    			<?php _e("missing amount", 'indiebooking'); ?>
    		</th>
           	<th class="<?php echo $tdClass; ?> zahlungsbetrag appartment_price_table_definition">
           		<?php echo number_format($buchungKopf->getZahlungsbetrag(), 2, ',', '.')." ".$waehrung;?>
           	</th>
       	</tr>
       	<tr>
       		<td>&nbsp;</td>
       	</tr>
       	<?php
       	if (!$bookingIsInquiry && $buchungKopf->getZahlungsbetrag() > 0) {
       	?>
       	<tr>
       		<?php
       		if ($buchungKopf->getHauptZahlungsart() == "INVOICE") {
           		$zahlungsartbezeichnung    = __("pay by invoice", 'indiebooking');
       		} else if ($buchungKopf->getHauptZahlungsart() == "STRIPECREDITCARD" || $buchungKopf->getHauptZahlungsart() == "CREDITCARD") {
       			$zahlungsartbezeichnung    = __("creditcard", 'indiebooking');
       		} else if ($buchungKopf->getHauptZahlungsart() == "STRIPESOFORT") {
       			$zahlungsartbezeichnung    = __("sofort", 'indiebooking');
       		} else if ($buchungKopf->getHauptZahlungsart() == "STRIPEGIROPAY") {
       			$zahlungsartbezeichnung    = __("giropay", 'indiebooking');
       		} else if ($buchungKopf->getHauptZahlungsart() == "STRIPESEPADIRECTDEBIT") {
       			$zahlungsartbezeichnung    = __("sepa direct debit", 'indiebooking');
       		} else if ($buchungKopf->getHauptZahlungsart() == "PAYPAL") {
       			$zahlungsartbezeichnung    = __("paypal", 'indiebooking');
       		} else if ($buchungKopf->getHauptZahlungsart() == "PAYPALEXPRESS") {
       			$zahlungsartbezeichnung    = __("paypal express", 'indiebooking');
       		} else if ($buchungKopf->getHauptZahlungsart() == "AMAZONPAYMENTS" || $buchungKopf->getHauptZahlungsart() == "AMAZONPAYMENTSEXPRESS") {
       			$zahlungsartbezeichnung    = __("amazon payments", 'indiebooking');
       		}
       		else {
       		    $zahlungsartbezeichnung    = $buchungKopf->getHauptZahlungsart();
       		}
       		?>
       		<td colspan="6">
       			<?php _e("Your selected payment method", 'indiebooking'); ?>:&nbsp;<?php echo $zahlungsartbezeichnung; ?>
       		</td>
       	</tr>
       	<tr>
       		<td colspan="6">
       			<?php
       			if ($buchungKopf->getZahlungsbetrag() > 0 && sizeof($printBankData) > 0 && (!$bookingIsInquiry)) {
       				$qrCodeReTxt = __("Easy and convenient to pay with QR code", 'indiebooking');
       				
       				$codeWaehrung	= "";
       				switch ($waehrung) {
       					case "EUR":
       						$codeWaehrung = "EUR";
       						break;
       					default:
       						$codeWaehrung = "EUR";
       						break;
       				}
       				$barcodeText	= "";
       				$companyName	= $printBankData['companyname'];
	       			$bankname 		= $printBankData['bank_name'];
	       			$bankiban 		= $printBankData['bank_iban'];
	       			$bankbic 		= $printBankData['bank_bic'];
	       			$bankaccount 	= $printBankData['bank_account'];
	       			$bankaccount	= html_entity_decode($bankaccount);
	       			$verwendungszw	= $companyName." ".$buchungKopf->getRechnung_nr();
	       			$zahlungsbetrag = number_format($buchungKopf->getZahlungsbetrag(), 2, '.', '');
	       			if ((!is_null($bankbic) && $bankbic != "") &&
	       				(!is_null($bankaccount) && $bankaccount != "") &&
	       				(!is_null($bankiban) && $bankiban != "")
	       				) {
	       				$crlf		 	= '\r\n';
		       			$barcodeText 	= "BCD".$crlf;
		       			$barcodeText 	= $barcodeText."002".$crlf;
		       			$barcodeText 	= $barcodeText."1".$crlf;
		       			$barcodeText 	= $barcodeText."SCT".$crlf;
		       			$barcodeText 	= $barcodeText.$bankbic.$crlf;
		       			$barcodeText 	= $barcodeText.$bankaccount.$crlf;
		       			$barcodeText 	= $barcodeText.$bankiban.$crlf;
		       			$barcodeText 	= $barcodeText.$codeWaehrung.$zahlungsbetrag.$crlf;
		       			$barcodeText 	= $barcodeText.$crlf;
		       			$barcodeText 	= $barcodeText.$crlf;
		       			$barcodeText 	= $barcodeText.$verwendungszw.$crlf;
		       			$barcodeText 	= $barcodeText.$crlf;
	       			}
	       			if ($barcodeText != "") {
	       				echo $qrCodeReTxt;
       			?>
       				<br />
       				<barcode code="<?php echo $barcodeText; ?>" disableborder="1" type="QR" class="barcode" size="1.5" error="M" />
       			<?php
	       			}
       			}
       			?>
       		</td>
       	</tr>
       	<?php
       	} //if (!$bookingIsInquiry)
       	if (!is_null($dankeText) && strlen(trim($dankeText)) > 0) {
       	?>
	       	<tr>
	       		<td colspan="6">
	       			<?php echo $dankeText; ?>
	       		</td>
	       	</tr>
       	<?php }
       	}
       	?>
   	<?php } //foreach buchungen as $buchungKopf
        if (!is_null($oberbuchung)) {
        	if ($buchungKopf->getBuchung_status() != 'rs_ib-storno') {
        	?>
            	<tr style="border-top: 5px double;"><td>&nbsp;</td></tr>
            <?php
        	}
        	if ($cancellationFees) {
	            foreach ($stornoZahlungen as $stornoZahlung) { ?>
	               	<tr>
	               		<th colspan="3">&nbsp;</th>
	    				<th style="text-align: left;" colspan="2"><?php echo $stornoZahlung->getBezeichnung();?></th>
	                   	<th class="<?php echo $tdClass; ?>" <?php echo $tdNumberStyle; ?>>
	                   		<?php echo number_format($stornoZahlung->getZahlungbetrag(), 2, ',', '.')." ".$waehrung;?>
	                   	</th>
	<!--                    	<td> &nbsp;</td> -->
	               	</tr>
	       	   <?php
	           	}
        	}
           	if ($buchungKopf->getBuchung_status() != 'rs_ib-storno' && $buchungKopf->getBuchung_status() != 'rs_ib-storno_paid') {
           		$endbetrag = $oberbuchung->getEndbetrag();
           	} else {
           		$endbetrag = $buchungKopf->getFullPrice();
           		foreach ($stornoZahlungen as $stornoZahlung) {
           			$endbetrag = $endbetrag - $stornoZahlung->getZahlungbetrag();
           		}
           	}
           	$extraClass ="";
           	if ($buchungKopf->getBuchung_status() == 'rs_ib-storno') {
           		$extraClass = "rs_ib_print_border_top";
           	}
           	?>
            <tr>
                <th colspan="3" >&nbsp;</th>
                <th class="<?php echo $extraClass; ?>" style="text-align: left;" colspan="2"><?php _e("missing amount", 'indiebooking'); ?></th>
                <th class="<?php echo $tdClass; ?> <?php echo $extraClass; ?>" <?php echo $tdNumberStyle; ?>>
                	<?php //echo number_format($oberbuchung->getEndbetrag(), 2, ',', '.')." ".$waehrung;?>
                	<?php echo number_format($endbetrag, 2, ',', '.')." ".$waehrung;?>
                </th>
<!--                	<td> &nbsp;</td> -->
           	</tr>
           	<tr>
           		<?php //if ($oberbuchung->getEndbetrag() > 0) { ?>
				<?php if ($endbetrag > 0) { ?>
           			<td colspan="6"><?php _e("Please pay the outstanding amount in the next few days.", 'indiebooking'); ?></td>
           		<?php //} elseif ($oberbuchung->getEndbetrag() < 0) { ?>
				<?php } elseif ($endbetrag < 0) { ?>
           			<td colspan="6"><?php _e("The outstanding amount will be paid in the next few days.", 'indiebooking'); ?></td>
           		<?php } ?>
           	</tr>
        <?php }  ?>
    </tbody>
</table>
<div class="defaultprinttext">
<?php
if (strlen($buchungKopf->getCustomText()) > 0) { ?>
<div>
	<h2 class="ibui_h2"><?php _e('Custom message', 'indiebooking')?></h2>
	<br />
	<?php
		echo nl2br($buchungKopf->getCustomText());
	?>
</div>
<?php
}
?>
<?php
if ($buchungKopf->getHauptZahlungsart() == "INVOICE" && (!$bookingIsInquiry) && $buchungKopf->getZahlungsbetrag() > 0) {
	$invoiceTermsOfPaymentTxt	= get_option('rs_indiebooking_settings_invoice_terms_of_payment_txt');
	echo $invoiceTermsOfPaymentTxt;
}
?>
</div>
<?php
