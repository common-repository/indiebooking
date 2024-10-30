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

// if ( ! class_exists( 'RS_IB_Model_BuchungMwSt' ) ) :
class RS_IB_Model_MailPrintJob
{
    const RS_TABLE              = "RS_IB_MAILPRINTJOB_TABLE";
    
    const JOB_ID      		= "jobId";
    const BOOKING_POST_ID   = "bookingPostId";
    const PRINT_TYPE    	= "printType";
    const PRINT_LANGUAGE	= "printLanguage";
    
    private $jobId;
    private $bookingPostId;
    private $printType;
    private $printLanguage;
    
    public function exchangeArray($data) {
    	if (isset($data[self::JOB_ID])) {
    		$this->jobId = $data[self::JOB_ID];
        }
        if (isset($data[self::BOOKING_POST_ID])) {
        	$this->bookingPostId = $data[self::BOOKING_POST_ID];
        }
        if (isset($data[self::PRINT_TYPE])) {
        	$this->printType = $data[self::PRINT_TYPE];
        }
        if (isset($data[self::PRINT_LANGUAGE])) {
        	$this->printLanguage = $data[self::PRINT_LANGUAGE];
        }
    }
    
	/**
	 * @return mixed
	 */
	public function getJobId()
	{
		return $this->jobId;
	}

	/**
	 * @return mixed
	 */
	public function getBookingPostId()
	{
		return $this->bookingPostId;
	}

	/**
	 * @return mixed
	 */
	public function getPrintType()
	{
		return $this->printType;
	}

	/**
	 * @param mixed $jobId
	 */
	public function setJobId($jobId)
	{
		$this->jobId = $jobId;
	}

	/**
	 * @param mixed $bookingPostId
	 */
	public function setBookingPostId($bookingPostId)
	{
		$this->bookingPostId = $bookingPostId;
	}

	/**
	 * @param mixed $printType
	 */
	public function setPrintType($printType)
	{
		$this->printType = $printType;
	}
	/**
	 * @return mixed
	 */
	public function getPrintLanguage()
	{
		return $this->printLanguage;
	}

	/**
	 * @param mixed $printLanguage
	 */
	public function setPrintLanguage($printLanguage)
	{
		$this->printLanguage = $printLanguage;
	}


    
}
// endif;