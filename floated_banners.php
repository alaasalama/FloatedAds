<?php
/**
 * Plugin Name: FloatedAds
 * Plugin URI: http://lab.alaadesign.com/project/floated-ads/
 * Description: Plugin for displaying floated banners ads on both sides of your website.
 * Author: Alaa Salama
 * Version: 2.0.0
 * Requires at least: 5.8
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Author URI: http://lab.alaadesign.com
 * Text Domain: apc
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define FLADS_PLUGIN_FILE if not already defined.
if ( ! defined( 'FLADS_PLUGIN_FILE' ) ) {
	define( 'FLADS_PLUGIN_FILE', __FILE__ );
}

/**
 * Admin Panel Section.
 *
 * Sets up the admin panel using the BF_Admin_Page_Class.
 */
// Include the main class file.
require_once dirname( FLADS_PLUGIN_FILE ) . '/admin/flban_admin_framework.php';

// Configure your admin page.
$flads_config = array(
	'menu'           => array( 'top' => 'floated-ads' ), // Register the menu item settings.
	'page_title'     => __( 'FloatedAds', 'apc' ),       // The name of this page.
	'capability'     => 'edit_themes',                   // The capability needed to view the page.
	'option_group'   => 'flads_options',                 // The name of the option to create in the database.
	'id'             => 'floated-ads',                   // Meta box id, unique per page.
	'fields'         => array(),                         // List of fields (can be added by field arrays).
	'local_images'   => false,                           // Use local or hosted images (meta box images for add/remove).
	'use_with_theme' => false,                           // Change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
);

// Initiate your admin page.
$flads_options_panel = new BF_Admin_Page_Class( $flads_config );
$flads_options_panel->OpenTabs_container( '' );

// Define your admin page tabs listing.
$flads_options_panel->TabsListing(
	array(
		'links' => array(
			'general'       => __( 'General', 'apc' ),
			'left_banner'   => __( 'Left Banner', 'apc' ),
			'right_banner'  => __( 'Right Content', 'apc' ), // Note: Original text was 'Right Content', might be a typo for 'Right Banner'. Keeping as is for now.
			'mobile_banner' => __( 'Mobile Banner', 'apc' ),
		),
	)
);

// General settings panel.
$flads_options_panel->OpenTab( 'general' );
$flads_options_panel->Title( __( 'General Settings', 'apc' ) );

// Theme content width.
// Getting the homepage url link.
$flads_home_url_link = get_home_url();
$flads_options_panel->addText(
	'main_content_width',
	array(
		'name'     => __( 'Enter the width of your main website container', 'apc' ),
		'std'      => 1220,
		/* translators: %s: URL to an external tool for measuring webpage width */
		'desc'     => sprintf( __( "Your banners will be positioned on the sides of this container width, use <a href='%s' class='measure_url'>this tool</a> to help you measuring your website container width.", 'apc' ), esc_url( 'https://www.piliapp.com/measure-webpage/?src=' . $flads_home_url_link ) ),
		'validate' => array(
			'numeric' => array(
				'param'   => '',
				'message' => __( 'must be numeric value', 'apc' ),
			),
		),
	)
);

// Minimum window width to display banners.
$flads_options_panel->addText(
	'container_min_width',
	array(
		'name'     => __( 'Minimum browser window size to show the banners', 'apc' ),
		'std'      => 1000,
		'desc'     => __( 'Your banners will not be displayed on browser window less than this value, suitable to hide the banners on small screens', 'apc' ),
		'validate' => array(
			'numeric' => array(
				'param'   => '',
				'message' => __( 'must be numeric value', 'apc' ),
			),
		),
	)
);

// Margin top for banners.
$flads_options_panel->addText(
	'FloatedAds_margin_top', // Original variable name, consider renaming to flads_margin_top for consistency if possible with framework.
	array(
		'name'     => __( 'Top Margin Adjustment', 'apc' ),
		'std'      => 0,
		'desc'     => __( 'Enter numeric value of the top margin adjustment', 'apc' ),
		'validate' => array(
			'numeric' => array(
				'param'   => '',
				'message' => __( 'must be numeric value', 'apc' ),
			),
		),
	)
);

// Left margin from the main layout.
$flads_options_panel->addText(
	'FloatedAds_margin_left', // Original variable name.
	array(
		'name'     => __( 'Left Margin Adjustment', 'apc' ),
		'std'      => 0,
		'desc'     => __( 'Enter numeric value of the left margin adjustment', 'apc' ),
		'validate' => array(
			'numeric' => array(
				'param'   => '',
				'message' => __( 'must be numeric value', 'apc' ),
			),
		),
	)
);

// Right margin from the main layout.
$flads_options_panel->addText(
	'FloatedAds_margin_right', // Original variable name.
	array(
		'name'     => __( 'Right Margin Adjustment', 'apc' ),
		'std'      => 0,
		'desc'     => __( 'Enter numeric value of the right margin adjustment', 'apc' ),
		'validate' => array(
			'numeric' => array(
				'param'   => '',
				'message' => __( 'must be numeric value', 'apc' ),
			),
		),
	)
);

// Show banners on homepage checkbox.
$flads_options_panel->addCheckbox(
	'show_on_homepage',
	array(
		'name' => __( 'Show banners on homepage only', 'apc' ), // Corrected typo "homeapge"
		'std'  => false,
		'desc' => __( 'Activate this option to show the banners on homepage only', 'apc' ),
	)
);
// End of general settings panel.
$flads_options_panel->CloseTab();

// Left banner settings panel.
$flads_options_panel->OpenTab( 'left_banner' );
$flads_options_panel->Title( __( 'Left Banner Settings', 'apc' ) );

// Checkbox to check if left banner is active.
$flads_options_panel->addCheckbox(
	'left_banner_active',
	array(
		'name' => __( 'Activate Left Banner Area', 'apc' ),
		'std'  => true,
		'desc' => __( 'Activate this option to display the left banner area', 'apc' ),
	)
);

// Checkbox to check if left banner is sticky.
$flads_options_panel->addCheckbox(
	'left_banner_sticky',
	array(
		'name' => __( 'Activate Sticky Option', 'apc' ),
		'std'  => true,
		'desc' => __( 'Activate this option to display the banner in a sticky style', 'apc' ),
	)
);

// Left banner image condition fields.
$flads_left_banner_image_cond   = array();
$flads_left_banner_image_cond[] = $flads_options_panel->addImage(
	'left_banner_image',
	array( 'name' => __( 'Upload banner image', 'apc' ) ),
	true
);
$flads_left_banner_image_cond[] = $flads_options_panel->addText(
	'left_banner_image_link',
	array(
		'name' => __( 'Enter your banner image link', 'apc' ),
		'std'  => '#',
		'desc' => __( "Add your banner image link, or keep it set to # in case you don't want to link your image anywhere!", 'apc' ),
	),
	true
);

// Left banner code condition fields.
$flads_left_banner_code_cond   = array();
$flads_left_banner_code_cond[] = $flads_options_panel->addTextarea(
	'left_banner_code',
	array(
		'name' => __( 'Add your banner custom code', 'apc' ),
		'std'  => __( 'You can use any banner code here including Google Adsense code', 'apc' ),
	),
	true
);
$flads_left_banner_code_cond[] = $flads_options_panel->addText(
	'left_banner_code_width',
	array(
		'name'     => __( 'Your custom banner code width', 'apc' ),
		'std'      => 160,
		'desc'     => __( 'Enter numeric value of the banner width', 'apc' ),
		'validate' => array(
			'numeric' => array(
				'param'   => '',
				'message' => __( 'must be numeric value', 'apc' ),
			),
		),
	),
	true
);
$flads_left_banner_code_cond[] = $flads_options_panel->addText(
	'left_banner_code_height',
	array(
		'name'     => __( 'Your custom banner height', 'apc' ),
		'std'      => 600,
		'desc'     => __( 'Enter numeric value of the banner height', 'apc' ),
		'validate' => array(
			'numeric' => array(
				'param'   => '',
				'message' => __( 'must be numeric value', 'apc' ),
			),
		),
	),
	true
);

// Conditional check box for left banner image.
$flads_options_panel->addCondition(
	'left_banner_image_state',
	array(
		'name'   => __( 'Use image banner?', 'apc' ),
		'fields' => $flads_left_banner_image_cond,
		'std'    => false,
	)
);

// Conditional check box for left banner code.
$flads_options_panel->addCondition(
	'left_banner_code_state',
	array(
		'name'   => __( 'Use custom code banner?', 'apc' ),
		'fields' => $flads_left_banner_code_cond,
		'std'    => false,
	)
);
// End of left banner settings panel.
$flads_options_panel->CloseTab();

// Right banner settings panel.
$flads_options_panel->OpenTab( 'right_banner' );
$flads_options_panel->Title( __( 'Right Banner Settings', 'apc' ) );

// Checkbox to check if right banner is active.
$flads_options_panel->addCheckbox(
	'right_banner_active',
	array(
		'name' => __( 'Activate Right Banner Area', 'apc' ),
		'std'  => true,
		'desc' => __( 'Activate this option to display the Right banner area', 'apc' ),
	)
);

// Checkbox to check if right banner is sticky.
$flads_options_panel->addCheckbox(
	'right_banner_sticky',
	array(
		'name' => __( 'Activate Sticky Option', 'apc' ),
		'std'  => false,
		'desc' => __( 'Activate this option to display the banner in a sticky style', 'apc' ),
	)
);

// Right banner image condition fields.
$flads_right_banner_image_cond   = array();
$flads_right_banner_image_cond[] = $flads_options_panel->addImage(
	'right_banner_image',
	array( 'name' => __( 'Upload banner image', 'apc' ) ),
	true
);
$flads_right_banner_image_cond[] = $flads_options_panel->addText(
	'right_banner_image_link',
	array(
		'name' => __( 'Enter your banner image link', 'apc' ),
		'std'  => '#',
		'desc' => __( "Add your banner image link, or keep it set to # in case you don't want to link your image anywhere!", 'apc' ),
	),
	true
);

// Right banner code condition fields.
$flads_right_banner_code_cond   = array();
$flads_right_banner_code_cond[] = $flads_options_panel->addTextarea(
	'right_banner_code',
	array(
		'name' => __( 'Add your banner custom code', 'apc' ),
		'std'  => __( 'You can use any banner code here including Google Adsense code', 'apc' ),
	),
	true
);
$flads_right_banner_code_cond[] = $flads_options_panel->addText(
	'right_banner_code_width',
	array(
		'name'     => __( 'Your custom banner width', 'apc' ),
		'std'      => 160,
		'desc'     => __( 'Enter numeric value of the banner width', 'apc' ),
		'validate' => array(
			'numeric' => array(
				'param'   => '',
				'message' => __( 'must be numeric value', 'apc' ),
			),
		),
	),
	true
);
$flads_right_banner_code_cond[] = $flads_options_panel->addText(
	'right_banner_code_height',
	array(
		'name'     => __( 'Your custom banner height', 'apc' ),
		'std'      => 600,
		'desc'     => __( 'Enter numeric value of the banner height', 'apc' ),
		'validate' => array(
			'numeric' => array(
				'param'   => '',
				'message' => __( 'must be numeric value', 'apc' ),
			),
		),
	),
	true
);

// Conditional check box for right banner image.
$flads_options_panel->addCondition(
	'right_banner_image_state',
	array(
		'name'   => __( 'Use image banner?', 'apc' ),
		'fields' => $flads_right_banner_image_cond,
		'std'    => false,
	)
);

// Conditional check box for right banner code.
$flads_options_panel->addCondition(
	'right_banner_code_state',
	array(
		'name'   => __( 'Use custom code banner?', 'apc' ),
		'fields' => $flads_right_banner_code_cond,
		'std'    => false,
	)
);
// End of right banner settings panel.
$flads_options_panel->CloseTab();

// Mobile banner settings panel.
$flads_options_panel->OpenTab( 'mobile_banner' );
$flads_options_panel->Title( __( 'Mobile Banner Settings', 'apc' ) );

// Checkbox to check if mobile banner is active or not.
$flads_options_panel->addCheckbox(
	'show_mobile_banner',
	array(
		'name' => __( 'Show Footer Banner on Mobile Devices', 'apc' ),
		'std'  => false,
		'desc' => __( 'Activate this option to show footer banner on mobile devices including tablets', 'apc' ),
	)
);

// Mobile banner image condition.
$flads_mobile_banner_image_cond   = array();
$flads_mobile_banner_image_cond[] = $flads_options_panel->addImage(
	'mobile_banner_image',
	array( 'name' => __( 'Upload banner image', 'apc' ) ),
	true
);
$flads_mobile_banner_image_cond[] = $flads_options_panel->addText(
	'mobile_banner_image_link',
	array(
		'name' => __( 'Enter your banner image link', 'apc' ),
		'std'  => '#',
		'desc' => __( "Add your banner image link, or keep it set to # in case you don't want to link your image anywhere!", 'apc' ),
	),
	true
);

// Mobile banner code condition.
$flads_mobile_banner_code_cond   = array();
$flads_mobile_banner_code_cond[] = $flads_options_panel->addTextarea(
	'mobile_banner_code',
	array(
		'name' => __( 'Add your banner custom code', 'apc' ),
		'std'  => __( 'You can use any banner code here including Google Adsense code', 'apc' ),
	),
	true
);
$flads_mobile_banner_code_cond[] = $flads_options_panel->addText(
	'mobile_banner_code_width',
	array(
		'name'     => __( 'Your custom banner width', 'apc' ),
		'std'      => 160,
		'desc'     => __( 'Enter numeric value of the banner width', 'apc' ),
		'validate' => array(
			'numeric' => array(
				'param'   => '',
				'message' => __( 'must be numeric value', 'apc' ),
			),
		),
	),
	true
);
$flads_mobile_banner_code_cond[] = $flads_options_panel->addText(
	'mobile_banner_code_height',
	array(
		'name'     => __( 'Your custom banner height', 'apc' ),
		'std'      => 600,
		'desc'     => __( 'Enter numeric value of the banner height', 'apc' ),
		'validate' => array(
			'numeric' => array(
				'param'   => '',
				'message' => __( 'must be numeric value', 'apc' ),
			),
		),
	),
	true
);

// Conditional check box for mobile banner image.
$flads_options_panel->addCondition(
	'mobile_banner_image_state',
	array(
		'name'   => __( 'Use image banner?', 'apc' ),
		'fields' => $flads_mobile_banner_image_cond,
		'std'    => false,
	)
);

// Conditional check box for mobile banner code.
$flads_options_panel->addCondition(
	'mobile_banner_code_state',
	array(
		'name'   => __( 'Use custom code banner?', 'apc' ),
		'fields' => $flads_mobile_banner_code_cond,
		'std'    => false,
	)
);
// End of mobile banner panel settings.
$flads_options_panel->CloseTab();

/**
 * End of admin panel section.
 */

/**
 * Front-end implementation.
 */

/**
 * Main function to initialize floated ads.
 *
 * Checks if banners are active and hooks in scripts and banner loading if they are.
 *
 * @since 1.0.0
 */
function floated_ads_main() {
	$floated_ads_data = get_option( 'flads_options' );

	if ( ! empty( $floated_ads_data['left_banner_active'] ) || ! empty( $floated_ads_data['right_banner_active'] ) || ! empty( $floated_ads_data['show_mobile_banner'] ) ) {
		// Include the script only if at least any of the banners is active.
		add_action( 'wp_enqueue_scripts', 'floated_ads_load_script' );
		add_action( 'wp_footer', 'floated_ads_load_ads_container' ); // Renamed to avoid direct output.
	}
}
add_action( 'init', 'floated_ads_main' );


/**
 * Enqueues scripts and styles for floated ads and localizes data for JavaScript.
 *
 * @since 1.0.0 Modified in 2.0.0 to use wp_localize_script.
 */
function floated_ads_load_script() {
	wp_enqueue_script( 'floatedads-js', plugins_url( '/js/FloatedAds.js', FLADS_PLUGIN_FILE ), array( 'jquery' ), '2.0.0', true ); // Added version, updated handle
	wp_enqueue_style( 'floatedads-css', plugins_url( '/css/style.css', FLADS_PLUGIN_FILE ), array(), '2.0.0' ); // Added version, updated handle

	$floated_ads_data = get_option( 'flads_options' );
	$localized_data   = array(
		'ajax_url'             => admin_url( 'admin-ajax.php' ),
		'left_banner_nonce'    => wp_create_nonce( 'floatedads_left_banner_nonce' ),
		'right_banner_nonce'   => wp_create_nonce( 'floatedads_right_banner_nonce' ),
		'mobile_banner_nonce'  => wp_create_nonce( 'floatedads_mobile_banner_nonce' ),
		'left_banner_on'       => 0,
		'right_banner_on'      => 0,
		'mobile_banner_on'     => 0,
		'left_banner_url'      => '',
		'left_banner_link'     => '',
		'LeftBannerW'          => 0, // Consider renaming to left_banner_w if JS is also updated.
		'LeftBannerH'          => 0, // Consider renaming to left_banner_h if JS is also updated.
		'left_banner_is_image_js' => 0,
		'left_banner_is_code_js'  => 0,
		'left_banner_custom'   => '',
		'right_banner_url'     => '',
		'right_banner_link'    => '',
		'RightBannerW'         => 0, // Consider renaming to right_banner_w.
		'RightBannerH'         => 0, // Consider renaming to right_banner_h.
		'right_banner_is_image_js' => 0,
		'right_banner_is_code_js'  => 0,
		'right_banner_custom'  => '',
		'mobile_banner_url'    => '',
		'mobile_banner_link'   => '',
		'MobileBannerW'        => 0, // Consider renaming to mobile_banner_w.
		'MobileBannerH'        => 0, // Consider renaming to mobile_banner_h.
		'mobile_banner_is_image' => 0,
		'mobile_banner_is_code'  => 0,
		'mobile_banner_custom' => '',
		'screen_min_width'     => isset( $floated_ads_data['container_min_width'] ) ? absint( $floated_ads_data['container_min_width'] ) : 1000,
		'MainContentW'         => isset( $floated_ads_data['main_content_width'] ) ? absint( $floated_ads_data['main_content_width'] ) : 1220, // Consider renaming to main_content_w.
		'LeftAdjust'           => isset( $floated_ads_data['FloatedAds_margin_left'] ) ? absint( $floated_ads_data['FloatedAds_margin_left'] ) : 0, // Consider renaming to left_adjust.
		'RightAdjust'          => isset( $floated_ads_data['FloatedAds_margin_right'] ) ? absint( $floated_ads_data['FloatedAds_margin_right'] ) : 0, // Consider renaming to right_adjust.
		'TopAdjust'            => isset( $floated_ads_data['FloatedAds_margin_top'] ) ? absint( $floated_ads_data['FloatedAds_margin_top'] ) : 0, // Consider renaming to top_adjust.
		'left_banner_sticky_js'  => ( isset( $floated_ads_data['left_banner_sticky'] ) && '1' === $floated_ads_data['left_banner_sticky'] ) ? 1 : 0,
		'right_banner_sticky_js' => ( isset( $floated_ads_data['right_banner_sticky'] ) && '1' === $floated_ads_data['right_banner_sticky'] ) ? 1 : 0,
		'device_is_mobile'     => wp_is_mobile() ? 1 : 0,
	);

	// Left Banner Data.
	if ( ! empty( $floated_ads_data['left_banner_active'] ) ) {
		$localized_data['left_banner_on'] = 1;
		if ( isset( $floated_ads_data['left_banner_image_state']['enabled'] ) && ! empty( $floated_ads_data['left_banner_image_state']['left_banner_image']['src'] ) ) {
			$localized_data['left_banner_is_image_js'] = 1;
			$localized_data['left_banner_url']         = esc_url_raw( $floated_ads_data['left_banner_image_state']['left_banner_image']['src'] );
			$localized_data['left_banner_link']        = esc_url_raw( $floated_ads_data['left_banner_image_state']['left_banner_image_link'] );
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- getimagesize can throw an error for invalid image.
			list( $width, $height )                    = @getimagesize( $localized_data['left_banner_url'] );
			$localized_data['LeftBannerW']             = $width ? absint( $width ) : 0;
			$localized_data['LeftBannerH']             = $height ? absint( $height ) : 0;
		} elseif ( isset( $floated_ads_data['left_banner_code_state']['enabled'] ) ) {
			$localized_data['left_banner_is_code_js'] = 1;
			$localized_data['left_banner_custom']     = $floated_ads_data['left_banner_code_state']['left_banner_code']; // Already sanitized with wp_kses_post by admin framework on save.
			$localized_data['LeftBannerW']            = absint( $floated_ads_data['left_banner_code_state']['left_banner_code_width'] );
			$localized_data['LeftBannerH']            = absint( $floated_ads_data['left_banner_code_state']['left_banner_code_height'] );
		}
	}

	// Right Banner Data.
	if ( ! empty( $floated_ads_data['right_banner_active'] ) ) {
		$localized_data['right_banner_on'] = 1;
		if ( isset( $floated_ads_data['right_banner_image_state']['enabled'] ) && ! empty( $floated_ads_data['right_banner_image_state']['right_banner_image']['src'] ) ) {
			$localized_data['right_banner_is_image_js'] = 1;
			$localized_data['right_banner_url']         = esc_url_raw( $floated_ads_data['right_banner_image_state']['right_banner_image']['src'] );
			$localized_data['right_banner_link']        = esc_url_raw( $floated_ads_data['right_banner_image_state']['right_banner_image_link'] );
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			list( $width, $height )                     = @getimagesize( $localized_data['right_banner_url'] );
			$localized_data['RightBannerW']             = $width ? absint( $width ) : 0;
			$localized_data['RightBannerH']             = $height ? absint( $height ) : 0;
		} elseif ( isset( $floated_ads_data['right_banner_code_state']['enabled'] ) ) {
			$localized_data['right_banner_is_code_js'] = 1;
			$localized_data['right_banner_custom']     = $floated_ads_data['right_banner_code_state']['right_banner_code']; // Already sanitized.
			$localized_data['RightBannerW']            = absint( $floated_ads_data['right_banner_code_state']['right_banner_code_width'] );
			$localized_data['RightBannerH']            = absint( $floated_ads_data['right_banner_code_state']['right_banner_code_height'] );
		}
	}

	// Mobile Banner Data.
	if ( ! empty( $floated_ads_data['show_mobile_banner'] ) ) {
		$localized_data['mobile_banner_on'] = 1;
		if ( isset( $floated_ads_data['mobile_banner_image_state']['enabled'] ) && ! empty( $floated_ads_data['mobile_banner_image_state']['mobile_banner_image']['src'] ) ) {
			$localized_data['mobile_banner_is_image'] = 1;
			$localized_data['mobile_banner_url']      = esc_url_raw( $floated_ads_data['mobile_banner_image_state']['mobile_banner_image']['src'] );
			$localized_data['mobile_banner_link']     = esc_url_raw( $floated_ads_data['mobile_banner_image_state']['mobile_banner_image_link'] );
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			list( $width, $height )                   = @getimagesize( $localized_data['mobile_banner_url'] );
			$localized_data['MobileBannerW']          = $width ? absint( $width ) : 0;
			$localized_data['MobileBannerH']          = $height ? absint( $height ) : 0;
		} elseif ( isset( $floated_ads_data['mobile_banner_code_state']['enabled'] ) ) {
			$localized_data['mobile_banner_is_code'] = 1;
			$localized_data['mobile_banner_custom']  = $floated_ads_data['mobile_banner_code_state']['mobile_banner_code']; // Already sanitized.
			$localized_data['MobileBannerW']         = absint( $floated_ads_data['mobile_banner_code_state']['mobile_banner_code_width'] );
			$localized_data['MobileBannerH']         = absint( $floated_ads_data['mobile_banner_code_state']['mobile_banner_code_height'] );
		}
	}

	wp_localize_script( 'floatedads-js', 'floatedAdsGlobalData', $localized_data );
}
// No add_action for floated_ads_load_script here, it's hooked in floated_ads_main.


/**
 * Loads the HTML container for banners in the footer.
 *
 * This function is hooked into `wp_footer`. The actual banner content
 * is loaded via AJAX into these containers.
 *
 * @since 2.0.0
 */
function floated_ads_load_ads_container() {
	// $floated_ads_data = get_option( 'flads_options' ); // Data is now passed via JS.
	// This function will now primarily just output the container divs if needed,
	// or can be removed if JS creates the divs dynamically.
	// For now, let's assume JS will create them if they don't exist.
	// If specific empty divs are needed for initial styling or structure, they could be added here.
	// Example:
	// if ( ! empty( $floated_ads_data['left_banner_active'] ) ) {
	// echo '<div id="divAdLeft" style="display:none;"></div>';
	// }
	// if ( ! empty( $floated_ads_data['right_banner_active'] ) ) {
	// echo '<div id="divAdRight" style="display:none;"></div>';
	// }
	// if ( ! empty( $floated_ads_data['show_mobile_banner'] ) && wp_is_mobile() ) {
	// echo '<div id="bottom_banner" style="display:none;"></div>';
	// }
	// However, the current JS implementation appends the AJAX response directly to body,
	// so these containers might not be strictly necessary in PHP beforehand.
	// For cleanliness, if JS handles div creation entirely, this function might become redundant
	// or just serve as a hook placeholder.
}

/**
 * AJAX handler for the left banner.
 *
 * Sanitizes input and includes the left banner template.
 *
 * @since 2.0.0
 */
function floated_ads_handle_left_banner() { // Renamed function
	if ( ! check_ajax_referer( 'floatedads_left_banner_nonce', 'nonce', false ) ) {
		wp_send_json_error( array( 'message' => __( 'Nonce verification failed for left banner.', 'apc' ) ) );
		// wp_die() is called by wp_send_json_error.
	}

	// Retrieve and Sanitize Input.
	// These are passed from JS `floatedAdsGlobalData` which gets them from PHP options.
	// So, they are somewhat pre-sanitized or controlled.
	// $clientwidth_php        = isset( $_POST['clientwidth_php'] ) ? absint( $_POST['clientwidth_php'] ) : 0; // Not directly used in template.
	// $screen_w               = isset( $_POST['screen_w'] ) ? absint( $_POST['screen_w'] ) : 0; // Not directly used in template.
	$left_banner_is_on      = isset( $_POST['left_banner_is_on'] ) ? absint( $_POST['left_banner_is_on'] ) : 0;
	$left_banner_sticky_php = isset( $_POST['left_banner_sticky_php'] ) ? absint( $_POST['left_banner_sticky_php'] ) : 0; // Used by JS, not template.
	$left_banner_w          = isset( $_POST['LeftBannerW'] ) ? absint( $_POST['LeftBannerW'] ) : 0;
	$left_banner_h          = isset( $_POST['LeftBannerH'] ) ? absint( $_POST['LeftBannerH'] ) : 0;

	$left_banner_is_image = isset( $_POST['left_banner_is_image'] ) ? absint( $_POST['left_banner_is_image'] ) : 0;
	$left_banner_is_code  = isset( $_POST['left_banner_is_code'] ) ? absint( $_POST['left_banner_is_code'] ) : 0;

	$left_banner_url    = '';
	$left_banner_link   = '';
	$left_banner_custom = '';

	if ( $left_banner_is_image ) {
		$left_banner_url  = isset( $_POST['left_banner_url'] ) ? esc_url_raw( wp_unslash( $_POST['left_banner_url'] ) ) : '';
		$left_banner_link = isset( $_POST['left_banner_link'] ) ? esc_url_raw( wp_unslash( $_POST['left_banner_link'] ) ) : '';
	} elseif ( $left_banner_is_code ) {
		// Custom code is pre-sanitized with wp_kses_post on save by the admin framework.
		// Here we just ensure it's correctly passed if it's a string.
		$left_banner_custom = isset( $_POST['left_banner_custom'] ) ? wp_kses_post( wp_unslash( $_POST['left_banner_custom'] ) ) : '';
	}

	// Pass Data & Include Banner Template.
	ob_start();
	// Define variables for the template.
	$LeftBannerW = $left_banner_w; // Match template variable name.
	$LeftBannerH = $left_banner_h; // Match template variable name.
	require_once plugin_dir_path( FLADS_PLUGIN_FILE ) . 'includes/left_banner.php';
	$banner_html = ob_get_clean();

	wp_send_json_success( array( 'html' => $banner_html ) );
	// wp_die() is called by wp_send_json_success.
}
add_action( 'wp_ajax_floatedads_left_banner', 'floated_ads_handle_left_banner' );
add_action( 'wp_ajax_nopriv_floatedads_left_banner', 'floated_ads_handle_left_banner' );

/**
 * AJAX handler for the right banner.
 *
 * Sanitizes input and includes the right banner template.
 *
 * @since 2.0.0
 */
function floated_ads_handle_right_banner() { // Renamed function
	if ( ! check_ajax_referer( 'floatedads_right_banner_nonce', 'nonce', false ) ) {
		wp_send_json_error( array( 'message' => __( 'Nonce verification failed for right banner.', 'apc' ) ) );
	}

	// Retrieve and Sanitize Input.
	$right_banner_is_on      = isset( $_POST['right_banner_is_on'] ) ? absint( $_POST['right_banner_is_on'] ) : 0;
	$right_banner_sticky_php = isset( $_POST['right_banner_sticky_php'] ) ? absint( $_POST['right_banner_sticky_php'] ) : 0; // Used by JS.
	$right_banner_w          = isset( $_POST['RightBannerW'] ) ? absint( $_POST['RightBannerW'] ) : 0;
	$right_banner_h          = isset( $_POST['RightBannerH'] ) ? absint( $_POST['RightBannerH'] ) : 0;

	$right_banner_is_image = isset( $_POST['right_banner_is_image'] ) ? absint( $_POST['right_banner_is_image'] ) : 0;
	$right_banner_is_code  = isset( $_POST['right_banner_is_code'] ) ? absint( $_POST['right_banner_is_code'] ) : 0;

	$right_banner_url    = '';
	$right_banner_link   = '';
	$right_banner_custom = '';

	if ( $right_banner_is_image ) {
		$right_banner_url  = isset( $_POST['right_banner_url'] ) ? esc_url_raw( wp_unslash( $_POST['right_banner_url'] ) ) : '';
		$right_banner_link = isset( $_POST['right_banner_link'] ) ? esc_url_raw( wp_unslash( $_POST['right_banner_link'] ) ) : '';
	} elseif ( $right_banner_is_code ) {
		$right_banner_custom = isset( $_POST['right_banner_custom'] ) ? wp_kses_post( wp_unslash( $_POST['right_banner_custom'] ) ) : '';
	}

	// Pass Data & Include Banner Template.
	ob_start();
	$RightBannerW = $right_banner_w; // Match template variable.
	$RightBannerH = $right_banner_h; // Match template variable.
	require_once plugin_dir_path( FLADS_PLUGIN_FILE ) . 'includes/right_banner.php';
	$banner_html = ob_get_clean();

	wp_send_json_success( array( 'html' => $banner_html ) );
}
add_action( 'wp_ajax_floatedads_right_banner', 'floated_ads_handle_right_banner' );
add_action( 'wp_ajax_nopriv_floatedads_right_banner', 'floated_ads_handle_right_banner' );

/**
 * AJAX handler for the mobile banner.
 *
 * Sanitizes input and includes the mobile banner template.
 *
 * @since 2.0.0
 */
function floated_ads_handle_mobile_banner() { // Renamed function
	if ( ! check_ajax_referer( 'floatedads_mobile_banner_nonce', 'nonce', false ) ) {
		wp_send_json_error( array( 'message' => __( 'Nonce verification failed for mobile banner.', 'apc' ) ) );
	}

	// Retrieve and Sanitize Input.
	$mobile_banner_is_on = isset( $_POST['mobile_banner_is_on'] ) ? absint( $_POST['mobile_banner_is_on'] ) : 0;
	$mobile_banner_w     = isset( $_POST['MobileBannerW'] ) ? absint( $_POST['MobileBannerW'] ) : 0;
	$mobile_banner_h     = isset( $_POST['MobileBannerH'] ) ? absint( $_POST['MobileBannerH'] ) : 0;

	$mobile_banner_is_image = isset( $_POST['mobile_banner_is_image'] ) ? absint( $_POST['mobile_banner_is_image'] ) : 0;
	$mobile_banner_is_code  = isset( $_POST['mobile_banner_is_code'] ) ? absint( $_POST['mobile_banner_is_code'] ) : 0;

	$mobile_banner_url    = '';
	$mobile_banner_link   = '';
	$mobile_banner_custom = '';

	if ( $mobile_banner_is_image ) {
		$mobile_banner_url  = isset( $_POST['mobile_banner_url'] ) ? esc_url_raw( wp_unslash( $_POST['mobile_banner_url'] ) ) : '';
		$mobile_banner_link = isset( $_POST['mobile_banner_link'] ) ? esc_url_raw( wp_unslash( $_POST['mobile_banner_link'] ) ) : '';
	} elseif ( $mobile_banner_is_code ) {
		$mobile_banner_custom = isset( $_POST['mobile_banner_custom'] ) ? wp_kses_post( wp_unslash( $_POST['mobile_banner_custom'] ) ) : '';
	}

	// Pass Data & Include Banner Template.
	ob_start();
	$MobileBannerW = $mobile_banner_w; // Match template variable.
	$MobileBannerH = $mobile_banner_h; // Match template variable.
	require_once plugin_dir_path( FLADS_PLUGIN_FILE ) . 'includes/mobile_banner.php';
	$banner_html = ob_get_clean();

	wp_send_json_success( array( 'html' => $banner_html ) );
}
add_action( 'wp_ajax_floatedads_mobile_banner', 'floated_ads_handle_mobile_banner' );
add_action( 'wp_ajax_nopriv_floatedads_mobile_banner', 'floated_ads_handle_mobile_banner' );

/**
 * Conditionally removes actions for banners if 'show_on_homepage' is active
 * and the current page is not the homepage or front page.
 *
 * @since 1.0.0
 */
function floated_ads_show_on_homepage_only() {
	$floated_ads_data = get_option( 'flads_options' );
	// Check if the homepage only option is checked.
	if ( isset( $floated_ads_data['show_on_homepage'] ) && '1' === $floated_ads_data['show_on_homepage'] ) {
		if ( ! is_home() && ! is_front_page() ) {
			// global $post; // $post global is not used here.
			remove_action( 'wp_enqueue_scripts', 'floated_ads_load_script' );
			remove_action( 'wp_footer', 'floated_ads_load_ads_container' );
		}
	}
}
add_action( 'get_header', 'floated_ads_show_on_homepage_only' );

?>