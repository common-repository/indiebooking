<?php
/**
 * @author schmitt
 *
 * @file rs_ib_date_util.php
 *
 * @class rs_ib_date_util
 *
 * @brief Test fuer eine kurze Beschreibung der Klasse
 *
 * Die Detaillierte Beschreibung soll hier folgen
 */
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
// if ( ! class_exists( 'rs_ib_date_util' ) ) :

class rs_ib_date_util
{

	/**
	 * @brief smalltest
	 * bigtestmessage
	 */
	public function test() {
		return "hallo";
	}
	
    public static function sortDateArrayByFromAsc($dateArray, $key = "from", $order = "asc") {
        $answer = usort($dateArray, create_function('$a, $b', 'return rs_ib_date_util::sortDateArrayByFromAsc_usort($a, $b, "' . $key . '", "' . $order . '");'));
//         $answer = usort($dateArray, array('rs_ib_date_util', "sortDateArrayByFromAsc_usort"));
        return $dateArray;
    }
    
    public static function sortDateArrayByFromAsc_usort( $a, $b, $key, $order ) {
        if ($order == "asc") {
            return strtotime($a[$key]) - strtotime($b[$key]);
        } elseif ($order == "desc") {
            return strtotime($b[$key]) - strtotime($a[$key]);
        }
    }
    
//     public  function sortFromToActiveFunctionDesc( $a, $b ) {
//         return strtotime($a["from"]) - strtotime($b["from"]);
//     }
    
    public static function isSchaltjahr($jahr){
        if(($jahr % 400) == 0 || (($jahr % 4) == 0 && ($jahr % 100) != 0)) {
            return true;
        } else {
            return false;
        }
    }
    
    
    /**
     * Die Funktion gibt das Tageskuerzel der Tagesnummer zurueck. (Bspw. Sonntag = 0)
     * @param int
     * @return String
     */
    public static function getDayName($dayNumber) {
    	switch ($dayNumber) {
    		case 0:
    			return "So";
    			break;
    		case 1:
    			return "Mo";
    			break;
    		case 2:
    			return "Di";
    			break;
    		case 3:
    			return "Mi";
    			break;
    		case 4:
    			return "Do";
    			break;
    		case 5:
    			return "Fr";
    			break;
    		case 6:
    			return "Sa";
    			break;
    	}
    }
    
    /**
     * Die Funktion gibt das Tageskuerzel der Tagesnummer zurueck. (Bspw. Sonntag = 0)
     * @param array, array, int, double
     * @return array
     */
    private static function addMissedDateLastInterval($saisonArray, $fillArray, $curYear, $defaultprice) {
        if ($curYear != 0) {
            end($fillArray);         // move the internal pointer to the end of the array
            $lastIndex      = key($fillArray);
            $lastEntry      = $fillArray[$lastIndex];
            $lastTo         = $lastEntry['to'];
            if ($lastTo != '31.12') {
                $toDate                     = rs_ib_date_util::convertDateValueToDateTime('31.12.'.$curYear);
                $newValue                   = array();
    
                $fromDate                   = clone rs_ib_date_util::convertDateValueToDateTime($lastEntry['fullto']);
                $fromDate->add(new DateInterval('P1D'));
    
                $newValue['from']           = $fromDate->format("d.m");
                $newValue['to']             = $toDate->format("d.m");
                $newValue['valid']          = $fromDate->format("Y");
                $newValue['fullfrom']       = $fromDate->format("Y-m-d");
                $newValue['fullto']         = $toDate->format("Y-m-d");
                
                $priceArray                 = self::getPriceFromSaisonBefore($saisonArray, $newValue);
                if ($priceArray == false) {
                    $newValue['price']      = $defaultprice;
                    $newValue['automatic']  = 1;
                    array_push($fillArray, $newValue);
                } else {
                    foreach ($priceArray as $newPriceValue) {
                        $newPriceValue['automatic']  = 2;
                        array_push($fillArray, $newPriceValue);
                    }
                }
            }
        }
        return $fillArray;
    }
    
//     private static function addMissedDateLastInterval_new($saisonArray, $fillArray, $curYear, $defaultprice) {
//         if ($curYear != 0) {
//             end($fillArray);         // move the internal pointer to the end of the array
//             $lastIndex      = key($fillArray);
//             $lastEntry      = $fillArray[$lastIndex];
//             $lastTo         = $lastEntry['to'];
//             if ($lastTo != '31.12') {
//                 $toDate                 = rs_ib_date_util::convertDateValueToDateTime('31.12.'.$curYear);
//                 $newValue               = array();
                
//                 $fromDate               = clone rs_ib_date_util::convertDateValueToDateTime($lastEntry['fullto']);
//                 $fromDate->add(new DateInterval('P1D'));
                
//                 //fromDate - toDate = fehlender Zeitraum bis Jahresende
//                 $firstDate              = clone $fromDate;
//                 foreach ($saisonArray as $dates) {
//                     //curYear bspw. 2018
//                     //suche nur nach kleineren Saisons, die bis mindestens zum gesuchten Jahr gültig sind!
//                     if ($curYear > intval($dates['valid']) &&
//                             (!key_exists("validto", $dates) || intval($dates['validto'] >= $curYear))) {
                        
//                         $vglFrom        = rs_ib_date_util::convertDateValueToDateTime($dates['fullfrom']);
//                         $vglTo          = rs_ib_date_util::convertDateValueToDateTime($dates['fullto']);
//                         $automatic      = $dates['automatic'];
//                         if (!key_exists("validto", $dates)) {
//                             $dates['validto'] = 0;
//                         }
//                         $validTo        = $dates['validto'];
                        
//                         $vglFrom->setDate($curYear, $vglFrom->format("m"), $vglFrom->format("d"));
//                         $vglTo->setDate($curYear, $vglTo->format("m"), $vglTo->format("d"));
//                         // || $automatic > 0
//                         if ($vglFrom > $firstDate) { //automatic > 0 = durch saison oder standard gefüllt
//                             //1. Durchlauf --> vglFrom > 01.01.xxxx
//                             $newValue                   = array();
//                             $newToDate                  = clone $vglFrom;
//                             $newToDate->sub(new DateInterval('P1D'));
//                             $newValue['from']           = $firstDate->format("d.m");
//                             $newValue['to']             = $newToDate->format("d.m");
//                             $newValue['valid']          = $firstDate->format("Y");
//                             $newValue['fullfrom']       = $firstDate->format("Y-m-d");
//                             $newValue['fullto']         = $newToDate->format("Y-m-d");
//                             $newValue['validto']        = $validTo;
                    
//                             //können theoretisch mehrere Zeiträume zurück kommen!!!
//                             $priceArray                 = self::getPriceFromSaisonBefore($saisonArray, $newValue);
//                             if ($priceArray == false) {
//                                 $newValue['price']      = $defaultprice;
//                                 $newValue['automatic']  = 1;
//                                 array_push($fillArray, $newValue);
//                             } else {
//                                 foreach ($priceArray as $newPriceValue) {
//                                     $newPriceValue['automatic']  = 2;
//                                     array_push($fillArray, $newPriceValue);
//                                 }
//                             }
//                         }
//                         $firstDate              = clone $vglTo;
//                         $firstDate->add(new DateInterval('P1D'));
//                     }
//                 }
                
//                 end($fillArray); // move the internal pointer to the end of the array
//                 $lastIndex      = key($fillArray);
//                 $lastEntry      = $fillArray[$lastIndex];
//                 $lastTo         = $lastEntry['to'];
//                 if ($lastTo != '31.12') {
//                     $toDate                 = rs_ib_date_util::convertDateValueToDateTime('31.12.'.$curYear);
//                     $newValue               = array();
                    
//                     $fromDate               = clone rs_ib_date_util::convertDateValueToDateTime($lastEntry['fullto']);
//                     $fromDate->add(new DateInterval('P1D'));
                    
//                     $newValue['from']       = $fromDate->format("d.m");
//                     $newValue['to']         = $toDate->format("d.m");
//                     $newValue['valid']      = $fromDate->format("Y");
//                     $newValue['fullfrom']   = $fromDate->format("Y-m-d");
//                     $newValue['fullto']     = $toDate->format("Y-m-d");
//                     $newValue['validto']    = 0;
//                     $newValue['price']      = $defaultprice;
//                     $newValue['automatic']  = 1;
//                     array_push($fillArray, $newValue);
//                 }
//             }
//         }
//         return $fillArray;
//     }
    
    public static function getPriceFromSaisonBefore($saisonArray, $curArray) {
        $newDates                           = false;
        $curYear                            = $curArray['valid'];
        $searchFrom                         = $curArray['from'];
        $searchTo                           = $curArray['to'];
        $baseFrom                           = $curArray['from'];
        $baseTo                             = $curArray['to'];
//         if ($searchFrom instanceof DateTime) {
//
//         }
//         $saisonCounter = sizeof($saisonArray);
//         for ($saisonCounter; $saisonCounter >= 0; $saisonCounter--) {
//             $dates = $saisonArray[$saisonCounter];
//         }
        $saisonArray                        = array_reverse($saisonArray);
        foreach ($saisonArray as $dates) {
            $automatic                      = 0;
            $saisonYear                     = $dates['valid'];
            $curPrice                       = $dates['price'];
            $validto                        = $dates['validto'];
            if (key_exists("automatic", $dates)) {
                $automatic                  = $dates["automatic"];
            }
            if ($automatic != 1) {
                //wenn automatic 1 ist, ist der gerade ausgewaehlte Preis ein durch den Standardpreis hinzugefuegter
                //demnach darf dieser an dieser Stelle nicht genommen werden. Wird der Zeitraum in keiner anderen
                //Saison gefunden, wird wiederum der Standardpreis genutzt.
                if (intval($saisonYear) < intval($curYear) && (intval($validto) >= intval($curYear) || $validto == "" || $validto == 0)) {
                    $checkFrom                  = self::convertDateValueToDateTime($searchFrom.".".$saisonYear);
                    $checkTo                    = self::convertDateValueToDateTime($searchTo.".".$saisonYear);
                    
                    $curFrom                    = self::convertDateValueToDateTime($dates["fullfrom"]);
                    $curTo                      = self::convertDateValueToDateTime($dates["fullto"]);
                    if (self::checkDateOverlap($curFrom, $curTo, $checkFrom, $checkTo)) {
                        $newRange               = array();
                        $range                  = self::getDateRangeToCalc($curFrom, $curTo, $checkFrom, $checkTo, false);
                        $fromDt                 = self::convertDateValueToDateTime($range['von']);
                        $toDt                   = self::convertDateValueToDateTime($range['bis']);
                        $tempFrom               = $fromDt->format('d.m');
                        $tempFrom               = $tempFrom.".".$curYear;
                        $tempTo                 = $toDt->format('d.m');
                        if ($tempFrom == '29.02') {
                            if (!$tempFrom::isSchaltjahr($curYear)) {
                                $tempTo = '28.02';
                            }
                        } elseif ($tempFrom == '28.02') {
                            if (self::isSchaltjahr($curYear)) {
                                $tempFrom = '29.02';
                            }
                        }
                        if ($tempTo == '29.02') {
                            if (!self::isSchaltjahr($curYear)) {
                                $tempTo = '28.02';
                            }
                        } elseif ($tempTo == '28.02') {
                            if (self::isSchaltjahr($curYear)) {
                                $tempTo = '29.02';
                            }
                        }
                        $tempTo                 = $tempTo.".".$curYear;
                        
                        $fromDt                 = self::convertDateValueToDateTime($tempFrom);
                        $toDt                   = self::convertDateValueToDateTime($tempTo);
                        $newRange['price']      = $curPrice;
                        $newRange['valid']      = $curYear;
                        $newRange['from']       = $fromDt->format('d.m');
                        $newRange['to']         = $toDt->format('d.m');
                        $newRange['fullfrom']   = $fromDt->format('Y-m-d');
                        $newRange['fullto']     = $toDt->format('Y-m-d');
                        if (intval($validto) < intval($curYear)) {
                            $validto            = $curYear;
                        }
                        $newRange['validto']    = $validto;
                        if ($newDates == false) {
                            $newDates           = array();
                        }
                        array_push($newDates, $newRange);
                        
                        $checkBaseFrom          = self::convertDateValueToDateTime($baseFrom.".".$curYear);
                        $checkBaseTo            = self::convertDateValueToDateTime($baseTo.".".$curYear);
                        
                        $searchFrom             = clone $toDt;
                        $searchFrom->add(new DateInterval('P1D'));
                        if ($searchFrom->getTimestamp() >= $checkBaseFrom->getTimestamp() &&
                            $searchFrom->getTimestamp() <= $checkBaseTo->getTimestamp()) {
                                
                            $searchFrom         = $searchFrom->format("d.m");
                        } else {
                            $searchFrom         = $baseFrom;
                        }
                    }
                }
            }
        }
        return $newDates;
    }
    
    public static function testSaisonpreisermittlung() {
        $saisonArray = array();
        
        $saison0            = array();
        $saison0['valid']   = 2015;
        $saison0['from']    = '05.06';
        $saison0['to']      = '25.06';
        $saison0['validto'] = '2020';
        $saison0['price']   = 5;
        
        
        $saison1            = array();
        $saison1['valid']   = 2016;
        $saison1['from']    = '01.01';
        $saison1['to']      = '31.01';
        $saison1['validto'] = '2019';
        $saison1['price']   = 20;
        
        $saison2            = array();
        $saison2['valid']   = 2016;
        $saison2['from']    = '01.04';
        $saison2['to']      = '31.05';
        $saison2['validto'] = '2020';
        $saison2['price']   = 30;
        
        $saison3            = array();
        $saison3['valid']   = 2019;
        $saison3['from']    = '01.01';
        $saison3['to']      = '15.01';
        $saison3['validto'] = '2020';
        $saison3['price']   = 40;
        
        $saison4            = array();
        $saison4['valid']   = 2019;
        $saison4['from']    = '13.06';
        $saison4['to']      = '21.06';
        $saison4['validto'] = '2020';
        $saison4['price']   = 60;
        
        $i                  = 0;
        $saisonArray[$i++]  = $saison0;
        $saisonArray[$i++]  = $saison1;
        $saisonArray[$i++]  = $saison2;
        $saisonArray[$i++]  = $saison3;
        $saisonArray[$i++]  = $saison4;
        
//         self::getPriceFromSaisonBefore_new($saisonArray);
        self::getSaisonPrices('01.05.2020', '01.07.2020', $saisonArray, 300);
    }
    
    
    public static function groupSaisonByYear($saisonArray) {
        $newsaisonarray = array();
        foreach ($saisonArray as $saison) {
            if (!key_exists($saison['valid'], $newsaisonarray)) {
                $newsaisonarray[$saison['valid']] = array();
            }
            array_push($newsaisonarray[$saison['valid']], $saison);
        }
        return $newsaisonarray;
    }
    
    public static function getSaisonPrices($originalFrom, $originalTo, $saisonArray, $defaultPrice, $observedYears = false, $adminSave = false) {
    	if (is_object($originalFrom)) {
	    	$from								= clone $originalFrom;
    	} else {
    		$from								= $originalFrom;
    	}
    	if (is_object($originalTo)) {
    		$to									= clone $originalTo;
    	} else {
    		$to									= $originalTo;
    	}
    	
        $searchFrom                         	= self::convertDateValueToDateTime($from);
        $searchTo                           	= self::convertDateValueToDateTime($to);
        $datesWithPrices                    	= array();
        if ($searchFrom instanceOf DateTime && $searchTo instanceof DateTime) {
	        $onlyValidSaisonValues              = array();
	        $checkArray                         = $saisonArray;
	        $minYear                            = $searchFrom->format("Y");
	        $maxYear                            = $searchTo->format("Y");
	        foreach ($checkArray as $saison) {
	        	$validYearFrom					= intval($saison['valid_from']);
	        	$validYearTo					= $saison['valid_to'];
	        	if ($validYearTo == '') {
	        		$validYearTo				= 0;
	        	} else {
	        		$validYearTo				= intval($validYearTo);
	        	}
	        	if ($validYearFrom <= $maxYear && ($validYearTo >= $minYear || $validYearTo == 0)) {
	                $saisonYear					= $minYear;
	                
	                /*
	                 * Carsten 27.12.2017
	                 * Damit Jahresuebergreifend alle moeglichen / validen Saisons beruecksichtigt werden,
	                 * muss ich die moeglichen Jahre zwischen min- und maxYear alle ueberpruefen,
	                 * wurde die Saison einmal hinzugefuegt, kann durch ein break raus gesprungen werden,
	                 * damit die Saison nicht mehrfach in den Array gepusht wird.
	                 */
	                while ($saisonYear <= $maxYear) {
	                	if ($saisonYear >= $validYearFrom && ($saisonYear <= $validYearTo || $validYearTo == 0)) {
		                	$saisonVon          = self::convertDateValueToDateTime($saison['date_from'].".".$saisonYear);
		                	$saisonBis          = self::convertDateValueToDateTime($saison['date_to'].".".$saisonYear);
			                if (self::checkDateOverlap($saisonVon, $saisonBis, $searchFrom, $searchTo)) {
			                    array_push($onlyValidSaisonValues, $saison);
			                    break;
			                }
	                	}
		                $saisonYear++;
	                }
	            }
	        }
	        $onlyValidSaisonValues              = array_reverse($onlyValidSaisonValues);
	        $currentDay                         = clone $searchTo;
	        
	        $lastYear							= 0;
	        $lastIndex                          = null;
	        $currentIndex                       = -1;
	        $lastWasDefault                     = false;
	        $lastDayFrom						= false;
	        $lastWasSaison						= false;
	        while ($currentDay >= $searchFrom) {
	            $search                         = true;
	            $year                           = $currentDay->format("Y");
	            if ($observedYears != false) {
	                $search                     = (key_exists($year, $observedYears));
	            }
	            if ($year != $lastYear) {
	            	/*
	            	 * Update Carsten Schmitt 23.05.2017
	            	 * Wenn das
	            	 */
	            	$lastIndex					= null;
	            }
	            $found                          = false;
	            if ($search) {
	                foreach ($onlyValidSaisonValues as $index => $validSaison) {
	                	/*
	                	 * Update 21.12.2017
	                	 * Wurde der Preis automatisch mit dem Standardpreis ergaenzt (added_automatic_kz = 1),
	                	 * soll aktuell der gepflegte Standardpreis gezogen werden.
	                	 * Dadurch taucht der Fehler bei gepflegten Saisonpreisen erst einmal nicht mehr auf.
	                	 */
// 	                	if (!key_exists('added_automatic_kz', $validSaison) || $validSaison['added_automatic_kz'] != 1) {
		                	if ($year >= $validSaison['valid_from'] && (($year <= $validSaison['valid_to'])
		                        || $validSaison['valid_to'] == "" || $validSaison['valid_to'] == 0)) {
		                        	
	// 	                        $monthFrom 	= substr($validSaison['date_from'], 3);
	// 	                        $monthTo 	= substr($validSaison['date_to'], 3);
	// 	                        $validTo	= $validSaison['valid_to'];
	//                         	$toYear		= $year;
	//                         	if ((intval($monthFrom) > intval($monthTo)) && ($validTo == "" || $validTo == 0 || $validTo > $valid)) {
	//                         		$toYear++;
	//                         	}
		                        
		                        $validSaisonFrom                    = self::convertDateValueToDateTime($validSaison['date_from'].".".$year);
		                        $validSaisonTo                      = self::convertDateValueToDateTime($validSaison['date_to'].".".$year);
		                        if ($currentDay >= $validSaisonFrom && $currentDay <= $validSaisonTo) {
		                            $found                          = true;
		                            if ($lastWasDefault) {
		                            	/*
		                            	 * Update 16.02.2018
		                            	 *
		                            	 * Abfrage von $currentDay > $searchFrom auf $currentDay >= $searchFrom
		                            	 * abgeaendert, da sonst bei einer Buchung, die genau auf das Ende
		                            	 * einer Saison zeigt der Saisontag nicht beruecksichtigt wurde, da die Position
		                            	 * wegen Anzahl Naechte 0 dann nicht angelegt wird.
		                            	 */
		                            	if ($currentIndex >= 0 && !$adminSave && $currentDay >= $searchFrom) {
		                            		$datesWithPrices[$currentIndex]['from']->sub(new DateInterval('P01D'));
		                            	}
		                            }
		                            $lastWasDefault                 = false;
		                            if (is_null($lastIndex) || $lastIndex != $index) {
		                                $dayValue                   = array();
		                                $dayValue['price']          = $validSaison['price'];
		                                $dayValue['valid']          = $validSaison['valid_from'];
		                                $dayValue['validto']        = $validSaison['valid_to'];
		                                $dayValue['to']             = clone $currentDay;
		                                
// 		                                if ($lastDayFrom) {
// 		                                	$anzahlTage          	= date_diff($lastDayFrom, $dayValue['to'], true);
// 		                                	$numberOfNights  		= intval($anzahlTage->format('%a'));
// 		                                	if ($anzahlTage == 1) {
// 			                                	$dayValue['from']		= clone $lastDayTo;
// 		                                	}
// 		                                }
		                                
		                                /*
		                                 * Update Carsten Schmitt 17.01.2017
		                                 * Ist das Jahr des Tages dem ich aktuell Suche
		                                 * gleich dem Von Jahr der Saison
		                                 * Kann ich mir sicher sein, dass der Saisonzeitraum der aktuellste ist.
		                                 * Somit kann ich den Zeitraum sofort festlegen und den aktuellen Tag
		                                 * auf den von-Tag der Saison stellen.
		                                 */
	                                    if ($lastWasSaison) {
	                                    	if ($currentIndex >= 0 && !$adminSave && $currentDay > $searchFrom) {
	                                    		$datesWithPrices[$currentIndex]['from']->sub(new DateInterval('P01D'));
	                                    	}
	                                    }
		                                if ($year == intval($validSaison['valid_from'])) {
// 		                                	$dayBefore				= clone $currentDay;
		                                    $currentDay             = clone $validSaisonFrom;
		                                }
		                                
		                                /*
		                                 * Update Carsten Schmitt 31.01.2017
		                                 * Ist der currentDay kleiner dem gesuchten von Tag, wird der gesuchte
		                                 * von Tag als from Value genommen.
		                                 * Ansonsten wird naemlich der Saison-Von als From Wert genommen,
		                                 * wodurch eine Buchung durchaus sehr teuer werden kann.
		                                 */
		                                if ($currentDay->getTimestamp() < $searchFrom->getTimestamp()) {
		                                	$dayValue['from']		= clone $searchFrom;
		                                } else {
		                                	$dayValue['from']       = clone $currentDay;
		                                }
		                                $lastDayFrom				= clone $dayValue['from'];
		                                $lastIndex                  = $index;
		                                if ($year != $validSaison['valid_from']) {
		                                    $dayValue['automatic']  = 2;
		                                    $dayValue['valid']      = $year;
		                                    $dayValue['validto']    = $year;
		                                } else {
		                                    $dayValue['automatic']  = 0;
		                                }
		                                $lastWasSaison				= true;
		                                $currentIndex++;
		                                $datesWithPrices[$currentIndex] = $dayValue;
		                            } else if ($lastIndex == $index) {
		                                $datesWithPrices[$currentIndex]['from'] = clone $currentDay;
// 		                                if (!$adminSave && $currentDay > $searchFrom) {
// 		                                	$datesWithPrices[$currentIndex]['from']->sub(new DateInterval('P01D'));
// 		                                }
		                            }
		                            break;
		                        }
		                    }
// 	                	}
	                }
	                if (!$found) {
	                	if ($lastWasDefault) {
	                        $datesWithPrices[$currentIndex]['from'] = clone $currentDay;
	                    } else {
	                    	/*
	                    	 * Update 28.05.2018
	                    	 *
	                    	 * Abfrage von $currentDay > $searchFrom auf $currentDay >= $searchFrom
	                    	 * Da es sonst passieren kann, dass wenn die Buchung einen Tag vor dem Saisonpreis
	                    	 * startet, der 1 Tag ignoriert wird, da hier anzahl Naechte = 0 ist.
	                    	 */
// 	                    	if ($currentIndex >= 0 && !$adminSave && $currentDay > $searchFrom) {
	                    	if ($currentIndex >= 0 && !$adminSave && $currentDay >= $searchFrom) {
	                    		$datesWithPrices[$currentIndex]['from']->sub(new DateInterval('P01D'));
	                    	}
	                        $currentIndex++;
	                        $dayValue                       = array();
	                        $dayValue['price']              = $defaultPrice;
	                        $dayValue['to']               	= clone $currentDay;
	                        $dayValue['from']               = clone $currentDay;
	                        $dayValue['automatic']          = 1;
	                        $dayValue['validto']            = $year;
	                        $dayValue['valid']              = $year;
	                        $datesWithPrices[$currentIndex] = $dayValue;
	                    }
	                    $lastWasDefault                     = true;
	                    $lastWasSaison						= false;
	                }
	            }
	            $lastYear	= $year;
	            $currentDay->sub(new DateInterval('P1D'));
	        }
        }
        return $datesWithPrices;
    }
    
    public static function getPriceFromSaisonBefore_new($saisonArray) {
        $newDates                           = false;
        $saisonArray                        = self::groupSaisonByYear($saisonArray);
        $saisonArray                        = array_reverse($saisonArray);
        $saisonArrayCopy                    = $saisonArray;
        foreach ($saisonArray as $currentYear => $dates) {
            $automatic                      = 0;
            $saisonYear                     = $dates['valid'];
            $curPrice                       = $dates['price'];
            $validto                        = $dates['validto'];
            
            $lastTo                         = $dates['to'];
            if ($lastTo != '31.12') {
                $searchFrom                 = self::convertDateValueToDateTime($lastTo.".".$currentYear);
                $searchTo                   = self::convertDateValueToDateTime("31.12.".$currentYear);
                
                foreach ($saisonArrayCopy as $saisonYearCopy => $checkSaison) {
                    $saisonValidTo          = $checkSaison['validto'];
                    if (intval($saisonYearCopy) < intval($currentYear) &&
                            (intval($saisonValidTo) >= intval($currentYear) ||
                            $saisonValidTo == "" || $saisonValidTo == 0)) {
                            
                        $saisonFrom         = $checkSaison['from'];
                        $saisonTo           = $checkSaison['to'];
                        
                        $saisonFrom         = self::convertDateValueToDateTime($saisonFrom.".".$currentYear);
                        $saisonTo           = self::convertDateValueToDateTime($saisonTo.".".$currentYear);
                        
                        if (self::checkDateOverlap($searchFrom, $searchTo, $saisonFrom, $saisonTo)) {
                            $newRange               = array();
                            $range                  = self::getDateRangeToCalc($curFrom, $curTo, $checkFrom, $checkTo, false);
                            $fromDt                 = self::convertDateValueToDateTime($range['von']);
                            $toDt                   = self::convertDateValueToDateTime($range['bis']);
                            $tempFrom               = $fromDt->format('d.m');
                            $tempFrom               = $tempFrom.".".$curYear;
                            $tempTo                 = $toDt->format('d.m');
                            if ($tempFrom == '29.02') {
                                if (!$tempFrom::isSchaltjahr($curYear)) {
                                    $tempTo = '28.02';
                                }
                            } elseif ($tempFrom == '28.02') {
                                if (self::isSchaltjahr($curYear)) {
                                    $tempFrom = '29.02';
                                }
                            }
                            if ($tempTo == '29.02') {
                                if (!self::isSchaltjahr($curYear)) {
                                    $tempTo = '28.02';
                                }
                            } elseif ($tempTo == '28.02') {
                                if (self::isSchaltjahr($curYear)) {
                                    $tempTo = '29.02';
                                }
                            }
                            $tempTo                 = $tempTo.".".$curYear;
                            
                            $fromDt                 = self::convertDateValueToDateTime($tempFrom);
                            $toDt                   = self::convertDateValueToDateTime($tempTo);
                            $newRange['price']      = $curPrice;
                            $newRange['valid']      = $curYear;
                            $newRange['from']       = $fromDt->format('d.m');
                            $newRange['to']         = $toDt->format('d.m');
                            $newRange['fullfrom']   = $fromDt->format('Y-m-d');
                            $newRange['fullto']     = $toDt->format('Y-m-d');
                            if (intval($validto) < intval($curYear)) {
                                $validto            = $curYear;
                            }
                            $newRange['validto']    = $validto;
                            if ($newDates == false) {
                                $newDates           = array();
                            }
                            array_push($newDates, $newRange);
                        }
                    }
                }
            }
            
                //wenn automatic 1 ist, ist der gerade ausgewaehlte Preis ein durch den Standardpreis hinzugefuegter
                //demnach darf dieser an dieser Stelle nicht genommen werden. Wird der Zeitraum in keiner anderen
                //Saison gefunden, wird wiederum der Standardpreis genutzt.
                if (intval($saisonYear) < intval($curYear) && (intval($validto) >= intval($curYear) || $validto == "" || $validto == 0)) {
                    $checkFrom                  = self::convertDateValueToDateTime($searchFrom.".".$saisonYear);
                    $checkTo                    = self::convertDateValueToDateTime($searchTo.".".$saisonYear);
    
                    $curFrom                    = self::convertDateValueToDateTime($dates["fullfrom"]);
                    $curTo                      = self::convertDateValueToDateTime($dates["fullto"]);
                    if (self::checkDateOverlap($curFrom, $curTo, $checkFrom, $checkTo)) {
                        
    
                        $checkBaseFrom          = self::convertDateValueToDateTime($baseFrom.".".$curYear);
                        $checkBaseTo            = self::convertDateValueToDateTime($baseTo.".".$curYear);
    
                        $searchFrom             = clone $toDt;
                        $searchFrom->add(new DateInterval('P1D'));
                        if ($searchFrom->getTimestamp() >= $checkBaseFrom->getTimestamp() &&
                            $searchFrom->getTimestamp() <= $checkBaseTo->getTimestamp()) {
    
                                $searchFrom         = $searchFrom->format("d.m");
                            } else {
                                $searchFrom         = $baseFrom;
                            }
                    }
                }
        }
        return $newDates;
    }
    
    
    public static function addMissedSaisonYears($saisonArray, $startYear, $endYear) {
        $currentYear    = $startYear;
        $currentSaison  = $saisonArray;
        while ($currentYear < $endYear) {
            $curArray           = array();
            
            $curArray['valid']  = $currentYear;
            $curArray['from']   = '01.01';
            $curArray['to']     = '31.12';
            $newDates           = self::getPriceFromSaisonBefore($currentSaison, $curArray);
            foreach ($newDates as $newSaison) {
                array_push($saisonArray, $newSaison);
            }
            $currentYear++;
        }
        return $saisonArray;
    }
    
    
    public static function addMissedDateInterval($saisonArray, $defaultprice, $onlySaveEdited = false, $adminSave = false) {
        $firstDate      = 0;
        $curYear        = 0;
        $lastDateOfYear = 0;
        $nextYear       = 0;
        $fillArray      = array();

        $editedYears    = array();
        foreach ($saisonArray as $key => $dates) {
            if ($dates['automatic'] == 1 || $dates['automatic'] == 2) {
                //wenn der Preis automatisch hinzugefügt wurde (1 = Standardpreis / 2 = Vorsaison)
                //werden diese Werte aus dem Array genommen um anschliessend neu befuellt zu werden
                //es kann ja sein, dass sich an der Vorsaison etwas geändert hat.
                unset($saisonArray[$key]);
            } else {
                $saisonArray[$key]['valid_from']    = $saisonArray[$key]['valid'];
                $saisonArray[$key]['valid_to']      = $saisonArray[$key]['validto'];
                $saisonArray[$key]['date_from']     = $saisonArray[$key]['from'];
                $saisonArray[$key]['date_to']       = $saisonArray[$key]['to'];
                
                $editedYears[$saisonArray[$key]['valid']] = $saisonArray[$key]['valid'];
            }
        }
        $saisonArray                	= array_values($saisonArray);
        $fillArray                  	= array();
        if (sizeof($saisonArray) > 0) {
	        $maxindex                   = sizeof($saisonArray);
	        $from                       = "01.01.".$saisonArray[0]["valid"];
	        $to                         = "31.12.".$saisonArray[$maxindex-1]["valid"];
	        
	        $saisons                    = self::getSaisonPrices($from, $to, $saisonArray, $defaultprice, $editedYears, $adminSave);
	        foreach ($saisons as $saison) {
	        	if ($saison['automatic'] != 1) {
		            $newValue               = array();
		            $newValue['from']       = $saison['from']->format("d.m");
		            $newValue['to']         = $saison['to']->format("d.m");
		            $newValue['fullfrom']   = $saison['from']->format("Y-m-d");
		            $newValue['fullto']     = $saison['to']->format("Y-m-d");
		            $newValue['automatic']  = $saison['automatic'];
		            $newValue['price']      = $saison['price'];
		            $newValue['valid']      = $saison['valid'];
		            $newValue['validto']    = $saison['validto'];
		            array_push($fillArray, $newValue);
	        	}
	        }
        }
        return $fillArray;
    }
    
    public static function convertDateValueToDateTime($dateToConvert, $datetime = false) {
        $convertedDate = "";
        if (!($dateToConvert instanceof DateTime)) {
            $dateToConvert = RS_IB_Data_Validation::check_with_whitelist($dateToConvert, RS_IB_Data_Validation::DATATYPE_TEXT);
            if (is_int($dateToConvert)) {
                $convertedDate  = new DateTime($dateToConvert);
            }
            elseif (is_string($dateToConvert)) {
                if (strlen($dateToConvert) == 19) {
                    try {
                        $convertedDate = new DateTime($dateToConvert);
                    } catch (Exception $e) {
                        $convertedDate = false;
                    }
                }
                if ($convertedDate == false) {
                    $convertedDate  = DateTime::createFromFormat("d.m.Y", $dateToConvert);
                    if ($convertedDate == false) {
                        $convertedDate  = DateTime::createFromFormat("Y-m-d", $dateToConvert);
                        if ($convertedDate == false) {
                            if ($dateToConvert != "") {
    //                             RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."]"." Fehlgeschlagen");
                            }
                        } else {
                            //nothing
                        }
                    } else {
                    	$year = $convertedDate->format('Y');
                    	if (intval($year) < 1000) {
                    		$currentYear 	= date("Y");
                    		$dateMonth		= $convertedDate->format("m");
                    		$dateday		= $convertedDate->format("d");
                    		$currentYear	= substr($currentYear, 0 ,2);
                    		$currentMil		= intval($currentYear) * 100;
                    		$year			= $currentMil + $year;
                    		$convertedDate->setDate($year, $dateMonth, $dateday);
                    	}
                    }
                }
            }
            if ($convertedDate instanceof DateTime && !$datetime) {
                $convertedDate->setTime(0, 0, 0);
            }
        } else {
            $convertedDate = $dateToConvert;
        }
        return $convertedDate;
    }
    
    public static function convertDateValueToTimestamp($dateToConvert) {
        if (is_int($dateToConvert)) {
            return $dateToConvert;
        }
        $dateToConvert = self::convertDateValueToDateTime($dateToConvert);
//         if (is_int($dateToConvert))
//                 return $dateToConvert;
//         if (is_string($dateToConvert)) {
//             $dateToConvert  = DateTime::createFromFormat("d.m.Y", $dateToConvert);
//         }
        if ($dateToConvert instanceof DateTime) {
            $dateToConvert = $dateToConvert->getTimestamp();
        }
        return $dateToConvert;
    }
    
    public static function isDateGreaterToday($date) {
        $date   = self::convertDateValueToTimestamp($date);
        $today  = self::convertDateValueToTimestamp(date("d.m.Y"));
        if ($date < $today) {
            return false;
        }
        return true;
    }
    
    /**
     * Erstellt aus Monat und Tag ein Datetime-Objekt
     * Wird kein Jahr mitgeliefert, wird das Jahr 1970 gesetzt um quasi ein leeres Jahr zu haben.
     *
     * @param unknown $datum
     * @param number $year
     * @return DateTime
     */
    public static function createYearlessDateFromString($datum, $year = 1970) {
        $day                    = intval(substr($datum, 0,2));
        $month                  = intval(substr($datum, 3,2));
        $yearlessDate           = new DateTime();//DateTime::createFromFormat("!d", "15");//$yearLessPrice['from']);
        $yearlessDate->setDate($year, $month, $day);
        $yearlessDate->setTime(0, 0, 0);
        return $yearlessDate;
    }
    
    /**
     * Prueft, ob sich die Datumswerte $dateFrom und $dateTo zeitlich mit den Datumswerten
     * in $dateCompareArray ueberschneiden.
     * Alle Datumswerte kuennen als Timestamp, Datetime oder String ('d.m.Y') angegeben werden.
     * $dateCompareArray[index]["from"]
     * $dateCompareArray[index]["to"]
     *
     * @param array $dateCompareArray
     * @param date $dateFrom
     * @param date $dateTo
     * @return boolean
     */
    public static function isDateOverlap($dateCompareArray, $dateFrom, $dateTo, $abIndex = 0, $fromEqToValid = false, $countOverlaps = false) {
//         $valid      = true;
        $overlap        = false;
        $countOfOverlap	= 0;
        $dateFrom       = self::convertDateValueToTimestamp($dateFrom);
        $dateTo         = self::convertDateValueToTimestamp($dateTo);
        for ($i = $abIndex; $i < sizeof($dateCompareArray); $i++) {
        	$active 	= true;
            $date       = $dateCompareArray[$i];
            if (key_exists('active', $date)) {
            	$active	= ($date['active'] == "on");
            }
            if ($active) {
	            $from       = self::convertDateValueToTimestamp($date["from"]);
	            $to         = self::convertDateValueToTimestamp($date["to"]);
	            $overlap    = self::checkDateOverlap($from, $to, $dateFrom, $dateTo);
	            if ($overlap) {
	            	if ($fromEqToValid && ($dateFrom == $to || ($dateTo == $from))) {
	                    $overlap = false;
	            	} else if (!$countOverlaps) {
	                    break;
	            	} else if ($countOverlaps) {
	            		$countOfOverlap++;
	            	}
	            }
            }
        }
        if (!$countOverlaps) {
        	return $overlap;
        } else {
        	return $countOfOverlap;
        }
    }
    
    /*
     * Update: 21.09.2016
     * Den Vergleichen ein = hinzugefügt, für den Fall, dass die Datumswerte übereinstimmenn.
     * Auch dann "überlappen" diese ja. (???)
     */
    public static function checkDateOverlap($compareFrom1, $compareTo1, $dateFrom1, $dateTo1) {
        $compareFrom    = self::convertDateValueToTimestamp($compareFrom1);
        $compareTo      = self::convertDateValueToTimestamp($compareTo1);
        $dateFrom       = self::convertDateValueToTimestamp($dateFrom1);
        $dateTo         = self::convertDateValueToTimestamp($dateTo1);
        
        $overlap        = false;
        
        if (($compareTo - $compareFrom) > ($dateTo - $dateFrom)) {
            if ($dateFrom <= $compareTo && $dateTo >= $compareFrom) {
                $overlap    = true;
            }
        } else {
            if ($compareFrom <= $dateTo && $compareTo >= $dateFrom) {
                $overlap    = true;
            }
        }
        return $overlap;
    }
    
    /**
     * gibt den berechneten Zeitraum zurueck
     * Objekte (Datetime = objekte) werden immer call by reference aufgerufen!!!
     * @param unknown $dtBuchungVon
     * @param unknown $dtBuchungBis
     * @param unknown $dtVglVon
     * @param unknown $dtVglBis
     * @return boolean[] | unknown[]
     * <ul>
     * <li>$return['von']</li>
     * <li>$return['bis']</li>
     * <li>$return['allInOne']</li>
     * </ul>
     */
    public static function getDateRangeToCalc($dtBuchungVon, $dtBuchungBis, $vglVon, $vglBis, $addDayDateTo = true) {
        $return                 = array();
    
        /*
         * Objekte (Datetime = objekt) werden immer call by reference aufgerufen!!!
         * Daher clone ich alle uebergebenen Objekte, um das eigentliche Objekt nicht zu veruendern.
         */
        $dtBasisVon             = clone $dtBuchungVon;
        $dtBasisBis             = clone $dtBuchungBis;
        $dtVglVon               = clone $vglVon;
        $dtVglBis               = clone $vglBis;
    
        $allInOneTimeRange      = false;
        if ($dtVglVon <= $dtBasisVon && $dtVglBis >= $dtBasisBis) {
            //Buchungszeitraum in einem einzigen Preiszeitraum
            $allPrices          = array();
            $dtReturnVon        = $dtBasisVon;
            $dtReturnBis        = $dtBasisBis;
            $allInOneTimeRange  = true;
        }
        elseif ($dtVglVon <= $dtBasisVon) {
            //Buchungszeitraum von in einem Preiszeitraum
            $dtReturnVon        = $dtBasisVon;
            $dtReturnBis        = $dtVglBis;
            if ($addDayDateTo) {
                $dtReturnBis->add(new DateInterval('P1D'));
            }
        }
        elseif ($dtVglBis >= $dtBasisBis) {
            //Buchungszeitraum bis in einem Preiszeitraum
            $dtReturnVon        = $dtVglVon;
            $dtReturnBis        = $dtBasisBis;
        }
        elseif ($dtVglVon > $dtBasisVon && $dtVglBis < $dtBasisBis) {
            //Preiszeitraum komplett in Buchungszeitraum
            $dtReturnVon        = $dtVglVon;
            $dtReturnBis        = $dtVglBis;
            if ($addDayDateTo) {
                $dtReturnBis->add(new DateInterval('P1D'));
            }
        }
        //         else {
        //             $dtReturnVon        = $dtBuchungVon;
        //             $dtReturnBis        = $dtBuchungBis;
        //         }
    
        $return['von']          = $dtReturnVon;
        $return['bis']          = $dtReturnBis;
        $return['allInOne']     = $allInOneTimeRange;
    
        return $return;
    }
    
    
    
    public static function getBiggestDateRange($dtBuchungVon, $dtBuchungBis, $vglVon, $vglBis) {
        $return                 = array();
    
        /*
         * Objekte (Datetime = objekt) werden immer call by reference aufgerufen!!!
         * Daher clone ich alle uebergebenen Objekte, um das eigentliche Objekt nicht zu veruendern.
         */
        $dtBasisVon             = clone $dtBuchungVon;
        $dtBasisBis             = clone $dtBuchungBis;
        $dtVglVon               = clone $vglVon;
        $dtVglBis               = clone $vglBis;
    
//         var_dump($dtBasisVon);
//         var_dump($dtBasisBis);
//         var_dump($dtVglVon);
//         var_dump($dtVglBis);
        
        $allInOneTimeRange      = false;
        if ($dtVglVon <= $dtBasisVon && $dtVglBis >= $dtBasisBis) {
            //Buchungszeitraum in einem einzigen Preiszeitraum
            $allPrices          = array();
            $dtReturnVon        = $dtVglVon;
            $dtReturnBis        = $dtVglBis;
            $allInOneTimeRange  = true;
        }
        elseif ($dtVglVon <= $dtBasisVon && $dtVglBis <= $dtBasisBis) {
            //Buchungszeitraum von in einem Preiszeitraum
            $dtReturnVon        = $dtVglVon;
            $dtReturnBis        = $dtBasisBis;
            $dtReturnBis->add(new DateInterval('P1D'));
        }
        elseif ($dtVglBis >= $dtBasisBis && $dtVglVon >= $dtBasisVon) {
            //Buchungszeitraum bis in einem Preiszeitraum
            $dtReturnVon        = $dtBasisVon;
            $dtReturnBis        = $dtVglBis;
        }
        elseif ($dtVglVon > $dtBasisVon && $dtVglBis < $dtBasisBis) {
            //Preiszeitraum komplett in Buchungszeitraum
            $dtReturnVon        = $dtBasisVon;
            $dtReturnBis        = $dtBasisBis;
            $dtReturnBis->add(new DateInterval('P1D'));
        }
        //         else {
        //             $dtReturnVon        = $dtBuchungVon;
        //             $dtReturnBis        = $dtBuchungBis;
        //         }
    
        $return['von']          = $dtReturnVon;
        $return['bis']          = $dtReturnBis;
        $return['allInOne']     = $allInOneTimeRange;
    
        return $return;
    }
    
    
}
// endif;
