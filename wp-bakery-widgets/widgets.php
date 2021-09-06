<?php

/**
* Adds new shortcodes and registers it to
* the WPBakery Visual Composer plugin
*
*/


// If this file is called directly, abort

if ( ! defined( 'ABSPATH' ) ) {
    die ('Ohhh..what are you doing here');
}

if ( ! class_exists( 'mwb_rma_vc_widget' ) ) {
	class mwb_rma_vc_widget {
        /**
		* Main constructor
		*
		*/
		public function __construct() {
			// Registers the shortcode in WordPress
			add_shortcode( 'mwb_rma_refund_form', array( 'mwb_rma_vc_widget', 'output1' ) );
            add_shortcode( 'mwb_rma_order_msg', array( 'mwb_rma_vc_widget', 'output2' ) );

			// Map shortcode to Visual Composer
			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mwb_rma_refund_form', array( 'mwb_rma_vc_widget', 'map1' ) );
                vc_lean_map( 'mwb_rma_order_msg', array( 'mwb_rma_vc_widget', 'map2' ) );
			}

		}
        /**
		* Map shortcode to VC
        *
        * This is an array of all your settings which become the shortcode attributes ($atts)
		* for the output.
		*
		*/
		public function map1() {
			return array(
				'name'        => esc_html__( 'Refund Form', 'woo-refund-and-exchange-lite' ),
				'description' => esc_html__( 'Add Refund Form into your page', 'woo-refund-and-exchange-lite' ),
				'base'        => 'vc_infobox',
				'category' => __('RMA FORMS', 'woo-refund-and-exchange-lite'),
				'icon' => plugin_dir_path( __FILE__ ) . 'assets/img/note.png',
            );
        }
         /**
		* Map shortcode to VC
        *
        * This is an array of all your settings which become the shortcode attributes ($atts)
		* for the output.
		*
		*/
		public function map2() {
			return array(
				'name'        => esc_html__( 'Order Message Form', 'woo-refund-and-exchange-lite' ),
				'description' => esc_html__( 'Add Order Message Form into your page', 'woo-refund-and-exchange-lite' ),
				'base'        => 'vc_infobox',
				'category'    => __('RMA FORMS', 'woo-refund-and-exchange-lite'),
				'icon'        => plugin_dir_path( __FILE__ ) . 'assets/img/note.png',
                'params'      => '',

            );
        }

        /**
		* Shortcode output
		*
		*/
		public function output1( $atts, $content = null ) {
            return include_once WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'public/partials/mwb-rma-refund-request-form.php';

		}

        /**
		* Shortcode output
		*
		*/
		public function output2( $atts, $content = null ) {
            $template = include_once WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'public/partials/mwb-rma-view-order-msg.php';
            return $template;

		}
    }
    new mwb_rma_vc_widget;
}