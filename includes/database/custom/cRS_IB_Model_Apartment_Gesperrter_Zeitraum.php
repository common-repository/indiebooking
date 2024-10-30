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

// if ( ! class_exists( 'RS_IB_Model_Apartment_Gesperrter_Zeitraum' ) ) :
class RS_IB_Model_Apartment_Gesperrter_Zeitraum
{
    const RS_TABLE              = "RS_IB_APARTMENT_GESPERRTER_ZEITRAUM_TABLE";
    
    const META_ID               = "meta_id";
    const POST_ID               = "post_id";
    const POSITION_ID           = "position_id";
    const DATE_FROM             = "date_from";
    const DATE_TO               = "date_to";
    
    private $meta_id;
    private $post_id;
    private $position_id;
    private $date_from;
    private $date_to;
    
    public function exchangeArray($data) {
        if (isset($data[self::META_ID])) {
            $this->meta_id = $data[self::META_ID];
        }
        if (isset($data[self::POST_ID])) {
            $this->post_id = $data[self::POST_ID];
        }
        if (isset($data[self::POSITION_ID])) {
            $this->position_id = $data[self::POSITION_ID];
        }
        if (isset($data[self::DATE_FROM])) {
            $this->date_from = $data[self::DATE_FROM];
        }
        if (isset($data[self::DATE_TO])) {
            $this->date_to = $data[self::DATE_TO];
        }
    }
    
    /**
     * @return the $meta_id
     */
    public function getMeta_id()
    {
        return $this->meta_id;
    }

    /**
     * @return the $post_id
     */
    public function getPost_id()
    {
        return $this->post_id;
    }

    /**
     * @return the $position_id
     */
    public function getPosition_id()
    {
        return $this->position_id;
    }

    /**
     * @return the $date_from
     */
    public function getDate_from()
    {
        return $this->date_from;
    }

    /**
     * @return the $date_to
     */
    public function getDate_to()
    {
        return $this->date_to;
    }

    /**
     * @param field_type $meta_id
     */
    public function setMeta_id($meta_id)
    {
        $this->meta_id = $meta_id;
    }

    /**
     * @param field_type $post_id
     */
    public function setPost_id($post_id)
    {
        $this->post_id = $post_id;
    }

    /**
     * @param field_type $position_id
     */
    public function setPosition_id($position_id)
    {
        $this->position_id = $position_id;
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
}
// endif;