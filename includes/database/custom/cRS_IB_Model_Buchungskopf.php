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
} ?>
<?php

// if ( ! class_exists( 'RS_IB_Model_Buchungskopf' ) ) :
class RS_IB_Model_Buchungskopf
{
    const RS_TABLE          = "RS_IB_BUCHUNGSKOPF_TABLE";
//     const RS_POSTTYPE           = "rsappartment_zeitraeume";
    
    const BUCHUNG_RECH_NR   = "rechnungNr";
    const BUCHUNG_NR        = "buchung_nr";
    const BUCHUNG_STATUS    = "buchung_status";
    const BUCHUNG_VON       = "buchung_von";
    const BUCHUNG_BIS       = "buchung_bis";
    const ANZAHL_NAECHTE    = "anzahl_naechte";
    
    const KUNDE_FIRMA       = "kunde_firma";
    const KUNDE_ABTEILUNG	= "kunde_abteilung";
    const KUNDE_TITEL       = "kunde_titel";
    const KUNDE_ANREDE      = "kunde_anrede";
    const KUNDE_NAME        = "kunde_name";
    const KUNDE_VORNAME     = "kunde_vorname";
    const KUNDE_STRASSE     = "kunde_strasse";
    const KUNDE_STRASSE_NR  = "kunde_strasse_nr";
    const KUNDE_LAND        = "kunde_land";
    const KUNDE_PLZ         = "kunde_plz";
    const KUNDE_ORT         = "kunde_ort";
    const KUNDE_EMAIL       = "kunde_email";
    const KUNDE_TELEFON     = "kunde_telefon";
    
    const USE_ADRESS2       = "use_adress2";
    const KUNDE_FIRMA2      = "kunde_firma2";
    const KUNDE_ABTEILUNG2	= "kunde_abteilung2";
    const KUNDE_TITEL2      = "kunde_titel2";
    const KUNDE_ANREDE2     = "kunde_anrede2";
    const KUNDE_NAME2       = "kunde_name2";
    const KUNDE_VORNAME2    = "kunde_vorname2";
    const KUNDE_STRASSE2    = "kunde_strasse2";
    const KUNDE_STRASSE_NR2 = "kunde_strasse_nr2";
    const KUNDE_LAND2       = "kunde_land2";
    const KUNDE_PLZ2        = "kunde_plz2";
    const KUNDE_ORT2        = "kunde_ort2";
    const KUNDE_EMAIL2      = "kunde_email2";
    const KUNDE_TELEFON2    = "kunde_telefon2";
    
    const BUCHUNG_WERT      = "buchung_wert";
    const ZAHLUNGSART       = "hauptzahlungsart";
    const NUTZUNGSB_KZ      = "nutzungsbedingung_kz";
    
    const USER_ID           = "user_id";
    const BUCHUNGS_DATUM    = "buchungdatum";
    const RECHNUNGS_DATUM   = "rechnungsdatum";
    
    const BOOKINGCOM_RESERVATIONID    = "bcom_reservationid";
    const BOOKINGCOM_SYNCHRONIZED_KZ  = "bcom_synchronizedkz";
    const BOOKINGCOM_BOOKING = "bcom_booking";
    
    const KUNDE_TYP			= "kunde_typ"; //bspw. company
    const KUNDE_FIRMA_NR	= "kunde_firma_nr";
    const KUNDE_FIRMA_NR_TYP = "kunde_firma_nr_typ";
    const BOOKINGCOM_GENIUS	= "bcom_genius_booker";
    
    const CHARGE_ID 		= "charge_id";
    const CUSTOM_MESSAGE 	= "custom_text";
    
    const ADMIN_KZ			= "admin_kz";
    const IGNOREMINIMUMPERIOD = "ignore_minimum_period";
    const ALLOWPASTBOOKING 	= "allow_past_booking";
    const CHANGEBILLDATE	= "change_bill_date";
    const BOOKINGTYPE		= "booking_type";
    
    const POSTID			= "post_id";
    const ANZAHLUNG			= "anzahlung";
    const ANZAHLUNGMAILKZ	= "anzahlungMailKz";
    const ANZAHLUNGBEZKZ	= "anzahlungBezahltKz";
    
    private $rechnung_nr;
    private $buchung_nr;
    private $buchung_status;
    private $buchung_von;
    private $buchung_bis;
    private $anzahl_naechte;
    
    private $kunde_firma;
    private $kunde_abteilung;
    private $kunde_title;
    private $kunde_anrede;
    private $kunde_name;
    private $kunde_vorname;
    private $kunde_strasse;
    private $kunde_plz;
    private $kunde_ort;
    private $kunde_email;
    private $kunde_telefon;
    private $kunde_strasse_nr;
    private $kunde_land;
    
    private $useAdress2;
    private $kunde_firma2;
    private $kunde_abteilung2;
    private $kunde_title2;
    private $kunde_anrede2;
    private $kunde_name2;
    private $kunde_vorname2;
    private $kunde_strasse2;
    private $kunde_plz2;
    private $kunde_ort2;
    private $kunde_email2;
    private $kunde_telefon2;
    private $kunde_strasse_nr2;
    private $kunde_land2;
    
    private $teilkoepfe         = array();
    private $rabatte            = array();
    private $calculatedPrice    = 0;
    private $fullPrice          = 0;
    private $fullMwstArray      = array();
    private $zahlungen          = array();
    private $zahlungsbetrag     = 0;
    private $hauptZahlungsart   = "";
    
    private $nutzungsbedinungKz = 0;
    private $userId             = 0;
    
    private $buchungsdatum;
    private $rechnungsdatum;
    
    private $bcomReservationId	= 0;
    private $bcomSynchronizedKZ	= 0;
    private $bcomBookingKz		= 0;
    
    
    private $kundeTyp			= "";
    private $kundeFirmaNr		= 0;
    private $kundeFirmaNrTyp	= "";
    private $bookingcomGenius	= 0;
    
    private $chargeId			= "";
    private $customText			= "";
    
    private $adminKz			= 0;
    
    private $ignore_minimum_period 	= 0;
    private $allowPastBooking 		= 0;
    private $changeBillDate 		= 0;
    private $bookingType			= 1; //1 = Buchung normal | 2 = nur Option
    
    private $postId				= 0;
    
    private $anzahlungsbetrag 	= 0;
    private $anzahlungmailkz	= 0;
    private $anzahlungBezahlt	= 0;
    
    private $laenderArray = array(
    	'AG' => 'Antigua und Barbuda',
    	'AR' => 'Argentinien',
    	'AM' => 'Armenien',
    	'AW' => 'Aruba',
    	'AU' => 'Australien',
    	'AT' => 'Österreich',
    	'AZ' => 'Aserbaidschan',
    	'BS' => 'Bahamas',
    	'BH' => 'Bahrain',
    	'BD' => 'Bangladesch',
    	'BB' => 'Barbados',
    	'BY' => 'Weißrussland',
    	'BE' => 'Belgien',
    	'BZ' => 'Belize',
    	'BJ' => 'Benin',
    	'BM' => 'Bermuda',
    	'BT' => 'Bhutan',
    	'BO' => 'Bolivien',
    	'BQ' => 'Bonaire, Sint Eustatius und Saba',
    	'BA' => 'Bosnien und Herzegowina',
    	'BW' => 'Botswana',
    	'BV' => 'Bouvetinsel',
    	'BR' => 'Brasilien',
    	'IO' => 'Britisches Territorium im Indischen Ozean',
    	'VG' => 'Britische Jungferninseln',
    	'BN' => 'Brunei',
    	'BG' => 'Bulgarien',
    	'BF' => 'Burkina Faso',
    	'BI' => 'Burundi',
    	'KH' => 'Kambodscha',
    	'CM' => 'Kamerun',
    	'CA' => 'Kanada',
    	'CV' => 'Kap Verde',
    	'KY' => 'Kaimaninseln',
    	'CF' => 'Zentralafrikanische Republik',
    	'TD' => 'Tschad',
    	'CL' => 'Chile',
    	'CN' => 'China',
    	'CX' => 'Weihnachtsinsel',
    	'CC' => 'Kokosinseln',
    	'CO' => 'Kolumbien',
    	'KM' => 'Komoren',
    	'CK' => 'Cookinseln',
    	'CR' => 'Costa Rica',
    	'HR' => 'Kroatien',
    	'CU' => 'Kuba',
    	'CW' => 'Curacao',
    	'CY' => 'Zypern',
    	'CZ' => 'Tschechische Republik',
    	'CD' => 'Demokratische Republik Kongo',
    	'DK' => 'Dänemark',
    	'DJ' => 'Dschibuti',
    	'DM' => 'Dominica',
    	'DO' => 'Dominikanische Republik',
    	'TL' => 'Osttimor',
    	'EC' => 'Ecuador',
    	'EG' => 'Ägypten',
    	'SV' => 'El Salvador',
    	'GQ' => 'Äquatorialguinea',
    	'ER' => 'Eritrea',
    	'EE' => 'Estland',
    	'ET' => 'Äthiopien',
    	'FK' => 'Falkland-Inseln',
    	'FO' => 'Färöer-Inseln',
    	'FJ' => 'Fidschi',
    	'FI' => 'Finnland',
    	'FR' => 'Frankreich',
    	'GF' => 'Französisch-Guayana',
    	'PF' => 'Französisch-Polynesien',
    	'TF' => 'Französische Süd- und Antarktisgebiete',
    	'GA' => 'Gabun',
    	'GM' => 'Gambia',
    	'GE' => 'Georgien',
    	'DE' => 'Deutschland',
    	'GH' => 'Ghana',
    	'GI' => 'Gibraltar',
    	'GR' => 'Griechenland',
    	'GL' => 'Grönland',
    	'GD' => 'Grenada',
    	'GP' => 'Guadeloupe',
    	'GU' => 'Guam',
    	'GT' => 'Guatemala',
    	'GG' => 'Guernsey',
    	'GN' => 'Guinea',
    	'GW' => 'Guinea-Bissau',
    	'GY' => 'Guyana',
    	'HT' => 'Haiti',
    	'HM' => 'Heard und McDonaldinseln',
    	'HN' => 'Honduras',
    	'HK' => 'Hongkong',
    	'HU' => 'Ungarn',
    	'IS' => 'Island',
    	'IN' => 'Indien',
    	'ID' => 'Indonesien',
    	'IR' => 'Iran',
    	'IQ' => 'Irak',
    	'IE' => 'Irland',
    	'IM' => 'Isle of Man',
    	'IL' => 'Israel',
    	'IT' => 'Italien',
    	'CI' => 'Elfenbeinküste',
    	'JM' => 'Jamaika',
    	'JP' => 'Japan',
    	'JE' => 'Jersey',
    	'JO' => 'Jordanien',
    	'KZ' => 'Kasachstan',
    	'KE' => 'Kenia',
    	'KI' => 'Kiribati',
    	'XK' => 'Kosovo',
    	'KW' => 'Kuwait',
    	'KG' => 'Kirgisistan',
    	'LA' => 'Laos',
    	'LV' => 'Lettland',
    	'LB' => 'Libanon',
    	'LS' => 'Lesotho',
    	'LR' => 'Liberia',
    	'LY' => 'Libyen',
    	'LI' => 'Liechtenstein',
    	'LT' => 'Litauen',
    	'LU' => 'Luxemburg',
    	'MO' => 'Macao',
    	'MK' => 'Mazedonien',
    	'MG' => 'Madagaskar',
    	'MW' => 'Malawi',
    	'MY' => 'Malaysia',
    	'MV' => 'Malediven',
    	'ML' => 'Mali',
    	'MT' => 'Malta',
    	'MH' => 'Marshallinseln',
    	'MQ' => 'Martinique',
    	'MR' => 'Mauretanien',
    	'MU' => 'Mauritius',
    	'YT' => 'Mayotte',
    	'MX' => 'Mexiko',
    	'FM' => 'Mikronesien',
    	'MD' => 'Moldawien',
    	'MC' => 'Monaco',
    	'MN' => 'Mongolei',
    	'ME' => 'Montenegro',
    	'MS' => 'Montserrat',
    	'MA' => 'Marokko',
    	'MZ' => 'Mosambik',
    	'MM' => 'Myanmar',
    	'NA' => 'Namibia',
    	'NR' => 'Nauru',
    	'NP' => 'Nepal',
    	'NL' => 'Niederlande',
    	'AN' => 'Niederländische Antillen',
    	'NC' => 'Neukaledonien',
    	'NZ' => 'Neuseeland',
    	'NI' => 'Nicaragua',
    	'NE' => 'Niger',
    	'NG' => 'Nigeria',
    	'NU' => 'Niue',
    	'NF' => 'Norfolkinsel',
    	'KP' => 'Nordkorea',
    	'MP' => 'Nördliche Marianen',
    	'NO' => 'Norwegen',
    	'OM' => 'Oman',
    	'PK' => 'Pakistan',
    	'PW' => 'Palau',
    	'PS' => 'Palästinensische Autonomiegebiete',
    	'PA' => 'Panama',
    	'PG' => 'Papua-Neuguinea',
    	'PY' => 'Paraguay',
    	'PE' => 'Peru',
    	'PH' => 'Philippinen',
    	'PN' => 'Pitcairninseln',
    	'PL' => 'Polen',
    	'PT' => 'Portugal',
    	'PR' => 'Puerto Rico',
    	'QA' => 'Katar',
    	'CG' => 'Republik Kongo',
    	'RE' => 'Réunion',
    	'RO' => 'Rumänien',
    	'RU' => 'Russland',
    	'RW' => 'Ruanda',
    	'BL' => 'Saint-Barthélemy',
    	'SH' => 'St. Helena',
    	'KN' => 'St. Kitts und Nevis',
    	'LC' => 'St. Lucia',
    	'MF' => 'St. Martin',
    	'PM' => 'Saint-Pierre und Miquelon',
    	'VC' => 'St. Vincent und die Grenadinen',
    	'WS' => 'Samoa',
    	'SM' => 'San Marino',
    	'ST' => 'São Tomé und Príncipe',
    	'SA' => 'Saudi-Arabien',
    	'SN' => 'Senegal',
    	'RS' => 'Serbien',
    	'CS' => 'Serbien und Montenegro',
    	'SC' => 'Seychellen',
    	'SL' => 'Sierra Leone',
    	'SG' => 'Singapur',
    	'SX' => 'Sint Maarten',
    	'SK' => 'Slowakei',
    	'SI' => 'Slowenien',
    	'SB' => 'Salomon-Inseln',
    	'SO' => 'Somalia',
    	'ZA' => 'Südafrika',
    	'GS' => 'Südgeorgien',
    	'KR' => 'Südkorea',
    	'SS' => 'Südsudan',
    	'ES' => 'Spanien',
    	'LK' => 'Sri Lanka',
    	'SD' => 'Sudan',
    	'SR' => 'Suriname',
    	'SJ' => 'Svalbard und Jan Mayen',
    	'SZ' => 'Swasiland',
    	'SE' => 'Schweden',
    	'CH' => 'Schweiz',
    	'SY' => 'Syrien',
    	'TW' => 'Taiwan',
    	'TJ' => 'Tadschikistan',
    	'TZ' => 'Tansania',
    	'TH' => 'Thailand',
    	'TG' => 'Togo',
    	'TK' => 'Tokelau',
    	'TO' => 'Tonga',
    	'TT' => 'Trinidad und Tobago',
    	'TN' => 'Tunesien',
    	'TR' => 'Türkei',
    	'TM' => 'Turkmenistan',
    	'TC' => 'Turks- und Caicosinseln',
    	'TV' => 'Tuvalu',
    	'VI' => 'Amerikanische Jungferninseln',
    	'UG' => 'Uganda',
    	'UA' => 'Ukraine',
    	'AE' => 'Vereinte Arabische Emirate',
    	'GB' => 'Großbritannien',
    	'US' => 'Vereinigte Staaten von Amerika (USA)',
    	'UM' => 'United States Minor Outlying Islands',
    	'UY' => 'Uruguay',
    	'UZ' => 'Usbekistan',
    	'VU' => 'Vanuatu',
    	'VA' => 'Vatikanstadt',
    	'VE' => 'Venezuela',
    	'VN' => 'Vietnam',
    	'WF' => 'Wallis und Futuna',
    	'EH' => 'Westsahara',
    	'YE' => 'Jemen',
    	'ZM' => 'Sambia',
    	'ZW' => 'Simbabwe',
    );
    
    
    function __clone() {
        $teilkoepfe = array();
        $rabatte    = array();
        $fullMwst   = array();
        $zahlungen  = array();
        if (!is_null($this->teilkoepfe) && sizeof($this->teilkoepfe) > 0) {
            foreach ($this->teilkoepfe as $key => $teilkopf) {
                array_push($teilkoepfe, clone $teilkopf);
            }
        }
        if (!is_null($this->rabatte) && isset($this->rabatte) && ($this->rabatte) && sizeof($this->rabatte) > 0) {
            foreach ($this->rabatte as $rkey => $rabatt) {
                array_push($rabatte, clone $rabatt);
            }
        }
        if (!is_null($this->fullMwstArray) && sizeof($this->fullMwstArray) > 0) {
            foreach ($this->fullMwstArray as $mkey => $mwst) {
                array_push($fullMwst, clone $mwst);
            }
        }
        if (!is_null($this->zahlungen) && sizeof($this->zahlungen) > 0) {
            foreach ($this->zahlungen as $zkey => $zahlung) {
                array_push($zahlungen, clone $zahlung);
            }
        }
        $this->teilkoepfe       = $teilkoepfe;
        $this->rabatte          = $rabatte;
        $this->fullMwstArray    = $fullMwst;
        $this->zahlungen        = $zahlungen;
    }
    
//     private $booking_positions  = array();
  
    public function exchangeArray($data) {
        if (isset($data[self::BUCHUNG_RECH_NR])) {
            $this->rechnung_nr = $data[self::BUCHUNG_RECH_NR];
        }
        if (isset($data[self::BUCHUNG_NR])) {
            $this->buchung_nr = $data[self::BUCHUNG_NR];
        }
        if (isset($data[self::BUCHUNG_STATUS])) {
            $this->buchung_status = $data[self::BUCHUNG_STATUS];
        }
        if (isset($data[self::BUCHUNG_VON])) {
            $this->buchung_von = new DateTime($data[self::BUCHUNG_VON]);
        }
        if (isset($data[self::BUCHUNG_BIS])) {
            $this->buchung_bis = new DateTime($data[self::BUCHUNG_BIS]);
        }
        if (isset($data[self::ANZAHL_NAECHTE])) {
            $this->anzahl_naechte = $data[self::ANZAHL_NAECHTE];
        }

        if (isset($data[self::KUNDE_FIRMA])) {
            $this->kunde_firma = $data[self::KUNDE_FIRMA];
        }
        if (isset($data[self::KUNDE_TITEL])) {
            $this->kunde_title = $data[self::KUNDE_TITEL];
        }
        if (isset($data[self::KUNDE_ANREDE])) {
            $this->kunde_anrede = $data[self::KUNDE_ANREDE];
        }
        if (isset($data[self::KUNDE_NAME])) {
            $this->kunde_name = $data[self::KUNDE_NAME];
        }
        if (isset($data[self::KUNDE_VORNAME])) {
            $this->kunde_vorname = $data[self::KUNDE_VORNAME];
        }
        if (isset($data[self::KUNDE_STRASSE])) {
            $this->kunde_strasse = $data[self::KUNDE_STRASSE];
        }
        if (isset($data[self::KUNDE_PLZ])) {
            $this->kunde_plz = $data[self::KUNDE_PLZ];
        }
        if (isset($data[self::KUNDE_ORT])) {
            $this->kunde_ort = $data[self::KUNDE_ORT];
        }
        if (isset($data[self::KUNDE_STRASSE_NR])) {
            $this->kunde_strasse_nr = $data[self::KUNDE_STRASSE_NR];
        }
        if (isset($data[self::KUNDE_LAND])) {
            $this->kunde_land = $data[self::KUNDE_LAND];
        }
        if (isset($data[self::KUNDE_EMAIL])) {
            $this->kunde_email = $data[self::KUNDE_EMAIL];
        }
        if (isset($data[self::KUNDE_TELEFON])) {
            $this->kunde_telefon = $data[self::KUNDE_TELEFON];
        }
        if (isset($data[self::KUNDE_ABTEILUNG])) {
        	$this->kunde_abteilung = $data[self::KUNDE_ABTEILUNG];
        }
        
        if (isset($data[self::USE_ADRESS2])) {
            $this->useAdress2 = $data[self::USE_ADRESS2];
        }
        if (isset($data[self::KUNDE_FIRMA2])) {
            $this->kunde_firma2 = $data[self::KUNDE_FIRMA2];
        }
        if (isset($data[self::KUNDE_TITEL2])) {
            $this->kunde_title2 = $data[self::KUNDE_TITEL2];
        }
        if (isset($data[self::KUNDE_ANREDE2])) {
            $this->kunde_anrede2 = $data[self::KUNDE_ANREDE2];
        }
        if (isset($data[self::KUNDE_NAME2])) {
            $this->kunde_name2 = $data[self::KUNDE_NAME2];
        }
        if (isset($data[self::KUNDE_VORNAME2])) {
            $this->kunde_vorname2 = $data[self::KUNDE_VORNAME2];
        }
        if (isset($data[self::KUNDE_STRASSE2])) {
            $this->kunde_strasse2 = $data[self::KUNDE_STRASSE2];
        }
        if (isset($data[self::KUNDE_PLZ2])) {
            $this->kunde_plz2 = $data[self::KUNDE_PLZ2];
        }
        if (isset($data[self::KUNDE_ORT2])) {
            $this->kunde_ort2 = $data[self::KUNDE_ORT2];
        }
        if (isset($data[self::KUNDE_STRASSE_NR2])) {
            $this->kunde_strasse_nr2 = $data[self::KUNDE_STRASSE_NR2];
        }
        if (isset($data[self::KUNDE_LAND2])) {
            $this->kunde_land2 = $data[self::KUNDE_LAND2];
        }
        if (isset($data[self::KUNDE_EMAIL2])) {
            $this->kunde_email2 = $data[self::KUNDE_EMAIL2];
        }
        if (isset($data[self::KUNDE_TELEFON2])) {
            $this->kunde_telefon2 = $data[self::KUNDE_TELEFON2];
        }
        
        if (isset($data[self::BUCHUNG_WERT])) {
            $this->calculatedPrice = $data[self::BUCHUNG_WERT];
        }
        if (isset($data[self::ZAHLUNGSART])) {
            $this->hauptZahlungsart = $data[self::ZAHLUNGSART];
        }
        if (isset($data[self::NUTZUNGSB_KZ])) {
            $this->nutzungsbedinungKz = $data[self::NUTZUNGSB_KZ];
        }
        if (isset($data[self::BUCHUNGS_DATUM])) {
            $this->buchungsdatum = new DateTime($data[self::BUCHUNGS_DATUM]);
        }
        if (isset($data[self::RECHNUNGS_DATUM])) {
        	$this->rechnungsdatum = new DateTime($data[self::RECHNUNGS_DATUM]);
        }
        if (isset($data[self::USER_ID])) {
            $this->userId = $data[self::USER_ID];
        }
        if (isset($data[self::BOOKINGCOM_RESERVATIONID])) {
        	$this->bcomReservationId = $data[self::BOOKINGCOM_RESERVATIONID];
        }
        if (isset($data[self::BOOKINGCOM_SYNCHRONIZED_KZ])) {
        	$this->bcomSynchronizedKZ = $data[self::BOOKINGCOM_SYNCHRONIZED_KZ];
        }
        if (isset($data[self::BOOKINGCOM_BOOKING])) {
        	$this->bcomBookingKz = $data[self::BOOKINGCOM_BOOKING];
        }
        if (isset($data[self::KUNDE_TYP])) {
        	$this->kundeTyp = $data[self::KUNDE_TYP];
        }
        if (isset($data[self::KUNDE_FIRMA_NR])) {
        	$this->kundeFirmaNr = $data[self::KUNDE_FIRMA_NR];
        }
        if (isset($data[self::KUNDE_FIRMA_NR_TYP])) {
        	$this->kundeFirmaNrTyp = $data[self::KUNDE_FIRMA_NR_TYP];
        }
        if (isset($data[self::KUNDE_ABTEILUNG2])) {
        	$this->kunde_abteilung2 = $data[self::KUNDE_ABTEILUNG2];
        }
        if (isset($data[self::BOOKINGCOM_GENIUS])) {
        	$this->bookingcomGenius = $data[self::BOOKINGCOM_GENIUS];
        }
        if (isset($data[self::CHARGE_ID])) {
        	$this->chargeId = $data[self::CHARGE_ID];
        }
        if (isset($data[self::CUSTOM_MESSAGE])) {
        	$this->customText = $data[self::CUSTOM_MESSAGE];
        }
        if (isset($data[self::ADMIN_KZ])) {
        	$this->adminKz = $data[self::ADMIN_KZ];
        }
        if (isset($data[self::IGNOREMINIMUMPERIOD])) {
        	$this->ignore_minimum_period = $data[self::IGNOREMINIMUMPERIOD];
        }
        if (isset($data[self::ALLOWPASTBOOKING])) {
        	$this->allowPastBooking = $data[self::ALLOWPASTBOOKING];
        }
        if (isset($data[self::CHANGEBILLDATE])) {
        	$this->changeBillDate = $data[self::CHANGEBILLDATE];
        }
        if (isset($data[self::BOOKINGTYPE])) {
        	$this->bookingType = $data[self::BOOKINGTYPE];
        }
        if (isset($data[self::POSTID])) {
        	$this->postId = $data[self::POSTID];
        }
        if (isset($data[self::ANZAHLUNG])) {
        	$this->anzahlungsbetrag = $data[self::ANZAHLUNG];
        }
        if (isset($data[self::ANZAHLUNGMAILKZ])) {
        	$this->anzahlungmailkz = $data[self::ANZAHLUNGMAILKZ];
        }
        if (isset($data[self::ANZAHLUNGBEZKZ])) {
        	$this->anzahlungBezahlt = $data[self::ANZAHLUNGBEZKZ];
        }
    }
    
    
    /**
     * @return the $buchung_nr
     */
    public function getBuchung_nr()
    {
        return $this->buchung_nr;
    }

    /**
     * @return the $buchung_status
     */
    public function getBuchung_status()
    {
        return $this->buchung_status;
    }

    /**
     * @return the $buchung_von
     */
    public function getBuchung_von()
    {
        return $this->buchung_von;
    }

    /**
     * @return the $buchung_bis
     */
    public function getBuchung_bis()
    {
        return $this->buchung_bis;
    }

    /**
     * @return the $anzahl_naechte
     */
    public function getAnzahl_naechte()
    {
        return $this->anzahl_naechte;
    }

    /**
     * @return the $kunde_name
     */
    public function getKunde_name()
    {
    	if (!isset($this->kunde_name) || is_null($this->kunde_name)) {
    		$this->kunde_name = "";
    	}
        return $this->kunde_name;
    }

    /**
     * @return the $kunde_vorname
     */
    public function getKunde_vorname()
    {
    	if (!isset($this->kunde_vorname) || is_null($this->kunde_vorname)) {
    		$this->kunde_vorname = "";
    	}
        return $this->kunde_vorname;
    }

    /**
     * @return the $kunde_strasse
     */
    public function getKunde_strasse()
    {
        return $this->kunde_strasse;
    }

    /**
     * @return the $kunde_plz
     */
    public function getKunde_plz()
    {
        return $this->kunde_plz;
    }

    /**
     * @return the $kunde_ort
     */
    public function getKunde_ort()
    {
        return $this->kunde_ort;
    }

    /**
     * @return the $kunde_email
     */
    public function getKunde_email()
    {
        return $this->kunde_email;
    }

    /**
     * @return the $kunde_telefon
     */
    public function getKunde_telefon()
    {
        return $this->kunde_telefon;
    }

    /**
     * @param field_type $buchung_nr
     */
    public function setBuchung_nr($buchung_nr)
    {
        $this->buchung_nr = $buchung_nr;
    }

    /**
     * @param field_type $buchung_status
     */
    public function setBuchung_status($buchung_status)
    {
        $this->buchung_status = $buchung_status;
    }

    /**
     * @param field_type $buchung_von
     */
    public function setBuchung_von($buchung_von)
    {
        $this->buchung_von = $buchung_von;
    }

    /**
     * @param field_type $buchung_bis
     */
    public function setBuchung_bis($buchung_bis)
    {
        $this->buchung_bis = $buchung_bis;
    }

    /**
     * @param field_type $anzahl_naechte
     */
    public function setAnzahl_naechte($anzahl_naechte)
    {
        $this->anzahl_naechte = $anzahl_naechte;
    }

    /**
     * @param field_type $kunde_name
     */
    public function setKunde_name($kunde_name)
    {
        $this->kunde_name = $kunde_name;
    }

    /**
     * @param field_type $kunde_vorname
     */
    public function setKunde_vorname($kunde_vorname)
    {
        $this->kunde_vorname = $kunde_vorname;
    }

    /**
     * @param field_type $kunde_strasse
     */
    public function setKunde_strasse($kunde_strasse)
    {
        $this->kunde_strasse = $kunde_strasse;
    }

    /**
     * @param field_type $kunde_plz
     */
    public function setKunde_plz($kunde_plz)
    {
        $this->kunde_plz = $kunde_plz;
    }

    /**
     * @param field_type $kunde_ort
     */
    public function setKunde_ort($kunde_ort)
    {
        $this->kunde_ort = $kunde_ort;
    }

    /**
     * @param field_type $kunde_email
     */
    public function setKunde_email($kunde_email)
    {
        $this->kunde_email = $kunde_email;
    }

    /**
     * @param field_type $kunde_telefon
     */
    public function setKunde_telefon($kunde_telefon)
    {
        $this->kunde_telefon = $kunde_telefon;
    }
    /**
     * @return the $teilkoepfe
     */
    public function getTeilkoepfe()
    {
        return $this->teilkoepfe;
    }

    /**
     * @param multitype: $teilkoepfe
     */
    public function setTeilkoepfe($teilkoepfe)
    {
        $this->teilkoepfe = $teilkoepfe;
    }


    public function getContactArray() {
        $contact                    = array();
        
        $contact['name']            = "";
        $contact['firstName']       = "";
        $contact['strasse']         = "";
        $contact['ort']             = "";
        $contact['plz']             = "";
        $contact['email']           = "";
        $contact['telefon']         = "";
        $contact['title']           = "";
        $contact['anrede']          = "";
        $contact['country']         = "";
        $contact['strasseNr']       = "";
        $contact['firma']           = "";
        $contact['abteilung']       = "";
        
        $contact['useAdress2']      = "";
        $contact['name2']           = "";
        $contact['firstName2']      = "";
        $contact['strasse2']        = "";
        $contact['ort2']            = "";
        $contact['plz2']            = "";
        $contact['email2']          = "";
        $contact['telefon2']        = "";
        $contact['title2']          = "";
        $contact['anrede2']         = "";
        $contact['country2']        = "";
        $contact['strasseNr2']      = "";
        $contact['firma2']          = "";
        $contact['abteilung2']       = "";
        
        if ($this->getKunde_name() !== 'dummy') {
            $contact['name']        = $this->getKunde_name();
        }
        if ($this->getKunde_vorname() !== 'dummy') {
            $contact['firstName']   = $this->getKunde_vorname();
        }
        if ($this->getKunde_strasse() !== 'dummy') {
            $contact['strasse']     = $this->getKunde_strasse();
        }
        if ($this->getKunde_ort() !== 'dummy') {
            $contact['ort']         = $this->getKunde_ort();
        }
        if ($this->getKunde_plz() !== 'dummy') {
            $contact['plz']         = $this->getKunde_plz();
        }
        if ($this->getKunde_email() !== 'dummy') {
            $contact['email']       = $this->getKunde_email();
        }
        if ($this->getKunde_telefon() !== 'dummy') {
            $contact['telefon']     = $this->getKunde_telefon();
        }
        if ($this->getKunde_telefon() !== 'dummy') {
            $contact['anrede']     = $this->getKunde_anrede();
        }
        if ($this->getKunde_telefon() !== 'dummy') {
            $contact['title']     = $this->getKunde_title();
        }
        if ($this->getKunde_land() !== 'dummy') {
            $contact['country']     = $this->getKunde_land();
        }
        if ($this->getKunde_strasse_nr() !== 'dummy') {
            $contact['strasseNr']     = $this->getKunde_strasse_nr();
        }
        if ($this->getKunde_firma() !== 'dummy') {
            $contact['firma']     = $this->getKunde_firma();
        }
        if ($this->getKunde_abteilung() !== 'dummy') {
        	$contact['abteilung']     = $this->getKunde_abteilung();
        }
        
        /*
         * Alternative Rechnungsadresse
         */
        if ($this->getUseAdress2() !== 'dummy') {
            $contact['useAdress2']   = $this->getUseAdress2();
        }
        if ($this->getKunde_name2() !== 'dummy') {
            $contact['name2']        = $this->getKunde_name2();
        }
        if ($this->getKunde_vorname2() !== 'dummy') {
            $contact['firstName2']   = $this->getKunde_vorname2();
        }
        if ($this->getKunde_strasse2() !== 'dummy') {
            $contact['strasse2']     = $this->getKunde_strasse2();
        }
        if ($this->getKunde_ort2() !== 'dummy') {
            $contact['ort2']         = $this->getKunde_ort2();
        }
        if ($this->getKunde_plz2() !== 'dummy') {
            $contact['plz2']         = $this->getKunde_plz2();
        }
        if ($this->getKunde_email2() !== 'dummy') {
            $contact['email2']       = $this->getKunde_email2();
        }
        if ($this->getKunde_telefon2() !== 'dummy') {
            $contact['telefon2']     = $this->getKunde_telefon2();
        }
        if ($this->getKunde_telefon2() !== 'dummy') {
            $contact['anrede2']     = $this->getKunde_anrede2();
        }
        if ($this->getKunde_title2() !== 'dummy') {
            $contact['title2']     = $this->getKunde_title2();
        }
        if ($this->getKunde_land2() !== 'dummy') {
            $contact['country2']     = $this->getKunde_land2();
        }
        if ($this->getKunde_strasse_nr2() !== 'dummy') {
            $contact['strasseNr2']     = $this->getKunde_strasse_nr2();
        }
        if ($this->getKunde_firma2() !== 'dummy') {
            $contact['firma2']     = $this->getKunde_firma2();
        }
        if ($this->getKunde_abteilung2() !== 'dummy') {
        	$contact['abteilung2']     = $this->getKunde_abteilung2();
        }
        
        return $contact;
    }
    /**
     * @return the $rabatte
     */
    public function getRabatte()
    {
        return $this->rabatte;
    }

    /**
     * @param multitype: $rabatte
     */
    public function setRabatte($rabatte)
    {
        $this->rabatte = $rabatte;
    }
    /**
     * @return the $calculatedPrice
     */
    public function getCalculatedPrice()
    {
        return $this->calculatedPrice;
    }

    
    public function getNettoBetrag() {
    	$netto 		= 0;
    	$fullMwst 	= 0;
    	foreach ($this->getFullMwstArray() as $mwstObj) {
    		if (!is_null($mwstObj->getMwst_prozent()) && $mwstObj->getMwst_prozent() > 0) {
    			$mwstValue = floatval(number_format($mwstObj->getMwst_wert(), 2));
//     			$fullMwst += $mwstObj->getMwst_wert();
    			$fullMwst += $mwstValue;
           	}
       	}
       	$netto = $this->getCalculatedPrice() - $fullMwst;
       	return $netto;
    }
    
    /**
     * @param number $calculatedPrice
     */
    public function setCalculatedPrice($calculatedPrice)
    {
        $this->calculatedPrice = $calculatedPrice;
    }

    
    /**
     * @return the $fullPrice
     */
    public function getFullPrice()
    {
        return $this->fullPrice;
    }

    /**
     * @param number $fullPrice
     */
    public function setFullPrice($fullPrice)
    {
        $this->fullPrice = $fullPrice;
    }
    /**
     * @return the $fullMwstArray
     */
    public function getFullMwstArray()
    {
        return $this->fullMwstArray;
    }

    /**
     * @param multitype: $fullMwstArray
     */
    public function setFullMwstArray($fullMwstArray)
    {
        $this->fullMwstArray = $fullMwstArray;
    }

	public function refreshCoupons() {
		global $RSBP_DATABASE;
		
		if (is_plugin_active('indiebooking-advanced/indiebooking_advanced.php')) {
			$gutscheinTable             = $RSBP_DATABASE->getTable(RS_IB_Model_Gutschein::RS_TABLE);
			$buchungTable               = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
			$rabattTbl                  = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungRabatt::RS_TABLE);
			$appartmentBuchungTbl       = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
			$apartmentBuchungController = new RS_IB_Appartment_Buchung_Controller(false);
			
			$buchungKopfId              = $this->getBuchung_nr();
			$modelAppartmentBuchung     = $buchungTable->getAppartmentBuchungByBuchungsKopfNr($buchungKopfId);
			if (!is_null($modelAppartmentBuchung)) {
				$coupons                = $modelAppartmentBuchung->getCoupons();
				if (sizeof($coupons) > 0) {
					foreach ($coupons as $coupon) {
						$answer        	= $apartmentBuchungController->checkApartmentCouponCode($coupon->getCode(), $this->getBuchung_nr(), false, true, $this);
					}
				}
			}
		}
	}
    
	/* @var $teilKopf RS_IB_Model_Teilbuchungskopf */
	/* @var $appartmentTable RS_IB_Table_Appartment */
	/* @var $modelApartment RS_IB_Model_Appartment */
	public function hasBookingInquiryApartments() {
		global $RSBP_DATABASE;
		
		$inquirtyApartment 			= false;
		$appartmentTable			= $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
		foreach ($this->getTeilkoepfe() as $teilKopf) {
			$apartmentId 			= $teilKopf->getAppartment_id();
			$modelApartment 		= $appartmentTable->getApartmentBaseData($apartmentId);
			$inquirtyApartment 		= $modelApartment->getOnlyInquire();
			if ($inquirtyApartment == "on") {
				$inquirtyApartment 	= true;
				break;
			} else {
				$inquirtyApartment 	= false;
			}
		}
		
		return $inquirtyApartment;
	}
	
    /* @var $rabatt RS_IB_Model_BuchungRabatt */
    /* @var $position RS_IB_Model_Buchungposition */
    /* @var $teilKopf RS_IB_Model_Teilbuchungskopf */
    /* @var $zahlung RS_IB_Model_BuchungZahlung */
    /* @var $gutschein RS_IB_Model_Gutschein */
	public function calculatePrice($recalculateCoupons = true) {
        global $RSBP_DATABASE;
        
        $calcPrice = 0;
        foreach ($this->getTeilkoepfe() as $teilKopf) {
            $calcPrice += $teilKopf->getCalculatedPrice();
        }
        $this->setFullPrice($calcPrice);
        if ($recalculateCoupons) {
        	$this->refreshCoupons();
        }
        $depositKz			= "off";
        $paymentData 		= get_option( 'rs_indiebooking_settings_payment');
        if (isset($paymentData) && $paymentData != false) {
        	$depositKz		= (key_exists('activedeposit_kz', $paymentData)) ? esc_attr__( $paymentData['activedeposit_kz'] ) : "off";
//         	$depositDays 	= (key_exists('deposit_days', $paymentlData)) ? esc_attr__( $paymentlData['deposit_days'] ) : 0;
        	$depositValue 	= (key_exists('deposit_value', $paymentData)) ? esc_attr__( $paymentData['deposit_value'] ) : 0;
        }
//         var_dump($calcPrice);
        $rabatte        = $this->getRabatte();
        if (is_array($rabatte) && (sizeof($rabatte) > 0)) {
        	$gutscheinTable				= null;
        	if (class_exists('RS_IB_Model_Gutschein')) {
            	$gutscheinTable         = $RSBP_DATABASE->getTable(RS_IB_Model_Gutschein::RS_TABLE);
        	}
            $buchungTable               = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
            $rabattTbl                  = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungRabatt::RS_TABLE);
            $appartmentBuchungTbl       = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
            $apartmentBuchungController = new RS_IB_Appartment_Buchung_Controller(false);
            $newRabatt                  = array();
            foreach ($rabatte as $rabatt) {
                $rabattOk               = true;
                if ($recalculateCoupons) {
	                $termId             = $rabatt->getRabatt_term_id();
	                if ($rabatt->getRabatt_art() == RS_IB_Model_BuchungRabatt::RABATT_ART_COUPON && !is_null($gutscheinTable)) { //1 = Aktion | 2 = Coupon | 3 Degression
	                    RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."] "."calculate Price - pruefe Gutschein");
	                    $gutschein      = $gutscheinTable->getGutscheinById($termId);
	//                 $answer             = $apartmentBuchungController->reCheckApartmentCouponCode($gutschein, $this);
	                    $answer         = $apartmentBuchungController->checkApartmentCouponCode($gutschein->getCode(), $this->getBuchung_nr(), false, true, $this);
	                    if ($answer['CODE'] == 1) {
	                        $rabattOk   = true;
	                    } else {
	                        $rabattOk   = false;
	                    }
	                } elseif ($rabatt->getRabatt_art() == RS_IB_Model_BuchungRabatt::RABATT_ART_AKTION) { //1 = Aktion | 2 = Coupon | 3 Degression
	                    //TODO Aktion erneut pruefen
	                    $rabattOk       = true;
	                } elseif ($rabatt->getRabatt_art() == RS_IB_Model_BuchungRabatt::RABATT_ART_DEGRESSION) { //1 = Aktion | 2 = Coupon | 3 Degression
	                	$rabattOk       = true;
	                }
                }
                if ($rabattOk) {
                	if ($rabatt->getBerechnung_art() != 4) {
	                    if ($rabatt->getPlus_minus_kz() == 1) {
	                        if ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_PROZENT) {
	                            $calcPrice  = $calcPrice * (1 - (abs($rabatt->getRabatt_wert()) / 100));
	                        } elseif ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_TOTAL) {
	                            $calcPrice  = $calcPrice - $rabatt->getRabatt_wert();
	                        }
	                    } else {
	                        if ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_PROZENT) {
	                            $calcPrice  = $calcPrice * (1 + (abs($rabatt->getRabatt_wert()) / 100));
	                        } elseif ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_TOTAL) {
	                            $calcPrice  = $calcPrice + $rabatt->getRabatt_wert();
	                        }
	                    }
                	}
//                 	else {
//                 		if ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_PROZENT) {
//                 			$calcPrice  = $calcPrice * (1 - (abs($rabatt->getRabatt_wert()) / 100));
//                 		} elseif ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_TOTAL) {
//                 			$calcPrice  = $calcPrice - ($rabatt->getRabatt_wert() * ;
//                 		}
//                 	}
                    array_push($newRabatt, $rabatt);
                } else {
                	/*
                    $buchungKopfId              = $this->getBuchung_nr();
                    $rabattTbl->deleteBuchungRabatt($rabatt);
                    $modelAppartmentBuchung     = $buchungTable->getAppartmentBuchungByBuchungsKopfNr($buchungKopfId);
                    if (!is_null($modelAppartmentBuchung)) {
                        $coupons                    = $modelAppartmentBuchung->getCoupons();
                        $postId                     = $modelAppartmentBuchung->getPostId();
                        $newCoupons                 = array();
    
                        foreach ($coupons as $coupon) {
                            if (!$coupon->getTermId() == $termId) {
                                array_push($newCoupons, $coupon);
                            }
                        }
                        $appartmentBuchungTbl->updateBookingCoupons($postId, $newCoupons);
                        $gutscheinTable->resetOneGutschein($gutschein);
                    }
                    */
                }
                $calcPrice      = round($calcPrice, 2);
            }
            $this->setRabatte($newRabatt);
        } else {
//             RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."] "."no rabatts found");
        }
        $calcPrice      = round($calcPrice, 2);
        $this->setCalculatedPrice($calcPrice);
        $this->calculatePositionValues();
        $zahlungen      = $this->getZahlungen();
        $zahlungsbetrag = $calcPrice;
        //TODO Pruefen ob es so wirklich passt!
        /*
         * Update 20.06.2016 - Carsten Schmitt
         * Wenn der berechnete Wert weniger wie 0 betraegt, handelt es sich (wahrscheinlich) um eine Stornierung
         * hier muss noch einmal geschaut werden, ob das wirklich so immer stimmt!
         */
        if ($calcPrice < 0 && (sizeof($zahlungen) <= 0)) { //Es handelt sich um eine Stornierung!
            $zahlungsbetrag = 0;
            RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."] "."Zahlungsbetrag = 0 - Stauts: ".$this->getBuchung_status());
        }
        if (is_array($zahlungen) && (sizeof($zahlungen) > 0)) {
            foreach ($zahlungen as $zahlung) {
                $zahlungsbetrag = $zahlungsbetrag - $zahlung->getZahlungbetrag();
            }
        }
        $this->setZahlungsbetrag($zahlungsbetrag);
        if ($depositKz == "on" && intval($depositValue) > 0) {
        	$bookingStatus 	= $this->getBuchung_status();
        	if ($bookingStatus != "rs_ib-storno") {
	        	$deposit		= ($calcPrice / 100) * $depositValue;
	        	$this->setAnzahlungsbetrag($deposit);
        	}
        } else if (!is_null($this->getAnzahlungsbetrag()) && floatval($this->getAnzahlungsbetrag()) > 0) {
        	//do nothing
        } else {
        	$this->setAnzahlungsbetrag(-1);
        }
    }
    
    /**
     * Diese Methode berechnet die Positionsgenauen Werte aus.
     * Ist ein Rabatt auf Buchungskopfebene gegeben, muss dieser auf die einzelnen Positionen verteilt werden
     * damit die MwSt am Ende korrekt ausgegeben wird.
     */
    /* @var $kopf RS_IB_Model_Teilbuchungskopf */
    /* @var $position RS_IB_Model_Buchungposition */
    /* @var $maxPos RS_IB_Model_Buchungposition */
    public function calculatePositionValues() {
//         $refValue                   = abs($this->getFullPrice()); //warum hier abs??
        $refValue                   = $this->getFullPrice();
        $maxPos                     = null;
        $summe                      = 0;
        $rabatte                    = $this->getRabatte();
        $mwstArray                  = array();
        $mwstObjArray               = array();
        if (is_array($rabatte) && (sizeof($rabatte) > 0)) {
            foreach ($rabatte as $rabatt) {
                $summe              = 0;
                if ($rabatt->getPlus_minus_kz() == 1) {
                    if ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_PROZENT) {
                        $prozentSatz    = ($rabatt->getRabatt_wert() / 100);
                        if ($refValue < 0 && $prozentSatz < 0) {
                            $prozentSatz = abs($prozentSatz);
                        }
                        $refValue       = $refValue - ($refValue * $prozentSatz);
                    } elseif ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_TOTAL) {
                        if ($refValue != 0) {
                            $prozentSatz    =  (100 / $refValue ) * $rabatt->getRabatt_wert();
                            $prozentSatz    = ($prozentSatz / 100);
                            $refValue       = $refValue - $rabatt->getRabatt_wert();
                        }
                    }
                } else {
                    if ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_PROZENT) {
                        $prozentSatz    = ($rabatt->getRabatt_wert() / 100);
                        if ($refValue < 0 && $prozentSatz < 0) {
                            $prozentSatz = abs($prozentSatz);
                        }
                        $refValue       = $refValue + ($refValue * $prozentSatz);
                    } elseif ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_TOTAL) {
                        if ($refValue != 0) {
                            $prozentSatz    =  (100 / $refValue ) * $rabatt->getRabatt_wert();
                            $prozentSatz    = ($prozentSatz / 100);
                            $refValue       = $refValue + $rabatt->getRabatt_wert();
                        }
                    }
                }
                foreach ($this->getTeilkoepfe() as $kopf) {
                    foreach ($kopf->getPositionen() as $position) {
//                         $posPrice   = $position->getCalculatedPrice();
                        $posPrice   = $position->getCalcPosPrice();
//                         echo $posPrice;
                        $posPrice   = $posPrice * (1 - $prozentSatz);
                        $posPrice   = round($posPrice, 2);
                        $position->setCalcPosPrice($posPrice);
                        if (is_null($maxPos) || $maxPos->getCalculatedPrice() < $position->getCalculatedPrice()) {
                            $maxPos = $position;
                        }
                        $summe      += $posPrice;
                    }
                }
            }
            //TODO!!!!!
            $differenz          = round(abs($summe - $this->getCalculatedPrice()), 2);
//             $differenz          = round(($summe - $this->getCalculatedPrice()), 2);
            if ($differenz > 0 && !is_null($maxPos)) {
                if ($summe > $this->getCalculatedPrice()) {
                    $maxPos->setCalcPosPrice($maxPos->getCalcPosPrice() - $differenz);
                } else {
                    $maxPos->setCalcPosPrice($maxPos->getCalcPosPrice() + $differenz);
                }
            }
        }
        foreach ($this->getTeilkoepfe() as $kopf) {
            foreach ($kopf->getPositionen() as $position) {
                $mwstObj                = new RS_IB_Model_BuchungMwSt();
                $mwstKey                = $position->getMwstTermId();
//                 $mwstKey                = $position->getMwst_prozent() * 100;
                $mwstKey = (string)$mwstKey;
                if (key_exists($mwstKey, $mwstArray)) {
                    $mwstObj            = $mwstArray[$mwstKey];
                } else {
                    $mwstObj            = new RS_IB_Model_BuchungMwSt();
                    $mwstObj->setMwst_wert(0);
                }
                $mwstObj->setBuchung_nr($this->getBuchung_nr());
                $mwstObj->setMwst_id($mwstKey);
                $mwstObj->setMwst_prozent($position->getMwst_prozent() * 100);
                $mwstObj->setMwst_wert($mwstObj->getMwst_wert() + $position->getMwst_wert());
                $mwstObj->setMwstBrutto($mwstObj->getMwstBrutto() + $position->getCalcPosPrice());
                $mwstArray[$mwstKey]    = $mwstObj;
            }
        }
        $this->setFullMwstArray($mwstArray);
    }
    /**
     * @return the $zahlungen
     */
    public function getZahlungen()
    {
        return $this->zahlungen;
    }

    /**
     * @param multitype: $zahlungen
     */
    public function setZahlungen($zahlungen)
    {
        $this->zahlungen = $zahlungen;
    }
    /**
     * @return the $zahlungsbetrag
     */
    public function getZahlungsbetrag()
    {
        return $this->zahlungsbetrag;
    }

    /**
     * @param number $zahlungsbetrag
     */
    public function setZahlungsbetrag($zahlungsbetrag)
    {
        $this->zahlungsbetrag = $zahlungsbetrag;
    }
    /**
     * @return the $hauptZahlungsart
     */
    public function getHauptZahlungsart()
    {
        if (is_null($this->hauptZahlungsart)) {
            return "";
        }
        return strtoupper($this->hauptZahlungsart);
    }

    /**
     * @param number $hauptZahlungsart
     */
    public function setHauptZahlungsart($hauptZahlungsart)
    {
        $this->hauptZahlungsart = $hauptZahlungsart;
    }
    /**
     * @return the $kunde_title
     */
    public function getKunde_title()
    {
        return $this->kunde_title;
    }

    /**
     * @return the $kunde_anrede
     */
    public function getKunde_anrede()
    {
        return $this->kunde_anrede;
    }

    /**
     * @param field_type $kunde_title
     */
    public function setKunde_title($kunde_title)
    {
        $this->kunde_title = $kunde_title;
    }

    /**
     * @param field_type $kunde_anrede
     */
    public function setKunde_anrede($kunde_anrede)
    {
        if (intval($kunde_anrede) > 0) {
            switch (intval($kunde_anrede)) {
                case 1:
                    $this->kunde_anrede = __("Mr.", 'indiebooking');
                    break;
                case 2:
                    $this->kunde_anrede = __("Mrs.", 'indiebooking');
                    break;
                case 3:
                	$this->kunde_anrede = __("", 'indiebooking');
                	break;
                case 4:
                    $this->kunde_anrede = __("Department", 'indiebooking');
                    break;
            }
        } else {
            $this->kunde_anrede = $kunde_anrede;
        }
    }
    /**
     * @return the $nutzungsbedinungKz
     */
    public function getNutzungsbedinungKz()
    {
        return $this->nutzungsbedinungKz;
    }

    /**
     * @param number $nutzungsbedinungKz
     */
    public function setNutzungsbedinungKz($nutzungsbedinungKz)
    {
        $this->nutzungsbedinungKz = $nutzungsbedinungKz;
    }
    /**
     * @return the $rechnung_nr
     */
    public function getRechnung_nr()
    {
        return $this->rechnung_nr;
    }

    /**
     * @param field_type $rechnung_nr
     */
    public function setRechnung_nr($rechnung_nr)
    {
        $this->rechnung_nr = $rechnung_nr;
    }
    /**
     * @return the $kunde_strasse_nr
     */
    public function getKunde_strasse_nr()
    {
        if (is_null($this->kunde_strasse_nr)) {
            return "";
        }
        return $this->kunde_strasse_nr;
    }

    /**
     * @return the $kunde_land
     */
    public function getKunde_land()
    {
        if (is_null($this->kunde_land)) {
            return "";
        }
        return $this->kunde_land;
    }

    /**
     * @param field_type $kunde_strasse_nr
     */
    public function setKunde_strasse_nr($kunde_strasse_nr)
    {
        $this->kunde_strasse_nr = $kunde_strasse_nr;
    }

    /**
     * @param field_type $kunde_land
     */
    public function setKunde_land($kunde_land)
    {
    	if (key_exists(strtoupper($kunde_land), $this->laenderArray)) {
    		$kunde_land 	= $this->laenderArray[strtoupper($kunde_land)];
    	}
        $this->kunde_land = $kunde_land;
    }
    /**
     * @return the $buchungsdatum
     */
    public function getBuchungsdatum()
    {
        if (is_null($this->buchungsdatum)) {
            return new DateTime('0000-00-00 00:00:00');
        }
        return $this->buchungsdatum;
    }

    /**
     * @param DateTime $buchungsdatum
     */
    public function setBuchungsdatum($buchungsdatum)
    {
        $this->buchungsdatum = $buchungsdatum;
    }
    /**
     * @return the $kunde_firma
     */
    public function getKunde_firma()
    {
        if (is_null($this->kunde_firma)) {
            return "";
        }
        return $this->kunde_firma;
    }

    /**
     * @param field_type $kunde_firma
     */
    public function setKunde_firma($kunde_firma)
    {
        $this->kunde_firma = $kunde_firma;
    }
    /**
     * @return the $kunde_firma2
     */
    public function getKunde_firma2()
    {
        if (is_null($this->kunde_firma2)) {
            return "";
        }
        return $this->kunde_firma2;
    }

    /**
     * @return the $kunde_title2
     */
    public function getKunde_title2()
    {
        if (is_null($this->kunde_title2)) {
            return "";
        }
        return $this->kunde_title2;
    }

    /**
     * @return the $kunde_anrede2
     */
    public function getKunde_anrede2()
    {
        if (is_null($this->kunde_anrede2)) {
            return "";
        }
        return $this->kunde_anrede2;
    }

    /**
     * @return the $kunde_name2
     */
    public function getKunde_name2()
    {
        if (is_null($this->kunde_name2)) {
            return "";
        }
        return $this->kunde_name2;
    }

    /**
     * @return the $kunde_vorname2
     */
    public function getKunde_vorname2()
    {
        if (is_null($this->kunde_vorname2)) {
            return "";
        }
        return $this->kunde_vorname2;
    }

    /**
     * @return the $kunde_strasse2
     */
    public function getKunde_strasse2()
    {
        if (is_null($this->kunde_strasse2)) {
            return "";
        }
        return $this->kunde_strasse2;
    }

    /**
     * @return the $kunde_plz2
     */
    public function getKunde_plz2()
    {
        if (is_null($this->kunde_plz2)) {
            return "";
        }
        return $this->kunde_plz2;
    }

    /**
     * @return the $kunde_ort2
     */
    public function getKunde_ort2()
    {
        if (is_null($this->kunde_ort2)) {
            return "";
        }
        return $this->kunde_ort2;
    }

    /**
     * @return the $kunde_email2
     */
    public function getKunde_email2()
    {
        if (is_null($this->kunde_email2)) {
            return "";
        }
        return $this->kunde_email2;
    }

    /**
     * @return the $kunde_telefon2
     */
    public function getKunde_telefon2()
    {
        if (is_null($this->kunde_telefon2)) {
            return "";
        }
        return $this->kunde_telefon2;
    }

    /**
     * @return the $kunde_strasse_nr2
     */
    public function getKunde_strasse_nr2()
    {
        if (is_null($this->kunde_strasse_nr2)) {
            return "";
        }
        return $this->kunde_strasse_nr2;
    }

    /**
     * @return the $kunde_land2
     */
    public function getKunde_land2()
    {
        if (is_null($this->kunde_land2)) {
            return "";
        }
        return $this->kunde_land2;
    }

    /**
     * @param field_type $kunde_firma2
     */
    public function setKunde_firma2($kunde_firma2)
    {
        $this->kunde_firma2 = $kunde_firma2;
    }

    /**
     * @param field_type $kunde_title2
     */
    public function setKunde_title2($kunde_title2)
    {
        $this->kunde_title2 = $kunde_title2;
    }

    /**
     * @param field_type $kunde_anrede2
     */
    public function setKunde_anrede2($kunde_anrede2)
    {
    	if (intval($kunde_anrede2) > 0) {
    		switch (intval($kunde_anrede2)) {
    			case 1:
    				$this->kunde_anrede2 = __("Mr.", 'indiebooking');
    				break;
    			case 2:
    				$this->kunde_anrede2 = __("Mrs.", 'indiebooking');
    				break;
    			case 3:
    				$this->kunde_anrede2 = __("", 'indiebooking');
    				break;
    			case 4:
    			    $this->kunde_anrede2 = __("Department", 'indiebooking');
    			    break;
    		}
    	} else {
    		$this->kunde_anrede2 = $kunde_anrede2;
    	}
    }

    /**
     * @param field_type $kunde_name2
     */
    public function setKunde_name2($kunde_name2)
    {
        $this->kunde_name2 = $kunde_name2;
    }

    /**
     * @param field_type $kunde_vorname2
     */
    public function setKunde_vorname2($kunde_vorname2)
    {
        $this->kunde_vorname2 = $kunde_vorname2;
    }

    /**
     * @param field_type $kunde_strasse2
     */
    public function setKunde_strasse2($kunde_strasse2)
    {
        $this->kunde_strasse2 = $kunde_strasse2;
    }

    /**
     * @param field_type $kunde_plz2
     */
    public function setKunde_plz2($kunde_plz2)
    {
        $this->kunde_plz2 = $kunde_plz2;
    }

    /**
     * @param field_type $kunde_ort2
     */
    public function setKunde_ort2($kunde_ort2)
    {
        $this->kunde_ort2 = $kunde_ort2;
    }

    /**
     * @param field_type $kunde_email2
     */
    public function setKunde_email2($kunde_email2)
    {
        $this->kunde_email2 = $kunde_email2;
    }

    /**
     * @param field_type $kunde_telefon2
     */
    public function setKunde_telefon2($kunde_telefon2)
    {
        $this->kunde_telefon2 = $kunde_telefon2;
    }

    /**
     * @param field_type $kunde_strasse_nr2
     */
    public function setKunde_strasse_nr2($kunde_strasse_nr2)
    {
        $this->kunde_strasse_nr2 = $kunde_strasse_nr2;
    }

    /**
     * @param field_type $kunde_land2
     */
    public function setKunde_land2($kunde_land2)
    {
        $this->kunde_land2 = $kunde_land2;
    }
    /**
     * @return the $useAdress2
     */
    public function getUseAdress2()
    {
        if (is_null($this->useAdress2)) {
            return "";
        }
        elseif (is_string($this->useAdress2)) {
            if ($this->useAdress2 == "1") {
                return true;
            }
            elseif ($this->useAdress2 == "0") {
                return false;
            }
            elseif ($this->useAdress2 == "false") {
                return false;
            }
            elseif ($this->useAdress2 == "true") {
                return true;
            }
        }
        return $this->useAdress2;
    }

    /**
     * @param field_type $useAdress2
     */
    public function setUseAdress2($useAdress2)
    {
        $this->useAdress2 = $useAdress2;
    }
    /**
     * @return the $userId
     */
    public function getUserId()
    {
        if (is_null($this->userId)) {
            return 0;
        }
        return $this->userId;
    }

    /**
     * @param number $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }
    
	public function getBcomReservationId() {
		return $this->bcomReservationId;
	}
	public function setBcomReservationId($bcomReservationId) {
		$this->bcomReservationId = $bcomReservationId;
		return $this;
	}
	public function getBcomSynchronizedKZ() {
		if (is_null($this->bcomSynchronizedKZ)) {
			$this->bcomSynchronizedKZ = 0;
		}
		return $this->bcomSynchronizedKZ;
	}
	public function setBcomSynchronizedKZ($bcomSynchronizedKZ) {
		$this->bcomSynchronizedKZ = $bcomSynchronizedKZ;
		return $this;
	}
	public function getBcomBookingKz() {
		return $this->bcomBookingKz;
	}
	public function setBcomBookingKz($bcomBookingKz) {
		$this->bcomBookingKz = $bcomBookingKz;
		return $this;
	}
	public function getKundeTyp() {
		return $this->kundeTyp;
	}
	public function setKundeTyp($kundeTyp) {
		$this->kundeTyp = $kundeTyp;
		return $this;
	}
	public function getKundeFirmaNr() {
		
		return $this->kundeFirmaNr;
	}
	public function setKundeFirmaNr($kundeFirmaNr) {
		$this->kundeFirmaNr = $kundeFirmaNr;
		return $this;
	}
	public function getKundeFirmaNrTyp() {
		if (is_null($this->kundeFirmaNrTyp)) {
			return "";
		}
		return $this->kundeFirmaNrTyp;
	}
	public function setKundeFirmaNrTyp($kundeFirmaNrTyp) {
		$this->kundeFirmaNrTyp = $kundeFirmaNrTyp;
		return $this;
	}
	public function getBookingcomGenius() {
		if (is_null($this->bookingcomGenius)) {
			return 0;
		}
		return $this->bookingcomGenius;
	}
	public function setBookingcomGenius($bookingcomGenius) {
		$this->bookingcomGenius = $bookingcomGenius;
		return $this;
	}
	
	public function getChargeId() {
		if (is_null($this->chargeId)) {
			$this->chargeId = "";
		}
		return $this->chargeId;
	}
	public function setChargeId($chargeId) {
		$this->chargeId = $chargeId;
		return $this;
	}
	
	public function getHauptzahlungsartBeschreibungsText() {
		if ($this->getHauptZahlungsart() == "INVOICE") {
			$zahlungsbezeichnung    = __("pay by invoice", 'indiebooking');
		} elseif ($this->getHauptZahlungsart() == "PAYPALEXPRESS") {
			$zahlungsbezeichnung    = __("paypal express payment", 'indiebooking');
		} elseif ($this->getHauptZahlungsart() == "PAYPAL") {
			$zahlungsbezeichnung    = __("paypal payment", 'indiebooking');
		} elseif ($this->getHauptZahlungsart() == "STRIPESOFORT") {
			$zahlungsbezeichnung    = __("sofort payment", 'indiebooking');
		} elseif ($this->getHauptZahlungsart() == "STRIPECREDITCARD") {
			$zahlungsbezeichnung    = __("credit card", 'indiebooking');
		}
		return $zahlungsbezeichnung;
	}
	
	public function getCustomText() {
		if (is_null($this->customText)) {
			return "";
		}
		return $this->customText;
	}
	
	public function setCustomText($customText) {
		$this->customText = $customText;
		return $this;
	}
	
	public function getAdminKz() {
		if (is_null($this->adminKz)) {
			$this->adminKz = 0;
		}
		return $this->adminKz;
	}
	public function setAdminKz($adminKz) {
		$this->adminKz = $adminKz;
		return $this;
	}
	
	public function getIgnoreMinimumPeriod() {
		if ($this->ignore_minimum_period === "on") {
			return 1;
		} else if ($this->ignore_minimum_period === "off" || !$this->ignore_minimum_period) {
// 		} else {
			return 0;
		}
		return $this->ignore_minimum_period;
	}
	
	public function setIgnoreMinimumPeriod($ignore_minimum_period) {
		if ($ignore_minimum_period == "on" || $ignore_minimum_period == 1) {
			$this->ignore_minimum_period = 1;
		} else {
			$this->ignore_minimum_period = 0;
		}
		return $this;
	}
	/**
	 * @return boolean
	 */
	public function getAllowPastBooking()
	{
		if ($this->allowPastBooking == "on" || $this->allowPastBooking == true || $this->allowPastBooking == 1) {
			return 1;
		} else {
			return 0;
		}
	}

	/**
	 * @param boolean $allowPastBooking
	 */
	public function setAllowPastBooking($allowPastBooking)
	{
		$this->allowPastBooking = $allowPastBooking;
	}
	/**
	 * @return number
	 */
	public function getChangeBillDate()
	{
		if ($this->changeBillDate === "on" || $this->changeBillDate == true || $this->changeBillDate == 1) {
			return 1;
		} else {
			return 0;
		}
	}

	/**
	 * @param number $changeBillDate
	 */
	public function setChangeBillDate($changeBillDate)
	{
		$this->changeBillDate = $changeBillDate;
	}
	/**
	 * @return number
	 */
	public function getBookingType()
	{
		if (is_null($this->bookingType)) {
			return 1;
		}
		return $this->bookingType;
	}

	/**
	 * @param number $bookingType
	 */
	public function setBookingType($bookingType)
	{
		$this->bookingType = $bookingType;
	}
	/**
	 * @return mixed
	 */
	public function getRechnungsdatum()
	{
		if (is_null($this->rechnungsdatum)) {
// 			return $this->getBuchungsdatum();
			return new DateTime('0000-00-00 00:00:00');
		}
		return $this->rechnungsdatum;
	}

	/**
	 * @param mixed $rechnungsdatum
	 */
	public function setRechnungsdatum($rechnungsdatum)
	{
		$this->rechnungsdatum = $rechnungsdatum;
	}
	/**
	 * @return mixed
	 */
	public function getKunde_abteilung()
	{
		return $this->kunde_abteilung;
	}

	/**
	 * @return mixed
	 */
	public function getKunde_abteilung2()
	{
		return $this->kunde_abteilung2;
	}

	/**
	 * @param mixed $kunde_abteilung
	 */
	public function setKunde_abteilung($kunde_abteilung)
	{
		$this->kunde_abteilung = $kunde_abteilung;
	}

	/**
	 * @param mixed $kunde_abteilung2
	 */
	public function setKunde_abteilung2($kunde_abteilung2)
	{
		$this->kunde_abteilung2 = $kunde_abteilung2;
	}
	/**
	 * @return Ambigous <number, unknown>
	 */
	/**
	 * @return number
	 */
	public function getPostId()
	{
		return $this->postId;
	}

	/**
	 * @param number $postId
	 */
	public function setPostId($postId)
	{
		$this->postId = $postId;
	}
	/**
	 * @return number
	 */
	public function getAnzahlungsbetrag()
	{
		if (!isset($this->anzahlungsbetrag) || is_null($this->anzahlungsbetrag)) {
			$this->anzahlungsbetrag = -1;
		}
		return $this->anzahlungsbetrag;
	}

	/**
	 * @param number $anzahlungsbetrag
	 */
	public function setAnzahlungsbetrag($anzahlungsbetrag)
	{
		$this->anzahlungsbetrag = $anzahlungsbetrag;
	}
	/**
	 * @return number
	 */
	public function getAnzahlungmailkz()
	{
		return $this->anzahlungmailkz;
	}

	/**
	 * @param number $anzahlungmailkz
	 */
	public function setAnzahlungmailkz($anzahlungmailkz)
	{
		$this->anzahlungmailkz = $anzahlungmailkz;
	}
	/**
	 * @return number
	 */
	public function getAnzahlungBezahlt()
	{
		return $this->anzahlungBezahlt;
	}

	/**
	 * @param number $anzahlungBezahlt
	 */
	public function setAnzahlungBezahlt($anzahlungBezahlt)
	{
		$this->anzahlungBezahlt = $anzahlungBezahlt;
	}






}
// endif;