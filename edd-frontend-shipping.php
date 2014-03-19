<?php
/**
 * Plugin Name: Easy Digital Downloads - Frontend Shipping
 * Plugin URI:  https://github.com/Astoundify/edd-frontend-shipping/
 * Description: Display a list of unshipped (and shipped) orders for frontend vendors.
 * Author:      Astoundify
 * Author URI:  http://astoundify.com
 * Version:     1.0.0
 * Text Domain: edd_fs
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class EDD_Frontend_Shipping {

	/**
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Make sure only one instance is only running.
	 */
	public static function instance() {
		if ( ! ( class_exists( 'Easy_Digital_Downloads' ) && class_exists( 'EDD_Front_End_Submissions' ) && class_exists( 'EDD_Simple_Shipping' ) ) ) {
			return;
		}

		if ( ! isset ( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Start things up.
	 *
	 * @since Easy Digital Downloads - Frontend Shipping 1.0.0
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
	 * @since Easy Digital Downloads - Frontend Shipping 1.0.0
	 *
	 * @return void
	 */
	private function setup_globals() {
		$this->file         = __FILE__;

		$this->basename     = plugin_basename( $this->file );
		$this->plugin_dir   = plugin_dir_path( $this->file );
		$this->plugin_url   = plugin_dir_url ( $this->file );

		$this->lang_dir     = trailingslashit( $this->plugin_dir . 'languages' );
		$this->domain       = 'edd_fs';
	}

	/**
	 * Loads the plugin language files
	 *
 	 * @since Easy Digital Downloads - Frontend Shipping 1.0.0
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
	 * @since Easy Digital Downloads - Frontend Shipping 1.0.0
	 *
	 * @return void
	 */
	private function setup_actions() {
		add_filter( 'edd_user_can_view_receipt', array( $this, 'edd_user_can_view_receipt' ), 10, 2 );
		add_filter( 'edd_template_paths', array( $this, 'edd_template_paths' ) );

		add_action( 'edd_payment_receipt_after', array( $this, 'edd_payment_receipt_after' ), 10, 2 );
		add_action( 'edd_fs_mark_shipped', array( $this, 'mark_shipped' ) );

		add_shortcode( 'edd_frontend_shipping', array( $this, 'edd_frontend_shipping' ) );
	}

	/**
	 * Create our own template directory to load template files from and allow themes
	 * and other plugins to set their own.
	 *
	 * @since Easy Digital Downloads - Frontend Shipping 1.0.0
	 *
	 * @param array $paths
	 * @return array $paths
	 */
	function edd_template_paths( $paths ) {
		$paths[90] = trailingslashit( $this->plugin_dir ) . trailingslashit( 'templates' );

		return $paths;
	}

	/**
	 * If the current user is the author of an item in someone's purchase, let
	 * them view the purchase receipt.
	 *
	 * @since Easy Digital Downloads - Frontend Shipping 1.0.0
	 *
	 * @param boolean $user_can_view
	 * @param array $edd_receipt_args
	 * @return boolean $user_can_view
	 */
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

	/**
	 * Add the shipping address to the end of the payment receipt.
	 *
	 * @since Easy Digital Downloads - Frontend Shipping 1.0.0
	 *
	 * @param object $payment
	 * @param array $edd_receipt_args
	 * @return void
	 */
	function edd_payment_receipt_after( $payment, $edd_receipt_args ) {
		$user_info = edd_get_payment_meta_user_info( $payment->ID );
		$address   = ! empty( $user_info[ 'shipping_info' ] ) ? $user_info[ 'shipping_info' ] : false;

		if ( ! $address ) {
			return;
		}

		echo '<tr>';
		echo '<td><strong>' . __( 'Shipping Address', 'edd_fs' ) . '</strong></td>';
		echo '<td>' . self::format_address( $user_info, $address ) . '<td>';
		echo '</tr>';
	}

	/**
	 * Mark a payment as shipped.
	 *
	 * @since Easy Digital Downloads - Frontend Shipping 1.0.0
	 *
	 * @return void
	 */
	function mark_shipped() {
		if ( ! wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'fs_mark_shipped' ) ) {
			wp_safe_redirect( wp_get_referer() );

			return exit();
		}

		$payment_id = absint( $_GET[ 'payment_id' ] );

		if ( ! $this->edd_user_can_view_receipt( false, array( 'id' => $payment_id ) ) ) {
			wp_safe_redirect( wp_get_referer() );

			return exit();
		}

		update_post_meta( $payment_id, '_edd_payment_shipping_status', '2' );

		wp_safe_redirect( wp_get_referer() );

		exit();
	}

	/**
	 * Format an address based on name and address information.
	 *
	 * For translators, a sample default address:
	 *
	 * (1) First (2) Last
	 * (3) Street Address 1
	 * (4) Street Address 2
	 * (5) City, (6) State (7) ZIP
	 * (8) Country
	 *
	 * @since Easy Digital Downloads - Frontend Shipping 1.0.0
	 *
	 * @param array $user_info
	 * @param array $address
	 * @return string $address
	 */
	public static function format_address( $user_info, $address ) {
		$user_info = array_map( 'esc_attr', $user_info );
		$address   = array_map( 'esc_attr', $address );

		$address = apply_filters( 'edd_fs_address_format', sprintf(
			__( '<strong>%1$s %2$s</strong><br />%3$s<br />%4$s<br />%5$s, %6$s %7$s<br />%8$s', 'edd_fs' ),
			$user_info[ 'first_name' ],
			$user_info[ 'last_name' ],
			$address[ 'address' ],
			$address[ 'address2' ],
			$address[ 'city' ],
			$address[ 'state' ],
			$address[ 'zip' ],
			$address[ 'country' ]
		) );

		return $address;
	}

	/**
	 * Shortcode output
	 *
	 * @since Easy Digital Downloads - Frontend Shipping 1.0.0
	 *
	 * @param array $atts
	 * @return void
	 */
	function edd_frontend_shipping( $atts ) {
		$atts = shortcode_atts( array(
			'show-shipped' => true
		), $atts, 'edd_frontend_shipping' );

		$user_id            = get_current_user_id();
		$published_products = EDD_FES()->queries->get_published_products( $user_id );
		$published_products = wp_list_pluck( $published_products, 'ID' );

		$payments = edd_get_payments( array(
			'download' => $published_products,
			'output'   => 'payments',
			'mode'     => 'all'
		) );

		if ( ! $payments ) {
			return;
		}

		$unshipped = array();
		$shipped   = array();

		foreach ( $payments as $payment ) {
			$status = get_post_meta( $payment->ID, '_edd_payment_shipping_status', true );

			if ( 2 == $status ) {
				$unshipped[] = $payment;
			} else {
				$shipped[] = $payment;
			}
		}

		ob_start();

		$template = edd_locate_template( array( 'frontend-shipping.php' ), false, false );

		include( $template );

		$content = ob_get_clean();

		wp_reset_query();

		return $content;
	}

}
add_action( 'plugins_loaded', array( 'EDD_Frontend_Shipping', 'instance' ) );