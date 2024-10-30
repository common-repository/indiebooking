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

// if ( ! class_exists( 'IndiebookingException' ) ) :
/**
 * Die Exceptionklasse für Indiebooking.
 * Diese Exception sorgt dafür, dass Fehler auch korrekt via ajax an das Indiebooking Frontend
 * zurückgegeben werden.
 *
 * @author schmitt
 *
 */
class IndiebookingException extends \Exception
{
    private $extendedInformation = array();
    private $invisibleExtendedInfos = array();
    
    // Die Exception neu definieren, damit die Mitteilung nicht optional ist
    public function __construct($message, $code = 0, Exception $previous = null) {
        // etwas Code
    
        // sicherstellen, dass alles korrekt zugewiesen wird
        parent::__construct($message, $code, $previous);
    }
    
    // mauegeschneiderte Stringdarstellung des Objektes
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
    
    public function customFunction() {
        
    }
    
    public function convertToArray() {
        $exception              = array();
        $exception['CODE']      = $this->getCode();
        $exception['MSG']       = $this->getMessage();
        
        foreach ($this->getExtendedInformation() as $key => $extInfos) {
            $exception[$key]    = $extInfos;
            if (is_array($extInfos) && sizeof($extInfos) > 0) {
                $exception['MSG']   = $exception['MSG']."<br />";
            	foreach ($extInfos as $info) {
		            $exception['MSG']   = $exception['MSG']." ".$info;
            	}
            }
        }
        
        /*
         * Diese Informationen dienen dazu, weitere Infos in dem Fehler mitzugeben, die evtl
         * via Javascript verarbeitet werden sollen.
         */
        foreach ($this->getInvisibleExtendedInfos() as $key => $extInfos) {
        	$exception[$key]    = $extInfos;
        }
        
        return $exception;
    }
    
    /**
     * @return the $extendedInformation
     */
    public function getExtendedInformation()
    {
        return $this->extendedInformation;
    }

    /**
     * @param multitype: $extendedInformation
     */
//     public function setExtendedInformation($extendedInformation)
//     {
//         $this->extendedInformation = $extendedInformation;
//     }
    
    public function pushExtendedInformation($extendedInformation = array())
    {
//         if (!key_exists($this->extendedInformation, 'data')) {
//             $this->extendedInformation['data'] = array();
//         }
        $this->extendedInformation = array_merge($this->extendedInformation, $extendedInformation);
    }
    
    public function pushInvisibleExtendedInformation($extendedInformation = array())
    {
    	//         if (!key_exists($this->extendedInformation, 'data')) {
    	//             $this->extendedInformation['data'] = array();
    	//         }
    	$this->invisibleExtendedInfos = array_merge($this->invisibleExtendedInfos, $extendedInformation);
    }
    
	/**
	 * @return multitype:
	 */
	public function getInvisibleExtendedInfos()
	{
		return $this->invisibleExtendedInfos;
	}

	/**
	 * @param multitype: $invisibleExtendedInfos
	 */
// 	public function setInvisibleExtendedInfos($invisibleExtendedInfos)
// 	{
// 		$this->invisibleExtendedInfos = $invisibleExtendedInfos;
// 	}

    
}
// endif;