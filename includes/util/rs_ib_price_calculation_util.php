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
// if ( ! class_exists( 'rs_ib_price_calculation_util' ) ) :
/**
 * @author schmitt
 * @deprecated because the method calcPrice was moved to RS_IB_Table_Appartment_Buchung
 */
class rs_ib_price_calculation_util
{
    /**
     * Berechnet den Brutto-, Netto & Steuerpreis
     * Diese Methode ist Analog der calcPrice-Methode in der appartment_buchung.js
     * Es werden beide Methoden benuetigt, um im Frontend den Preis schnell berechnen und anzeigen zu kuennen
     * im Backend jedoch die sicherheit vor manipulationen zu gewuehrleisten
     *
     * @param unknown $price
     * @param unknown $tax
     * @param unknown $priceIsNet
     * @param unknown $calcType
     * @param unknown $numberOfNights
     * @param unknown $couponsToCalc
     * @param unknown $square
     */
    public static function calcPrice($price, $tax, $priceIsNetKz, $calcType, $numberOfNights, $square) { //$couponsToCalc
        $priceObj           = array();
        $brutto				= 0;
        $calcBrutto			= 0;
        $fullBrutto			= 0;
        $fullRabatt         = 0;
        if ($price == '') {
            $price          = 0;
        } else {
            $price          = floatval(str_replace(",", ".", $price));
        }
        if ($price > 0) {
            if ($priceIsNetKz == "on") {
//                 $netto		= $value;
                $calcBrutto = ($price * (1 + $tax));
            } else {
                $calcBrutto	= $price;
//                 $netto		= ($brutto / (1 + $tax));
            }
            $calcBrutto		= round($calcBrutto, 2);
            if ($calcType == 0) {//price total
                $brutto     = $calcBrutto;
            }
            elseif ($calcType == 1) {//price / night
                $brutto     = $calcBrutto * $numberOfNights;
            }
            elseif ($calcType == 2) {//price / qm
                $brutto 	= $calcBrutto * $square;
            }
            elseif ($calcType == 3) {//price / qm / night
                $brutto     = $calcBrutto * $square * $numberOfNights;
            }
        }
        $fullBrutto         = $brutto;
//         foreach ($couponsToCalc as $coupon) {
//             $fullRabatt     = $fullRabatt + ($brutto * $coupon->getPercent()/100);
//             $brutto			= $brutto - ($brutto * $coupon->getPercent()/100);
//         }
        
        $fullBrutto             = round($fullBrutto, 2);
        $priceObj['calcBrutto'] = $calcBrutto;
        $priceObj['fullBrutto'] = $fullBrutto; // round($fullBrutto * 100); //(round($fullBrutto * 100) / 100);
        $priceObj['fullNetto']  = round(($fullBrutto / ($tax+1)) ,2);
        $priceObj['fullTax']    = $priceObj['fullBrutto'] - $priceObj['fullNetto'];//round(($priceObj['fullBrutto'] / ($tax+1)),2);
        $priceObj['fullRabatt'] = $fullRabatt;
        
        $brutto                 = round($brutto, 2);
        $priceObj['brutto']     = $brutto;
        $priceObj['netto']      = round(($brutto / ($tax+1)) ,2);
        $priceObj['tax']        = $priceObj['brutto'] - $priceObj['netto']; //round(($priceObj['brutto'] / ($tax+1)) ,2);
        
        return $priceObj;
    }
    
    public static function getOptionPrices($options, $anzahlNaechte, $coupons, $priceIsNet, $couponsToCalc, $square) {
        //$sumeNaechte,
        $allOptionPrices                = array();
        foreach ($options as $option) {
            $priceObj                   = array();
            $optionObj                  = array();
            $optionFree                 = false;
            $id                         = $option['id'];
            foreach ($coupons as $myCoupon) {
                $optionIdArray          = array();
                $options                = $myCoupon->getApartmentOption();
                if (sizeof($options) > 0) {
                    foreach ($options as $testOption) {
                        if ($id == $testOption) {
                            $optionFree = true;
                            break;
                        }
                    }
                    if ($optionFree) {
                        break;
                    }
                }
            }
            $mwstPercent                = $option['mwst'];
            $tax                        = str_replace(",", ".", $mwstPercent);
            $key                        = ((String)((float)$tax));
            $tax                        = ((float)$tax)/100;
            
            $price                      = str_replace(",", ".", $option['price']);
            $price                      = (float)$price;
            $calcType                   = $option['calc'];
            
            $priceObj                   = self::calcPrice($price, $tax, $priceIsNet, $calcType, $anzahlNaechte, $square); //$couponsToCalc
            
            $optionObj['id']            = $id;
            $optionObj['price']	        = $option['price'];
            $optionObj['name']	        = $option['name'];
            $optionObj['calculation']   = $calcType;
            $optionObj['mwstPercent']   = $key;
            $optionObj['optionFree']    = $optionFree;
            $optionObj["brutto"]        = $priceObj["brutto"];
            $optionObj["netto"]         = $priceObj["netto"];
            $optionObj["tax"]           = $priceObj["tax"];
            
            array_push($allOptionPrices, $optionObj);
        }
        return $allOptionPrices;
    }

    public static function getDatePrices($dtFromdate, $dtToDate, $yearPrices, $yearlessPrices, $priceIsNet, $couponsToCalc, $aktionenToCalc) {
        $allPrices           = array();
        $anzahlTage          = date_diff($dtFromdate, $dtToDate, true);
        $allInOneTimeRange   = false;
//         $count               = 0;
        foreach ($yearPrices as $yearPrice) {
            $positionArray   = array();
            $dtPositionFrom  = 0;
            $dtPositionTo    = 0;
            $numberOfNights  = intval($anzahlTage->format('%a'));
            $netto           = 0;
            $brutto          = 0;
            $tax             = 0;
            
            $tax             = 0;
            $datePriceFrom   = DateTime::createFromFormat("d.m.Y", $yearPrice['from']);
            $datePriceTo     = DateTime::createFromFormat("d.m.Y", $yearPrice['to']);
            $datePrice       = $yearPrice['calcPrice'];
            $tax             = $yearPrice['tax'];
            $tax             = str_replace(",", ".", $tax);
            $datePrice       = str_replace(",", ".", $datePrice);
            if ($datePriceFrom <= $dtFromdate && $datePriceTo >= $dtToDate) {
                //Buchungszeitraum in einem einzigen Preiszeitraum
                $allPrices                    = array();
                $dtPositionFrom           = $dtFromdate;
                $dtPositionTo             = $dtToDate;
                $allInOneTimeRange            = true;
            }
            elseif ($datePriceFrom <= $dtFromdate) {
                //Buchungszeitraum von in einem Preiszeitraum
                $datePriceTo->add(new DateInterval('P1D'));
                $dtPositionFrom           = $dtFromdate;
                $dtPositionTo             = $datePriceTo;
            }
            elseif ($datePriceTo >= $dtToDate) {
                //Buchungszeitraum bis in einem Preiszeitraum
                $dtPositionFrom           = $datePriceFrom;
                $dtPositionTo             = $dtToDate;
            }
            elseif ($datePriceFrom > $dtFromdate && $datePriceTo < $dtToDate) {
                //Preiszeitraum komplett in Buchungszeitraum
                $datePriceTo->add(new DateInterval('P1D'));
                $dtPositionFrom           = $datePriceFrom;
                $dtPositionTo             = $datePriceTo;
            } else {
                $dtPositionFrom           = $dtFromdate;
                $dtPositionTo             = $dtToDate;
            }
            $numberOfNights = date_diff($dtPositionTo, $dtPositionFrom);
            $numberOfNights = intval($numberOfNights->format('%a'));
            if ($numberOfNights > 0) {
                $positionArray["from"]                  = $dtPositionFrom;
                $positionArray["to"]                    = $dtPositionTo;
                
                
                $positionArray["numberOfNights"]        = $numberOfNights;
                $prices                                 = self::calcPrice($datePrice, $tax, $priceIsNet, 1, $numberOfNights, 0); //$couponsToCalc
                $positionArray["brutto"]                = $prices["brutto"];
                $positionArray["netto"]                 = $prices["netto"];
                $positionArray["tax"]                   = $prices["tax"];
                
                $positionArray["fullBrutto"]            = $prices["fullBrutto"];
                $positionArray["fullNetto"]             = $prices["fullNetto"];
                $positionArray["fullTax"]               = $prices["fullTax"];
                
                $positionArray["pricePerNight"]         = $prices["calcBrutto"];
                $positionArray["mwstPercent"]           = $tax*100;
                
                array_push($allPrices, $positionArray);
                
                foreach ($aktionenToCalc as $aktionToCalc) {
                    $validAktionDates = $aktionToCalc->getValidDates();//$aktionToCalc['validDates'];
                    foreach ($validAktionDates as $validDate) {
                        $aktionPosition               = array();
                        $completeAktion               = false;
                        $calcAktionFrom               = 0;
                        $calcAktionTo                 = 0;
                        $aktionFrom                   = DateTime::createFromFormat("d.m.Y", $validDate["from"]);
                        $aktionTo                     = DateTime::createFromFormat("d.m.Y", $validDate["to"]);
                
                        if ($aktionFrom <= $dtPositionFrom && $aktionTo >= $dtPositionTo) {
                            //Gebuchter Zeitraum komplett in der Aktion
                            $calcAktionFrom           = $dtPositionFrom;
                            $calcAktionTo             = $dtPositionTo;
                            $completeAktion           = true;
                        }
                        elseif ($aktionFrom <= $dtPositionFrom) {
                            //Aktionszeitrum von in einem Preiszeitraum
                            $aktionTo->add(new DateInterval('P1D'));
                            $calcAktionFrom           = $dtPositionFrom;
                            $calcAktionTo             = $aktionTo;
                        }
                        elseif ($aktionTo >= $dtPositionTo) {
                            //Aktionszeitrum bis in einem Preiszeitraum
                            $calcAktionFrom           = $aktionFrom;
                            $calcAktionTo             = $dtPositionTo;
                        }
                        elseif ($aktionFrom > $dtFromdate && $aktionTo < $dtToDate) {
                            //Aktionszeitraum komplett in Buchungszeitraum
                            $aktionTo->add(new DateInterval('P1D'));
                            $calcAktionFrom           = $aktionFrom;
                            $calcAktionTo             = $aktionTo;
                        }
                        $actionNumberOfNights = date_diff($calcAktionTo, $calcAktionFrom);
                        $actionNumberOfNights = intval($actionNumberOfNights->format('%a'));
                        if ($actionNumberOfNights > 0) {
                            $aktionPosition["from"]             = $calcAktionFrom;
                            $aktionPosition["to"]               = $calcAktionTo;
                            $aktionPosition["numberOfNights"]   = $actionNumberOfNights;
                            $aktionPosition["brutto"]           = 0;
                            $aktionPosition["netto"]            = 0;
                            $aktionPosition["tax"]              = 0;
                
                            $aktionPosition["fullBrutto"]       = 0;
                            $aktionPosition["fullNetto"]        = 0;
                            $aktionPosition["fullTax"]          = 0;
                
                            $aktionPosition["pricePerNight"]    = 0;
                            $aktionPosition["mwstPercent"]      = 0;
                
                            array_push($allPrices, $aktionPosition);
                        }
                        if ($completeAktion) {
                            break;
                        }
                    }
                }
                
                if ($allInOneTimeRange) {
                    break;
                }
            }
        }
        return $allPrices;
    }
    
    /**
     * Ist die Analoge Methode zu "calculateLivePrice()" in der appartment_buchung.js
     */
    public static function calculateBookingPrices($dtFromdate, $dtTodate, $priceIsNet, $yearPrices, $yearlessPrices, $coupons, $options, $aktionen, $square) {
        $brutto                 = 0;
        $netto          		= 0;
        $fullNetto              = 0;
        $fullBrutto	            = 0;
        $calcBrutto             = 0;
        $tax				    = 0;
        $pricePerNight		    = 0;
        $calculatedBrutto       = 0;
        $allPrices              = array();
        $allOptionPrices        = array();
        $fullPrices             = array();
        $freeOptions            = array();
        $allTaxes               = array();
        $allPricesObj           = array();
        $fullPricesDays         = array();
        $fullPricesOption       = array();
        $fullPrices["brutto"]   = 0;
        $fullPrices["netto"]    = 0;
//         $mwstPercent				= jQuery("#price_per_night").attr('data-mwst'),
        $anzahlTage             = date_diff($dtFromdate, $dtTodate, true);
        $anzahlNaechte          = intval($anzahlTage->format('%a'));
        if ($anzahlNaechte > 0) {
            $aktionenToCalc     = array();
            $couponsToCalc      = array();
            $gutscheinToCalc    = array();
            
            
            $allPrices          = self::getDatePrices($dtFromdate, $dtTodate, $yearPrices, $yearlessPrices, $priceIsNet, $couponsToCalc, $aktionenToCalc);
            $allOptionPrices    = self::getOptionPrices($options, $anzahlNaechte, $coupons, $priceIsNet, $couponsToCalc, $square);
//             $options, $sumeNaechte, $anzahlNaechte, $coupons, $priceIsNet, $couponsToCalc, $square
            for ($int=0; $int < sizeOf($allPrices); $int++) {
                $brutto         = $brutto + $allPrices[$int]["brutto"];
            }
            for ($int=0; $int < sizeOf($allOptionPrices); $int++) {
                $brutto         = $brutto + $allOptionPrices[$int]["brutto"];
            }
            
            $fullNetto          = $netto;
            $fullBrutto         = $brutto;
            $calculatedBrutto   = $fullBrutto;
            foreach ($aktionen as $aktion) {
                $aktionOk       = true;
                if ($aktion->getConditionType() == "1") { //min. nights
//                     $aktionOk   = false;
                }
                if ($aktionOk) {
                    $validDates     = $aktion->getValidDates();
                    if (rs_ib_date_util::isDateOverlap($validDates, $dtFromdate, $dtTodate)) {
                        if ($aktion->getCombinable() == "off" && sizeof($aktionenToCalc) == 0) {
                            array_push($aktionenToCalc, $aktion);
                            break;
                        } elseif ($aktion->getCombinable() == "on") {
                            array_push($aktionenToCalc, $aktion);
                        }
                    }
                }
            }
//             var_dump($aktionenToCalc);
            if (sizeof($coupons) > 0 ) {
                foreach ($coupons as $myCoupon) {
                    if ($myCoupon->getGutscheinKz() == "off") { //Wertrabatt --> kein Gutschein
                        if ($myCoupon->getType() == 1) { //VOLL
                            $myCoupon->setPercent((100 / $calculatedBrutto) * $myCoupon->getValue());
                        } elseif ($myCoupon->getType() == 2) { //PROZENT
                            $myCoupon->setPercent($myCoupon->getValue());
                        }
                        $calculatedBrutto = $calculatedBrutto - ($calculatedBrutto * ($myCoupon->getPercent() / 100));
                        array_push($couponsToCalc, $myCoupon);
                    } else {
                        array_push($gutscheinToCalc, $myCoupon);
                    }
                }
                $allPrices          = self::getDatePrices($dtFromdate, $dtTodate, $yearPrices, $yearlessPrices, $priceIsNet, $couponsToCalc, $aktionenToCalc);
                $allOptionPrices    = self::getOptionPrices($options, $anzahlNaechte, $coupons, $priceIsNet, $couponsToCalc, $square);
            }
            
            $fullPricesDays["brutto"] 	= 0;
            $fullPricesDays["netto"] 	= 0;
            $biggestBrutto              = array();
            $biggestBrutto["wert"]      = 0;
            $biggestBrutto["index"]     = -1;
            $biggestBrutto["type"]      = 0;
//             foreach ($allPrices as $price) {
            for ($i = 0; $i < sizeof($allPrices); $i++) {
                $price                  = $allPrices[$i];
                $from                   = $price['from'];
                $from                   = $from->format('d.m.Y');
            
                $to                     = $price['to'];
                $to                     = $to->format('d.m.Y');
                
                if ($price['brutto'] > $biggestBrutto["wert"]) {
                    $biggestBrutto["wert"]      = $price['brutto'];
                    $biggestBrutto["index"]     = $i;
                    $biggestBrutto["type"]      = 1;
                }
                
                $fullPricesDays['netto']    = $fullPricesDays['netto'] + $price['netto'];
                $fullPricesDays['brutto']   = $fullPricesDays['brutto'] + $price['brutto'];
                $key                    = (String)$price['mwstPercent'];
                if (!(array_key_exists($key, $fullPrices))) {
                    $fullPrices[$key]   = $price['tax'];
                    $allTaxes[$key]     = $price['tax'];
                } else {
                    $fullPrices[$key]   = $fullPrices[$key] + $price['tax'];
                    $allTaxes[$key]     = $allTaxes[$key] + $price['tax'];
                }
            }
            $fullPrices["brutto"]       = $fullPrices["brutto"] + $fullPricesDays["brutto"];
            $fullPrices["netto"]        = $fullPrices["netto"] + $fullPricesDays["netto"];
            
            $fullPricesOption["brutto"] = 0;
            $fullPricesOption["netto"] 	= 0;
//             foreach ($allOptionPrices as $option) {
            for ($i = 0; $i < sizeof($allOptionPrices); $i++) {
                $option                 = $allOptionPrices[$i];
                $optionFree             = false;
                $netto                  = $option['netto'];
                $brutto                 = $option['brutto'];
                $taxes                  = $option['tax'];
                // 		          $key                    = $option['taxkey'];
                $mwstPercent            = $option['mwstPercent'];
                $key                    = $mwstPercent;
                foreach ($coupons as $coupon) {
                    $couponOptions = $coupon->getApartmentOption();
                    if (in_array(((String)$option['id']), $couponOptions)) {
                        $optionFree     = true;
                    }
                }
                if ($optionFree == false) {
                    $fullPricesOption["netto"]    = $fullPricesOption["netto"] + $netto;
                    $fullPricesOption["brutto"]   = $fullPricesOption["brutto"] + $brutto;
                    
                    if ($brutto > $biggestBrutto["wert"]) {
                        $biggestBrutto["wert"]      = $brutto;
                        $biggestBrutto["index"]     = $i;
                        $biggestBrutto["type"]      = 2;
                    }
                    
                    if (!(array_key_exists($key, $fullPrices))) {
                        $fullPrices[$key]     = $taxes;
                        $allTaxes[$key]       = $taxes;
                    } else {
                        $fullPrices[$key]     = $fullPrices[$key] + $taxes;
                        $allTaxes[$key]       = $allTaxes[$key] + $taxes;
                    }
                } else {
                    $freeOptions[$freeOptionsCount]['id']      = (String)$option['id'];
                    $freeOptions[$freeOptionsCount]['name']    = (String)$option['name'];
                    $netto    = 0;
                    $taxes    = 0;
                    $brutto   = 0;
                    $freeOptionsCount++;
                }
            }
            $fullPrices['netto']    = $fullPrices['netto'] + $fullPricesOption["netto"];
            $fullPrices['brutto']   = $fullPrices['brutto'] + $fullPricesOption["brutto"];

            $bruttoVorGutschein = $fullPrices['brutto'];
            if (round($calculatedBrutto, 2) <> round($bruttoVorGutschein, 2)) {
                //CENT DIFFERENZ!
                $differenz = round($calculatedBrutto, 2) - round($bruttoVorGutschein, 2);//$calculatedBrutto - $bruttoVorGutschein;
                $differenz = (round($differenz*100) / 100);
                if ($fullPricesDays["brutto"] > $fullPricesOption["brutto"]) {
                    $fullPricesDays["brutto"] = $fullPricesDays["brutto"] + ($differenz);
                } else {
                    $fullPricesOption["brutto"] = $fullPricesOption["brutto"] + ($differenz);
                }
                if ($biggestBrutto["type"] == 1) {//days biggest
                    $price                  = $allPrices[$biggestBrutto["index"]];
                    $tax                    = ($option['mwstPercent'] / 100);
                    $price['brutto']        = $price['brutto'] + ($differenz);
                    $price['netto']         = round(($price['brutto'] / ($tax+1)) ,2);
                    $price['tax']           = $price['brutto'] - $price['netto'];
                    $allPrices[$biggestBrutto["index"]] = $price;
                } elseif ($biggestBrutto["type"] == 2) { //option biggest
                    $option                 = $allOptionPrices[$biggestBrutto["index"]];
                    $tax                    = ($option['mwstPercent'] / 100);
                    $option['brutto']       = $option['brutto'] + ($differenz);
                    $option['netto']        = round(($option['brutto'] / ($tax+1)) ,2);
                    $option['tax']          = $option['brutto'] - $option['netto'];
                    $allOptionPrices[$biggestBrutto["index"]] = $option;
                }
                
                $fullPrices["brutto"]   = $calculatedBrutto;
                $calculatedNetto        = $calculatedBrutto;
                foreach ($fullPrices as $key => $fullPrice) {
                    if ($key == "netto") {
                    } elseif ($key == "brutto") {
                    } else {
                        $calculatedNetto    = $calculatedNetto - $fullPrice;
                    }
                }
                $fullPrices["netto"]	= $calculatedNetto;
            }
            $bruttoVorGutschein = $fullPrices['brutto'];
            if (is_plugin_active('indiebooking-advanced/indiebooking_advanced.php')) {
	            foreach ($coupons as $coupon) {
	                if ($coupon->getGutscheinKz() == "on") {
	                    if ($coupon->getType() == RS_IB_Model_Gutschein::GUTSCHEIN_TOTAL) {
	                        $fullPrices['brutto'] = $fullPrices['brutto'] - $coupon->getValue();
	                    } elseif ($coupon->getType() == RS_IB_Model_Gutschein::GUTSCHEIN_PERCENT) {
	                        $fullPrices['brutto'] = $fullPrices['brutto'] - (($fullPrices['brutto'] / 100) * $coupon->getValue());
	                    }
			        }
	            }
            }
            $allPricesObj["allDatePrices"]      = $allPrices;
            $allPricesObj["allOptionPrices"]    = $allOptionPrices;
            $allPricesObj["fullPricesOption"]   = $fullPricesOption;
            $allPricesObj["fullPricesDays"]     = $fullPricesDays;
            $allPricesObj["fullPrices"]         = $fullPrices;
            $allPricesObj["allTaxes"]           = $allTaxes;
            $allPricesObj["freeOptions"]        = $freeOptions;
            $allPricesObj["wertCoupons"]        = $couponsToCalc;
            $allPricesObj["gutschein"]          = $gutscheinToCalc;
            $allPricesObj["fullBrutto"]         = $fullBrutto;
            $allPricesObj["bruttoVorGutschein"] = $bruttoVorGutschein;
            $allPricesObj["numberOfNights"]     = $anzahlNaechte;
            
            return $allPricesObj;
        }
    }
}
// endif;