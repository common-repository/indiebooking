<?php
if (class_exists('WP_Customize_Control'))
{
	/**
	 * Class to create a custom menu control
	 */
	class Pagelinks_Dropdown_Custom_control extends WP_Customize_Control
	{
		/**
		 * Render the content on the theme customizer page
		 */
		public function render_content() {
			$dropdown = wp_dropdown_pages(
				array(
					'name'              => '_customize-dropdown-pages-' . $this->id,
					'echo'              => 0,
					'show_option_none'  => __( '&mdash; Select &mdash;' , 'indiebooking'),
					'option_none_value' => '0',
					'selected'          => $this->value(),
				)
				);
			
			// Hackily add in the data link parameter.
			$dropdown = str_replace( '<select', '<select ' . $this->get_link(), $dropdown );
			
			printf(
				'<label class="customize-control-select"><span class="customize-control-title">%s</span> %s</label>',
				$this->label,
				$dropdown
			);
		}
	}
}
?>