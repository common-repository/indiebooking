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

/*
 * Regulaere Ausdruecke
 * /    = Starten des Regulaeren Ausdrucks (Wenn es am Anfang steht)
 * /    = Beenden des Regulaeren Ausdrucks (Wenn es am Ende steht)
 * [a-z] = Alle Buchstaben im Bereich zwischen a und z. Geht genauso mit Grouebuchstaben und Zahlen
 *
 * .    = Wiildcard, passt zu jedem Zeichen --> h.llo asst zu allen Texten die h beliebiges Zeichen gefolgt von llo enthalten
 * []   = ueberprueft ob einer der Zeichen enthalten ist. Bspw. [aeiou]
 * ^    = ueberprueft den Anfang des Textes. Kann auch fuer nicht stehen.
 *        Bspw: /^test/ - Der Text muss mit test beginnen
                /hall[^aeiou]/ - Die Buchstaben hall duerfen nicht mit a, e, i, o oder u enden
 * $    = ueberprueft den Ende des Textes Bspw. /test$/ - Der Text muss mit test aufhueren
 * |    = Ermoeglicht Alternativen Bspw:
 *          /(der|das)/ -Passt zu der und das
            /Kind(er|ergarten|le)/ - Passt zu Kinder, Kindergarten und Kindle.
 * ?    = Vorheriges Zeichen ist optional Bspw: /iPhone[1-7]?/ -Passt zu iPhone, iPhone2 usw. bis iPhone7
 * *    = Wiederholung des vorherigen Elements (0 oder hueufiger mal) Bspw:
 *          Windows [0-9]* - Passt zu Windows, Windows 98 und Windows 7, aber nicht Windows7.
 * +    = Wiederholung des vorherigen Elements (1 oder hueufiger mal) Bspw: /[0-9]+/ - Passt zu allen natuerlichen Zahlen.
 * {n}  = Exakt n-mal Wiederholung des vorherigen Elements. Bspw: /[0-9]{3}/ - Passt zu allen 3 stelligen Zahlen.
 * {m,n} =  Wiederholung des vorherigen Elements mindestens m-mal, maximal n-mal. Bspw: /[0-9]{1,4}/ - Passt zu allen 1 bis 4 stelligen Zahlen.
 */
// if ( ! class_exists( 'RS_IB_Data_Validation' ) ) :
class RS_IB_Data_Validation {
    
    const RS_IB_MAX_ALLOWED_SPECIAL_CHARS = " _!#\+,.ue\/\-";
    
    const DATATYPE_ALL              = "ALL";
    const DATATYPE_ARRAY            = "ARRAY";
    const DATATYPE_TEXT             = "TEXT";
    const DATATYPE_DATUM            = "DATUM";
    const DATATYPE_EMAIL            = "EMAIL";
    const DATATYPE_TELEFON          = "TELEFON";
    const DATATYPE_NUMBER           = "NUMBER";
    const DATATYPE_INTEGER          = "INTEGER";
    const DATATYPE_BOOKINGOBJ       = "BOOKINGOBJ";
    const DATATYPE_STRINGARRAY      = "STRINGARRAY";
    const DATATYPE_TEXTAREA			= "TEXTAREA";
    
    //spezial Datentypen
    const DATATYPE_CONTACT_ARRAY    = "CONTACT_ARRAY";
    
    const USERROLE_IB_READER		= "rs_indiebooking_reader_only";
    const USERROLE_IB_BASIC_CUST	= "basic_indiebooking_customer";
    
    public static function check_indiebooking_ajax_referer($action, $query) {
    	$nonceOk 			= check_ajax_referer($action, $query, false);
    	if (!$nonceOk) {
    		$answer = array(
    				'CODE' => 403,
    				'MSG'  => __('You are not authorized to perform this function', 'indiebooking'),
    		);
    		wp_send_json_error($answer, 403);
    		wp_die( -1, 403 );
    	}
    	return $nonceOk;
    }
    
    public static function check_with_whitelist($value, $type = "ALL") {
        $whitelistValue = $value;
        if (isset($value)) {
            switch ($type) {
                case self::DATATYPE_TEXT:
                    $whitelistValue = self::validate_Text_Value($value);
                    break;
                case self::DATATYPE_TEXTAREA:
                	$whitelistValue = self::validate_TextArea_Value($value);
                	break;
                case self::DATATYPE_STRINGARRAY:
                    if (is_array($value)) {
                        foreach ($value as $index => $text) {
                            $value[$index] = self::check_with_whitelist($text, self::DATATYPE_TEXT);
                        }
                    }
                    break;
                case self::DATATYPE_INTEGER:
                    $whitelistValue = intval($value);
                    break;
                case self::DATATYPE_NUMBER:
                    $whitelistValue = $value;
                    break;
                case self::DATATYPE_TELEFON:
                    $whitelistValue = self::validate_Telefon_Number($value);
                    break;
                case self::DATATYPE_EMAIL:
                    $whitelistValue = sanitize_email($value);
                    break;
                case self::DATATYPE_DATUM:
                    if (!$value instanceof DateTime) {
                        $whitelistValue = self::validate_Number($value);
                    } else {
                        $whitelistValue = $value;
                    }
                    break;
                case self::DATATYPE_CONTACT_ARRAY:
                    $whitelistValue = self::validateContactArray($value);
                    break;
                case self::DATATYPE_BOOKINGOBJ:
                    if (is_array($value)) {
                        if (key_exists('bookingPostId', $value)) {
                            $value['bookingPostId'] = self::check_with_whitelist($value['bookingPostId'], self::DATATYPE_INTEGER);
                        }
                        if (key_exists('appartments', $value)) {
                            if (is_array($value['appartments'])) {
                                foreach ($value['appartments'] as $index => $apartmentvalues) {
                                    if (key_exists('id', $apartmentvalues)) {
                                        $apartmentvalues['id'] = self::check_with_whitelist($apartmentvalues['id'], self::DATATYPE_INTEGER);
                                    }
                                    if (key_exists('buchungVon', $apartmentvalues)) {
                                        $apartmentvalues['buchungVon'] = self::check_with_whitelist($apartmentvalues['buchungVon'], self::DATATYPE_DATUM);
                                    }
                                    if (key_exists('buchungBis', $apartmentvalues)) {
                                        $apartmentvalues['buchungBis'] = self::check_with_whitelist($apartmentvalues['buchungBis'], self::DATATYPE_DATUM);
                                    }
                                    if (key_exists('arrivalDayOk', $apartmentvalues)) {
                                        $apartmentvalues['arrivalDayOk'] = self::check_with_whitelist($apartmentvalues['arrivalDayOk'], self::DATATYPE_TEXT);
                                    }
                                    if (key_exists('anzPers', $apartmentvalues)) {
                                        $apartmentvalues['anzPers'] = self::check_with_whitelist($apartmentvalues['anzPers'], self::DATATYPE_INTEGER);
                                    }
                                    $value['appartments'][$index] = $apartmentvalues;
                                }
                            }
//                             $value['bookingPostId'] = self::check_with_whitelist($value['bookingPostId'], self::DATATYPE_INTEGER);
                        }
                        if (key_exists('arrivalDaysOk', $value)) {
                            $value['arrivalDaysOk'] = self::check_with_whitelist($value['arrivalDaysOk'], self::DATATYPE_TEXT);
                        }
                        
                    }
                case self::DATATYPE_ALL:
                default:
                    $whitelistValue = $value;
                    break;
            }
        } else {
            $whitelistValue = "";
        }
        return $whitelistValue;
    }
    
    private static function validate_Text_Value($text) {
//         $whitelistValue = preg_replace('/[^a-zA-Z0-9_ ]/', '', $value);
//         $whitelistValue = preg_replace('/[^a-zA-Z0-9_ \p{L}\p{N}]/u', '', $text); //hier fehlen - und # und . und ,
//         $whitelistValue = preg_replace('/[^a-zA-Z0-9_#%$?§\-:., \p{L}\p{N}]/u', '', $text); //hier fehlt &
    	$whitelistValue = preg_replace('/[^a-zA-Z0-9_#&%$?|§\-:., \p{L}\p{N}]/u', '', $text);
        $whitelistValue = sanitize_text_field($whitelistValue);
        return $whitelistValue;
    }
    
    private static function validate_TextArea_Value($text) {
//     	$whitelistValue = preg_replace('/[^a-zA-Z0-9_#&%$?§\-:., \p{L}\p{N}]/u', '', $text);
    	$whitelistValue = sanitize_textarea_field($text);
    	return $whitelistValue;
    }
    
    private static function validate_Number($number) {
        $validatetNumber = preg_replace("/[^0-9 -\+.,]/", '',  $number);
        return $validatetNumber;
    }
    
    private static function validate_Telefon_Number($telefonNr) {
        $validatetNumber = self::validate_Number($telefonNr);
//         $validatetNumber = preg_replace("/^\+?([0-9\/ -]+)?$/", '', $telefonNr);
//         $validatetNumber = preg_replace('/^(?:0{2}|\+)?[0-9]{2,3}? [1-9][0-9]* [0-9]+$/', '', $telefonNr);
        return $validatetNumber;
    }
    
    public static function encrypt_data($string, $secret_key, $secret_iv, $action = 'e', $encrypt_method = "AES-256-CBC") {
    	// you may change these values to your own
//     	$secret_key 	= 'my_simple_secret_key';
//     	$secret_iv 		= 'my_simple_secret_iv';
    	$output 		= false;
    	$key 			= hash( 'sha256', $secret_key );
    	$iv 			= substr( hash( 'sha256', $secret_iv ), 0, 16 );
    	
    	if( $action == 'e' ) {
    		$output 	= base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
    	}
    	else if( $action == 'd' ){
    		$output 	= openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
    	}
    	
    	return $output;
    }
    
    public static function check_array_key_with_whitelist($key, $array, $type = "ALL", $default = "") {
        $value = $default;
        if (key_exists($key, $array)) {
            $value = self::check_with_whitelist($array[$key], $type);
        }
        return $value;
    }
    
    private static function validateContactArray($contactArray) {
        $contactArray['firma']      = self::check_array_key_with_whitelist('firma', $contactArray, self::DATATYPE_TEXT);
        $contactArray['anrede']     = self::check_array_key_with_whitelist('anrede', $contactArray, self::DATATYPE_TEXT);
        $contactArray['titel']      = self::check_array_key_with_whitelist('titel', $contactArray, self::DATATYPE_TEXT);
        $contactArray['name']       = self::check_array_key_with_whitelist('name', $contactArray, self::DATATYPE_TEXT);
        $contactArray['firstName']  = self::check_array_key_with_whitelist('firstName', $contactArray, self::DATATYPE_TEXT);
        $contactArray['plz']        = self::check_array_key_with_whitelist('plz', $contactArray, self::DATATYPE_TEXT);
        $contactArray['strasse']    = self::check_array_key_with_whitelist('strasse', $contactArray, self::DATATYPE_TEXT);
        $contactArray['ort']        = self::check_array_key_with_whitelist('ort', $contactArray, self::DATATYPE_TEXT);
        $contactArray['strasseNr']  = self::check_array_key_with_whitelist('strasseNr', $contactArray, self::DATATYPE_NUMBER);
        $contactArray['country']    = self::check_array_key_with_whitelist('country', $contactArray, self::DATATYPE_TEXT);
        $contactArray['email']      = self::check_array_key_with_whitelist('email', $contactArray, self::DATATYPE_EMAIL);
        $contactArray['telefon']    = self::check_array_key_with_whitelist('telefon', $contactArray, self::DATATYPE_TELEFON);
        
        $contactArray['altAdress']  = self::check_array_key_with_whitelist('altAdress', $contactArray, self::DATATYPE_TEXT);
        $contactArray['firma2']     = self::check_array_key_with_whitelist('firma2', $contactArray, self::DATATYPE_TEXT);
        $contactArray['anrede2']    = self::check_array_key_with_whitelist('anrede2', $contactArray, self::DATATYPE_TEXT);
        $contactArray['titel2']     = self::check_array_key_with_whitelist('titel2', $contactArray, self::DATATYPE_TEXT);
        $contactArray['name2']      = self::check_array_key_with_whitelist('name2', $contactArray, self::DATATYPE_TEXT);
        $contactArray['firstName2'] = self::check_array_key_with_whitelist('firstName2', $contactArray, self::DATATYPE_TEXT);
        $contactArray['plz2']       = self::check_array_key_with_whitelist('plz2', $contactArray, self::DATATYPE_TEXT);
        $contactArray['strasse2']   = self::check_array_key_with_whitelist('strasse2', $contactArray, self::DATATYPE_TEXT);
        $contactArray['ort2']       = self::check_array_key_with_whitelist('ort2', $contactArray, self::DATATYPE_TEXT);
        $contactArray['strasseNr2'] = self::check_array_key_with_whitelist('strasseNr2', $contactArray, self::DATATYPE_TEXT);
        $contactArray['country2']   = self::check_array_key_with_whitelist('country2', $contactArray, self::DATATYPE_TEXT);
        $contactArray['email2']     = self::check_array_key_with_whitelist('email2', $contactArray, self::DATATYPE_EMAIL);
        $contactArray['telefon2']   = self::check_array_key_with_whitelist('telefon2', $contactArray, self::DATATYPE_TELEFON);
        
//         $contactArray['firma']      = self::check_with_whitelist($contactArray['firma'], self::DATATYPE_TEXT);
//         $contactArray['anrede']     = self::check_with_whitelist($contactArray['anrede'], self::DATATYPE_TEXT);
//         $contactArray['titel']      = self::check_with_whitelist($contactArray['titel'], self::DATATYPE_TEXT);
//         $contactArray['name']       = self::check_with_whitelist($contactArray['name'], self::DATATYPE_TEXT);
//         $contactArray['firstName']  = self::check_with_whitelist($contactArray['firstName'], self::DATATYPE_TEXT);
//         $contactArray['plz']        = self::check_with_whitelist($contactArray['plz'], self::DATATYPE_TEXT);
//         $contactArray['strasse']    = self::check_with_whitelist($contactArray['strasse'], self::DATATYPE_TEXT);
//         $contactArray['ort']        = self::check_with_whitelist($contactArray['ort'], self::DATATYPE_TEXT);
//         $contactArray['strasseNr']  = self::check_with_whitelist($contactArray['strasseNr'], self::DATATYPE_NUMBER);
//         $contactArray['country']    = self::check_with_whitelist($contactArray['country'], self::DATATYPE_TEXT);
//         $contactArray['email']      = self::check_with_whitelist($contactArray['email'], self::DATATYPE_EMAIL);
//         $contactArray['telefon']    = self::check_with_whitelist($contactArray['telefon'], self::DATATYPE_TELEFON);
        
//         $contactArray['altAdress']  = self::check_with_whitelist($contactArray['altAdress'], self::DATATYPE_TEXT);
//         $contactArray['firma2']     = self::check_with_whitelist($contactArray['firma2'], self::DATATYPE_TEXT);
//         $contactArray['anrede2']    = self::check_with_whitelist($contactArray['anrede2'], self::DATATYPE_TEXT);
//         $contactArray['titel2']     = self::check_with_whitelist($contactArray['titel2'], self::DATATYPE_TEXT);
//         $contactArray['name2']      = self::check_with_whitelist($contactArray['name2'], self::DATATYPE_TEXT);
//         $contactArray['firstName2'] = self::check_with_whitelist($contactArray['firstName2'], self::DATATYPE_TEXT);
//         $contactArray['plz2']       = self::check_with_whitelist($contactArray['plz2'], self::DATATYPE_TEXT);
//         $contactArray['strasse2']   = self::check_with_whitelist($contactArray['strasse2'], self::DATATYPE_TEXT);
//         $contactArray['ort2']       = self::check_with_whitelist($contactArray['ort2'], self::DATATYPE_TEXT);
//         $contactArray['strasseNr2'] = self::check_with_whitelist($contactArray['strasseNr2'], self::DATATYPE_TEXT);
//         $contactArray['country2']   = self::check_with_whitelist($contactArray['country2'], self::DATATYPE_TEXT);
//         $contactArray['email2']     = self::check_with_whitelist($contactArray['email2'], self::DATATYPE_EMAIL);
//         $contactArray['telefon2']   = self::check_with_whitelist($contactArray['telefon2'], self::DATATYPE_TELEFON);
        
        return $contactArray;
    }
}
// endif;
?>