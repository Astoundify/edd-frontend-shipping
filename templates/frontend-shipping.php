<?php
/**
 * List payments
 */
?>

<?php if ( $unshipped ) : ?>

<h2><?php _e( 'Unshipped Orders', 'edd_fes' ); ?></h2>

<table class="fes_table table-condensed" id="fes-published-products">
	<thead>
		<tr>
			<th><?php _e( 'Order', 'edd_fs' ); ?></th>
			<th><?php _e( 'Shipping Address', 'edd_fs' ); ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ( $unshipped as $payment ) : ?>
		<?php
			$user_info = edd_get_payment_meta_user_info( $payment->ID );
			$address   = ! empty( $user_info[ 'shipping_info' ] ) ? $user_info[ 'shipping_info' ] : false;
		?>
		<tr>
			<td>
				<a href="<?php echo esc_url( add_query_arg( 'payment_key', $payment->key, edd_get_success_page_uri() ) ); ?>" style="text-decoration: none;"><?php printf( '<strong>Order #%1$d</strong> on %2$s', $payment->ID, date_i18n( get_option( 'date_format' ), strtotime( $payment->date ) ) ); ?></a> <br />
			</td>
			<td>
				<?php echo EDD_Frontend_Shipping::format_address( $user_info, $address ); ?>
			</td>
			<td>
				<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'edd_action' => 'fs_mark_shipped', 'payment_id' => $payment->ID ), get_permalink() ) ), 'fs_mark_shipped' ); ?>"><?php _e( 'Mark Shipped', 'edd_fs' ); ?></a>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<?php endif; ?>

<?php if ( $shipped ) : ?>

<h2><?php _e( 'Shipped Orders', 'edd_fes' ); ?></h2>

<table class="fes_table table-condensed" id="fes-published-products">
	<thead>
		<tr>
			<th><?php _e( 'Order', 'edd_fes' ); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ( $shipped as $payment ) : ?>
		<?php $user_info = edd_get_payment_meta_user_info( $payment->ID ); ?>
		<tr>
			<td>
				<a href="<?php echo esc_url( add_query_arg( 'payment_key', $payment->key, edd_get_success_page_uri() ) ); ?>" style="text-decoration: none;"><?php printf( '<strong>Order #%1$d</strong> on %2$s', $payment->ID, date_i18n( get_option( 'date_format' ), strtotime( $payment->date ) ) ); ?></a> <br />
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<?php endif; ?>