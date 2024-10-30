<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Twenty Seventeen suport.
 *
 */
class rs_indiebooking_twenty_sixteen {

	/**
	 * Theme init.
	 */
	public static function init() {
		add_action( 'indiebooking_before_main_content', array( __CLASS__, 'output_content_wrapper' ), 10 );
		add_action( 'indiebooking_after_main_content', array( __CLASS__, 'output_content_wrapper_end' ), 10 );
// 		add_filter( 'indiebooking_enqueue_styles', array( __CLASS__, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'include_frontend_styles' ));
	}

	/**
	 * Enqueue CSS for this theme.
	 *
	 * @param  array $styles
	 * @return array
	 */
	public static function include_frontend_styles() {
		
		wp_enqueue_style( 'rs_ib_twentysixteen_styles', cRS_Indiebooking::plugin_url() . '/assets/css/' . 'rs_ib_twenty_sixteen.css' );
		/*
		unset( $styles['woocommerce-general'] );

		$styles['woocommerce-twenty-seventeen'] = array(
				'src'     => str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/css/twenty-seventeen.css',
				'deps'    => '',
				'version' => WC_VERSION,
				'media'   => 'all',
		);

		return apply_filters( 'woocommerce_twenty_seventeen_styles', $styles );
		*/
	}

	/**
	 * Open the Twenty Seventeen wrapper.
	 */
	public static function output_content_wrapper() { ?>
		<div class="wrap">
			<div id="primary" class="content-area rs_indiebooking_twentysixteen">
				<main id="main" class="site-main" role="main">
		<?php
	}

	/**
	 * Close the Twenty Seventeen wrapper.
	 */
	public static function output_content_wrapper_end() { ?>
				</main>
			</div>
			<?php get_sidebar(); ?>
		</div>
		<?php
	}
}

rs_indiebooking_twenty_sixteen::init();
