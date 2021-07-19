<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Woo_Refund_And_Exchange_Lite
 * @subpackage Woo_Refund_And_Exchange_Lite/public/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$flag = false;

if ( isset( $_GET['mwb_rma_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['mwb_rma_nonce'] ) ), 'mwb_rma_nonce' ) && isset( $_GET['order_id'] ) ) {
	$order_id = sanitize_text_field( wp_unslash( $_GET['order_id'] ) );
}

// Send Order messages code start.
$get_nonce = isset( $_POST['mwb_order_msg_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_order_msg_nonce'] ) ) : '';
if ( isset( $_POST['mwb_order_msg_submit'] ) && ! empty( $_POST['mwb_order_msg_submit'] ) && isset( $order_id ) ) {
	if ( wp_verify_nonce( $get_nonce, 'mwb_order_msg_nonce' ) ) {
		$msg    = isset( $_POST['mwb_order_new_msg'] ) ? filter_input( INPUT_POST, 'mwb_order_new_msg' ) : '';
		$to     = get_option( 'admin_email', false );
		$sender = 'Customer';
		$flag   = mwb_rma_lite_send_order_msg_callback( $order_id, $msg, $sender, $to );
	}
}
// Send Order messages code end.
$upload_attach = get_option( 'mwb_rma_general_enable_om_attachment', 'no' );
get_header( 'shop' );

do_action( 'woocommerce_before_main_content' );

if ( isset( $order_id ) ) {
	?>
<div class="mwb_rma_order_msg_wrapper">
	<?php if ( $flag ) { ?>	
	<div class="mwb_order_msg_notice_wrapper">
		<p class="mwb_order_msg_sent_notice"><strong><?php esc_html_e( 'Message has been sent.', 'woo-refund-and-exchange-lite' ); ?></strong><a href="#" class="mwb_order_send_msg_dismiss">X</a></p>
	</div>
	<?php } ?>
	<div class="mwb_order_msg_container">
		<form id="mwb_order_new_msg_form" method="post" enctype="multipart/form-data">
			<div class="mwb_order_msg_title">
				<h4><?php esc_html_e( 'Add a message', 'woo-refund-and-exchange-lite' ); ?></h4>
			</div>
			<textarea id="mwb_order_new_msg" name="mwb_order_new_msg" placeholder="<?php esc_html_e( 'Write a message you want to sent to the Shop Manager.', 'woo-refund-and-exchange-lite' ); ?>" rows="6" maxlength='10000' required ></textarea>
			<?php if ( isset( $upload_attach ) && 'on' === $upload_attach ) : ?>
				<div class="mwb-order-msg-attachment-wrapper">
					<div><label for="mwb_order_msg_attachment"> <?php esc_html_e( 'Attach files: ', 'woo-refund-and-exchange-lite' ); ?></label></div>
					<div><input type="file" id="mwb_order_msg_attachment" name="mwb_order_msg_attachment[]" multiple ></div>
				</div>
			<?php endif; ?>
			<div class="mwb-order-msg-btn">
				<input type="submit" id="mwb_order_msg_submit" name="mwb_order_msg_submit" value="<?php esc_html_e( 'Send', 'woo-refund-and-exchange-lite' ); ?>">
				<input 	type="hidden" name="mwb_order_msg_nonce" value="<?php echo esc_attr( wp_create_nonce( 'mwb_order_msg_nonce' ) ); ?>"> 
			</div>
		</form>
		<div class="mwb_order_msg_reload_notice_wrapper">
			<p class="mwb_order_msg_sent_notice"><strong><?php esc_html_e( 'Messages Refreshed Successfully.', 'woo-refund-and-exchange-lite' ); ?></strong></p>
		</div>
		<div class="mwb_order_msg_history_container">
			<div class="mwb_order_msg_history_title">
			<h4 class="mwb-order-heading"><?php esc_html_e( 'Message History', 'woo-refund-and-exchange-lite' ); ?>
				<a href="" class="mwb_reload_messages"><img src="<?php echo esc_url( WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL ) . 'public/images/reload-icon.png'; ?>" class="reload-icon"></a>
			</h4>
			</div>
			<div class="mwb_order_msg_sub_container">
				<?php
				$mwb_order_messages = get_option( $order_id . '-mwb_cutomer_order_msg', array() );
				if ( isset( $mwb_order_messages ) && is_array( $mwb_order_messages ) && ! empty( $mwb_order_messages ) ) {
					foreach ( array_reverse( $mwb_order_messages ) as $o_key => $o_val ) {
						foreach ( $o_val as $om_key => $om_val ) {
							?>
							<div class="mwb_order_msg_main_container mwb_order_messages">
								<div>
									<div class="mwb_order_msg_sender"><?php echo esc_html( ( 'Customer' === $om_val['sender'] ) ? __( 'Customer', 'woo-refund-and-exchange-lite' ) : __( 'Shop Manager', 'woo-refund-and-exchange-lite' ) ); ?></div>
									<span class="mwb_order_msg_date"><?php echo esc_html( get_date_from_gmt( gmdate( 'Y-m-d h:i a', $om_key ), 'Y-m-d h:i a' ) ); ?></span>
								</div>
								<div class="mwb_order_msg_detail_container">
									<span><?php echo esc_html( $om_val['msg'] ); ?></span>
								</div>
								<?php if ( isset( $om_val['files'] ) && ! empty( $om_val['files'] ) ) { ?>

									<div class="mwb_order_msg_attach_container">
										<div class="mwb_order_msg_attachments_title"><?php esc_html_e( 'Message attachments:', 'woo-refund-and-exchange-lite' ); ?></div>
										<?php
										foreach ( $om_val['files'] as $fkey => $fval ) {
											if ( ! empty( $fval['name'] ) ) {
												$is_image = $fval['img'];
												?>
												<div class="mwb_order_msg_single_attachment">
													<a target="_blank" href="<?php echo esc_url( get_home_url() ) . '/wp-content/attachment/' . esc_html( $order_id ) . '-' . esc_html( $fval['name'] ); ?>">
														<img class="mwb_order_msg_attachment_thumbnail" src="<?php echo $is_image ? esc_url( get_home_url() ) . '/wp-content/attachment/' . esc_html( $order_id ) . '-' . esc_html( $fval['name'] ) : esc_url( MWB_REFUND_N_EXCHANGE_LITE_URL ) . '/public/images/attachment.png'; ?>">
														<span class="mwb_order_msg_attachment_file_name"><?php echo esc_html( $fval['name'] ); ?></span>
													</a>
												</div>
											<?php } ?>
										<?php } ?>
									</div>
								<?php } ?>
							</div>
							<?php
						}
					}
				}
				?>
			</div>
		</div>
	</div>
	<?php
}
do_action( 'woocommerce_after_main_content' );

do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );