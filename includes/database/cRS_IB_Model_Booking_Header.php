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

// if ( ! class_exists( 'RS_IB_Model_Booking_Header' ) ) :
class RS_IB_Model_Booking_Header
{
    const RS_TABLE              = "RS_IB_BOOKING_HEADER_TABLE";
//     const RS_POSTTYPE           = "rsappartment_zeitraeume";
    
    const BOOKING_ID            = "booking_id";
    const BOOKING_STATUS        = "booking_status";
    const DATE_FROM             = "date_from";
    const DATE_TO               = "date_to";
    const NUMBER_OF_NIGHTS      = "number_nights";
    const CUSTOMER_NAME         = "customer_name";
    const CUSTOMER_FIRST_NAME   = "customer_first_name";
    const CUSTOMER_LOCATION     = "customer_location";
    const CUSTOMER_EMAIL        = "customer_email";
    const CUSTOMER_TELEFON      = "customer_telefon";
    const CUSTOMER_STRASSE      = "customer_strasse";
    
    private $booking_id;
    private $booking_status;
    private $date_from;
    private $date_to;
    private $number_of_nights;
    private $customer_name;
    private $customer_first_name;
    private $customer_location;
    private $customer_email;
    private $customer_telefon;
    private $customer_strasse;
    
    private $booking_positions  = array();
    
    public function exchangeArray($data) {
        $booking_id             = 0;
        $booking_status         = "";
        $date_from              = 0;
        $date_to                = 0;
        $number_of_nights       = 0;
        $customer_name          = "";
        $customer_first_name    = "";
        $customer_location      = "";
        $customer_email         = "";
        $customer_telefon       = "";
        
        if (isset($data[self::BOOKING_ID])) {
            
        }
        if (isset($data[self::BOOKING_STATUS])) {
            
        }
        if (isset($data[self::DATE_FROM])) {
            
        }
        if (isset($data[self::DATE_TO])) {
            
        }
        if (isset($data[self::NUMBER_OF_NIGHTS])) {
            
        }
        if (isset($data[self::CUSTOMER_NAME])) {
            
        }
        if (isset($data[self::CUSTOMER_FIRST_NAME])) {
            
        }
        if (isset($data[self::CUSTOMER_LOCATION])) {
            
        }
        if (isset($data[self::CUSTOMER_EMAIL])) {
            
        }
        if (isset($data[self::CUSTOMER_TELEFON])) {
            
        }
        
        $this->setBooking_id($booking_id);
        $this->setBooking_status($booking_status);
        $this->setDate_from($date_from);
        $this->setDate_to($date_to);
        $this->setNumber_of_nights($number_of_nights);
        $this->setCustomer_name($customer_name);
        $this->setCustomer_first_name($customer_first_name);
        $this->setCustomer_location($customer_location  );
        $this->setCustomer_email($customer_email);
        $this->setCustomer_telefon($customer_telefon);
    }
    
    
    /**
     * @return the $booking_id
     */
    public function getBooking_id()
    {
        $bookId = $this->booking_id;
        if (is_null($bookId)) {
            $bookId = 0;
        }
        return $bookId;
    }

    /**
     * @return the $booking_status
     */
    public function getBooking_status()
    {
        $status = $this->booking_status;
        if (is_null($status)) {
            $status = "";
        }
        return $status;
    }

    /**
     * @return the $date_from
     */
    public function getDate_from()
    {
        $df = $this->date_from;
        if (is_null($df)) {
            $df = "00.00.0000";
        }
        return $df;
    }

    /**
     * @return the $date_to
     */
    public function getDate_to()
    {
        $dt = $this->date_to;
        if (is_null($dt)) {
            $dt = "00.00.0000";
        }
        return $dt;
    }

    /**
     * @return the $number_of_nights
     */
    public function getNumber_of_nights()
    {
        $nOn = $this->number_of_nights;
        if (is_null($nOn)) {
            $nOn = 0;
        }
        return $nOn;
    }

    /**
     * @return the $customer_name
     */
    public function getCustomer_name()
    {
        $cust_name = $this->customer_name;
        if (is_null($cust_name)) {
            $cust_name = "";
        }
        return $cust_name;
    }

    /**
     * @return the $customer_first_name
     */
    public function getCustomer_first_name()
    {
        $cust_name = $this->customer_first_name;
        if (is_null($cust_name)) {
            $cust_name = "";
        }
        return $cust_name;
    }

    /**
     * @return the $customer_location
     */
    public function getCustomer_location()
    {
        $cust_loc = $this->customer_location;
        if (is_null($cust_loc)) {
            $cust_loc = "";
        }
        return $cust_loc;
    }

    /**
     * @return the $customer_email
     */
    public function getCustomer_email()
    {
        $cust_mail = $this->customer_email;
        if (is_null($cust_mail)) {
            $cust_mail = "";
        }
        return $cust_mail;
    }

    /**
     * @return the $customer_telefon
     */
    public function getCustomer_telefon()
    {
        $cust_tel = $this->customer_telefon;
        if (is_null($cust_tel)) {
            $cust_tel = "";
        }
        return $cust_tel;
    }

    /**
     * @param field_type $booking_id
     */
    public function setBooking_id($booking_id)
    {
        $this->booking_id = $booking_id;
    }

    /**
     * @param field_type $booking_status
     */
    public function setBooking_status($booking_status)
    {
        $this->booking_status = $booking_status;
    }

    /**
     * @param field_type $date_from
     */
    public function setDate_from($date_from)
    {
        $this->date_from = $date_from;
    }

    /**
     * @param field_type $date_to
     */
    public function setDate_to($date_to)
    {
        $this->date_to = $date_to;
    }

    /**
     * @param field_type $number_of_nights
     */
    public function setNumber_of_nights($number_of_nights)
    {
        $this->number_of_nights = $number_of_nights;
    }

    /**
     * @param field_type $customer_name
     */
    public function setCustomer_name($customer_name)
    {
        $this->customer_name = $customer_name;
    }

    /**
     * @param field_type $customer_first_name
     */
    public function setCustomer_first_name($customer_first_name)
    {
        $this->customer_first_name = $customer_first_name;
    }

    /**
     * @param field_type $customer_location
     */
    public function setCustomer_location($customer_location)
    {
        $this->customer_location = $customer_location;
    }

    /**
     * @param field_type $customer_email
     */
    public function setCustomer_email($customer_email)
    {
        $this->customer_email = $customer_email;
    }

    /**
     * @param field_type $customer_telefon
     */
    public function setCustomer_telefon($customer_telefon)
    {
        $this->customer_telefon = $customer_telefon;
    }
    /**
     * @return the $booking_positions
     */
    public function getBooking_positions()
    {
        return $this->booking_positions;
    }

    /**
     * @param multitype: $booking_positions
     */
    public function setBooking_positions($booking_positions)
    {
        $this->booking_positions = $booking_positions;
    }
    /**
     * @return the $customer_strasse
     */
    public function getCustomer_strasse()
    {
        return $this->customer_strasse;
    }

    /**
     * @param field_type $customer_strasse
     */
    public function setCustomer_strasse($customer_strasse)
    {
        $this->customer_strasse = $customer_strasse;
    }

}
// endif;