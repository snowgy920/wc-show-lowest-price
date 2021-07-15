<?php

add_action( 'wp_enqueue_scripts', 'porto_child_css', 1001 );

// Load CSS
function porto_child_css() {
	// porto child theme styles
	wp_deregister_style( 'styles-child' );
	wp_register_style( 'styles-child', esc_url( get_stylesheet_directory_uri() ) . '/style.css' );
	wp_enqueue_style( 'styles-child' );

	if ( is_rtl() ) {
		wp_deregister_style( 'styles-child-rtl' );
		wp_register_style( 'styles-child-rtl', esc_url( get_stylesheet_directory_uri() ) . '/style_rtl.css' );
		wp_enqueue_style( 'styles-child-rtl' );
	}
}

add_action( 'wp_enqueue_scripts', 'porto_child_js', 1002 );
function porto_child_js() {
	wp_register_script( 'porto-child', esc_url( get_stylesheet_directory_uri() ) . '/custom.js', array('jquery', 'porto-theme-async'), '', true);
	wp_enqueue_script( 'porto-child' );
}

add_action( 'init', 'porto_child_init', 20 );
function porto_child_init() {
	add_filter( 'script_loader_tag', 'porto_child_script_add_async_attribute', 10, 2 );
}
function porto_child_script_add_async_attribute( $tag, $handle ) {
	// add script handles to the array below
	$scripts_to_async = array( 'porto-child' );
	if ( in_array( $handle, $scripts_to_async ) ) {
		return str_replace( ' src', ' async="async" src', $tag );
	}
	return $tag;
}

add_action('wp_footer', 'porto_child_render_register_form');
function porto_child_render_register_form() {
	if ( ! is_checkout() && ! is_user_logged_in() && ( ! isset( $porto_settings['woo-account-login-style'] ) || ! $porto_settings['woo-account-login-style'] ) ) {
		echo '<div id="login-form-popup" class="lightbox-content mfp-hide">';
		echo wc_get_template_part( 'myaccount/form-register-popup' );
		echo '</div>';
	}
}

add_filter( 'woocommerce_variable_sale_price_html', 'wpglorify_variation_price_format', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'wpglorify_variation_price_format', 10, 2 );
function wpglorify_variation_price_format( $price, $product ) {
	// Main Price
	$prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
	$price = $prices[0] !== $prices[1] ? sprintf( __( 'From: %1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );

	// Sale Price
	$prices = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
	sort( $prices );
	$saleprice = $prices[0] !== $prices[1] ? sprintf( __( 'From: %1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );

	if ( $price !== $saleprice ) {
		$price = '<del>' . $saleprice . $product->get_price_suffix() . '</del> <ins>' . $price . $product->get_price_suffix() . '</ins>';
	}
	return $price;
}