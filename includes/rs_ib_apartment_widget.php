<?php
class rs_ib_apartment_widget extends WP_Widget {
	
	public function show_apartments() {
		?>
		<section id="apartments">
				<div class="container">
					<div class="row">
						<div class="col-md-12 text-center">
							<h1><?php _e("Our apartments", "indiebooking");?></h1>
		                </div>
		            </div>
		            <div class="row">
		                <div class="item_slider_startseite_apartments">
		                    <?php
		                    do_action("rs_indiebooking_rsappartment_show_first_page_apartments");
		                    ?>
		                </div>
		            </div>
		            <?php do_action("rs_indiebooking_rsappartment_show_first_page_apartment_slider"); ?>
		        </div>
		    </section>
		<?php
	}
	
	public function __construct() {
		add_action("rs_indiebooking_widget_show_apartments", array($this, 'show_apartments'));
		$widget_options = array(
				'classname' => 'apartment_widget',
				'description' => 'This widget puts the apartment on your page',
		);
		parent::__construct( 'rs_indiebooking_apartment_widget', 'Apartment Widget', $widget_options );
	}
	
	public function widget( $args, $instance ) {
		do_action("rs_indiebooking_widget_show_apartments");
	}
}