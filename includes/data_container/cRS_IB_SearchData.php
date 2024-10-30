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
// if ( ! class_exists( 'RS_IB_SearchData' ) ) :
/**
 * @author schmitt
 *
 */
class RS_IB_SearchData {
    
    private $dateFrom       = "";
    private $dateTo         = "";
    private $categorie      = array();
    private $numberOfBeds   = array();
    private $numberOfRooms  = array();
    private $numberOfGuests = "";
    private $options        = array();
    private $location       = "";
    private $features		= array();
    
    public function __construct() {
          
    }
    
    /**
     * @return the $dateFrom
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * @return the $dateTo
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * @return the $categorie
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * @return the $numberOfBeds
     */
    public function getNumberOfBeds()
    {
        return $this->numberOfBeds;
    }

    /**
     * @return the $numberOfGuests
     */
    public function getNumberOfGuests()
    {
        return $this->numberOfGuests;
    }

    /**
     * @param string $dateFrom
     */
    public function setDateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;
    }

    /**
     * @param string $dateTo
     */
    public function setDateTo($dateTo)
    {
        $this->dateTo = $dateTo;
    }

    /**
     * @param string $categorie
     */
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;
    }

    /**
     * @param string $numberOfBeds
     */
    public function setNumberOfBeds($numberOfBeds)
    {
        $this->numberOfBeds = $numberOfBeds;
    }

    /**
     * @param string $numberOfGuests
     */
    public function setNumberOfGuests($numberOfGuests)
    {
        $this->numberOfGuests = $numberOfGuests;
    }
    /**
     * @return the $options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param multitype: $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }
    /**
     * @return the $location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }
    /**
     * @return the $numberOfRooms
     */
    public function getNumberOfRooms()
    {
        return $this->numberOfRooms;
    }

    /**
     * @param multitype: $numberOfRooms
     */
    public function setNumberOfRooms($numberOfRooms)
    {
        $this->numberOfRooms = $numberOfRooms;
    }
    
	public function getFeatures() {
		return $this->features;
	}
	
	public function setFeatures($features) {
		$this->features = $features;
		return $this;
	}
	
}
// endif;