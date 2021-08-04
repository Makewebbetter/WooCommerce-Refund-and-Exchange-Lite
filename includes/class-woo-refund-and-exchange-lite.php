<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link  https://makewebbetter.com/
 * @since 1.0.0
 *
 * @package    Woo_Refund_And_Exchange_Lite
 * @subpackage Woo_Refund_And_Exchange_Lite/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Woo_Refund_And_Exchange_Lite
 * @subpackage Woo_Refund_And_Exchange_Lite/includes
 */
class Woo_Refund_And_Exchange_Lite {


	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since 1.0.0
	 * @var   Woo_Refund_And_Exchange_Lite_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since 1.0.0
	 * @var   string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since 1.0.0
	 * @var   string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The current version of the plugin.
	 *
	 * @since 1.0.0
	 * @var   string    $wrael_onboard    To initializsed the object of class onboard.
	 */
	protected $wrael_onboard;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area,
	 * the public-facing side of the site and common side of the site.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		if ( defined( 'WOO_REFUND_AND_EXCHANGE_LITE_VERSION' ) ) {

			$this->version = WOO_REFUND_AND_EXCHANGE_LITE_VERSION;
		} else {

			$this->version = '1.0.0';
		}

		$this->plugin_name = 'Return Refund and Exchange for WooCommerce';

		$this->woo_refund_and_exchange_lite_dependencies();
		$this->woo_refund_and_exchange_lite_locale();
		if ( is_admin() ) {
			$this->woo_refund_and_exchange_lite_admin_hooks();
		} else {
			$this->woo_refund_and_exchange_lite_public_hooks();
		}
		$this->woo_refund_and_exchange_lite_common_hooks();

		$this->woo_refund_and_exchange_lite_api_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Woo_Refund_And_Exchange_Lite_Loader. Orchestrates the hooks of the plugin.
	 * - Woo_Refund_And_Exchange_Lite_i18n. Defines internationalization functionality.
	 * - Woo_Refund_And_Exchange_Lite_Admin. Defines all hooks for the admin area.
	 * - Woo_Refund_And_Exchange_Lite_Common. Defines all hooks for the common area.
	 * - Woo_Refund_And_Exchange_Lite_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since 1.0.0
	 */
	private function woo_refund_and_exchange_lite_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-refund-and-exchange-lite-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-refund-and-exchange-lite-i18n.php';

		if ( is_admin() ) {

			// The class responsible for defining all actions that occur in the admin area.
			include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woo-refund-and-exchange-lite-admin.php';

			// The class responsible for on-boarding steps for plugin.
			if ( is_dir( plugin_dir_path( dirname( __FILE__ ) ) . 'onboarding' ) && ! class_exists( 'Woo_Refund_And_Exchange_Lite_Onboarding_Steps' ) ) {
				include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-refund-and-exchange-lite-onboarding-steps.php';
			}

			if ( class_exists( 'Woo_Refund_And_Exchange_Lite_Onboarding_Steps' ) ) {
				$wrael_onboard_steps = new Woo_Refund_And_Exchange_Lite_Onboarding_Steps();
			}
		} else {

			// The class responsible for defining all actions that occur in the public-facing side of the site.
			include_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woo-refund-and-exchange-lite-public.php';

		}

		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'package/rest-api/class-woo-refund-and-exchange-lite-rest-api.php';

		/**
		 * This class responsible for defining common functionality
		 * of the plugin.
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'common/class-woo-refund-and-exchange-lite-common.php';

		$this->loader = new Woo_Refund_And_Exchange_Lite_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Woo_Refund_And_Exchange_Lite_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since 1.0.0
	 */
	private function woo_refund_and_exchange_lite_locale() {

		$plugin_i18n = new Woo_Refund_And_Exchange_Lite_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Define the name of the hook to save admin notices for this plugin.
	 *
	 * @since 1.0.0
	 */
	private function mwb_saved_notice_hook_name() {
		$mwb_plugin_name                            = ! empty( explode( '/', plugin_basename( __FILE__ ) ) ) ? explode( '/', plugin_basename( __FILE__ ) )[0] : '';
		$mwb_plugin_settings_saved_notice_hook_name = $mwb_plugin_name . '_settings_saved_notice';
		return $mwb_plugin_settings_saved_notice_hook_name;
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since 1.0.0
	 */
	private function woo_refund_and_exchange_lite_admin_hooks() {
		$wrael_plugin_admin = new Woo_Refund_And_Exchange_Lite_Admin( $this->wrael_get_plugin_name(), $this->wrael_get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $wrael_plugin_admin, 'wrael_admin_enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $wrael_plugin_admin, 'wrael_admin_enqueue_scripts' );

		// Add settings menu for Woo Refund And Exchange Lite.
		$this->loader->add_action( 'admin_menu', $wrael_plugin_admin, 'wrael_options_page' );
		$this->loader->add_action( 'admin_menu', $wrael_plugin_admin, 'mwb_rma_remove_default_submenu', 50 );

		// All admin actions and filters after License Validation goes here.
		$this->loader->add_filter( 'mwb_add_plugins_menus_array', $wrael_plugin_admin, 'wrael_admin_submenu_page', 15 );
		$this->loader->add_filter( 'wrael_general_settings_array', $wrael_plugin_admin, 'wrael_admin_general_settings_page', 10 );

		// Saving tab settings.
		$this->loader->add_action( 'mwb_rma_settings_saved_notice', $wrael_plugin_admin, 'wrael_admin_save_tab_settings' );

		// Developer's Hook Listing.
		$this->loader->add_action( 'wrael_developer_admin_hooks_array', $wrael_plugin_admin, 'mwb_developer_admin_hooks_listing' );
		$this->loader->add_action( 'wrael_developer_public_hooks_array', $wrael_plugin_admin, 'mwb_developer_public_hooks_listing' );

		// Register settings.
		$this->loader->add_filter( 'mwb_rma_refund_settings_array', $wrael_plugin_admin, 'mwb_rma_refund_settings_page', 10 );
		$this->loader->add_filter( 'mwb_rma_policies_settings_array', $wrael_plugin_admin, 'mwb_rma_policies_settings_page', 10 );
		$this->loader->add_filter( 'mwb_rma_order_message_settings_array', $wrael_plugin_admin, 'mwb_rma_order_message_settings_page', 10 );

		// Add metaboxes.
		$this->loader->add_action( 'add_meta_boxes', $wrael_plugin_admin, 'mwb_wrma_add_metaboxes' );

		// Ajax hooks.
		$this->loader->add_action( 'wp_ajax_mwb_rma_order_messages_save', $wrael_plugin_admin, 'mwb_rma_order_messages_save' );
		$this->loader->add_action( 'wp_ajax_mwb_rma_return_req_approve', $wrael_plugin_admin, 'mwb_rma_return_req_approve' );
		$this->loader->add_action( 'wp_ajax_mwb_rma_return_req_cancel', $wrael_plugin_admin, 'mwb_rma_return_req_cancel' );
		$this->loader->add_action( 'wp_ajax_mwb_rma_manage_stock', $wrael_plugin_admin, 'mwb_rma_manage_stock' );

		// Update Refund Created amount.
		$this->loader->add_action( 'woocommerce_refund_created', $wrael_plugin_admin, 'mwb_rma_action_woocommerce_order_refunded', 10, 2 );

		// Send Email.
		$this->loader->add_action( 'mwb_rma_refund_req_accept_email', $wrael_plugin_admin, 'mwb_rma_refund_req_accept_email', 10 );
		$this->loader->add_action( 'mwb_rma_refund_req_cancel_email', $wrael_plugin_admin, 'mwb_rma_refund_req_cancel_email', 10 );

		// Save policies setting.
		$this->loader->add_action( 'init', $wrael_plugin_admin, 'show_notices' );
		$this->loader->add_action( 'init', $wrael_plugin_admin, 'mwb_rma_save_policies_setting' );
	}

	/**
	 * Register all of the hooks related to the common functionality
	 * of the plugin.
	 *
	 * @since 1.0.0
	 */
	private function woo_refund_and_exchange_lite_common_hooks() {

		$wrael_plugin_common = new Woo_Refund_And_Exchange_Lite_Common( $this->wrael_get_plugin_name(), $this->wrael_get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $wrael_plugin_common, 'wrael_common_enqueue_styles' );

		$this->loader->add_action( 'wp_enqueue_scripts', $wrael_plugin_common, 'wrael_common_enqueue_scripts' );

		// license validation.
		$this->loader->add_action( 'wp_ajax_mwb_rma_validate_license_key', $wrael_plugin_common, 'mwb_rma_validate_license_key' );

		// Add the RMA Email.
		$this->loader->add_filter( 'woocommerce_email_classes', $wrael_plugin_common, 'mwb_rma_woocommerce_emails' );

		// Save atachment on the refund request form.
		$this->loader->add_action( 'wp_ajax_mwb_rma_return_upload_files', $wrael_plugin_common, 'mwb_rma_order_return_attach_files' );
		$this->loader->add_action( 'wp_ajax_nopriv_mwb_rma_return_upload_files', $wrael_plugin_common, 'mwb_rma_order_return_attach_files' );

		// Save Return Request.
		$this->loader->add_action( 'wp_ajax_mwb_rma_save_return_request', $wrael_plugin_common, 'mwb_rma_save_return_request' );
		$this->loader->add_action( 'wp_ajax_nopriv_mwb_rma_save_return_request', $wrael_plugin_common, 'mwb_rma_save_return_request' );

		// Add custom order status.
		$this->loader->add_action( 'init', $wrael_plugin_common, 'mwb_rma_register_custom_order_status' );
		$this->loader->add_filter( 'wc_order_statuses', $wrael_plugin_common, 'mwb_rma_add_custom_order_status' );

		// add capabilities, priority must be after the initial role.
		$this->loader->add_action( 'init', $wrael_plugin_common, 'mwb_rma_role_capability', 11 );

		// Send Emails.
		$this->loader->add_action( 'mwb_rma_refund_req_email', $wrael_plugin_common, 'mwb_rma_refund_req_email', 10 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since 1.0.0
	 */
	private function woo_refund_and_exchange_lite_public_hooks() {

		$wrael_plugin_public = new Woo_Refund_And_Exchange_Lite_Public( $this->wrael_get_plugin_name(), $this->wrael_get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $wrael_plugin_public, 'wrael_public_enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $wrael_plugin_public, 'wrael_public_enqueue_scripts' );

		$this->loader->add_filter( 'woocommerce_my_account_my_orders_actions', $wrael_plugin_public, 'mwb_rma_refund_button', 10, 2 );
		$this->loader->add_action( 'woocommerce_order_details_after_order_table', $wrael_plugin_public, 'mwb_rma_return_button_and_details' );

		// template include.
		$this->loader->add_filter( 'template_include', $wrael_plugin_public, 'mwb_rma_product_return_template' );

	}

	/**
	 * Register all of the hooks related to the api functionality
	 * of the plugin.
	 *
	 * @since 1.0.0
	 */
	private function woo_refund_and_exchange_lite_api_hooks() {
		$wrael_plugin_api = new Woo_Refund_And_Exchange_Lite_Rest_Api( $this->wrael_get_plugin_name(), $this->wrael_get_version() );
		$this->loader->add_action( 'rest_api_init', $wrael_plugin_api, 'mwb_rma_add_endpoint' );

	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function wrael_run() {
		$this->loader->wrael_run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since  1.0.0
	 * @return string    The name of the plugin.
	 */
	public function wrael_get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since  1.0.0
	 * @return Woo_Refund_And_Exchange_Lite_Loader    Orchestrates the hooks of the plugin.
	 */
	public function wrael_get_loader() {
		return $this->loader;
	}


	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since  1.0.0
	 * @return Woo_Refund_And_Exchange_Lite_Onboard    Orchestrates the hooks of the plugin.
	 */
	public function wrael_get_onboard() {
		return $this->wrael_onboard;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since  1.0.0
	 * @return string    The version number of the plugin.
	 */
	public function wrael_get_version() {
		return $this->version;
	}

	/**
	 * Predefined default mwb_rma_plug tabs.
	 *
	 * @return Array An key=>value pair of Woo Refund And Exchange Lite tabs.
	 */
	public function mwb_rma_plug_default_tabs() {
		$wrael_default_tabs = array();
		$wrael_default_tabs['woo-refund-and-exchange-lite-general']       = array(
			'title'     => esc_html__( 'General', 'woo-refund-and-exchange-lite' ),
			'name'      => 'woo-refund-and-exchange-lite-general',
			'file_path' => WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'admin/partials/woo-refund-and-exchange-lite-general.php',
		);
		$wrael_default_tabs['woo-refund-and-exchange-lite-refund']        = array(
			'title'     => esc_html__( 'Refund', 'woo-refund-and-exchange-lite' ),
			'name'      => 'woo-refund-and-exchange-lite-refund',
			'file_path' => WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'admin/partials/woo-refund-and-exchange-lite-refund.php',
		);
		$wrael_default_tabs = apply_filters( 'mwb_rma_plugin_admin_settings_tabs_addon', $wrael_default_tabs );
		$wrael_default_tabs['woo-refund-and-exchange-lite-order-message'] = array(
			'title'     => esc_html__( 'Order Message', 'woo-refund-and-exchange-lite' ),
			'name'      => 'woo-refund-and-exchange-lite-order-message',
			'file_path' => WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'admin/partials/woo-refund-and-exchange-lite-order-message.php',
		);
		$wrael_default_tabs['woo-refund-and-exchange-lite-policies']      = array(
			'title'     => esc_html__( 'RMA Policies', 'woo-refund-and-exchange-lite' ),
			'name'      => 'woo-refund-and-exchange-lite-refund-policies',
			'file_path' => WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'admin/partials/woo-refund-and-exchange-lite-policies.php',
		);
		$wrael_default_tabs['woo-refund-and-exchange-lite-developer']     = array(
			'title'     => esc_html__( 'Developer', 'woo-refund-and-exchange-lite' ),
			'name'      => 'woo-refund-and-exchange-lite-developer',
			'file_path' => WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'admin/partials/woo-refund-and-exchange-lite-developer.php',
		);
		$wrael_default_tabs['woo-refund-and-exchange-lite-overview']      = array(
			'title'     => esc_html__( 'Overview', 'woo-refund-and-exchange-lite' ),
			'name'      => 'woo-refund-and-exchange-lite-overview',
			'file_path' => WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . 'admin/partials/woo-refund-and-exchange-lite-overview.php',
		);
		$wrael_default_tabs = apply_filters( 'mwb_rma_plugin_standard_admin_settings_tabs', $wrael_default_tabs );
		return $wrael_default_tabs;
	}

	/**
	 * Locate and load appropriate tempate.
	 *
	 * @since 1.0.0
	 * @param string $path   path file for inclusion.
	 * @param array  $params parameters to pass to the file for access.
	 */
	public function mwb_rma_plug_load_template( $path, $params = array() ) {

		// $wrael_file_path = WOO_REFUND_AND_EXCHANGE_LITE_DIR_PATH . $path;

		if ( file_exists( $path ) ) {

			include $path;
		} else {

			/* translators: %s: file path */
			$wrael_notice = sprintf( esc_html__( 'Unable to locate file at location "%s". Some features may not work properly in this plugin. Please contact us!', 'woo-refund-and-exchange-lite' ), $path );
			$this->mwb_rma_plug_admin_notice( $wrael_notice, 'error' );
		}
	}

	/**
	 * Show admin notices.
	 *
	 * @param string $wrael_message Message to display.
	 * @param string $type        notice type, accepted values - error/update/update-nag.
	 * @since 1.0.0
	 */
	public static function mwb_rma_plug_admin_notice( $wrael_message, $type = 'error' ) {

		$wrael_classes = 'notice ';

		switch ( $type ) {

			case 'update':
				$wrael_classes .= 'updated is-dismissible';
				break;

			case 'update-nag':
				$wrael_classes .= 'update-nag is-dismissible';
				break;

			case 'success':
				$wrael_classes .= 'notice-success is-dismissible';
				break;

			default:
				$wrael_classes .= 'notice-error is-dismissible';
		}

		$wrael_notice  = '<div class="' . esc_attr( $wrael_classes ) . '">';
		$wrael_notice .= '<p>' . esc_html( $wrael_message ) . '</p>';
		$wrael_notice .= '</div>';

		echo wp_kses_post( $wrael_notice );
	}


	/**
	 * Show WordPress and server info.
	 *
	 * @return Array $wrael_system_data returns array of all WordPress and server related information.
	 * @since  1.0.0
	 */
	public function mwb_rma_plug_system_status() {
		global $wpdb;
		$wrael_system_status    = array();
		$wrael_wordpress_status = array();
		$wrael_system_data      = array();

		// Get the web server.
		$wrael_system_status['web_server'] = isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '';

		// Get PHP version.
		$wrael_system_status['php_version'] = function_exists( 'phpversion' ) ? phpversion() : __( 'N/A (phpversion function does not exist)', 'woo-refund-and-exchange-lite' );

		// Get the server's IP address.
		$wrael_system_status['server_ip'] = isset( $_SERVER['SERVER_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ) : '';

		// Get the server's port.
		$wrael_system_status['server_port'] = isset( $_SERVER['SERVER_PORT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_PORT'] ) ) : '';

		// Get the uptime.
		$wrael_system_status['uptime'] = function_exists( 'exec' ) ? @exec( 'uptime -p' ) : __( 'N/A (make sure exec function is enabled)', 'woo-refund-and-exchange-lite' );

		// Get the server path.
		$wrael_system_status['server_path'] = defined( 'ABSPATH' ) ? ABSPATH : __( 'N/A (ABSPATH constant not defined)', 'woo-refund-and-exchange-lite' );

		// Get the OS.
		$wrael_system_status['os'] = function_exists( 'php_uname' ) ? php_uname( 's' ) : __( 'N/A (php_uname function does not exist)', 'woo-refund-and-exchange-lite' );

		// Get WordPress version.
		$wrael_wordpress_status['wp_version'] = function_exists( 'get_bloginfo' ) ? get_bloginfo( 'version' ) : __( 'N/A (get_bloginfo function does not exist)', 'woo-refund-and-exchange-lite' );

		// Get and count active WordPress plugins.
		$wrael_wordpress_status['wp_active_plugins'] = function_exists( 'get_option' ) ? count( get_option( 'active_plugins' ) ) : __( 'N/A (get_option function does not exist)', 'woo-refund-and-exchange-lite' );

		// See if this site is multisite or not.
		$wrael_wordpress_status['wp_multisite'] = function_exists( 'is_multisite' ) && is_multisite() ? __( 'Yes', 'woo-refund-and-exchange-lite' ) : __( 'No', 'woo-refund-and-exchange-lite' );

		// See if WP Debug is enabled.
		$wrael_wordpress_status['wp_debug_enabled'] = defined( 'WP_DEBUG' ) ? __( 'Yes', 'woo-refund-and-exchange-lite' ) : __( 'No', 'woo-refund-and-exchange-lite' );

		// See if WP Cache is enabled.
		$wrael_wordpress_status['wp_cache_enabled'] = defined( 'WP_CACHE' ) ? __( 'Yes', 'woo-refund-and-exchange-lite' ) : __( 'No', 'woo-refund-and-exchange-lite' );

		// Get the total number of WordPress users on the site.
		$wrael_wordpress_status['wp_users'] = function_exists( 'count_users' ) ? count_users() : __( 'N/A (count_users function does not exist)', 'woo-refund-and-exchange-lite' );

		// Get the number of published WordPress posts.
		$wrael_wordpress_status['wp_posts'] = wp_count_posts()->publish >= 1 ? wp_count_posts()->publish : __( '0', 'woo-refund-and-exchange-lite' );

		// Get PHP memory limit.
		$wrael_system_status['php_memory_limit'] = function_exists( 'ini_get' ) ? (int) ini_get( 'memory_limit' ) : __( 'N/A (ini_get function does not exist)', 'woo-refund-and-exchange-lite' );

		// Get the PHP error log path.
		$wrael_system_status['php_error_log_path'] = ! ini_get( 'error_log' ) ? __( 'N/A', 'woo-refund-and-exchange-lite' ) : ini_get( 'error_log' );

		// Get PHP max upload size.
		$wrael_system_status['php_max_upload'] = function_exists( 'ini_get' ) ? (int) ini_get( 'upload_max_filesize' ) : __( 'N/A (ini_get function does not exist)', 'woo-refund-and-exchange-lite' );

		// Get PHP max post size.
		$wrael_system_status['php_max_post'] = function_exists( 'ini_get' ) ? (int) ini_get( 'post_max_size' ) : __( 'N/A (ini_get function does not exist)', 'woo-refund-and-exchange-lite' );

		// Get the PHP architecture.
		if ( PHP_INT_SIZE == 4 ) {
			$wrael_system_status['php_architecture'] = '32-bit';
		} elseif ( PHP_INT_SIZE == 8 ) {
			$wrael_system_status['php_architecture'] = '64-bit';
		} else {
			$wrael_system_status['php_architecture'] = 'N/A';
		}

		// Get server host name.
		$wrael_system_status['server_hostname'] = function_exists( 'gethostname' ) ? gethostname() : __( 'N/A (gethostname function does not exist)', 'woo-refund-and-exchange-lite' );

		// Show the number of processes currently running on the server.
		$wrael_system_status['processes'] = function_exists( 'exec' ) ? @exec( 'ps aux | wc -l' ) : __( 'N/A (make sure exec is enabled)', 'woo-refund-and-exchange-lite' );

		// Get the memory usage.
		$wrael_system_status['memory_usage'] = function_exists( 'memory_get_peak_usage' ) ? round( memory_get_peak_usage( true ) / 1024 / 1024, 2 ) : 0;

		// Get CPU usage.
		// Check to see if system is Windows, if so then use an alternative since sys_getloadavg() won't work.
		if ( stristr( PHP_OS, 'win' ) ) {
			$wrael_system_status['is_windows']        = true;
			$wrael_system_status['windows_cpu_usage'] = function_exists( 'exec' ) ? @exec( 'wmic cpu get loadpercentage /all' ) : __( 'N/A (make sure exec is enabled)', 'woo-refund-and-exchange-lite' );
		}

		// Get the memory limit.
		$wrael_system_status['memory_limit'] = function_exists( 'ini_get' ) ? (int) ini_get( 'memory_limit' ) : __( 'N/A (ini_get function does not exist)', 'woo-refund-and-exchange-lite' );

		// Get the PHP maximum execution time.
		$wrael_system_status['php_max_execution_time'] = function_exists( 'ini_get' ) ? ini_get( 'max_execution_time' ) : __( 'N/A (ini_get function does not exist)', 'woo-refund-and-exchange-lite' );

		// Get outgoing IP address.
		$wrael_system_status['outgoing_ip'] = function_exists( 'file_get_contents' ) ? file_get_contents( 'http://ipecho.net/plain' ) : __( 'N/A (file_get_contents function does not exist)', 'woo-refund-and-exchange-lite' );

		$wrael_system_data['php'] = $wrael_system_status;
		$wrael_system_data['wp']  = $wrael_wordpress_status;

		return $wrael_system_data;
	}

	/**
	 * Generate html components.
	 *
	 * @param string $wrael_components html to display.
	 * @since 1.0.0
	 */
	public function mwb_rma_plug_generate_html( $wrael_components = array() ) {
		if ( is_array( $wrael_components ) && ! empty( $wrael_components ) ) {
			foreach ( $wrael_components as $wrael_component ) {
				if ( ! empty( $wrael_component['type'] ) && ! empty( $wrael_component['id'] ) ) {
					switch ( $wrael_component['type'] ) {

						case 'hidden':
						case 'number':
						case 'email':
						case 'text':
							?>
						<div class="mwb-form-group mwb-wrael-<?php echo esc_attr( $wrael_component['type'] ); ?>">
							<div class="mwb-form-group__label">
								<label for="<?php echo esc_attr( $wrael_component['id'] ); ?>" class="mwb-form-label"><?php echo ( isset( $wrael_component['title'] ) ? esc_html( $wrael_component['title'] ) : '' ); ?></label>
							</div>
							<div class="mwb-form-group__control">
								<label class="mdc-text-field mdc-text-field--outlined">
									<span class="mdc-notched-outline">
										<span class="mdc-notched-outline__leading"></span>
										<span class="mdc-notched-outline__notch">
							<?php if ( 'number' !== $wrael_component['type'] ) { ?>
												<span class="mdc-floating-label" id="my-label-id" style=""><?php echo ( isset( $wrael_component['placeholder'] ) ? esc_attr( $wrael_component['placeholder'] ) : '' ); ?></span>
						<?php } ?>
										</span>
										<span class="mdc-notched-outline__trailing"></span>
									</span>
									<input
									class="mdc-text-field__input <?php echo ( isset( $wrael_component['class'] ) ? esc_attr( $wrael_component['class'] ) : '' ); ?>" 
									name="<?php echo ( isset( $wrael_component['name'] ) ? esc_html( $wrael_component['name'] ) : esc_html( $wrael_component['id'] ) ); ?>"
									id="<?php echo esc_attr( $wrael_component['id'] ); ?>"
									type="<?php echo esc_attr( $wrael_component['type'] ); ?>"
									value="<?php echo ( isset( $wrael_component['value'] ) ? esc_attr( $wrael_component['value'] ) : '' ); ?>"
									placeholder="<?php echo ( isset( $wrael_component['placeholder'] ) ? esc_attr( $wrael_component['placeholder'] ) : '' ); ?>"
									<?php
									if ( 'number' === $wrael_component['type'] ) {
										?>
									min = "<?php echo ( isset( $wrael_component['min'] ) ? esc_attr( $wrael_component['min'] ) : '' ); ?>"
									max = "<?php echo ( isset( $wrael_component['max'] ) ? esc_attr( $wrael_component['max'] ) : '' ); ?>"
										<?php
									}
									?>
									>
								</label>
								<div class="mdc-text-field-helper-line">
									<div class="mdc-text-field-helper-text--persistent mwb-helper-text" id="" aria-hidden="true"><?php echo ( isset( $wrael_component['description'] ) ? esc_attr( $wrael_component['description'] ) : '' ); ?></div>
								</div>
							</div>
						</div>
							<?php
							break;

						case 'password':
							?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label for="<?php echo esc_attr( $wrael_component['id'] ); ?>" class="mwb-form-label"><?php echo ( isset( $wrael_component['title'] ) ? esc_html( $wrael_component['title'] ) : '' ); ?></label>
							</div>
							<div class="mwb-form-group__control">
								<label class="mdc-text-field mdc-text-field--outlined mdc-text-field--with-trailing-icon">
									<span class="mdc-notched-outline">
										<span class="mdc-notched-outline__leading"></span>
										<span class="mdc-notched-outline__notch">
										</span>
										<span class="mdc-notched-outline__trailing"></span>
									</span>
									<input 
									class="mdc-text-field__input <?php echo ( isset( $wrael_component['class'] ) ? esc_attr( $wrael_component['class'] ) : '' ); ?> mwb-form__password" 
									name="<?php echo ( isset( $wrael_component['name'] ) ? esc_html( $wrael_component['name'] ) : esc_html( $wrael_component['id'] ) ); ?>"
									id="<?php echo esc_attr( $wrael_component['id'] ); ?>"
									type="<?php echo esc_attr( $wrael_component['type'] ); ?>"
									value="<?php echo ( isset( $wrael_component['value'] ) ? esc_attr( $wrael_component['value'] ) : '' ); ?>"
									placeholder="<?php echo ( isset( $wrael_component['placeholder'] ) ? esc_attr( $wrael_component['placeholder'] ) : '' ); ?>"
									>
									<i class="material-icons mdc-text-field__icon mdc-text-field__icon--trailing mwb-password-hidden" tabindex="0" role="button">visibility</i>
								</label>
								<div class="mdc-text-field-helper-line">
									<div class="mdc-text-field-helper-text--persistent mwb-helper-text" id="" aria-hidden="true"><?php echo ( isset( $wrael_component['description'] ) ? esc_attr( $wrael_component['description'] ) : '' ); ?></div>
								</div>
							</div>
						</div>
							<?php
							break;

						case 'textarea':
							?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label class="mwb-form-label" for="<?php echo esc_attr( $wrael_component['id'] ); ?>"><?php echo ( isset( $wrael_component['title'] ) ? esc_html( $wrael_component['title'] ) : '' ); ?></label>
							</div>
							<div class="mwb-form-group__control">
								<label class="mdc-text-field mdc-text-field--outlined mdc-text-field--textarea"      for="text-field-hero-input">
									<span class="mdc-notched-outline">
										<span class="mdc-notched-outline__leading"></span>
										<span class="mdc-notched-outline__notch">
											<span class="mdc-floating-label"><?php echo ( isset( $wrael_component['placeholder'] ) ? esc_attr( $wrael_component['placeholder'] ) : '' ); ?></span>
										</span>
										<span class="mdc-notched-outline__trailing"></span>
									</span>
									<span class="mdc-text-field__resizer">
										<textarea rows=<?php echo esc_attr( $wrael_component['rows'] ); ?> cols=<?php echo esc_attr( $wrael_component['cols'] ); ?> class="mdc-text-field__input <?php echo ( isset( $wrael_component['class'] ) ? esc_attr( $wrael_component['class'] ) : '' ); ?>" rows="2" cols="25" aria-label="Label" name="<?php echo ( isset( $wrael_component['name'] ) ? esc_html( $wrael_component['name'] ) : esc_html( $wrael_component['id'] ) ); ?>" id="<?php echo esc_attr( $wrael_component['id'] ); ?>" placeholder="<?php echo ( isset( $wrael_component['placeholder'] ) ? esc_attr( $wrael_component['placeholder'] ) : '' ); ?>"><?php echo ( isset( $wrael_component['value'] ) ? esc_textarea( $wrael_component['value'] ) : '' ); ?></textarea>
									</span>
								</label>
							</div>
						</div>
							<?php
							break;

						case 'select':
						case 'multiselect':
							?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label class="mwb-form-label" for="<?php echo esc_attr( $wrael_component['id'] ); ?>"><?php echo ( isset( $wrael_component['title'] ) ? esc_html( $wrael_component['title'] ) : '' ); ?></label>
							</div>
							<div class="mwb-form-group__control">
								<div class="mwb-form-select">
									<select id="<?php echo esc_attr( $wrael_component['id'] ); ?>" name="<?php echo ( isset( $wrael_component['name'] ) ? esc_html( $wrael_component['name'] ) : esc_html( $wrael_component['id'] ) ); ?><?php echo ( 'multiselect' === $wrael_component['type'] ) ? '[]' : ''; ?>" id="<?php echo esc_attr( $wrael_component['id'] ); ?>" class="mdl-textfield__input <?php echo ( isset( $wrael_component['class'] ) ? esc_attr( $wrael_component['class'] ) : '' ); ?>" <?php echo 'multiselect' === $wrael_component['type'] ? 'multiple="multiple"' : ''; ?> >
							<?php
							foreach ( $wrael_component['options'] as $wrael_key => $wrael_val ) {
								?>
											<option value="<?php echo esc_attr( $wrael_key ); ?>"
												<?php
												if ( is_array( $wrael_component['value'] ) ) {
													selected( in_array( (string) $wrael_key, $wrael_component['value'], true ), true );
												} else {
													selected( $wrael_component['value'], (string) $wrael_key );
												}
												?>
												>
												<?php echo esc_html( $wrael_val ); ?>
											</option>
										<?php
							}
							?>
									</select>
									<label class="mdl-textfield__label" for="<?php echo esc_attr( $wrael_component['id'] ); ?>"><?php echo ( isset( $wrael_component['description'] ) ? esc_attr( $wrael_component['description'] ) : '' ); ?></label>
								</div>
							</div>
						</div>

							<?php
							break;

						case 'checkbox':
							?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label for="<?php echo esc_attr( $wrael_component['id'] ); ?>" class="mwb-form-label"><?php echo ( isset( $wrael_component['title'] ) ? esc_html( $wrael_component['title'] ) : '' ); ?></label>
							</div>
							<div class="mwb-form-group__control mwb-pl-4">
								<div class="mdc-form-field">
									<div class="mdc-checkbox">
										<input 
										name="<?php echo ( isset( $wrael_component['name'] ) ? esc_html( $wrael_component['name'] ) : esc_html( $wrael_component['id'] ) ); ?>"
										id="<?php echo esc_attr( $wrael_component['id'] ); ?>"
										type="checkbox"
										class="mdc-checkbox__native-control <?php echo ( isset( $wrael_component['class'] ) ? esc_attr( $wrael_component['class'] ) : '' ); ?>"
										value="<?php echo ( isset( $wrael_component['value'] ) ? esc_attr( $wrael_component['value'] ) : '' ); ?>"
							<?php checked( $wrael_component['value'], '1' ); ?>
										/>
										<div class="mdc-checkbox__background">
											<svg class="mdc-checkbox__checkmark" viewBox="0 0 24 24">
												<path class="mdc-checkbox__checkmark-path" fill="none" d="M1.73,12.91 8.1,19.28 22.79,4.59"/>
											</svg>
											<div class="mdc-checkbox__mixedmark"></div>
										</div>
										<div class="mdc-checkbox__ripple"></div>
									</div>
									<label for="checkbox-1"><?php echo ( isset( $wrael_component['description'] ) ? esc_attr( $wrael_component['description'] ) : '' ); ?></label>
								</div>
							</div>
						</div>
							<?php
							break;

						case 'radio':
							?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label for="<?php echo esc_attr( $wrael_component['id'] ); ?>" class="mwb-form-label"><?php echo ( isset( $wrael_component['title'] ) ? esc_html( $wrael_component['title'] ) : '' ); ?></label>
							</div>
							<div class="mwb-form-group__control mwb-pl-4">
								<div class="mwb-flex-col">
							<?php
							foreach ( $wrael_component['options'] as $wrael_radio_key => $wrael_radio_val ) {
								?>
										<div class="mdc-form-field">
											<div class="mdc-radio">
												<input
												name="<?php echo ( isset( $wrael_component['name'] ) ? esc_html( $wrael_component['name'] ) : esc_html( $wrael_component['id'] ) ); ?>"
												value="<?php echo esc_attr( $wrael_radio_key ); ?>"
												type="radio"
												class="mdc-radio__native-control <?php echo ( isset( $wrael_component['class'] ) ? esc_attr( $wrael_component['class'] ) : '' ); ?>"
								<?php checked( $wrael_radio_key, $wrael_component['value'] ); ?>
												>
												<div class="mdc-radio__background">
													<div class="mdc-radio__outer-circle"></div>
													<div class="mdc-radio__inner-circle"></div>
												</div>
												<div class="mdc-radio__ripple"></div>
											</div>
											<label for="radio-1"><?php echo esc_html( $wrael_radio_val ); ?></label>
										</div>    
								<?php
							}
							?>
								</div>
							</div>
						</div>
							<?php
							break;

						case 'radio-switch':
							?>

						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label for="" class="mwb-form-label"><?php echo ( isset( $wrael_component['title'] ) ? esc_html( $wrael_component['title'] ) : '' ); ?></label>
							</div>
							<div class="mwb-form-group__control">
								<div>
									<div class="mdc-switch">
										<div class="mdc-switch__track"></div>
										<div class="mdc-switch__thumb-underlay">
											<div class="mdc-switch__thumb"></div>
											<input name="<?php echo ( isset( $wrael_component['name'] ) ? esc_html( $wrael_component['name'] ) : esc_html( $wrael_component['id'] ) ); ?>" type="checkbox" id="<?php echo esc_html( $wrael_component['id'] ); ?>" value="on" class="mdc-switch__native-control <?php echo ( isset( $wrael_component['class'] ) ? esc_attr( $wrael_component['class'] ) : '' ); ?>" role="switch" aria-checked="
							<?php
							if ( 'on' === $wrael_component['value'] ) {
								echo 'true';
							} else {
								echo 'false';
							}
							?>
											"
											<?php checked( $wrael_component['value'], 'on' ); ?>
											>
										</div>
									</div>
								</div>
								<div class="mdc-text-field-helper-line">
									<div class="mdc-text-field-helper-text--persistent mwb-helper-text" id="" aria-hidden="true"><?php echo ( isset( $wrael_component['description'] ) ? esc_attr( $wrael_component['description'] ) : '' ); ?></div>
								</div>
							</div>
						</div>
							<?php
							break;

						case 'button':
							?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label"></div>
							<div class="mwb-form-group__control">
								<button class="mdc-button mdc-button--raised" name= "<?php echo ( isset( $wrael_component['name'] ) ? esc_html( $wrael_component['name'] ) : esc_html( $wrael_component['id'] ) ); ?>"
									id="<?php echo esc_attr( $wrael_component['id'] ); ?>"> <span class="mdc-button__ripple"></span>
									<span class="mdc-button__label <?php echo ( isset( $wrael_component['class'] ) ? esc_attr( $wrael_component['class'] ) : '' ); ?>"><?php echo ( isset( $wrael_component['button_text'] ) ? esc_html( $wrael_component['button_text'] ) : '' ); ?></span>
								</button>
							</div>
						</div>

							<?php
							break;

						case 'multi':
							?>
							<div class="mwb-form-group mwb-wrael-<?php echo esc_attr( $wrael_component['type'] ); ?>">
								<div class="mwb-form-group__label">
									<label for="<?php echo esc_attr( $wrael_component['id'] ); ?>" class="mwb-form-label"><?php echo ( isset( $wrael_component['title'] ) ? esc_html( $wrael_component['title'] ) : '' ); ?></label>
									</div>
									<div class="mwb-form-group__control">
							<?php
							foreach ( $wrael_component['value'] as $component ) {
								?>
											<label class="mdc-text-field mdc-text-field--outlined">
												<span class="mdc-notched-outline">
													<span class="mdc-notched-outline__leading"></span>
													<span class="mdc-notched-outline__notch">
								<?php if ( 'number' !== $component['type'] ) { ?>
															<span class="mdc-floating-label" id="my-label-id" style=""><?php echo ( isset( $wrael_component['placeholder'] ) ? esc_attr( $wrael_component['placeholder'] ) : '' ); ?></span>
							<?php } ?>
													</span>
													<span class="mdc-notched-outline__trailing"></span>
												</span>
												<input 
												class="mdc-text-field__input <?php echo ( isset( $wrael_component['class'] ) ? esc_attr( $wrael_component['class'] ) : '' ); ?>" 
												name="<?php echo ( isset( $wrael_component['name'] ) ? esc_html( $wrael_component['name'] ) : esc_html( $wrael_component['id'] ) ); ?>"
												id="<?php echo esc_attr( $component['id'] ); ?>"
												type="<?php echo esc_attr( $component['type'] ); ?>"
												value="<?php echo ( isset( $wrael_component['value'] ) ? esc_attr( $wrael_component['value'] ) : '' ); ?>"
												placeholder="<?php echo ( isset( $wrael_component['placeholder'] ) ? esc_attr( $wrael_component['placeholder'] ) : '' ); ?>"
												>
											</label>
							<?php } ?>
									<div class="mdc-text-field-helper-line">
										<div class="mdc-text-field-helper-text--persistent mwb-helper-text" id="" aria-hidden="true"><?php echo ( isset( $wrael_component['description'] ) ? esc_attr( $wrael_component['description'] ) : '' ); ?></div>
									</div>
								</div>
							</div>
								<?php
							break;
						case 'color':
						case 'date':
						case 'file':
							?>
							<div class="mwb-form-group mwb-wrael-<?php echo esc_attr( $wrael_component['type'] ); ?>">
								<div class="mwb-form-group__label">
									<label for="<?php echo esc_attr( $wrael_component['id'] ); ?>" class="mwb-form-label"><?php echo ( isset( $wrael_component['title'] ) ? esc_html( $wrael_component['title'] ) : '' ); ?></label>
								</div>
								<div class="mwb-form-group__control">
									<label>
										<input 
										class="<?php echo ( isset( $wrael_component['class'] ) ? esc_attr( $wrael_component['class'] ) : '' ); ?>" 
										name="<?php echo ( isset( $wrael_component['name'] ) ? esc_html( $wrael_component['name'] ) : esc_html( $wrael_component['id'] ) ); ?>"
										id="<?php echo esc_attr( $wrael_component['id'] ); ?>"
										type="<?php echo esc_attr( $wrael_component['type'] ); ?>"
										value="<?php echo ( isset( $wrael_component['value'] ) ? esc_attr( $wrael_component['value'] ) : '' ); ?>"
									<?php echo esc_html( ( 'date' === $wrael_component['type'] ) ? 'max=' . gmdate( 'Y-m-d', strtotime( gmdate( 'Y-m-d', mktime() ) . ' + 365 day' ) ) . 'min=' . gmdate( 'Y-m-d' ) . '' : '' ); ?>
										>
									</label>
									<div class="mdc-text-field-helper-line">
										<div class="mdc-text-field-helper-text--persistent mwb-helper-text" id="" aria-hidden="true"><?php echo ( isset( $wrael_component['description'] ) ? esc_attr( $wrael_component['description'] ) : '' ); ?></div>
									</div>
								</div>
							</div>
							<?php
							break;

						case 'submit':
							?>
						<tr valign="top">
							<td scope="row">
								<input type="submit" class="button button-primary" 
								name="<?php echo ( isset( $wrael_component['name'] ) ? esc_html( $wrael_component['name'] ) : esc_html( $wrael_component['id'] ) ); ?>"
								id="<?php echo esc_attr( $wrael_component['id'] ); ?>"
								class="<?php echo ( isset( $wrael_component['class'] ) ? esc_attr( $wrael_component['class'] ) : '' ); ?>"
								value="<?php echo esc_attr( $wrael_component['button_text'] ); ?>"
								/>
							</td>
						</tr>
							<?php
							break;
						case 'breaker':
							?>
						<!-- 	<hr> -->
							<div class="mwb-form-group__breaker">
							<span><b><?php echo ( isset( $wrael_component['name'] ) ? esc_html( $wrael_component['name'] ) : esc_html( $wrael_component['id'] ) ); ?></span></b>
							</div>
							<!-- <hr> -->
							<?php
							break;
						default:
							break;
					}
				}
			}
		}
	}
}
