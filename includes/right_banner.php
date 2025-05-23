<?php
/**
 * Template for displaying the Right Banner.
 *
 * This template is included via AJAX and relies on several variables
 * being defined and sanitized in the calling AJAX handler function
 * (`floated_ads_handle_right_banner` in `floated_banners.php`).
 *
 * Expected variables:
 * - $right_banner_is_on (int): Whether the right banner is active (1 or 0).
 * - $right_banner_is_image (int): Whether the banner is an image (1 or 0).
 * - $right_banner_is_code (int): Whether the banner is custom code (1 or 0).
 * - $RightBannerW (int): Width of the banner.
 * - $RightBannerH (int): Height of the banner.
 * - $right_banner_link (string): URL for the banner image link.
 * - $right_banner_url (string): URL for the banner image source.
 * - $right_banner_custom (string): Custom HTML/JS code for the banner (pre-sanitized).
 *
 * @package FloatedAds
 * @since 2.0.0
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( 1 === $right_banner_is_on && 1 === $right_banner_is_image && ! empty( $right_banner_url ) ) {
	$style = 'position: absolute; top: 0px; width:' . absint( $RightBannerW ) . 'px;height:' . absint( $RightBannerH ) . 'px;overflow:hidden;';
	echo '<div id="divAdRight" style="' . esc_attr( $style ) . '">';
	echo '<a href="' . esc_url( $right_banner_link ) . '"><img src="' . esc_url( $right_banner_url ) . '" alt="' . esc_attr__( 'Right Banner Ad', 'apc' ) . '" /></a>';
	echo '</div>';
} elseif ( 1 === $right_banner_is_on && 1 === $right_banner_is_code && ! empty( $right_banner_custom ) ) {
	$style = 'position: absolute; top: 0px; width:' . absint( $RightBannerW ) . 'px;height:' . absint( $RightBannerH ) . 'px;overflow:hidden;';
	echo '<div id="divAdRight" style="' . esc_attr( $style ) . '">';
	// $right_banner_custom is pre-sanitized with wp_kses_post in the AJAX handler.
	echo $right_banner_custom;
	echo '</div>';
} else {
	echo '<div id="divAdRight"></div>';
}

// The JavaScript for adding 'fixed_float' class is removed from here.
// This class will be added by the main FloatedAds.js if $right_banner_sticky_php (from floatedAdsGlobalData) is true.
?>