<?php
/**
 * Extendable functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Extendable
 */

/**
 * Theme setup.
 *
 * @return void
 */
function extendable_support() {

	// Add support for block styles.
	add_theme_support( 'wp-block-styles' );

	// Enqueue editor styles.
	add_editor_style(
		array( 'assets/theme.css' )
	);

	// Add logo to the Customizer, for those who are used to that flow.
	add_theme_support(
		'custom-logo',
		array(
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
}
add_action( 'after_setup_theme', 'extendable_support' );

/**
 * Enqueue scripts and styles.
 * @since xxx Larry Judd added `extendable-woocommerce`
 * @return void
 */
function extendable_scripts() { wp_enqueue_style( 'extendable-woocommerce', get_template_directory_uri() . '/style.css', array(), time() ); 
	wp_enqueue_style( 'extendable-style', get_theme_file_uri( '/assets/theme.css' ), wp_get_theme()->get( 'Version' ) );
	wp_enqueue_script( 'extendable-js', get_theme_file_uri( '/assets/js/demo.js' ), array(), wp_get_theme()->get( 'Version' ), true );
}
add_action( 'wp_enqueue_scripts', 'extendable_scripts' );

/**
 * Enqueue block editor scripts.
 *
 * @return void
 */
function extendable_editor_scripts() {
	wp_enqueue_script( 'extendable-editor', get_theme_file_uri( '/assets/js/block-styles.js' ), array( 'wp-blocks', 'wp-dom' ), wp_get_theme()->get( 'Version' ), true );
}
add_action( 'enqueue_block_editor_assets', 'extendable_editor_scripts' );

/**
 * Add theme meta tag
 *
 * Currently uses the "light" accent color for light, and the "primary" accent color for dark.
 * Test in the future if I want to use the "primary" color all around perhaps.
 * Downside is that this does not support CSS custom properties, so I can't use theme.json colors here.
 */
function extendable_theme_meta_tag() {
	echo '<meta name="theme-color" content="#CFC9F2" media="(prefers-color-scheme: light)">';
	echo '<meta name="theme-color" content="#CFC9F2" media="(prefers-color-scheme: dark)">';
}
add_action( 'wp_head', 'extendable_theme_meta_tag' );

// Add block patterns
require get_template_directory() . '/inc/block-patterns.php';




// FROM OLD:
function extendable_theme_footer_scripts() {
	echo '<script type="text/javascript">!function(e,t,n){function a(){var e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],"complete"===t.readyState)return a();e.attachEvent?e.attachEvent("onload",a):e.addEventListener("load",a,!1)}(window,document,window.Beacon||function(){});</script>';
	echo '<script type="text/javascript">window.Beacon("init", "2b8c11c0-5afc-4cb9-bee0-a5cb76b2fc91")</script>';
}
add_action( 'wp_print_footer_scripts', 'extendable_theme_footer_scripts' );

function extendable_theme_footer_seo() {
	echo '<script async src="https://www.googletagmanager.com/gtag/js?id=UA-184681817-1"></script>';
	echo '<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag("js", new Date());

  gtag("config", "UA-184681817-1");</script>';
}
add_action( 'wp_print_footer_scripts', 'extendable_theme_footer_seo' );





/*
 * Removing default "Coupon form" from the top of the checkout page
 */
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );

/*
 * Hooking "Coupon form" after order total in checkout page with custom function
 */
add_action( 'woocommerce_review_order_after_order_total', 'woocommerce_checkout_coupon_form_custom' );

/*
 * Rendering html for "Coupon form" with custom function
 */
function woocommerce_checkout_coupon_form_custom() {
	echo '<tr class="coupon-form"> <td colspan="2"> ';
	wc_get_template(
		'checkout/form-coupon.php',
		array(
			'checkout' => WC()->checkout(),
		)
	);
	echo '</td></tr>';
}

/*
 * Redirect to checkout page
 */
add_filter( 'woocommerce_add_to_cart_redirect', 'codedocx_redirect_checkout' );

function codedocx_redirect_checkout( $url ) {
	return wc_get_checkout_url();
}

/*
 * Disable add to cart notice
 */
add_filter( 'wc_add_to_cart_message_html', '__return_false' );

/*
***************************************
 Remove some fields from billing form
***************************************
*/
add_filter( 'woocommerce_billing_fields', 'remove_billing_fields' );
function remove_billing_fields( $fields = array() ) {
	if ( ! is_wc_endpoint_url( 'edit - address' ) ) {
		unset( $fields['billing_company'] );
		unset( $fields['billing_address_1'] );
		unset( $fields['billing_address_2'] );
		unset( $fields['billing_state'] );
		unset( $fields['billing_city'] );
		unset( $fields['billing_postcode'] );
		unset( $fields['billing_country'] );
		unset( $fields['billing_phone'] );
	}
	return $fields;
}
// remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );


/*
***************************************
 Change field order from billing form
***************************************
*/
add_filter( 'woocommerce_checkout_fields', 'change_field_order_for_billing_form' );
function change_field_order_for_billing_form( $checkout_fields ) {
	if ( ! is_wc_endpoint_url( 'edit - address' ) ) {
		$checkout_fields['billing']['billing_email']['priority'] = 4;
	}
	return $checkout_fields;
}

function validate( $data, $errors ) {
	// Do your data processing here and in case of an
	// error add it to the errors array like:
	$errors->add( 'validation', __( 'Please input that correctly . ' ) );
}
// add_action('woocommerce_after_checkout_validation', 'validate',10,2);

/*
**********************************************
 Remove product image and link from cart page
**********************************************
*/
add_filter( 'woocommerce_cart_item_permalink', '__return_null' );
add_filter( 'woocommerce_cart_item_thumbnail', '__return_false' );


/*
**************************************************
 Remove previous product from cart adding new one
**************************************************
*/
add_filter( 'woocommerce_add_cart_item_data', 'empty_cart', 10, 3 );
function empty_cart( $cart_item_data, $product_id, $variation_id ) {
	global $woocommerce;
	$woocommerce->cart->empty_cart();

	return $cart_item_data;
}

/*
*****************************************
 Remove Billing word from checkout page
*****************************************
*/
function customize_wc_errors( $error ) {
	if ( strpos( $error, 'Billing ' ) !== false ) {
		$error = str_replace( 'Billing ', '', $error );
	}
	return $error;
}
add_filter( 'woocommerce_add_error', 'customize_wc_errors' );

/*
 * Misc
 */

remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review' );

add_action( 'woocommerce_checkout_before_customer_details', 'woocommerce_order_review' );

/*
 * Remove menu link for account page
 */
add_filter( 'woocommerce_account_menu_items', 'misha_remove_my_account_links' );
function misha_remove_my_account_links( $menu_links ) {

	//unset( $menu_links['dashboard'] ); // Remove Dashboard
	//unset( $menu_links['payment - methods'] ); // Remove Payment Methods
	//unset( $menu_links['orders'] ); // Remove Orders
	//unset( $menu_links['downloads'] ); // Disable Downloads
	//unset( $menu_links['edit - account'] ); // Remove Account details tab
	//unset( $menu_links['customer - logout'] ); // Remove Logout link

	return $menu_links;
}


/*
 * Override WooCommerce Navigation
 */
remove_action( 'woocommerce_account_navigation', 'woocommerce_account_navigation' );

add_action(
	'woocommerce_account_navigation',
	function () {
		$links_to_exclude_labels = array(
			'API Downloads',
			'Downloads',
		); ?>

		<?php do_action( 'woocommerce_before_account_navigation' ); ?>

<nav class="woocommerce-MyAccount-navigation">
	<ul>
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
			<?php if ( ! in_array( $label, $links_to_exclude_labels ) ) : ?>
				<li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
					<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
				</li>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>
</nav>

		<?php do_action( 'woocommerce_after_account_navigation' ); ?>

		<?php
	}
);

// include_once( dirname( __FILE__ ) . '/braintree/filter.php' );


// Disable cc option from checkout

add_filter(
	'wc_braintree_paypal_disabled_funding_options',
	function( $disabled_funding_options ) {

		return array_merge( $disabled_funding_options, array( 'card' ) );
	}
);

// Customize coupon text for checkout page

add_filter(
	'woocommerce_checkout_coupon_message',
	function() {
		return '<a href="#" class="showcoupon"> Have a coupon? </a>';
	}
);


// Disable coupon notice for checkout page

add_filter(
	'woocommerce_coupon_message',
	function() {
		return '';
	}
);

// Change woocommerce product additional information

// Removes Order Notes Title
add_filter( 'woocommerce_enable_order_notes_field', '__return_false', 9999 );

// Remove Order Notes Field
add_filter(
	'woocommerce_checkout_fields',
	function ( $fields ) {
		unset( $fields['order']['order_comments'] );
		return $fields;
	}
);

/*
add_action(
	'woocommerce_after_order_notes',
	function() {
		?>
		<h3><?php esc_html_e( 'Additional information' ); ?></h3>
		<br />
		<p>Extendify Pro is trusted by 10,000+ users to turn WordPress into a superpower.</p>
		<ul style="list-style-type: none; padding-left: 0;">
			<li>✅ Full-page templates</li>
			<li>✅ Designer patterns</li>
			<li>✅ 1,000+ designs</li>
			<li>✅ Lightweight and fast</li>
			<li>✅ 14 day money back guarantee</li>
		</ul>
		<?php
	}
);
*/
// add placeholder input field for checkout page

add_filter( 'woocommerce_checkout_fields', 'add_billing_input_fields_placeholder' );

function add_billing_input_fields_placeholder( $fields ) {

	$fields['billing']['billing_first_name']['placeholder'] = 'First Name';
	$fields['billing']['billing_last_name']['placeholder']  = 'Last Name';
	$fields['billing']['billing_email']['placeholder']      = 'Email Address';

	return $fields;

}

// add "stripe truest logo" image for checkout page

add_action(
	'woocommerce_credit_card_form_start',
	function() {
		?>
		<div class="stripe_field_wrapper">
			<?php
	}
);

add_action(
	'woocommerce_credit_card_form_end',
	function() {
		?>
	</div>
	<div class="stripe-trust-logo">
		<img src="https://extendify.com/content/uploads/2021/07/secure-stripe@2x.png" alt="Secure Payments by Stripe"/>
	</div>
		<?php
	}
);

// Remove login notice for checkout page


add_action( 'woocommerce_before_checkout_form', 'force_checkout_login_for_unlogged_customers', 4 );
function force_checkout_login_for_unlogged_customers() {
	if ( ! is_user_logged_in() ) {
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
	}
}

/* =================================
 * START CHANGES BY LARRY @CODEABLE 
 * ================================= */
/* Maybe make file extendable-custom-checkout.php and include instead */

// woocommerce_order_button_text
add_filter('woocommerce_order_button_text', 'extendable_theme_subscriptions_submit_button_text' );
function extendable_theme_subscriptions_submit_button_text( $order_button_text ) {
    if ( WC_Subscriptions_Cart::cart_contains_subscription() ) {
        $order_button_text =  __( 'Complete Checkout', 'woocommerce-subscriptions'  );
    } else {
        // You can change it here for other products types in cart
        # $order_button_text =  __( 'Purchase', 'woocommerce-subscriptions'  );
    }
    return $order_button_text;
}

/**
 * Include custom checkout configuration
 * @since 2022-02-12 Larry Judd [codeable]
 * @maybe_use require get_template_directory() . '/inc/extendable-custom-checkout.php'; 
 */
add_action('extendable_theme_payments_details', 'extendable_theme_payments_details_render');
function extendable_theme_payments_details_render() {
	if( !is_checkout() ) return;
	$img  = 'http://tswdev.com/extendify/wp-content/uploads/2022/02/trust-icons-14day-compressor.webp';
	$alt  = __( '14 day money back gaurantee', 'extendable');
	$img_2= 'http://tswdev.com/extendify/wp-content/uploads/2022/02/paypal-verified.webp';
	printf(
	'<div class="text-center mt-4 mb-4">
		<img src="%s" alt="%s" class="inline mr-6" style="height: 90px;"> 
		<img src="%s" alt="PayPal Verified" class="inline " style="height: 80px;">
	</div>',
	esc_url($img),
	esc_attr($alt),
	esc_url($img_2)
	);
}

/**
 * Include custom checkout configuration
 * @since 2022-02-12 Larry Judd [codeable]
 * @maybe_use require get_template_directory() . '/inc/extendable-custom-checkout.php'; 
 */
add_action('extendable_theme_package_details', 'extendable_theme_package_details_render');
function extendable_theme_package_details_render() {
	if( !is_checkout() ) return;
		$orig_checkmark = '✅ ';
		$checkmark = '<svg xmlns="http://www.w3.org/2000/svg" width="15" fill="#20A175" viewBox="0 0 512 512" aria-hidden="true" focusable="false" class="inline-block mr-2 mb-2"><path d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z"></path></svg>';
		$label = __('What You Get');
		$item_a= __(' Unlimited template imports');
		$item_b= __(' 14 day money back guarantee');
		$item_c= __(' Top Level support');
		$item_d= __(' Instant Download');
		$item_e= __(' Exclusive Bonuses');
		$pkgimg= "http://tswdev.com/extendify/wp-content/uploads/2022/02/stockphoto-pkg.jpg";
		$alt   = "package";
		$review= "This plugin helped me create better looking websites faster and easier. ";
		$pkimg2= "http://tswdev.com/extendify/wp-content/uploads/2022/02/large_customer.jpeg";

	printf(
	'<div class="extndbl-theme-package-details"><label>%s</label>
		<div class="extndbl-col">
		
		<div class="extndbl-pkglist">
			<ul><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li></ul>
		</div>
			<div class="extndbl-pkgimg">
				<img src="%s" alt="%s"/>
			</div>
		</div>
		<div class="extndbl-col">	
			<div class="extndbl-pkgreview">
				<div class="mt-8">
					<p class="relative testimonials text-gray-600 bg-neutral-5 text-sm py-3 px-6 rounded-lg font-serif italic">
					%s <span class="inline pl-3">
					<svg xmlns="http://www.w3.org/2000/svg" width="15" fill="#ffcc00" viewBox="0 0 576 512" 
					role="img" aria-hidden="true" focusable="false" class="inline"><path d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"></path></svg> <svg xmlns="http://www.w3.org/2200/svg" width="15" fill="#ffcc00" viewBox="0 0 576 512" role="img" aria-hidden="true" focusable="false" class=" inline"><path d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"></path></svg> <svg xmlns="http://www.w3.org/2200/svg" width="15" fill="#ffcc00" viewBox="0 0 576 512" role="img" aria-hidden="true" focusable="false" class=" inline"><path d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"></path></svg> <svg xmlns="http://www.w3.org/2200/svg" width="15" fill="#ffcc00" viewBox="0 0 576 512" role="img" aria-hidden="true" focusable="false" class=" inline"><path d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"></path></svg> <svg xmlns="http://www.w3.org/2200/svg" width="15" fill="#ffcc00" viewBox="0 0 576 512" role="img" aria-hidden="true" focusable="false" class=" inline"><path d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"></path></svg>
					</span></p> 
					<div class="flex items-center mt-4 ml-4 text-neutral-60">
						<img src="%s" class="rounded-full mr-4" style="width: 50px;"> 
						<span class="ht-sm"><strong>Larry Johnston</strong>
						<br>WordPress Developer</span>
					</div>
				</div>
			</div>
		</div>
	</div>',
		esc_html($label),
		$checkmark . ' <span>' . esc_html($item_a) . '</span>',
		$checkmark . ' <span>' . esc_html($item_b) . '</span>',
		$checkmark . ' <span>' . esc_html($item_c) . '</span>',
		$checkmark . ' <span>' . esc_html($item_d) . '</span>',
		$checkmark . ' <span>' . esc_html($item_e) . '</span>',
		esc_url($pkgimg),
		esc_attr($alt),
		esc_html($review),
		esc_url($pkimg2)
	);
}

/**
 * Include custom checkout configuration
 * @since 2022-02-16 Larry Judd [codeable]
 * @return HTML 
 */
add_action('extendable_theme_checkout_termsand', 'extendable_theme_checkout_termsand_render'); 
function extendable_theme_checkout_termsand_render(){
	$urla = '';
	$urlb = '';

	printf( '<div class="checkout-termsand">
	<p><strong>Please note: </strong> Your purchase is protected by our 14-day money back guarantee. 
	You can update payment method or cancel your plan at any time by logging into your account. 
	By clicking "Complete Checkout" button above, you agree to the Extendify 
	<a href="%s" title="Terms & Conditions">Terms & Conditions</a> and 
	<a href="%s" title="Privacy Policy">Privacy Policy</a>.</p>
	</div>',
	esc_url($urla),
	esc_url($urlb) 
	);
}
/**
 * Include custom checkout configuration
 * @since 2022-02-16 Larry Judd [codeable]
 * @maybe_use require get_template_directory() . '/assets/images'; 
 */
add_action('extendable_theme_checkout_progress', 'extendable_theme_checkout_progress_bar_render'); 
function extendable_theme_checkout_progress_bar_render(){

ob_start();
echo '<div class="extndbl-checkout-progress">
		<img src="'. get_theme_file_uri( '/assets/images/progressbar.png' ) .'" 
		title="progress" class="extndbl-progress-bar"/>
		</div>';
		$out = ob_get_clean();

		echo $out;

}

/**
 * Redirect to alternative page after checkout
 * @see https://www.tychesoftwares.com/how-to-customize-the-woocommerce-thank-you-page/
 */
//add_action( 'template_redirect', 'extendable_theme_custom_redirect_after_purchase' );
function extendable_theme_custom_redirect_after_purchase() {
	global $wp;
	if ( is_checkout() && !empty( $wp->query_vars['order-received'] ) ) {
		wp_redirect( get_home_url() . '/thank-you-page-name/' );
		exit;
	}
}
/* =================================
 * ENDS  CHANGES BY LARRY @CODEABLE 
 * ================================= */ 