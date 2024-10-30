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
// if ( ! class_exists( 'RS_IB_Customer' ) ) :
/**
 * @author schmitt
 *
 */
class RS_IB_Customer {
    private $firma      = "";
    private $anrede     = "";
    private $titel      = "";
    private $firstName  = "";
    private $lastName   = "";
    private $street     = "";
    private $zipCode    = "";
    private $location   = "";
    private $email      = "";
    private $telefon    = "";
    
    private $firma2     = "";
    private $anrede2    = "";
    private $titel2     = "";
    private $firstName2 = "";
    private $lastName2  = "";
    private $street2    = "";
    private $zipCode2   = "";
    private $location2  = "";
    private $email2     = "";
    private $telefon2   = "";
    
    public function __construct($firstName, $lastName, $street, $zipCode, $location, $email, $telefon) {
        $this->firstName = $firstName;
        $this->lastName  = $lastName;
        $this->street    = $street;
        $this->zipCode   = $zipCode;
        $this->location  = $location;
        $this->email     = $email;
        $this->telefon   = $telefon;
    }
    
    
    /**
     * @return the $firstName
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return the $lastName
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return the $street
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @return the $zipCode
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @return the $location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return the $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return the $telefon
     */
    public function getTelefon()
    {
        return $this->telefon;
    }

    /**
     * @param Ambigous <string, unknown> $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @param Ambigous <string, unknown> $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @param Ambigous <string, unknown> $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @param Ambigous <string, unknown> $zipCode
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @param Ambigous <string, unknown> $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @param Ambigous <string, unknown> $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param Ambigous <string, unknown> $telefon
     */
    public function setTelefon($telefon)
    {
        $this->telefon = $telefon;
    }
    /**
     * @return the $anrede
     */
    public function getAnrede()
    {
        return $this->anrede;
    }

    /**
     * @return the $titel
     */
    public function getTitel()
    {
        return $this->titel;
    }

    /**
     * @param string $anrede
     */
    public function setAnrede($anrede)
    {
        $this->anrede = $anrede;
    }

    /**
     * @param string $titel
     */
    public function setTitel($titel)
    {
        $this->titel = $titel;
    }
    /**
     * @return the $firma
     */
    public function getFirma()
    {
        return $this->firma;
    }

    /**
     * @return the $firma2
     */
    public function getFirma2()
    {
        return $this->firma2;
    }

    /**
     * @return the $anrede2
     */
    public function getAnrede2()
    {
        return $this->anrede2;
    }

    /**
     * @return the $titel2
     */
    public function getTitel2()
    {
        return $this->titel2;
    }

    /**
     * @return the $firstName2
     */
    public function getFirstName2()
    {
        return $this->firstName2;
    }

    /**
     * @return the $lastName2
     */
    public function getLastName2()
    {
        return $this->lastName2;
    }

    /**
     * @return the $street2
     */
    public function getStreet2()
    {
        return $this->street2;
    }

    /**
     * @return the $zipCode2
     */
    public function getZipCode2()
    {
        return $this->zipCode2;
    }

    /**
     * @return the $location2
     */
    public function getLocation2()
    {
        return $this->location2;
    }

    /**
     * @return the $email2
     */
    public function getEmail2()
    {
        return $this->email2;
    }

    /**
     * @return the $telefon2
     */
    public function getTelefon2()
    {
        return $this->telefon2;
    }

    /**
     * @param string $firma
     */
    public function setFirma($firma)
    {
        $this->firma = $firma;
    }

    /**
     * @param string $firma2
     */
    public function setFirma2($firma2)
    {
        $this->firma2 = $firma2;
    }

    /**
     * @param string $anrede2
     */
    public function setAnrede2($anrede2)
    {
        $this->anrede2 = $anrede2;
    }

    /**
     * @param string $titel2
     */
    public function setTitel2($titel2)
    {
        $this->titel2 = $titel2;
    }

    /**
     * @param string $firstName2
     */
    public function setFirstName2($firstName2)
    {
        $this->firstName2 = $firstName2;
    }

    /**
     * @param string $lastName2
     */
    public function setLastName2($lastName2)
    {
        $this->lastName2 = $lastName2;
    }

    /**
     * @param string $street2
     */
    public function setStreet2($street2)
    {
        $this->street2 = $street2;
    }

    /**
     * @param string $zipCode2
     */
    public function setZipCode2($zipCode2)
    {
        $this->zipCode2 = $zipCode2;
    }

    /**
     * @param string $location2
     */
    public function setLocation2($location2)
    {
        $this->location2 = $location2;
    }

    /**
     * @param string $email2
     */
    public function setEmail2($email2)
    {
        $this->email2 = $email2;
    }

    /**
     * @param string $telefon2
     */
    public function setTelefon2($telefon2)
    {
        $this->telefon2 = $telefon2;
    }
}
// endif;