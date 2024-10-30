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
<div class="panel panel-default">
	<div class="panel-body">
        <table id="price_display_table_small">
            <thead>
<!--             	<tr> -->
            		<!-- <th colspan="2">Buchung - <?php //echo $buchungKopf->getBuchung_nr(); ?></th> -->
<!--             	</tr> -->
            </thead>
            <tbody>
            <?php
            if (!isset($waehrung)) {
                $waehrung   = rs_ib_currency_util::getCurrentCurrency();
            }
            $tdClass        = "right_td";
            $tdvorzeichen   = "";
            ?>
            <?php
            foreach ($buchungKopf->getTeilkoepfe() as $teilKopf) {
                ?>
                	<tr>
                		<th class="teilkopfheader" colspan="2">
                			<?php
                			if (!$showCategoryAsName) {
                				echo $teilKopf->getAppartment_name();
                			} else {
                				echo $teilKopf->getAppartment_category_name();
                			}
                			?>
                		</th>
                	</tr>
                	<tr>
                		<th class="text_darkgreen"><?php _e('booking from', 'indiebooking');?></th>
                		<td class="<?php echo $tdClass; ?> text_darkgreen"><?php echo $teilKopf->getTeilbuchung_von()->format('d.m.Y'); ?></td>
                	</tr>
                	<tr>
                		<th class="text_darkgreen"><?php _e('booking to', 'indiebooking');?></th>
                		<td class="<?php echo $tdClass; ?> text_darkgreen"><?php echo $teilKopf->getTeilbuchung_bis()->format('d.m.Y'); ?></td>
                	</tr>
                	<tr>
                		<th class="text_darkgreen"><?php _e('number of nights', 'indiebooking');?></th>
                		<td class="<?php echo $tdClass; ?> text_darkgreen"><?php echo $teilKopf->getNumberOfNights(); ?></td>
                	</tr>
                
<!--                 <tr> -->
                	<!-- <th colspan="7"><?php //_e("booking part", 'indiebooking');?>&nbsp;-&nbsp;<?php //echo $teilKopf->getTeilbuchung_id(); ?>&nbsp;-&nbsp;<?php //echo $teilKopf->getAppartment_name(); ?></th> -->
<!--                 </tr> -->
                <?php
//                 var_dump($teilKopf->getPositionen());
                $teilbuchungPositionen  = $teilKopf->getPositionen();
//                 $teilbuchungPositionen  = array_reverse($teilbuchungPositionen);
                foreach ($teilbuchungPositionen as $position) {
                	$specialCssClass 	= "";
                    $preisVon   		= $position->getPreis_von()->format('d.m.Y');
                    $preisBis   		= $position->getPreis_bis()->format('d.m.Y');
                    ?>
                	<tr>
                		<th colspan="2" class="price_display_subhead" style="text-align: center;">
                			<?php
                			$label = $position->getPosition_typ();
                			switch (strtoupper($label)) {
                				case "APPARTMENT_PRICE":
                					$label = __("Apartment price", "indiebooking");
                					break;
                				case "APPARTMENT_OPTION":
                					$label = __("Apartment option", "indiebooking");
                					break;
                				default:
                					$label = $label;
                					break;
                			}
                			if ($position->getPosition_typ() == "appartment_option") {
                			     $label = $label .  " - " . $position->getBezeichnung();
                			}
                			echo $label;
                			 ?>
                		</th>
                	</tr>
                	<?php
                		$position->calculateExpelPrice();
                	?>
                	<?php
                	if ($position->getBerechnung_type() == RS_IB_Model_Buchungposition::RS_BERECHNUNG_TYP_PREISPRONACHT ||
                		$position->getBerechnung_type() == RS_IB_Model_Buchungposition::RS_BERECHNUNG_TYP_PREISPROPERSONUNDNACHT ||
                		$position->getBerechnung_type() == RS_IB_Model_Buchungposition::RS_BERECHNUNG_TYP_PREISPROQMUNDNACHT) {
                	?>
	                	<tr>
	                		<th><?php _e('price from', 'indiebooking');?></th>
	                		<td class="<?php echo $tdClass; ?>"><?php echo $preisVon; ?></td>
	                	</tr>
	                	<tr>
	                		<th><?php _e('price to', 'indiebooking');?></th>
	                		<td class="<?php echo $tdClass; ?>"><?php echo $preisBis; ?></td>
	                	</tr>
                	<?php
                	}
                	?>
                	<?php
                	if ($position->getBerechnung_type() != RS_IB_Model_Buchungposition::RS_BERECHNUNG_TYP_SUMME) {
//                 			&&
//                 		$position->getBerechnung_type() != RS_IB_Model_Buchungposition::RS_BERECHNUNG_TYP_PREISPROPERSON &&
//                 		$position->getBerechnung_type() != RS_IB_Model_Buchungposition::RS_BERECHNUNG_TYP_PREISPROQM) {
                		$priceLabel = "";
                		switch ($position->getBerechnung_type()) {
                			case RS_IB_Model_Buchungposition::RS_BERECHNUNG_TYP_PREISPRONACHT:
                				$priceLabel = __('Price/Night', 'indiebooking');
                				break;
                			case RS_IB_Model_Buchungposition::RS_BERECHNUNG_TYP_PREISPROPERSONUNDNACHT:
                				$priceLabel = __('Price/Night & Person', 'indiebooking');
                				break;
                			case RS_IB_Model_Buchungposition::RS_BERECHNUNG_TYP_PREISPROQMUNDNACHT:
                				$priceLabel = __('Price/Night & Qm', 'indiebooking');
                				break;
							case RS_IB_Model_Buchungposition::RS_BERECHNUNG_TYP_PREISPROPERSON:
                				$priceLabel = __('Price/Person', 'indiebooking');
                				break;
                			case RS_IB_Model_Buchungposition::RS_BERECHNUNG_TYP_PREISPROQM:
                				$priceLabel = __('Price/Qm', 'indiebooking');
                				break;
                		}
                		if ($position->getHasDegression() == true || sizeof($position->getRabatteEinzelPrice()) > 0) {
                			$specialCssClass = "ibui_linethrought_price";
                		}
                		?>
	                	<tr>
	                		<th class="<?php echo $specialCssClass; ?>"><?php echo $priceLabel; ?></th>
	                		<td class="<?php echo $tdClass; ?> <?php echo $specialCssClass; ?> appartment_price_table_definition">
	                			<?php
	        		            $einzelpreis    = $position->getAusschreibEinzelPrice();
// 	                			$einzelpreis     = $position->getEinzelpreis();
	//                             $einzelpreis    = $this->getFullPrice();
	                			?>
	                			<?php echo number_format($einzelpreis, 2, ',', '.')." ".$waehrung;?>
	            			</td>
	                	</tr>
                	<?php
                		if ($position->getHasDegression() == true) {
                			$specialCssClass	= "";
	                		if (sizeof($position->getRabatteEinzelPrice()) > 0) {
	                			$specialCssClass = "ibui_linethrought_price";
	                		}
                			?>
		                	<tr>
		                		<th class="ibui_degression_price">
		                			<?php
			                			$sign = rs_ib_currency_util::getCurrentCurrency();
			                			if ($position->getDegressionRabattTyp() == RS_IB_Model_BuchungRabatt::RABATT_TYP_PROZENT) {
			                				$sign = "%";
			                			}
			                			printf('%s ( -%s %s)', $priceLabel, $position->getDegressionRabattValue(), $sign);
		                			?>
		                		</th>
		                		<td class="<?php echo $tdClass; ?> <?php echo $specialCssClass; ?> appartment_price_table_definition ibui_degression_price">
		                			<?php
		                			$degEinzelpreis	= $position->getDegressionEinzelPrice();
		                			?>
		                			<?php echo number_format($degEinzelpreis, 2, ',', '.')." ".$waehrung;?>
		            			</td>
		                	</tr>
                			<?php
                		}
                		if (sizeof($position->getRabatteEinzelPrice()) > 0) {
                			foreach ($position->getRabatteEinzelPrice() as $key => $rabattPrice) {
//                 				$specialCssClass = "ibui_linethrought_price";
//                 				if ($key == (sizeof($position->getRabatteEinzelPrice())-1)) {
//                 					$specialCssClass = "";
//                 				}
                				?>
			                	<tr>
			                		<th class="rs_indiebooking_rabatt_bezeichnung">
			                			<?php
				                			$sign = rs_ib_currency_util::getCurrentCurrency();
				                			$plusMinusKz = $rabattPrice['plusMinusKz'];
				                			if ($plusMinusKz == 1) {
				                				$plusMinusKz = '-';
				                			} else {
				                				$plusMinusKz = '+';
				                			}
				                			/*
				                			$rabattPreis = array(
                								'price' => $rabattEinzelPreis,
                								'rabattValue' => $rabatt->getRabatt_wert(),
                								'rabattTyp' => $rabatt->getRabatt_typ(),
                								'description' => $rabatt->getBezeichnung(),
                								'rabattArt' => $rabatt->getRabatt_art(),
                							);
				                			 */
				                			if ($rabattPrice['rabattTyp'] == RS_IB_Model_BuchungRabatt::RABATT_TYP_PROZENT) {
				                				$sign = "%";
				                			}
				                			$artDesc = "";
				                			if ($rabattPrice['rabattArt'] == 2) {
				                				$artDesc = __("Coupon", "indiebooking");
				                			}
				                			printf('%s (%s)', $artDesc, $rabattPrice['description']);
			                			?>
			                		</th>
			                		<td class="<?php echo $tdClass; ?> appartment_price_table_definition">
			                			<?php echo $plusMinusKz .' '.number_format($rabattPrice['rabattValue'], 2, ',', '.')." ".$sign;?>
			            			</td>
			                	</tr>
	                			<?php
                			}
                			?>
		                	<tr>
		                		<th class="rs_indiebooking_rabatt_bezeichnung ibui_degression_price">
		                			<?php
		                			$rabattPrices		= $position->getRabatteEinzelPrice();
		                			$endPricePerNight 	= $rabattPrices[sizeof($rabattPrices)-1];
				                	$sign 				= rs_ib_currency_util::getCurrentCurrency();
				                		/*
				                		$rabattPreis = array(
                							'price' => $rabattEinzelPreis,
                							'rabattValue' => $rabatt->getRabatt_wert(),
                							'rabattTyp' => $rabatt->getRabatt_typ(),
                							'description' => $rabatt->getBezeichnung(),
                							'rabattArt' => $rabatt->getRabatt_art(),
                						);
				                		 */
				                		printf('%s', $priceLabel);
			                			?>
		                		</th>
		                		<td class="<?php echo $tdClass; ?> appartment_price_table_definition ibui_degression_price">
		                			<?php echo number_format($endPricePerNight['price'], 2, ',', '.')." ".$sign;?>
		            			</td>
		                	</tr>
                			<?php
                		}
                	}
                	?>
                	<tr>
                		<th><?php _e('price', 'indiebooking');?></th>
                		<td class="<?php echo $tdClass; ?> appartment_price_table_definition appartment_full_price_table_definition">
                			<?php
        		            $posPrice    = $position->getAusschreibFullPrice();
                			?>
                			<?php echo number_format($posPrice, 2, ',', '.')." ".$waehrung;?>
            			</td>
                	</tr>
					<?php
					if (is_array($position->getRabatte()) && sizeof($position->getRabatte()) > 0) {
					   	foreach ($position->getRabatte() as $rabatt) {
							if ($rabatt->getBerechnung_art() != 4) {
								/*
								 * die Berechnungsart 4 (= position) wurde bereits in der Methode calculateExpelPrice
								 * beruecksichtigt und auf der entsprechenden Position dargestellt. Daher darf der Rabatt
								 * an dieser stelle nicht erneut ausgeschrieben werden.
								 */
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
	    								<td class="rs_indiebooking_rabatt_bezeichnung">
	    									<?php //echo $rabatt->getBezeichnung(); ?>
				    						<?php
				    						if ($rabatt->getRabatt_art() == RS_IB_Model_BuchungRabatt::RABATT_ART_COUPON) {
				    							_e("Coupon", 'indiebooking');
				    							echo " (".$rabatt->getBezeichnung().")";
				    						} else {
				    							echo $rabatt->getBezeichnung();
				    						}
				    						?>
	    								</td>
	                                   	<td class="<?php echo $tdClass; ?> appartment_price_table_definition">
	                                   		<?php echo $rabattVz." "; ?><?php echo number_format($rabatt->getRabatt_wert(), 2, ',', '.')." ".$wertTyp;?>
	                                   	</td>
	                               </tr>
	       						<?php
						       	}
							}
					   }
					}
                } //foreach teilbuchung
                $specialTeilsummeClass = "teilsummenzeile text_bold";
                if (is_array($teilKopf->getRabatte()) && sizeof($teilKopf->getRabatte()) > 0) {
                	$specialTeilsummeClass = "rabattierteteilsummenzeile";
                	$currentTeilsumme = $teilKopf->getOriCalcPrice();
                } else {
                	$currentTeilsumme = $teilKopf->getCalculatedPrice();
                }
                if ((sizeof($buchungKopf->getTeilkoepfe()) > 1) || (is_array($teilKopf->getRabatte()) && sizeof($teilKopf->getRabatte()) > 0)) {
                ?>
               	<tr>
					<th class="<?php echo $specialTeilsummeClass; ?>"><?php _e("subtotal", 'indiebooking');?></th>
                   	<th class="<?php echo $tdClass; ?> <?php echo $specialTeilsummeClass; ?> appartment_price_table_definition">
                   		<?php //echo number_format($teilKopf->getCalculatedPrice(), 2, ',', '.')." ".$waehrung;?>
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
                            ?>
                           	<tr>
            					<td class="rs_indiebooking_rabatt_bezeichnung">
            						<?php //echo $rabatt->getBezeichnung(); ?>
		    						<?php
		    						if ($rabatt->getRabatt_art() == RS_IB_Model_BuchungRabatt::RABATT_ART_COUPON) {
		    							_e("Coupon", 'indiebooking');
		    							echo " (".$rabatt->getBezeichnung().")";
		    						} else {
		    							echo $rabatt->getBezeichnung();
		    						}
		    						?>
            					</td>
                               	<td class="<?php echo $tdClass; ?> appartment_price_table_definition">
                               		<?php echo $rabattVz." "; ?><?php echo number_format($rabatt->getRabatt_wert(), 2, ',', '.')." ".$wertTyp;?>
                               	</td>
                           </tr>
    				<?php
                        }
    			   }
    			   ?>
	               	<tr>
						<th class="teilsummenzeile text_bold"><?php _e("subtotal", 'indiebooking');?></th>
	                   	<th class="<?php echo $tdClass; ?> teilsummenzeile appartment_price_table_definition text_bold">
	                   		<?php //echo number_format($teilKopf->getCalculatedPrice(), 2, ',', '.')." ".$waehrung;?>
	                   		<?php echo number_format($teilKopf->getCalculatedPrice(), 2, ',', '.')." ".$waehrung;?>
	                   	</th>
	               </tr>
	                <?php
    			}
    			?>
			<?php
            }
            ?>
           	<tr>
				<th class="fullprice_td"><?php _e("sum", 'indiebooking');?></th>
               	<th class="<?php echo $tdClass; ?> fullprice_td appartment_price_table_definition">
               		<?php echo number_format($buchungKopf->getFullPrice(), 2, ',', '.')." ".$waehrung;?>
               	</th>
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
    						<td class="rs_indiebooking_rabatt_bezeichnung">
	    						<?php
	    						if ($rabatt->getRabatt_art() == RS_IB_Model_BuchungRabatt::RABATT_ART_COUPON) {
	    							_e("Coupon", 'indiebooking');
	    							echo " (".$rabatt->getBezeichnung().")";
	    						} else {
	    							echo $rabatt->getBezeichnung();
	    						}
	    						?>
    						</td>
                           	<td class="<?php echo $tdClass; ?> appartment_price_table_definition">
                           		<?php echo $rabattVz." "; ?>
                           		<?php echo number_format($rabatt->getRabatt_wert(), 2, ',', '.')." ".$wertTyp;?>
                           	</td>
                       </tr>
				<?php
                    }
			   }
			}
            ?>
           	<tr>
				<th class="fullprice_td text_bold"><?php _e("invoice amount", 'indiebooking'); ?></th>
               	<th class="<?php echo $tdClass; ?> fullprice_td appartment_price_table_definition text_bold">
               		<?php echo number_format($buchungKopf->getCalculatedPrice(), 2, ',', '.')." ".$waehrung;?>
               	</th>
           	</tr>
           	<?php
//            	var_dump($buchungKopf->getFullMwstArray());
//            	foreach ($buchungKopf->getFullMwstArray() as $key => $value) {
           	foreach ($buchungKopf->getFullMwstArray() as $mwstObj) {
           	   ?>
               	<tr>
               		<!--
    				<th>
    					<?php //_e("incl.", 'indiebooking')?><?php //echo $mwstObj->getMwst_prozent();?>%
    				</th>
    				 -->
    				 <th>
    				 <?php
    				 printf(__("incl. %s %s VAT", "indiebooking"), $mwstObj->getMwst_prozent(), '%');
    				 ?>
    				 </th>
                   	<th class="<?php echo $tdClass; ?> appartment_price_table_definition">
                   		<?php echo number_format($mwstObj->getMwst_wert(), 2, ',', '.')." ".$waehrung;?>
                   	</th>
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
						//echo $zahlung->getBezeichnung();
    					?>
    				</th>
                   	<th class="<?php echo $tdClass; ?> appartment_price_table_definition">
                   		<?php
                   		echo "- ".number_format($zahlung->getZahlungbetrag(), 2, ',', '.')." ".$waehrung;
                   		?>
                   	</th>
               	</tr>
           	   <?php
           	}
           	?>
           	<tr>
				<th ><?php _e("payment amount", 'indiebooking'); ?></th>
               	<th class="<?php echo $tdClass; ?> appartment_price_table_definition">
               		<?php echo number_format($buchungKopf->getZahlungsbetrag(), 2, ',', '.')." ".$waehrung;?>
               	</th>
           	</tr>
           	<?php
           	if ($buchungKopf->getAnzahlungsbetrag() > -1) {
           	?>
			<tr>
				<th ><?php _e("deposit", 'indiebooking'); ?></th>
               	<th class="<?php echo $tdClass; ?> appartment_price_table_definition">
               		<?php echo number_format($buchungKopf->getAnzahlungsbetrag(), 2, ',', '.')." ".$waehrung;?>
               	</th>
           	</tr>
           	<?php } ?>
            </tbody>
        </table>
    </div>
    <?php
    $args = array(
        'small'         => true
    );
    do_action("rs_indiebooking_show_extra_payment_info", $args);
//     do_action("rs_indiebooking_show_extra_payment_info");
    ?>
</div>