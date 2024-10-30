<?php
class rs_indiebooking_fast_link_widget extends WP_Widget {//WP_Widget_Media { //WP_Widget {
	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'rs_indiebooking_fast_link_widget',
			
			// Widget name will appear in UI
			__('Indiebooking Fast Access Link', 'indiebooking'),
			
			// Widget description
			array(
				'description' => __( 'Creates a Fast Acces Link in indiebooking style', 'indiebooking' ),
			)
		);
		
		add_action( 'admin_enqueue_scripts', array( $this, 'include_widget_scripts' ));
		add_action( 'wp_enqueue_scripts', array( $this, 'include_widget_scripts' ));
	}
	
	public static function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}
	
	public function include_widget_scripts() {
		$jsPath      = self::plugin_url() . '/js/';
		
		wp_enqueue_script( 'rs_indiebooking_indiebooking_fast_link_widget',
			$jsPath.'indiebooking_fast_link_widget.js',
			array( 'jquery' )
		);
	}
	
	// Creating widget front-end
	public function widget( $args, $instance ) {
		$title 				= apply_filters( 'widget_title', $instance['title'] );
		$fastLinkImgUrl		= '';
		$fastLinkUrl		= "";
		$fastlinkimgid 		= "";
		$ownPageId			= 0;
		$default_lang 		= apply_filters( 'wpml_default_language', NULL );
		$my_current_lang 	= apply_filters( 'wpml_current_language', NULL );
		
		if ( isset( $instance[ 'indiebooking_fast_link_url' ] ) ) {
			$fastLinkUrl = $instance[ 'indiebooking_fast_link_url' ];
		}
		if (isset($instance['indiebooking_fast_link_own_page'])) {
			$ownPageId = $instance['indiebooking_fast_link_own_page'];
		}
		if ( isset( $instance[ 'fastlinkimageid' ] ) ) {
			$fastlinkimgid 	= $instance[ 'fastlinkimageid' ];
			$fastlinkimgArr	= wp_get_attachment_image_src($fastlinkimgid);
			if (isset($fastlinkimgArr) && sizeof($fastlinkimgArr) > 0) {
				$fastLinkImgUrl = $fastlinkimgArr[0];
			}
		}
		
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		
		if ($fastLinkImgUrl != "") {
			if ($ownPageId > 0) {
				if ( function_exists('icl_object_id') ) {
					$fastLinkUrl = apply_filters('wpml_element_link', $ownPageId, 'page', $title, array(), "", false);
					if ($fastLinkUrl != "") {
						$endLinkPos 	= strpos($fastLinkUrl, '">');
						$startLinkPos 	= strpos($fastLinkUrl, 'href="');
						$fastLinkUrl	= substr($fastLinkUrl, $startLinkPos, ($endLinkPos-1));
						$fastLinkUrl	= str_replace('href="', "", $fastLinkUrl);
						$fastLinkUrl	= str_replace('">', "", $fastLinkUrl);
					}
				} else {
					$fastLinkUrl = get_page_link($ownPageId);
				}
			}
			?>
			<a href="<?php echo $fastLinkUrl;?>" target="_blank">
			<?php
		}
		
		if ( ! empty( $title ) ) {
		//	echo $args['before_title'] . $title . $args['after_title'];
		}
		
		if ($fastLinkImgUrl != "") {
			?>
			<img src="<?php echo esc_url($fastLinkImgUrl);?>" class="img-responsive center-block">
			<?php
		}
		// This is where you run the code and display the output
		echo $title;
		
		if ($fastLinkImgUrl != "") {
			?>
			</a>
			<?php
		}
		
		echo $args['after_widget'];
	}
	
	// Widget Backend
	public function form( $instance ) {
		$fastlinkimgid 	= "";
		$fastlinkimg 	= "";
		$fastLinkUrl	= "";
		$fastLinkOwnPage	= 0;
		$fastLinkImgUrl = "";
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'wpb_widget_domain' );
		}
		
		//$instance['wpml_language']
		
		if ( isset( $instance[ 'indiebooking_fast_link_url' ] ) ) {
			$fastLinkUrl = $instance[ 'indiebooking_fast_link_url' ];
		}
		else {
			$fastLinkUrl = '';
		}
		
		if ( isset( $instance[ 'fastlinkimageid' ] ) ) {
			$fastlinkimgid 	= $instance[ 'fastlinkimageid' ];
			$fastlinkimgArr	= wp_get_attachment_image_src($fastlinkimgid);
			if (isset($fastlinkimgArr) && sizeof($fastlinkimgArr) > 0) {
				$fastLinkImgUrl = $fastlinkimgArr[0];
			}
		}
		
		if (isset( $instance['indiebooking_fast_link_own_page'])) {
			$fastLinkOwnPage = $instance['indiebooking_fast_link_own_page'];
		}
		
		$fieldid 	= $this->get_field_id('indiebooking_fast_link_own_page');
		$fieldname 	= $this->get_field_name('indiebooking_fast_link_own_page');
		
		// Widget admin form
		$dropdown = wp_dropdown_pages(
			array (
				'id'				=> $fieldid,
				'name'              => $fieldname,//'_customize-dropdown-pages-' . $this->id,
				'echo'              => 0,
				'show_option_none'  => __( '&mdash; Select &mdash;' , 'indiebooking'),
				'option_none_value' => '0',
				'selected'			=> $fastLinkOwnPage
			)
		);
		
		// Hackily add in the data link parameter.
// 		$dropdown = str_replace( '<select', '<select ' . $this->get_link(), $dropdown );

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
					name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
// 		printf(
// 			'<label class="customize-control-select"><span class="customize-control-title">%s</span> %s</label>',
// 			__('own page:', 'indiebooking'),
// 			$dropdown
// 		);
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'indiebooking_fast_link_own_page' ); ?>"><?php _e( 'own page:', 'indiebooking' ); ?></label>
			<?php echo $dropdown; ?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'indiebooking_fast_link_url' ); ?>"><?php _e( 'or URL:', 'indiebooking' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'indiebooking_fast_link_url' ); ?>"
					name="<?php echo $this->get_field_name( 'indiebooking_fast_link_url' ); ?>" type="text"
					value="<?php echo esc_attr( $fastLinkUrl ); ?>" />
		</p>
		<div class="indiebooking_fast_link_container wp-core-ui attachement-preview">
			<img class="indiebooking_fast_link_widget_preview" src="<?php echo $fastLinkImgUrl; ?>" />
			<input id="<?php echo $this->get_field_id( 'indiebooking_fast_link_widget_img_id' ); ?>"
					class="indiebooking_fast_link_widget_img_id"  value="<?php echo esc_attr($fastlinkimgid); ?>"
					hidden='hidden'
					name="<?php echo $this->get_field_name( 'fastlinkimageid' ); ?>"/>
			<div class="add_apartment_images_fastlink"><!-- hide-if-no-js -->
				<a href="#" class="ibui_add_btn"
					data-choose="<?php esc_attr_e( 'Add Image', 'indiebooking' ); ?>"
					data-update="<?php esc_attr_e( 'Accept', 'indiebooking' ); ?>"
					data-delete="<?php esc_attr_e( 'Delete image', 'indiebooking' ); ?>"
					data-text="<?php esc_attr_e( 'Delete', 'indiebooking' ); ?>">
						<?php _e( 'Add Image', 'indiebooking' ); ?>
				</a>
	        </div>
        </div>
		<?php
	}
	
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance 								= array();
		$instance['title'] 						= ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['fastlinkimageid'] 			= ( ! empty( $new_instance['fastlinkimageid'] ) ) ? strip_tags( $new_instance['fastlinkimageid'] ) : '';
		$instance['indiebooking_fast_link_url'] = ( ! empty( $new_instance['indiebooking_fast_link_url'] ) ) ? strip_tags( $new_instance['indiebooking_fast_link_url'] ) : '';
		$instance['indiebooking_fast_link_own_page'] = ( ! empty( $new_instance['indiebooking_fast_link_own_page'] ) ) ? strip_tags( $new_instance['indiebooking_fast_link_own_page'] ) : '';
// 		$instance['wpml_language'] 				= ( ! empty( $new_instance['wpml_language'] ) ) ? strip_tags( $new_instance['wpml_language'] ) : '';
		
		return $instance;
	}

} // Class rs_indiebooking_fast_link_widget ends here