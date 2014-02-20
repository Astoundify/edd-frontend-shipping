<?php
/**
 * Plugin Name: Easy Digital Downloads - FES + Shipping + Commissions
 * Plugin URI:  https://github.com/Astoundify/wp-job-manager-gravityforms-apply/
 * Description: Link up Frontend Submissions with Simple Shipping and Commissions
 * Author:      Astoundify
 * Author URI:  http://astoundify.com
 * Version:     1.0
 * Text Domain: eed_fsc
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Astoundify_EDD_FSC {

	/**
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Make sure only one instance is only running.
	 */
	public static function instance() {
		if ( ! isset ( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Start things up.
	 *
	 * @since Easy Digital Downloads + FES/Shipping/Commissions 1.0
	 */
	public function __construct() {
		$this->setup_actions();
		$this->setup_globals();
		$this->load_textdomain();
	}

	/**
	 * Set some smart defaults to class variables. Allow some of them to be
	 * filtered to allow for early overriding.
	 *
	 * @since Easy Digital Downloads + FES/Shipping/Commissions 1.0
	 *
	 * @return void
	 */
	private function setup_globals() {
		$this->file         = __FILE__;

		$this->basename     = plugin_basename( $this->file );
		$this->plugin_dir   = plugin_dir_path( $this->file );
		$this->plugin_url   = plugin_dir_url ( $this->file );

		$this->lang_dir     = trailingslashit( $this->plugin_dir . 'languages' );
		$this->domain       = 'job_manager_alerts_sms';
	}

	/**
	 * Loads the plugin language files
	 *
 	 * @since Easy Digital Downloads + FES/Shipping/Commissions 1.0
	 */
	public function load_textdomain() {
		$locale        = apply_filters( 'plugin_locale', get_locale(), $this->domain );
		$mofile        = sprintf( '%1$s-%2$s.mo', $this->domain, $locale );

		$mofile_local  = $this->lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/' . $this->domain . '/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			return load_textdomain( $this->domain, $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			return load_textdomain( $this->domain, $mofile_local );
		}

		return false;
	}

	/**
	 * Setup the default hooks and actions
	 *
	 * @since Easy Digital Downloads + FES/Shipping/Commissions 1.0
	 *
	 * @return void
	 */
	private function setup_actions() {
		add_filter( 'edd_fes_vendor_dashboard_menu', array( $this, 'fes_menu_items' ) );
		add_filter( 'edd_user_can_view_receipt', array( $this, 'edd_user_can_view_receipt' ), 10, 2 );
		add_filter( 'eddc_sale_alert_email', array( $this, 'eddc_sale_alert_email' ), 10, 5 );
	}

	public function fes_menu_items( $items ) {
		$orders = array(
			'orders' => array(
				'icon' => 'credit-card',
				'task' => array( 'orders' ),
				'name' => __( 'Orders', 'edd_fsc' )
			)
		);

		array_splice( $items, count( $items ) - 1, 0, $orders );

		return $items;
	}

	function edd_user_can_view_receipt( $user_can_view, $edd_receipt_args ) {
		$cart = edd_get_payment_meta_cart_details( $edd_receipt_args[ 'id' ] );

		foreach ( $cart as $item ) {
			$item = get_post( $item[ 'id' ] );

			if ( $item->post_author == get_current_user_id() ) {
				$user_can_view = true;

				break;
			}
		}

		return $user_can_view;
	}

	function eddc_sale_alert_email( $message, $user_id, $commission_amount, $rate, $download_id ) {

	}

}
add_action( 'plugins_loaded', array( 'Astoundify_EDD_FSC', 'instance' ) );