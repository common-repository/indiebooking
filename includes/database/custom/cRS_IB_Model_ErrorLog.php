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

// if ( ! class_exists( 'RS_IB_Model_ErrorLog' ) ) :
class RS_IB_Model_ErrorLog
{
    const RS_TABLE              = "RS_IB_ERRORLOG_TABLE";
    
    const ID                    = "id";
    const DATE                  = "date";
    const TEXT                  = "text";
    const CLASSTXT              = "class";
    const LINE                  = "line";
    const EXTRA_ID              = "extra_id";
    const EXTRA_TEXT            = "extra_text";
    const TYPE                  = "type";
    
    
    private $id;
    private $date;
    private $text;
    private $class;
    private $line;
    private $extra_id;
    private $extra_text;
    private $type;
    
    public function exchangeArray($data) {
        if (isset($data[self::ID])) {
            $this->id   = $data[self::ID];
        }
        if (isset($data[self::DATE])) {
            $this->date   = $data[self::DATE];
        }
        if (isset($data[self::TEXT])) {
            $this->text = $data[self::TEXT];
        }
        if (isset($data[self::CLASSTXT])) {
            $this->class = $data[self::CLASSTXT];
        }
        if (isset($data[self::LINE])) {
            $this->line = $data[self::LINE];
        }
        if (isset($data[self::EXTRA_ID])) {
            $this->extra_id = $data[self::EXTRA_ID];
        }
        if (isset($data[self::EXTRA_TEXT])) {
        	$this->extra_text = $data[self::EXTRA_TEXT];
        }
        if (isset($data[self::TYPE])) {
        	$this->type = $data[self::TYPE];
        }
    }
    
    
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	
	public function getDate() {
		return $this->date;
	}
	public function setDate($date) {
		$this->date = $date;
		return $this;
	}
	
	public function getText() {
		return $this->text;
	}
	public function setText($text) {
		$this->text = $text;
		return $this;
	}
	
	public function getClass() {
		return $this->class;
	}
	public function setClass($class) {
		$this->class = $class;
		return $this;
	}
	
	public function getLine() {
		return $this->line;
	}
	public function setLine($line) {
		$this->line = $line;
		return $this;
	}
	
	public function getExtraId() {
		return $this->extra_id;
	}
	public function setExtraId($extra_id) {
		$this->extra_id = $extra_id;
		return $this;
	}
	
	public function getExtraText() {
		return $this->extra_text;
	}
	public function setExtraText($extra_text) {
		$this->extra_text = $extra_text;
		return $this;
	}
	
	public function getType() {
		return $this->type;
	}
	public function setType($type) {
		$this->type = $type;
		return $this;
	}
}
// endif;