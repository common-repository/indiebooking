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
// if ( ! class_exists( 'RS_IB_Model_Termmeta' ) ) :
abstract class RS_IB_Model_Termmeta extends RS_IB_Model_Meta
{
    const RS_IB_TERMID              = "term_id";
    const RS_IB_NAME                = "name";
    const RS_IB_SLUG                = "slug";
    const RS_IB_TERM_GROUP          = "term_group";
    const RS_IB_TERM_TAXONOMY_ID    = "term_taxonomy_id";
    const RS_IB_TAXONOMY            = "taxonomy";
    const RS_IB_DESCRIPTION         = "description";
    const RS_IB_PARENT              = "parent";
    const RS_IB_COUNT               = "count";
    const RS_IB_FILTER              = "filter";
    
    protected $termId;
    protected $name;
    protected $slug;
    protected $term_group;
    protected $term_taxonomy_id;
    protected $taxonomy;
    protected $description;
    protected $parent;
    protected $count;
    protected $filter;
    
    public function exchangeArray($data) {
        if (isset($data)) {
            if (is_a($data, 'stdClass') || is_a($data, 'WP_Term')) {
                $this->setTermId($data->term_id);
                $this->setName($data->name);
                $this->setSlug($data->slug);
                $this->setTerm_group($data->term_group);
                $this->setTerm_taxonomy_id($data->term_taxonomy_id);
                $this->setTaxonomy($data->taxonomy);
                $this->setDescription($data->description);
                $this->setParent($data->parent);
                $this->setCount($data->count);
//                 $this->setFilter($data->filter);
            } elseif (is_array($data)) {
                $this->setTermId(isset($data[RS_IB_Model_Termmeta::RS_IB_TERMID])                       ? $data[RS_IB_Model_Termmeta::RS_IB_TERMID]             : null);
                $this->setName(isset($data[RS_IB_Model_Termmeta::RS_IB_NAME])                           ? $data[RS_IB_Model_Termmeta::RS_IB_NAME]               : null);
                $this->setSlug(isset($data[RS_IB_Model_Termmeta::RS_IB_SLUG])                           ? $data[RS_IB_Model_Termmeta::RS_IB_SLUG]               : null);
                $this->setTerm_group(isset($data[RS_IB_Model_Termmeta::RS_IB_TERM_GROUP])               ? $data[RS_IB_Model_Termmeta::RS_IB_TERM_GROUP]         : null);
                $this->setTerm_taxonomy_id(isset($data[RS_IB_Model_Termmeta::RS_IB_TERM_TAXONOMY_ID])   ? $data[RS_IB_Model_Termmeta::RS_IB_TERM_TAXONOMY_ID]   : null);
                $this->setTaxonomy(isset($data[RS_IB_Model_Termmeta::RS_IB_TAXONOMY])                   ? $data[RS_IB_Model_Termmeta::RS_IB_TAXONOMY]           : null);
                $this->setDescription(isset($data[RS_IB_Model_Termmeta::RS_IB_DESCRIPTION])             ? $data[RS_IB_Model_Termmeta::RS_IB_DESCRIPTION]        : null);
                $this->setParent(isset($data[RS_IB_Model_Termmeta::RS_IB_PARENT])                       ? $data[RS_IB_Model_Termmeta::RS_IB_PARENT]             : null);
                $this->setCount(isset($data[RS_IB_Model_Termmeta::RS_IB_COUNT])                         ? $data[RS_IB_Model_Termmeta::RS_IB_COUNT]              : null);
//                 $this->setFilter(isset($data[RS_IB_Model_Termmeta::RS_IB_FILTER])                       ? $data[RS_IB_Model_Termmeta::RS_IB_FILTER]             : null);
            }
            
//             else {
//                 $this->setTermId($data->term_id);
//                 $this->setName($data->name);
//                 $this->setSlug($data->slug);
//                 $this->setTerm_group($data->term_group);
//                 $this->setTerm_taxonomy_id($data->term_taxonomy_id);
//                 $this->setTaxonomy($data->taxonomy);
//                 $this->setDescription($data->description);
//                 $this->setParent($data->parent);
//                 $this->setCount($data->count);
//             }
        }
    }
    
    private $metaType = 'rewabp_term';
    
    public function sortFromToActiveFunctionAsc( $a, $b ) {
        return strtotime($b["from"]) - strtotime($a["from"]);
    }
    
    public  function sortFromToActiveFunctionDesc( $a, $b ) {
        return strtotime($a["from"]) - strtotime($b["from"]);
    }
    
    public function object_to_array() {
        return get_object_vars($this);
    }
    
    public function setTermId($term_id) {
        $this->termId = $term_id;
    }

    public function getTermId() {
        return $this->termId;
    }
    
    public function getMetaType() {
        return $this->metaType;
    }
 /**
     * @return the $name
     */
    public function getName()
    {
        return $this->name;
    }

 /**
     * @return the $slug
     */
    public function getSlug()
    {
        return $this->slug;
    }

 /**
     * @return the $term_group
     */
    public function getTerm_group()
    {
        return $this->term_group;
    }

 /**
     * @return the $term_taxonomy_id
     */
    public function getTerm_taxonomy_id()
    {
        return $this->term_taxonomy_id;
    }

 /**
     * @return the $taxonomy
     */
    public function getTaxonomy()
    {
        return $this->taxonomy;
    }

 /**
     * @return the $description
     */
    public function getDescription()
    {
        return $this->description;
    }

 /**
     * @return the $parent
     */
    public function getParent()
    {
        return $this->parent;
    }

 /**
     * @return the $count
     */
    public function getCount()
    {
        return $this->count;
    }

 /**
     * @return the $filter
     */
    public function getFilter()
    {
        return $this->filter;
    }

 /**
     * @param field_type $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

 /**
     * @param field_type $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

 /**
     * @param field_type $term_group
     */
    public function setTerm_group($term_group)
    {
        $this->term_group = $term_group;
    }

 /**
     * @param field_type $term_taxonomy_id
     */
    public function setTerm_taxonomy_id($term_taxonomy_id)
    {
        $this->term_taxonomy_id = $term_taxonomy_id;
    }

 /**
     * @param field_type $taxonomy
     */
    public function setTaxonomy($taxonomy)
    {
        $this->taxonomy = $taxonomy;
    }

 /**
     * @param field_type $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

 /**
     * @param field_type $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

 /**
     * @param field_type $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

 /**
     * @param field_type $filter
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

 /**
     * @param string $metaType
     */
    public function setMetaType($metaType)
    {
        $this->metaType = $metaType;
    }
    
}
// endif;