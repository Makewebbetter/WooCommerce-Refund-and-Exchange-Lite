<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html field for general tab.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Woo_Refund_And_Exchange_Lite
 * @subpackage Woo_Refund_And_Exchange_Lite/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wrael_mwb_rma_obj;
$wrael_api_settings =
// The General Settings.
apply_filters( 'mwb_rma_api_settings_array', array() );
?>
<!--  template file for admin settings. -->
<form action="" method="POST" class="mwb-wrael-gen-section-form">
	<div class="wrael-secion-wrap">
		<?php
		$wrael_api_html = $wrael_mwb_rma_obj->mwb_rma_plug_generate_html( $wrael_api_settings );
		echo esc_html( $wrael_api_html );
		wp_nonce_field( 'admin_save_data', 'mwb_tabs_nonce' );
		?>
	</div>
</form>