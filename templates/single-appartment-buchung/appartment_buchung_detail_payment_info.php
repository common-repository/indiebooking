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

/* @var $buchungKopf RS_IB_Model_Buchungskopf */
/* @var $teilKopf RS_IB_Model_Teilbuchungskopf */
/* @var $buchungTable RS_IB_Table_Appartment_Buchung */
/* @var $position RS_IB_Model_Buchungposition */
/* @var $rabatt RS_IB_Model_BuchungRabatt */
/* @var $zahlung RS_IB_Model_BuchungZahlung */
?>
<!-- 	<div class="panel-body">	 -->
<div id="detail_paymentbox_sm">
    <h5 style="padding-bottom:10px;">
    	<?php _e("Your booking number:", 'indiebooking');?>&nbsp;<?php echo $buchungKopf->getBuchung_nr(); ?>
    </h5>
        <table id="price_display_table1" class="table table-condensed table-responsive price_table">
            <thead>
            	<tr>
                	<th style="width: 120px; max-width: 120px;">&nbsp;</th>
                    <th class="myTableRow hidden-xs"><?php _e('from', 'indiebooking');?></th>
                    <th class="myTableRow hidden-xs"><?php _e('to', 'indiebooking');?></th>
                    <th class="myTableRow hidden-xs appartment_price_table_definition"><?php _e('Price', 'indiebooking');?></th>
                    <th class="myTableRow hidden-xs appartment_price_table_definition"><?php _e('Amount', 'indiebooking');?></th>
                    <!-- <th class="myTableRow"><?php //_e('Net', 'indiebooking');?></th>
                    <th class="myTableRow"><?php //_e('Taxes', 'indiebooking');?></th>  -->
                    <th class="myTableRow appartment_price_table_definition"><?php _e('Gross', 'indiebooking');?></th>
                    <th class="myTableRow hidden-xs appartment_price_table_definition"><?php _e('MwSt', 'indiebooking');?></th>
                    <!--<th class="myTableRow"><?php //_e('CalcPosPrice', 'indiebooking');?></th> -->
                    <!--<th class="myTableRow"><?php //_e('MwSt-Value', 'indiebooking');?></th>   -->
                </tr>
            </thead>
            <tbody>
            <?php
            if (!isset($waehrung)) {
                $waehrung   = rs_ib_currency_util::getCurrentCurrency();
            }
            $tdClass        = "";
            $tdvorzeichen   = "";
            $anzTeilKoepfe	= sizeof($buchungKopf->getTeilkoepfe());
            foreach ($buchungKopf->getTeilkoepfe() as $teilKopf) {
            	if ($anzTeilKoepfe > 1) {
                ?>
                <tr>
                	<th colspan="7" class="text_center">
                		<h5>
                			<?php _e("booking part", 'indiebooking');?>&nbsp;-&nbsp;<?php
                			echo $teilKopf->getTeilbuchung_id();
                			?>&nbsp;-&nbsp;<?php
                			if (!$showCategoryAsName) {
                				echo $teilKopf->getAppartment_name();
                			} else {
                				echo $teilKopf->getAppartment_category_name();
                			}
                			?>
                		</h5>
                	</th>
                </tr>
                <?php
                }
                foreach ($teilKopf->getPositionen() as $position) {
//                     $preisVonDt = datetime::createFromFormat("Y-m-d h:i:s", $position->getPreis_von());
                    $preisVon   		= $position->getPreis_von()->format('d.m.Y');
//                     $preisBisDt = datetime::createFromFormat("Y-m-d h:i:s", $position->getPreis_bis());
                    $preisBis   		= $position->getPreis_bis()->format('d.m.Y');
                    ?>
        			<?php
        			$position->calculateExpelPrice();
        			$position->setQuadratmeter($teilKopf->getAppartment_qm());
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
							$lastRabatt 	= $rabattEinzelpreise[sizeof($rabattEinzelpreise)-1];
							$posEinzelPreis = $lastRabatt['price'];
						}
        			}
        			?>
                    <tr>
                    	<td><?php
                    		if (!$showCategoryAsName || $position->getPosition_typ() != "appartment_price") {
                    			echo $position->getBezeichnung();
                    		} else if ($showCategoryAsName && $position->getPosition_typ() == "appartment_price") {
                    			echo $teilKopf->getAppartment_category_name();
                    		}
                    	?>
                    	<?php //echo $position->getPosition_typ(); ?>
                    	</td>
                        <td class="<?php echo $tdClass; ?> hidden-xs">
                        	<?php echo $preisVon;?>
                        </td>
                        <td class="<?php echo $tdClass; ?> hidden-xs">
                        	<?php echo $preisBis;?>
                        </td>
                        <td class="<?php echo $tdClass; ?> <?php echo $specialDegClass; ?> hidden-xs appartment_price_table_definition">
                        	<?php echo $tdvorzeichen." "; ?><?php echo number_format($posEinzelPreis, 2, ',', '.')." ".$waehrung.$specialSign;?>
                        </td>
                        <td class="<?php echo $tdClass; ?> hidden-xs appartment_price_table_definition">
                        	<?php //echo $position->getAnzahl_naechte();?>
                        	<?php echo $position->getBerechnungsAnzahlEinheit(); ?>
                        </td>
                        <td class="<?php echo $tdClass; ?> appartment_price_table_definition">
                        	<?php echo number_format($posFullPrice, 2, ',', '.')." ".$waehrung;?>
                        </td>
                        <td class="<?php echo $tdClass; ?> hidden-xs appartment_price_table_definition">
                        	<?php echo number_format($position->getMwst_prozent() * 100, 2, ',', '.')." %";?>
                        </td>
                        <!--<td class="<?php //echo $tdClass; ?> appartment_price_table_definition"><?php //echo number_format($position->getCalcPosPrice(), 2, ',', '.')." ".$waehrung;?></td>-->
                        <!--<td class="<?php //echo $tdClass; ?> appartment_price_table_definition"><?php //echo number_format($position->getMwst_wert(), 2, ',', '.')." ".$waehrung;?></td>   -->
                    </tr>
					<?php
					if (is_array($position->getRabatte()) && sizeof($position->getRabatte()) > 0) {
						$rabattkey 		= 0;
						$einzelRabatte 	= $position->getRabatteEinzelPrice();
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
			        					<td colspan="5">
			        						<?php //echo $rabatt->getBezeichnung(); ?>
			    						<?php
			    						if ($rabatt->getRabatt_art() == RS_IB_Model_BuchungRabatt::RABATT_ART_COUPON) {
			    							_e("coupon", 'indiebooking');
			    							echo " (".$rabatt->getBezeichnung().")";
			    						} else {
			    							echo $rabatt->getBezeichnung();
			    						}
			    						?>
			        					</td>
	                                   	<td class="<?php echo $tdClass; ?> appartment_price_table_definition">
	                                   		<?php echo $rabattVz." "; ?><?php echo number_format($rabatt->getRabatt_wert(), 2, ',', '.')." ".$wertTyp;?>
	                                   	</td>
	                                   	<td> &nbsp;</td>
	                               </tr>
   								<?php
					       			} else {
// 					       				$basisPrice		= number_format($lastRabatt['basis'], 2, ',', '.')." ".$waehrung;
					       				$basisPrice		= number_format($einzelRabatte[$rabattkey]['basis'], 2, ',', '.')." ".$waehrung;
					       				$description 	= "";
					       				if ($rabatt->getRabatt_art() == RS_IB_Model_BuchungRabatt::RABATT_ART_COUPON) {
					       					$description = __("coupon", 'indiebooking');
					       					$description = $description . " (".$rabatt->getBezeichnung().")";
					       				} else {
					       					$description = $rabatt->getBezeichnung();
					       				}
					       				$rabattkey++;
					       				$priceDesc 		= $rabattVz . " " . number_format($rabatt->getRabatt_wert(), 2, ',', '.')." ".$wertTyp;
					       				?>
				                        <tr>
				    						<td colspan="7" style="font-style: italic;">
				    							*<?php printf(__("%s %s considered (before %s)", "indiebooking"), $description, $priceDesc, $basisPrice); ?>
				    						</td>
				                       	</tr>
								       	<?php
					       			}
					       		}
					   		} else {
					   		?>
	                        <tr>
	    						<td colspan="7" style="font-style: italic;">
	    							*<?php printf(__("Discount according to price season from %s %s considered (before %s)", "indiebooking"),number_format($rabatt->getRabatt_wert(), 2, ',', '.'),  $wertTyp, $originalPrice); ?>
	    						</td>
	                       	</tr>
					       	<?php
					       	}
					   }
					}
                }
                $specialTeilsummeClass = "background_grey";
                if (is_array($teilKopf->getRabatte()) && sizeof($teilKopf->getRabatte()) > 0) {
                	$specialTeilsummeClass = "rabattierteteilsummenzeile";
                	$currentTeilsumme = $teilKopf->getOriCalcPrice();
                } else {
                	$currentTeilsumme = $teilKopf->getCalculatedPrice();
                }
                if ((sizeof($buchungKopf->getTeilkoepfe()) > 1) || (is_array($teilKopf->getRabatte()) && sizeof($teilKopf->getRabatte()) > 0)) {
                	?>
               	<tr>
					<th colspan="5" class="<?php echo $specialTeilsummeClass; ?>"><?php _e("subtotal", 'indiebooking');?></th>
                   	<th class="<?php echo $tdClass; ?> appartment_price_table_definition <?php echo $specialTeilsummeClass; ?>">
                   		<?php echo number_format($currentTeilsumme, 2, ',', '.')." ".$waehrung;?>
                   	</th>
                    <th class="<?php echo $specialTeilsummeClass; ?>">&nbsp;</th>
               </tr>
               <?php
                }
               if (is_array($teilKopf->getRabatte()) && sizeof($teilKopf->getRabatte()) > 0) {
               	foreach ($teilKopf->getRabatte() as $rabatt) {
               		$rabattVz = "-";
               		if ($rabatt->getRabatt_typ() == 1) {
               			$wertTyp = rs_ib_currency_util::getCurrentCurrency();
               		} elseif ($rabatt->getRabatt_typ() == 2) {
               			$wertTyp = "%";
               		}
               		?>
                       	<tr>
        					<td colspan="5">
        						<?php //echo $rabatt->getBezeichnung(); ?>
    						<?php
    						if ($rabatt->getRabatt_art() == RS_IB_Model_BuchungRabatt::RABATT_ART_COUPON) {
    							_e("coupon", 'indiebooking');
    							echo " (".$rabatt->getBezeichnung().")";
    						} else {
    							echo $rabatt->getBezeichnung();
    						}
    						?>
        					</td>
                           	<td class="<?php echo $tdClass; ?> appartment_price_table_definition"><?php echo $rabattVz." "; ?><?php echo number_format($rabatt->getRabatt_wert(), 2, ',', '.')." ".$wertTyp;?></td>
                           	<td> &nbsp;</td>
                       </tr>
    				<?php
    			   }
    			?>
               	<tr>
					<th colspan="5" class="background_grey"><?php _e("subtotal", 'indiebooking');?></th>
                   	<th class="<?php echo $tdClass; ?> appartment_price_table_definition background_grey">
                   		<?php echo number_format($teilKopf->getCalculatedPrice(), 2, ',', '.')." ".$waehrung;?>
                   	</th>
                    <th class="background_grey">&nbsp;</th>
               </tr>
               <?php
    			}
            }
            ?>
           	<tr>
				<th colspan="5" class="background_lightgreen"><?php _e("sum", 'indiebooking');?></th>
               	<th class="<?php echo $tdClass; ?> appartment_price_table_definition background_lightgreen"><?php echo number_format($buchungKopf->getFullPrice(), 2, ',', '.')." ".$waehrung;?></th>
               	<td class="background_lightgreen">&nbsp;</td>
           </tr>
            <?php
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
                        ?>
                       	<tr>
    						<td colspan="5">
    						<?php
//     							echo $rabatt->getBezeichnung();
    						?>
    						<?php
    						if ($rabatt->getRabatt_art() == RS_IB_Model_BuchungRabatt::RABATT_ART_COUPON) {
    							_e("coupon", 'indiebooking');
    							echo " (".$rabatt->getBezeichnung().")";
    						} else {
    							echo $rabatt->getBezeichnung();
    						}
    						?>
    						</td>
                           	<td class="<?php echo $tdClass; ?> appartment_price_table_definition"><?php echo $rabattVz." "; ?><?php echo number_format($rabatt->getRabatt_wert(), 2, ',', '.')." ".$wertTyp;?></td>
                           	<td> &nbsp;</td>
                       </tr>
				<?php
			       }
			   }
			}
            ?>
           	<tr>
				<th colspan="5"><?php _e("invoice amount", 'indiebooking'); ?></th>
               	<th class="<?php echo $tdClass; ?> appartment_price_table_definition"><?php echo number_format($buchungKopf->getCalculatedPrice(), 2, ',', '.')." ".$waehrung;?></th>
               	<td> &nbsp;</td>
           	</tr>
           	<?php
//            	var_dump($buchungKopf->getFullMwstArray());
//            	foreach ($buchungKopf->getFullMwstArray() as $key => $value) {
           	foreach ($buchungKopf->getFullMwstArray() as $mwstObj) {
           		if (!is_null($mwstObj->getMwst_prozent()) && $mwstObj->getMwst_prozent() > 0) {
           	   ?>
               	<tr>
    				<!-- <th colspan="5"><?php //echo $mwstObj->getMwst_prozent();?>%</th> -->
    				<th colspan="5">
    				<?php
    				 printf(__("incl. %s %s VAT", "indiebooking"), $mwstObj->getMwst_prozent(), '%');
    				 ?>
    				 </th>
                   	<th class="<?php echo $tdClass; ?> appartment_price_table_definition"><?php echo number_format($mwstObj->getMwst_wert(), 2, ',', '.')." ".$waehrung;?></th>
                   	<td> &nbsp;</td>
               	</tr>
           	   <?php
           		}
           	}
           	foreach ($buchungKopf->getZahlungen() as $zahlung) {
           	    ?>
               	<tr>
    				<th colspan="5">
    				<?php
    					if ($zahlung->getZahlungart() == RS_IB_Model_BuchungZahlung::ZAHLART_GUTSCHEIN) {
    						_e("voucher", 'indiebooking');
    						echo " (".$zahlung->getBezeichnung().")";
    					} else {
    						echo $zahlung->getBezeichnung();
    					}
    				?>
    				</th>
                   	<th class="<?php echo $tdClass; ?> appartment_price_table_definition"><?php echo "- ".number_format($zahlung->getZahlungbetrag(), 2, ',', '.')." ".$waehrung;?></th>
                   	<td> &nbsp;</td>
               	</tr>
           	   <?php
           	}
           	?>
           	<tr>
				<th colspan="5" class="background_lightgreen"><?php _e("payment amount", 'indiebooking'); ?></th>
               	<th class="<?php echo $tdClass; ?> appartment_price_table_definition background_lightgreen"><?php echo number_format($buchungKopf->getZahlungsbetrag(), 2, ',', '.')." ".$waehrung;?></th>
               	<td class="background_lightgreen">&nbsp;</td>
           	</tr>
			<?php
           	if ($buchungKopf->getAnzahlungsbetrag() > -1) {
           	?>
			<tr>
				<th colspan="5" class="background_lightgreen"><?php _ex("deposit", 'anzahlung', 'indiebooking'); ?></th>
               	<th class="<?php echo $tdClass; ?> appartment_price_table_definition background_lightgreen">
               		<?php echo number_format($buchungKopf->getAnzahlungsbetrag(), 2, ',', '.')." ".$waehrung;?>
               	</th>
               	<td class="background_lightgreen">&nbsp;</td>
           	</tr>
           	<?php } ?>
            </tbody>
        </table>
<!--     </div> -->
</div>
<div id="detail_paymentbox_xs">
    <h5 style="padding-bottom:10px;">Ihre Buchungsnummer: <?php echo $buchungKopf->getBuchung_nr(); ?></h5>
        <table id="price_display_table1" class="table table-condensed table-responsive price_table">
            <thead>
            	<tr>
                	<th style="width: 120px; max-width: 120px;">&nbsp;</th>
                    <th class="myTableRow appartment_price_table_definition"><?php _e('Gross', 'indiebooking');?></th>
                </tr>
            </thead>
            <tbody>
            <?php
            if (!isset($waehrung)) {
                $waehrung   = rs_ib_currency_util::getCurrentCurrency();
            }
            $tdClass        = "";
            $tdvorzeichen   = "";
            foreach ($buchungKopf->getTeilkoepfe() as $teilKopf) {
                ?>
                <tr>
                	<th colspan="2" class="text_center"><h5><?php _e("booking part", 'indiebooking');?>&nbsp;-&nbsp;<?php echo $teilKopf->getTeilbuchung_id(); ?>&nbsp;-&nbsp;<?php echo $teilKopf->getAppartment_name(); ?></h5></th>
                </tr>
                <?php
                foreach ($teilKopf->getPositionen() as $position) {
//                     $preisVonDt = datetime::createFromFormat("Y-m-d h:i:s", $position->getPreis_von());
                    $preisVon   = $position->getPreis_von()->format('d.m.Y');
//                     $preisBisDt = datetime::createFromFormat("Y-m-d h:i:s", $position->getPreis_bis());
                    $preisBis   = $position->getPreis_bis()->format('d.m.Y');
                    ?>
        			<?php
        			$position->calculateExpelPrice();
		            $posFullPrice    = $position->getAusschreibFullPrice();
        			$posEinzelPreis  = $position->getAusschreibEinzelPrice();
        			?>
                    <tr>
                    	<td><?php echo $position->getPosition_typ(); ?></td>
                        <td class="<?php echo $tdClass; ?> appartment_price_table_definition"><?php echo number_format($posFullPrice, 2, ',', '.')." ".$waehrung;?></td>
                    </tr>
					<?php
					if (is_array($position->getRabatte()) && sizeof($position->getRabatte()) > 0) {
					   foreach ($position->getRabatte() as $rabatt) {
					   	if ($rabatt->getRabatt_ausschreiben_kz() == 1 && ($rabatt->getRabatt_art() != RS_IB_Model_BuchungRabatt::RABATT_ART_DEGRESSION) ) {
        				       $rabattVz = "-";
        				       if ($rabatt->getPlus_minus_kz() == 2) {
        				           $rabattVz = "";
        				       }
    					       if ($rabatt->getRabatt_typ() == 1) {
    					           $wertTyp = rs_ib_currency_util::getCurrentCurrency();
    					       } elseif ($rabatt->getRabatt_typ() == 2) {
    					           $wertTyp = "%";
    					       }
    					       ?>
                               	<tr>
    								<td><?php echo $rabatt->getBezeichnung(); ?></td>
                                   	<td class="<?php echo $tdClass; ?> appartment_price_table_definition"><?php echo $rabattVz." "; ?><?php echo number_format($rabatt->getRabatt_wert(), 2, ',', '.')." ".$wertTyp;?></td>
                               </tr>
   						<?php
                            }
					   }
					}
                }
                $specialTeilsummeClass = "background_grey";
                if (is_array($teilKopf->getRabatte()) && sizeof($teilKopf->getRabatte()) > 0) {
                	$specialTeilsummeClass = "rabattierteteilsummenzeile";
                	$currentTeilsumme = $teilKopf->getOriCalcPrice();
                } else {
                	$currentTeilsumme = $teilKopf->getCalculatedPrice();
                }
                ?>
               	<tr>
					<th class="<?php echo $specialTeilsummeClass; ?>"><?php _e("subtotal", 'indiebooking');?></th>
                   	<th class="<?php echo $tdClass; ?> appartment_price_table_definition <?php echo $specialTeilsummeClass; ?>">
                   		<?php echo number_format($currentTeilsumme, 2, ',', '.')." ".$waehrung;?>
                   	</th>
               </tr>
			<?php
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
                            ?>
                           	<tr>
            					<td><?php echo $rabatt->getBezeichnung(); ?></td>
                               	<td class="<?php echo $tdClass; ?> appartment_price_table_definition"><?php echo $rabattVz." "; ?><?php echo number_format($rabatt->getRabatt_wert(), 2, ',', '.')." ".$wertTyp;?></td>
                           </tr>
    				<?php
                       }
    			   }
    			   ?>
	               	<tr>
						<th class="background_grey"><?php _e("subtotal", 'indiebooking');?></th>
	                   	<th class="<?php echo $tdClass; ?> appartment_price_table_definition background_grey">
	                   		<?php echo number_format($teilKopf->getCalculatedPrice(), 2, ',', '.')." ".$waehrung;?>
	                   	</th>
	               </tr>
				<?php
    			}
            }
            ?>
           	<tr>
				<th class="background_lightgreen"><?php _e("sum", 'indiebooking');?></th>
               	<th class="<?php echo $tdClass; ?> appartment_price_table_definition background_lightgreen"><?php echo number_format($buchungKopf->getFullPrice(), 2, ',', '.')." ".$waehrung;?></th>
           </tr>
            <?php
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
                        ?>
                       	<tr>
    						<td><?php echo $rabatt->getBezeichnung(); ?></td>
                           	<td class="<?php echo $tdClass; ?> appartment_price_table_definition"><?php echo $rabattVz." "; ?><?php echo number_format($rabatt->getRabatt_wert(), 2, ',', '.')." ".$wertTyp;?></td>
                       </tr>
				<?php
                    }
			   }
			}
            ?>
           	<tr>
				<th><?php _e("invoice amount", 'indiebooking'); ?></th>
               	<th class="<?php echo $tdClass; ?> appartment_price_table_definition"><?php echo number_format($buchungKopf->getCalculatedPrice(), 2, ',', '.')." ".$waehrung;?></th>
           	</tr>
           	<?php
//            	var_dump($buchungKopf->getFullMwstArray());
//            	foreach ($buchungKopf->getFullMwstArray() as $key => $value) {
           	foreach ($buchungKopf->getFullMwstArray() as $mwstObj) {
           	   ?>
               	<tr>
    				<!-- <th><?php //echo $mwstObj->getMwst_prozent();?>%</th>-->
    				<th>
    				<?php
    				printf(__("incl. %s %s VAT", "indiebooking"), $mwstObj->getMwst_prozent(), '%');
    				 ?>
    				 </th>
                   	<th class="<?php echo $tdClass; ?> appartment_price_table_definition"><?php echo number_format($mwstObj->getMwst_wert(), 2, ',', '.')." ".$waehrung;?></th>
               	</tr>
           	   <?php
           	}
           	foreach ($buchungKopf->getZahlungen() as $zahlung) {
           	    ?>
               	<tr>
    				<th>
    				<?php
    					if ($zahlung->getZahlungart() == RS_IB_Model_BuchungZahlung::ZAHLART_GUTSCHEIN) {
    						_e("voucher", 'indiebooking');
    						echo " (".$zahlung->getBezeichnung().")";
    					} else {
    						echo $zahlung->getBezeichnung();
    					}
    				?>
    				</th>
                   	<th class="<?php echo $tdClass; ?>"><?php echo number_format($zahlung->getZahlungbetrag(), 2, ',', '.');?></th>
               	</tr>
           	   <?php
           	}
           	?>
           	<tr>
				<th class="background_lightgreen"><?php _e("payment amount", 'indiebooking'); ?></th>
               	<th class="<?php echo $tdClass; ?> appartment_price_table_definition background_lightgreen"><?php echo number_format($buchungKopf->getZahlungsbetrag(), 2, ',', '.')." ".$waehrung;?></th>
           	</tr>
			<?php
           	if ($buchungKopf->getAnzahlungsbetrag() > -1) {
           	?>
			<tr>
				<th class="background_lightgreen"><?php _ex("deposit", 'anzahlung', 'indiebooking'); ?></th>
               	<th class="<?php echo $tdClass; ?> appartment_price_table_definition background_lightgreen">
               		<?php echo number_format($buchungKopf->getAnzahlungsbetrag(), 2, ',', '.')." ".$waehrung;?>
               	</th>
           	</tr>
           	<?php } ?>
            </tbody>
        </table>
<!--     </div> -->
</div>
<?php
    $args = array(
        'small'         => false,
        'showCouponBox' => $showCouponBox
    );
    do_action("rs_indiebooking_show_extra_payment_info", $args);
?>
<!-- </div> -->