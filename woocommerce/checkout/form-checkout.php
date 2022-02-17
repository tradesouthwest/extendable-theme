<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */
// TODO check test for id="customer_details"
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

?>
	<div class="extndbl-checkout-logo">
		<?php if ( function_exists( 'extendable_theme_checkout_logo_render' ) ):
			echo do_action('extendable_theme_checkout_logo'); endif; 
		?>
	</div>
		<div class="extndbl-wide">

        <?php if( function_exists('extendable_theme_checkout_progress_bar_render')):
			do_action('extendable_theme_checkout_progress'); endif;
		?>
		
	<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

	<?php if ( $checkout->get_checkout_fields() ) : ?>

		<div class="col2-set" id="customer_details">
			<div class="col-1">
				<div class="extndbl-chkout extndbl-chkout-billing">
					<?php do_action( 'woocommerce_checkout_billing' ); ?>
				</div>
				<h3><?php esc_html_e('Payment Information', 'extendable'); ?></h3>
				<div id="order_review" class="woocommerce-checkout-review-order">
					<?php do_action( 'woocommerce_checkout_order_review' ); ?>
				</div>

				<?php do_action('extendable_theme_checkout_termsand'); ?>
			</div>

			<div class="col-2">
				<div class="extndbl-chkout-before">
				<h3><?php esc_html_e('Order Summary', 'extendable'); ?></h3>

					<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
				</div>

				<div class="extndbl-chkout-after">		
				
					<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
				</div>
				<div class="extndbl-checkout-details">
				<?php if(function_exists( 'extendable_theme_package_details_render' ) ) : 
					do_action('extendable_theme_payments_details');
					do_action('extendable_theme_package_details'); endif; 
				?>
				</div>
			</div>
		</div>

		<div class="col2-set" id="extendbl_checkout">
			<div class="col-1">
				<div class="extndbl-chkout-shiping">
					<?php do_action( 'woocommerce_checkout_shipping' ); ?>
				</div>
			</div>
		
			<div class="col-2">
				
			</div>
		</div>
	<?php endif; ?>
	
		<span class="extndbl-review-before">	
			<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
		</span>
		<span class="extndbl-review-heading">	
			<h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h3>
		</span>
	
		<div class="extndbl-before-order-review">	
			<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
		</div>
	
		

		<div class="extndbl-review-">
			<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
		</div>
</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
<div class="checkout-copyright"><p>Copyright © 2022 Extendify</p></div>
</div>

