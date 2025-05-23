<?php
/**
 * Template for displaying the Mobile Banner.
 *
 * This template is included via AJAX and relies on several variables
 * being defined and sanitized in the calling AJAX handler function
 * (`floated_ads_handle_mobile_banner` in `floated_banners.php`).
 *
 * Expected variables:
 * - $mobile_banner_is_on (int): Whether the mobile banner is active (1 or 0).
 * - $mobile_banner_is_image (int): Whether the banner is an image (1 or 0).
 * - $mobile_banner_is_code (int): Whether the banner is custom code (1 or 0).
 * - $MobileBannerW (int): Width of the banner.
 * - $MobileBannerH (int): Height of the banner.
 * - $mobile_banner_link (string): URL for the banner image link.
 * - $mobile_banner_url (string): URL for the banner image source.
 * - $mobile_banner_custom (string): Custom HTML/JS code for the banner (pre-sanitized).
 *
 * @package FloatedAds
 * @since 2.0.0
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( 1 === $mobile_banner_is_on && 1 === $mobile_banner_is_image && ! empty( $mobile_banner_url ) ) {
	// The style for the banner itself (width/height) would typically be handled by CSS if fixed,
	// or could be added here if dynamic and necessary beyond what CSS can do.
	// For now, assuming general CSS handles the banner container's appearance.
	echo '<div id="bottom_banner">';
	echo '<span class="close-btn"></span>'; // Close button functionality to be handled by js/FloatedAds.js
	echo '<a href="' . esc_url( $mobile_banner_link ) . '"><img src="' . esc_url( $mobile_banner_url ) . '" alt="' . esc_attr__( 'Mobile Banner Ad', 'apc' ) . '" style="width:' . absint( $MobileBannerW ) . 'px; height:' . absint( $MobileBannerH ) . 'px;" /></a>';
	echo '</div>';
} elseif ( 1 === $mobile_banner_is_on && 1 === $mobile_banner_is_code && ! empty( $mobile_banner_custom ) ) {
	echo '<div id="bottom_banner" style="width:' . absint( $MobileBannerW ) . 'px; height:' . absint( $MobileBannerH ) . 'px; overflow:auto;">'; // Added overflow:auto for safety with custom code.
	echo '<span class="close-btn"></span>'; // Close button functionality to be handled by js/FloatedAds.js
	// $mobile_banner_custom is pre-sanitized with wp_kses_post in the AJAX handler.
	echo $mobile_banner_custom;
	echo '</div>';
} else {
	// Output an empty div if the banner is not supposed to be shown or is misconfigured.
	echo '<div id="bottom_banner"></div>';
}

// All JavaScript, including for the close button and its styling, is removed.
// This functionality will be handled by the main FloatedAds.js file.
?>