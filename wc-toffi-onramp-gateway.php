<?php
/*
 * Plugin Name: Toffi Onramp Payment Gateway
 * Description: Accept custom credit card payments on your online store.
 * Author: Kagami
 * Author URI: Kagami.dev
 * Version: 3.1.9
 * 
 * Requires at least: 6.1
 * 
 * WC requires at least: 8.1
 * 
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Toffi_Gateway plugin class.
 *
 * @class WC_Toffi_Gateway
 */
class WC_Toffi_Gateway {

	/**
	 * Plugin bootstrapping.
	 */
	public static function init() {

		// Invoice Payments setup
		add_action( 'init', array( __CLASS__, 'plugin_setup' ) );

		// Invoice Payments gateway class.
		add_action( 'plugins_loaded', array( __CLASS__, 'includes' ), 0 );

		// Invoice Payments text domain
    	add_action( 'init', array( __CLASS__, 'load_plugin_textdomain' ) );

		// Make the Invoice Payments gateway available to WC.
		add_filter( 'woocommerce_payment_gateways', array( __CLASS__, 'add_gateway' ) );

		// Declare HPOS compaibility.
		add_action( 'before_woocommerce_init', array( __CLASS__, 'wc_declare_hpos_compatibility' ) );

	}

  /**
   * Setup all the things.
   * Only executes if WooCommerce core plugin is active.
   * If WooCommerce is not installed or inactive an admin notice is displayed.
   * @return void
   */
  public static function plugin_setup() {
    if ( class_exists( 'WooCommerce' ) ) {
      add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( __CLASS__, 'plugin_action_links' ) );
    } else {
      add_action( 'admin_notices', array( __CLASS__, 'install_woocommerce_core_notice' ) );
    }
  }

  /**
   * Load the localisation file.
   * @access  public
   * @since   1.0.0
   * @return  void
   */
  public static function load_plugin_textdomain() {
    load_plugin_textdomain( 'wc-toffi-onramp-gateway', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
  }

	/**
	 * Add the Invoice Payment gateway to the list of available gateways.
	 *
	 * @param array
	 */
	public static function add_gateway( $gateways ) {
		$gateways[] = 'WC_Toffi_Onramp_Gateway';
		return $gateways;
	}

	/**
	 * Plugin includes.
	 */
	public static function includes() {

		// Make the WC_Invoice_Gateway class available.
		if ( class_exists( 'WC_Payment_Gateway' ) ) {
			require_once 'includes/class-wc-toffi-onramp-gateway.php';
 			require_once 'includes/custom-endpoint-onramp.php';
		}
	}

	/**
	 * Plugin url.
	 *
	 * @return string
	 */
	public static function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Plugin url.
	 *
	 * @return string
	 */
	public static function plugin_abspath() {
		return trailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
   * Show action links on the plugin screen.
   * @access  public
   * @since   1.0.0
   * @param	mixed $links Plugin Action links
   * @return	array
   */
  public static function plugin_action_links( $links ) {
    $action_links = array(
      'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=toffi-onramp-source' ) . '" title="' . esc_attr( __( 'View WooCommerce Settings', 'wc-toffi-onramp-gateway' ) ) . '">' . __( 'Settings', 'wc-toffi-onramp-gateway' ) . '</a>',
    );

    return array_merge( $action_links, $links );
  }

  /**
   * WooCommerce Invoice Gateway plugin install notice.
   * If the user activates this plugin while not having the WooCommerce Dynamic Pricing plugin installed or activated, prompt them to install WooCommerce Dynamic Pricing.
   * @since   1.0.0
   * @return  void
   */
  public static function install_woocommerce_core_notice() {
    echo '<div class="notice notice-error is-dismissible">
      <p>' . __( 'The WooCommerce Invoice Gateway extension requires that you have the WooCommerce core plugin installed and activated.', 'wc-toffi-onramp-gateway' ) . ' <a href="https://wordpress.org/plugins/woocommerce/" target="_blank">' . __( 'Install WooCommerce', 'wc-toffi-onramp-gateway' ) . '</a></p>
    </div>';
  }

	/**
	 * Declare HPOS compatibility.
	 * @since   2.0.1
	 * @return  void
	 */
	public static function wc_declare_hpos_compatibility() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}

}

WC_Toffi_Gateway::init();
