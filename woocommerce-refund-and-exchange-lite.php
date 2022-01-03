<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link    https://wpswings.com/
 * @since   1.0.0
 * @package woo-refund-and-exchange-lite
 *
 * @wordpress-plugin
 * Plugin Name:       Return Refund and Exchange for WooCommerce
 * Plugin URI:        https://wpswings.com/product/woo-refund-and-exchange-lite/
 * Description:       WooCommerce Refund and Exchange lite allows users to submit product refund. The plugin provides a dedicated mailing system that would help to communicate better between store owner and customers.This is lite version of Woocommerce Refund And Exchange.
 * Version:           4.0.0
 * Author:            WpSwings
 * Author URI:        https://wpswings.com/
 * Text Domain:       woo-refund-and-exchange-lite
 * Domain Path:       /languages
 *
 * Requires at least: 4.6
 * Tested up to: 5.8.2
 * WC requires at least: 4.0
 * WC tested up to: 6.0.0
 *
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

$activated      = true;
$active_plugins = get_option( 'active_plugins', array() );
if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	$active_network_wide = get_site_option( 'active_sitewide_plugins', array() );
	if ( ! empty( $active_network_wide ) ) {
		foreach ( $active_network_wide as $key => $value ) {
			$active_plugins[] = $key;
		}
	}
	$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	if ( ! in_array( 'woocommerce/woocommerce.php', $active_plugins, true ) ) {
		$activated = false;
	}
} else {
	if ( ! in_array( 'woocommerce/woocommerce.php', $active_plugins, true ) ) {
		$activated = false;
	}
}
if ( $activated ) {
	/**
	 * Define plugin constants.
	 *
	 * @since 1.0.0
	 */
	function define_woo_refund_and_exchange_lite_constants() {
		woo_refund_and_exchange_lite_constants( 'WOO_REFUND_AND_EXCHANGE_LITE_VERSION', '4.0.0' );
		woo_refund_and_exchange_lite_constants( 'WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH', plugin_dir_path( __FILE__ ) );
		woo_refund_and_exchange_lite_constants( 'WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL', plugin_dir_url( __FILE__ ) );
		woo_refund_and_exchange_lite_constants( 'WOO_REFUND_AND_EXCHANGE_LITE_SERVER_URL', 'https://makewebbetter.com' );
		woo_refund_and_exchange_lite_constants( 'WOO_REFUND_AND_EXCHANGE_LITE_ITEM_REFERENCE', 'Woo Refund And Exchange Lite' );
	}

	/**
	 * Define mwb-site update feature.
	 *
	 * @since 1.0.0
	 */
	function auto_update_woo_refund_and_exchange_lite() {
		if ( ! defined( 'WOO_REFUND_AND_EXCHANGE_LITE_SPECIAL_SECRET_KEY' ) ) {
			define( 'WOO_REFUND_AND_EXCHANGE_LITE_SPECIAL_SECRET_KEY', '59f32ad2f20102.74284991' );
		}

		if ( ! defined( 'WOO_REFUND_AND_EXCHANGE_LITE_LICENSE_SERVER_URL' ) ) {
			define( 'WOO_REFUND_AND_EXCHANGE_LITE_LICENSE_SERVER_URL', 'https://makewebbetter.com' );
		}

		if ( ! defined( 'WOO_REFUND_AND_EXCHANGE_LITE_ITEM_REFERENCE' ) ) {
			define( 'WOO_REFUND_AND_EXCHANGE_LITE_ITEM_REFERENCE', 'Woo Refund And Exchange Lite' );
		}
		woo_refund_and_exchange_lite_constants( 'WOO_REFUND_AND_EXCHANGE_LITE_BASE_FILE', __FILE__ );
	}

	/**
	 * Callable function for defining plugin constants.
	 *
	 * @param String $key   Key for contant.
	 * @param String $value value for contant.
	 * @since 1.0.0
	 */
	function woo_refund_and_exchange_lite_constants( $key, $value ) {
		if ( ! defined( $key ) ) {

			define( $key, $value );
		}
	}

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-woo-refund-and-exchange-lite-activator.php
	 *
	 * @param string $network_wide .
	 */
	function activate_woo_refund_and_exchange_lite( $network_wide ) {
		include_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-refund-and-exchange-lite-activator.php';
		Woo_Refund_And_Exchange_Lite_Activator::woo_refund_and_exchange_lite_activate( $network_wide );
		$mwb_rma_active_plugin = get_option( 'mwb_all_plugins_active', false );
		if ( is_array( $mwb_rma_active_plugin ) && ! empty( $mwb_rma_active_plugin ) ) {
			$mwb_rma_active_plugin['woo-refund-and-exchange-lite'] = array(
				'plugin_name' => esc_html__( 'Woo Refund And Exchange Lite', 'woo-refund-and-exchange-lite' ),
				'active'      => '1',
			);
		} else {
			$mwb_rma_active_plugin                                 = array();
			$mwb_rma_active_plugin['woo-refund-and-exchange-lite'] = array(
				'plugin_name' => esc_html__( 'Woo Refund And Exchange Lite', 'woo-refund-and-exchange-lite' ),
				'active'      => '1',
			);
		}
		update_option( 'mwb_all_plugins_active', $mwb_rma_active_plugin );
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-woo-refund-and-exchange-lite-deactivator.php
	 *
	 * @param string $network_wide .
	 */
	function deactivate_woo_refund_and_exchange_lite( $network_wide ) {
		include_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-refund-and-exchange-lite-deactivator.php';
		Woo_Refund_And_Exchange_Lite_Deactivator::woo_refund_and_exchange_lite_deactivate( $network_wide );
		$mwb_rma_deactive_plugin = get_option( 'mwb_all_plugins_active', false );
		if ( is_array( $mwb_rma_deactive_plugin ) && ! empty( $mwb_rma_deactive_plugin ) ) {
			foreach ( $mwb_rma_deactive_plugin as $mwb_rma_deactive_key => $mwb_rma_deactive ) {
				if ( 'woo-refund-and-exchange-lite' === $mwb_rma_deactive_key ) {
					$mwb_rma_deactive_plugin[ $mwb_rma_deactive_key ]['active'] = '0';
				}
			}
		}
		update_option( 'mwb_all_plugins_active', $mwb_rma_deactive_plugin );
	}

	register_activation_hook( __FILE__, 'activate_woo_refund_and_exchange_lite' );
	register_deactivation_hook( __FILE__, 'deactivate_woo_refund_and_exchange_lite' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-woo-refund-and-exchange-lite.php';


	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since 1.0.0
	 */
	function run_woo_refund_and_exchange_lite() {
		define_woo_refund_and_exchange_lite_constants();
		auto_update_woo_refund_and_exchange_lite();
		$wrael_plugin_standard = new Woo_Refund_And_Exchange_Lite();
		$wrael_plugin_standard->wrael_run();
		$GLOBALS['wrael_mwb_rma_obj'] = $wrael_plugin_standard;
		if ( function_exists( 'vc_lean_map' ) ) {
			include_once WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'wp-bakery-widgets/class-mwb-rma-vc-widgets.php';
		}
		include_once WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'includes/woo-refund-and-exchange-lite-common-functions.php';

	}
	run_woo_refund_and_exchange_lite();

	// Add settings link on plugin page.
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'woo_refund_and_exchange_lite_settings_link' );

	/**
	 * Settings link.
	 *
	 * @since 1.0.0
	 * @param Array $links Settings link array.
	 */
	function woo_refund_and_exchange_lite_settings_link( $links ) {
		$my_link = array(
			'<a href="' . admin_url( 'admin.php?page=woo_refund_and_exchange_lite_menu' ) . '">' . esc_html__( 'Settings', 'woo-refund-and-exchange-lite' ) . '</a>',
		);
		return array_merge( $my_link, $links );
	}

	/**
	 * Adding custom setting links at the plugin activation list.
	 *
	 * @param  array  $links_array      array containing the links to plugin.
	 * @param  string $plugin_file_name plugin file name.
	 * @return array
	 */
	function woo_refund_and_exchange_lite_custom_settings_at_plugin_tab( $links_array, $plugin_file_name ) {
		if ( strpos( $plugin_file_name, basename( __FILE__ ) ) ) {
			$links_array[] = '<a href="https://docs.makewebbetter.com/woocommerce-refund-and-exchange-lite" target="_blank"><img src="' . esc_html( WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL ) . 'admin/image/Documentation.svg" class="mwb-info-img" alt="documentation image">' . esc_html__( 'Documentation', 'woo-refund-and-exchange-lite' ) . '</a>';
			$links_array[] = '<a href="https://makewebbetter.com/contact-us/" target="_blank"><img src="' . esc_html( WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL ) . 'admin/image/Support.svg" class="mwb-info-img" alt="support image">' . esc_html__( 'Support', 'woo-refund-and-exchange-lite' ) . '</a>';
		}
		return $links_array;
	}
	add_filter( 'plugin_row_meta', 'woo_refund_and_exchange_lite_custom_settings_at_plugin_tab', 10, 2 );

	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'mwb_rma_lite_settings_link' );

	/**
	 * Settings tab of the plugin.
	 *
	 * @name rewardeem_woocommerce_points_rewards_settings_link
	 * @param array $links array of the links.
	 * @since    1.0.0
	 */
	function mwb_rma_lite_settings_link( $links ) {

		if ( ! is_plugin_active( 'woocommerce-rma-for-return-refund-and-exchange/mwb-woocommerce-rma.php' ) ) {

			$links['goPro'] = '<a style="color:#FFF;background:linear-gradient(to right,#7a28ff 0,#00a1ff 100%);padding:5px;border-radius:6px;" target="_blank" href="https://makewebbetter.com/product/woocommerce-rma-return-refund-exchange/">' . esc_html__( 'GO PREMIUM', 'woo-refund-and-exchange-lite' ) . '</a>';
		}

		return $links;
	}

	/**
	 * Function to restore the setting
	 */
	function mwb_rma_lite_setting_restore() {
		$check_if_refund_exist = get_option( 'mwb_wrma_return_enable', false );
		$check_key_exist       = get_option( 'mwb_rma_lite_setting_restore', false );
		if ( ! $check_key_exist && $check_if_refund_exist && function_exists( 'mwb_rma_lite_migrate_settings' ) ) {
			mwb_rma_lite_migrate_settings();
			update_option( 'mwb_rma_lite_setting_restore', true );
		}
	}
	register_activation_hook( __FILE__, 'mwb_rma_lite_setting_restore' );
} else {
	/**
	 * Show warning message if woocommerce is not install
	 */
	function mwb_rma_plugin_error_notice_lite() {
		?>
		<div class="error notice is-dismissible">
			<p><?php esc_html_e( 'Woocommerce is not activated, Please activate Woocommerce first to install WooCommerce Refund and Exchange Lite.', 'woo-refund-and-exchange-lite' ); ?></p>
		</div>
		<style>
		#message{display:none;}
		</style>
		<?php
	}
	add_action( 'admin_init', 'mwb_rma_plugin_deactivate_lite' );


	/**
	 * Call Admin notices
	 *
	 * @name ced_rnx_plugin_deactivate_lite()
	 * @author MakeWebBetter<webmaster@makewebbetter.com>
	 * @link http://www.makewebbetter.com/
	 */
	function mwb_rma_plugin_deactivate_lite() {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'network_admin_notices', 'mwb_rma_plugin_error_notice_lite' );
		add_action( 'admin_notices', 'mwb_rma_plugin_error_notice_lite' );
	}
}

