<?php
/**
 * Template for cancel email.
 *
 * @link  https://makewebbetter.com/
 * @since 1.0.0
 *
 * @package    Woo_Refund_And_Exchange_Lite
 * @subpackage Woo_Refund_And_Exchange_Lite/admin/partials
 */

$order_obj      = wc_get_order( $order_id );
$message        =
'<div class="mwb_rma_refund_cancel_email>
    <div class="Order">
        <h4>Order #' . $order_id . '</h4>
    </div>
    <div class="header">
        <h2>' . __( 'Your Refund Request is Cancelled', 'woo-refund-and-exchange-lite' ) . '</h2>
    </div>';
$attachment     = array();
$customer_email = WC()->mailer()->emails['mwb_rma_refund_request_cancel_email'];
$customer_email->trigger( $message, $attachment, $order_obj->get_billing_email(), $order_id );

