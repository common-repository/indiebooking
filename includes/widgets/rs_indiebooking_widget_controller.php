<?php
class rs_indiebooking_widget_controller {

	public function __construct() {
		$this->include_widgets();
	}
	
	/**
	 * Get the plugin path.
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
	
	private function include_widgets() {
		include_once( $this->plugin_path().'/pagelinks_dropdown_custom_control.php');
		include_once( $this->plugin_path().'/fast_link/rs_indiebooking_fast_link_widget.php');
		
		add_action( 'widgets_init', array($this, 'register_widgets') );
	}
	
	public function register_widgets() {
		register_widget( 'rs_indiebooking_fast_link_widget' );
	}
}
new rs_indiebooking_widget_controller();