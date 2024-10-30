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
// $pluginFolder = cRS_Indiebooking::RS_IB_PLUGIN_FOLDER;
if ( ! defined ( 'ABSPATH' ) ) {
    exit;
} ?>
<?php
// if ( ! class_exists( 'RS_IB_Appartmentcategory' ) ) :
class RS_IB_Appartmentcategory
{
    
    public function __construct($popup = false) {
        if ($popup == false) {
            $this->createAdminView();
            
            //das Popup wird durch die action "rs_indiebooking_create_admin_popup" in "RS_IB_Popup_Add_Popups.php"
            //erzeugt, daher muss es hier nicht nochmal gemacht werden.
            //die Action wiederum wird in "cRS_IB_Admin_Appartment.php" in der admin_notices aufgerufen.
            //ob das der optimale Weg ist, weiß ich nicht. Funktioniert aber!
            //Update 28.10.2016
            //in der  "cRS_IB_Admin_Appartment.php"  wird das Popup nun nur erstellt, wenn der PostType = rsappartment ist.
            add_action( 'rsappartmentcategories_add_form_fields', 'rs_ib_createAddCategoryPopup');
            
            add_action( 'rs_indiebooking_create_metadummy_rsappartmentcategories', array( $this, 'createApartmentCategoryListItemHtml' ), 10, 4);
            add_action( 'rs_indiebooking_createApartmentMetalistItem', array( $this, 'createApartmentCategoryListItem' ), 10, 4);
        }
    }
    
    public function createApartmentCategoryListItemHtml($id = "", $name = "", $checked = "", $term = null) {
        $termValue      = "";
        $termLbl        = "";
        $spanclass      = "";
        if ($name == "" && $id == "") {
            $name       = "dummy_rsappartmentcategories";
            $id         = "dummy_rsappartmentcategories";
        }
        if (!is_null($term)) {
            $termValue  = $term->slug;
            $termLbl    = $term->name;
        } else {
            $spanclass  = "ibui_hiddenMetaSpan";
        }
        ?>
        <span id="span_meta_<?php echo esc_attr($id);?>" class='<?php echo esc_attr($spanclass);?>'>
            <input type='checkbox' name='<?php echo esc_attr($name);?>'id='in-<?php echo esc_attr($id);?>'
            		class='ibui_checkbox' <?php echo esc_attr($checked);?> value='<?php echo esc_attr($termValue);?>' />
    		<label for='in-<?php echo esc_attr($id);?>'>
    			<?php echo esc_attr($termLbl);?>
    		</label>
    		<br />
		</span>
        <?php
    }
    
    public function createApartmentCategoryListItem($post = null, $term = null, $taxonomy = "", $checked = "") {
        $id         = "";
        $name       = "";
//         $termValue  = "";
//         $termLbl    = "";
        if (!is_null($term)) {
            if ($term->taxonomy === RS_IB_Model_Appartmentkategorie::RS_TAXONOMY) {
                $id                 = $taxonomy.'-'.$term->term_id;
                $name               = 'tax_input[' . $taxonomy . '][]';
//                 $termValue          = $term->slug;
//                 $termLbl            = $term->name;
                
                $this->createApartmentCategoryListItemHtml($id, $name, $checked, $term);
//                 echo "<input type='checkbox' name='{$name}'id='in-$id' class='ibui_checkbox'"
//                         . $checked ."value='$term->slug' /><label for='in-$id'> $term->name</label><br />";
            }
        }
    }
    
    public function createAdminView() {
        $this->includes();
        //https://developer.wordpress.org/reference/hooks/taxonomy_add_form_fields/
        add_action( 'rsappartmentcategories_add_form_fields', array( $this, 'add_appartmentCategory_head' ) );
        add_action( 'rsappartmentcategories_edit_form_fields', array( $this, 'add_appartmentCategory_head' ) );
        
//         add_action( 'rsappartmentoption_edit_form_fields', array( $this, 'edit_appartmentOption_fields' ));
        
//         add_action( 'create_rsappartmentoption', array( $this, 'save_appartmentOption_fields' ), 10, 2 );
//         add_action( 'edit_rsappartmentoption', array( $this, 'save_appartmentOption_fields' ), 10, 2 );
        
        add_action( 'rsappartmentcategories_add_form_fields', array( $this, 'add_appartmentCategory_foot' ) );
        add_action( 'rsappartmentcategories_edit_form_fields', array( $this, 'add_appartmentCategory_foot' ) );
        
        add_action( 'admin_footer', array($this, 'addAddCategoryButton'));
        
    //         configureAppartmentOptionOverview();
        //         add_action('quick_edit_custom_box',  array($this, 'rs_ib_apartment_options_add_quick_edit'), 10, 2);
    }
    
//     public function rs_ib_apartment_options_add_quick_edit($column_name, $post_type) {
//         var_dump($column_name);
//         echo "huhu";
//     }
    
    public function addAddCategoryButton() {
        $screen     = get_current_screen();
        $addBtnTxt  = __("add category", "indiebooking");
        if ($screen->taxonomy == RS_IB_Model_Appartmentkategorie::RS_TAXONOMY) {
        ?>
            <script>
    			jQuery(document).ready(function() {
    				var postsFilterForm = jQuery("#posts-filter");
    				var tableNav		= jQuery(postsFilterForm).find(".tablenav.top");
    				var bulkActions		= jQuery(tableNav).find(".alignleft.actions.bulkactions");
    				jQuery(bulkActions).append('<a id="rs_ib_btn_create_category" href="#" class="ibui_add_btn"><?php echo $addBtnTxt;?></a>');
    			});
            </script>
            <?php
        }
    }
    
    public function add_appartmentCategory_head() {
        ?>
        <div class="appartment_option_admin_view">
        <div class="modal"></div>
    <?php
    }

    public function add_appartmentCategory_foot() {
    ?>
        </div>
    <?php
    }
    
    private function includes() {
    }
        
}
// endif;
new RS_IB_Appartmentcategory();