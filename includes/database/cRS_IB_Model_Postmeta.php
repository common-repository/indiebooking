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
// if ( ! class_exists( 'RS_IB_Model_Postmeta' ) ) :
abstract class RS_IB_Model_Postmeta extends RS_IB_Model_Meta
{
    const POST_STATUS = "post_status";
     
    private $postId;
    private $post_title;
    private $post_type;
    private $post_content;
    private $post_status;
    private $post_status_label;
//     private $post_name;
//     private $metaType = 'rewabp_term';
    
    
    public function __construct($data = array(), $postId = 0) {
        parent::__construct($data);
        if ($postId > 0) {
            $status             = get_post_status($postId);
            $this->post_status  = $status;
            $this->postId       = $postId;
            $this->post_title   = get_the_title($postId);
            $statusObj          = get_post_status_object( get_post_status( $postId) );
            if (!is_null($statusObj) && isset($statusObj)) {
                $this->post_status_label = $statusObj->label;
            }
        }
    }
    
    public function exchangeArray($data) {
        $status             = get_post_status(get_the_ID());
        $this->post_status  = $status;
        $this->postId       = get_the_ID();
        $this->post_title   = get_the_title(get_the_ID());
        $statusObj          = get_post_status_object( get_post_status( get_the_ID()) );
        if (!is_null($statusObj) && isset($statusObj)) {
            $this->post_status_label = $statusObj->label;
        }
    }
    
    public function setPostId($postId) {
        $this->postId = $postId;
    }

    public function getPostId() {
        return $this->postId;
    }
    /**
     * @return the $post_title
     */
    public function getPost_title()
    {
        if ($this->post_title == "") {
            return get_the_title($this->postId);
        }
        return $this->post_title;
    }

    /**
     * @return the $post_type
     */
    public function getPost_type()
    {
        return $this->post_type;
    }

    /**
     * @return the $post_content
     */
    public function getPost_content()
    {
        return $this->post_content;
    }

    /**
     * @return the $post_status
     */
    public function getPost_status()
    {
        return $this->post_status;
    }

    /**
     * @param field_type $post_title
     */
    public function setPost_title($post_title)
    {
        $this->post_title = $post_title;
    }

    /**
     * @param field_type $post_type
     */
    public function setPost_type($post_type)
    {
        $this->post_type = $post_type;
    }

    /**
     * @param field_type $post_content
     */
    public function setPost_content($post_content)
    {
        $this->post_content = $post_content;
    }

    /**
     * @param field_type $post_status
     */
    public function setPost_status($post_status)
    {
        $this->post_status = $post_status;
    }
    /**
     * @return the $post_status_label
     */
    public function getPost_status_label()
    {
        return $this->post_status_label;
    }

    /**
     * @param field_type $post_status_label
     */
    public function setPost_status_label($post_status_label)
    {
        $this->post_status_label = $post_status_label;
    }
    
}
// endif;