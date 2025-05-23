<?php
/**
 * Template for displaying the Left Banner.
 *
 * This template is included via AJAX and relies on several variables
 * being defined and sanitized in the calling AJAX handler function
 * (`floated_ads_handle_left_banner` in `floated_banners.php`).
 *
 * Expected variables:
 * - $left_banner_is_on (int): Whether the left banner is active (1 or 0).
 * - $left_banner_is_image (int): Whether the banner is an image (1 or 0).
 * - $left_banner_is_code (int): Whether the banner is custom code (1 or 0).
 * - $LeftBannerW (int): Width of the banner.
 * - $LeftBannerH (int): Height of the banner.
 * - $left_banner_link (string): URL for the banner image link.
 * - $left_banner_url (string): URL for the banner image source.
 * - $left_banner_custom (string): Custom HTML/JS code for the banner (pre-sanitized).
 *
 * Note: $left_banner_sticky_php and other contextual variables like $clientwidth_php, $screen_w
 * are handled by the JavaScript that initiates the AJAX call or by the main plugin logic.
 *
 * @package FloatedAds
 * @since 2.0.0
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( 1 === $left_banner_is_on && 1 === $left_banner_is_image && ! empty( $left_banner_url ) ) {
	// Ensure $LeftBannerW and $LeftBannerH are integers for CSS. absint() is used in handler, but as defense-in-depth.
	$style = 'position: absolute; top: 0px; width:' . absint( $LeftBannerW ) . 'px;height:' . absint( $LeftBannerH ) . 'px;overflow:hidden;';
	echo '<div id="divAdLeft" style="' . esc_attr( $style ) . '">';
	echo '<a href="' . esc_url( $left_banner_link ) . '"><img src="' . esc_url( $left_banner_url ) . '" alt="' . esc_attr__( 'Left Banner Ad', 'apc' ) . '" /></a>'; // Changed text domain to 'apc'
	echo '</div>';
} elseif ( 1 === $left_banner_is_on && 1 === $left_banner_is_code && ! empty( $left_banner_custom ) ) {
	// Ensure $LeftBannerW and $LeftBannerH are integers for CSS.
	$style = 'position: absolute; top: 0px; width:' . absint( $LeftBannerW ) . 'px;height:' . absint( $LeftBannerH ) . 'px;overflow:hidden;';
	echo '<div id="divAdLeft" style="' . esc_attr( $style ) . '">';
	// $left_banner_custom is pre-sanitized with wp_kses_post in the AJAX handler.
	echo $left_banner_custom;
	echo '</div>';
} else {
	// Output an empty div if the banner is not supposed to be shown or is misconfigured.
	// This helps the JS to still find the div if it needs to manage its visibility.
	echo '<div id="divAdLeft"></div>';
}

// The JavaScript for adding 'fixed_float' class is removed from here.
// This class will be added by the main FloatedAds.js if $left_banner_sticky_php (from floatedAdsGlobalData) is true.
?>