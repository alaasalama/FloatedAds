<?php
/**
 * Plugin Name:     FloatedAds
 * Plugin URI:      https://lab.alaadesign.com
 * Description:     Display floated banner ads on both sides of your website with a modern interface.
 * Author:          Alaa Salama
 * Version:         2.0.0
 * Requires at least: 5.8
 * Tested up to:     6.7
 * Requires PHP:      7.4
 * Author URI:       https://lab.alaadesign.com
 * Text Domain:      floated-ads
 * Domain Path:      /languages
 * License:          GPL v3 or later
 * License URI:      https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package FloatedAds
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'FLADS_VERSION', '2.0.0' );
define( 'FLADS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'FLADS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'FLADS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Main plugin class.
 */
final class FloatedAds {

	/**
	 * Plugin options.
	 *
	 * @var array
	 */
	private static $options = null;

	/**
	 * Option group name.
	 *
	 * @var string
	 */
	const OPTION_GROUP = 'flads_options';

	/**
	 * Init the plugin.
	 */
	public static function init() {
		$plugin = new self();
		$plugin->setup_hooks();
	}

	/**
	 * Setup WordPress hooks.
	 */
	private function setup_hooks() {
		// Admin hooks.
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

		// Frontend hooks.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
		add_action( 'wp_footer', array( $this, 'render_banner_html' ), 100 );

		// REST API.
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );

		// Load textdomain.
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		// Plugin action links.
		add_filter( 'plugin_action_links_' . FLADS_PLUGIN_BASENAME, array( $this, 'add_action_links' ) );
	}

	/**
	 * Load text domain for translations.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'floated-ads', false, dirname( FLADS_PLUGIN_BASENAME ) . '/languages' );
	}

	/**
	 * Add settings link on plugin page.
	 *
	 * @param array $links Existing links.
	 * @return array
	 */
	public function add_action_links( $links ) {
		$settings_link = '<a href="' . admin_url( 'admin.php?page=floated-ads' ) . '">' . esc_html__( 'Settings', 'floated-ads' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	/**
	 * Register the admin menu page.
	 */
	public function add_admin_menu() {
		add_menu_page(
			esc_html__( 'FloatedAds', 'floated-ads' ),
			esc_html__( 'FloatedAds', 'floated-ads' ),
			'manage_options',
			'floated-ads',
			array( $this, 'render_admin_page' ),
			'dashicons-align-wide',
			100
		);
	}

	/**
	 * Register plugin settings with WordPress Settings API.
	 */
	public function register_settings() {
		register_setting(
			self::OPTION_GROUP,
			self::OPTION_GROUP,
			array(
				'sanitize_callback' => array( $this, 'sanitize_options' ),
				'default'           => $this->get_default_options(),
			)
		);

		// General Settings Section.
		add_settings_section(
			'flads_general',
			esc_html__( 'General Settings', 'floated-ads' ),
			'__return_empty_string',
			'floated-ads-general'
		);

		add_settings_field(
			'main_content_width',
			esc_html__( 'Website Container Width', 'floated-ads' ),
			array( $this, 'field_text' ),
			'floated-ads-general',
			'flads_general',
			array(
				'id'          => 'main_content_width',
				'std'         => 1220,
				'description' => esc_html__( 'Your banners will be positioned on the sides of this container width.', 'floated-ads' ),
				'type'        => 'number',
			)
		);

		add_settings_field(
			'container_min_width',
			esc_html__( 'Minimum Window Width', 'floated-ads' ),
			array( $this, 'field_text' ),
			'floated-ads-general',
			'flads_general',
			array(
				'id'          => 'container_min_width',
				'std'         => 1000,
				'description' => esc_html__( 'Hide banners on screens narrower than this value.', 'floated-ads' ),
				'type'        => 'number',
			)
		);

		add_settings_field(
			'FloatedAds_margin_top',
			esc_html__( 'Top Margin (px)', 'floated-ads' ),
			array( $this, 'field_text' ),
			'floated-ads-general',
			'flads_general',
			array(
				'id'          => 'FloatedAds_margin_top',
				'std'         => 0,
				'description' => esc_html__( 'Vertical offset for banner positioning.', 'floated-ads' ),
				'type'        => 'number',
			)
		);

		add_settings_field(
			'FloatedAds_margin_left',
			esc_html__( 'Left Margin (px)', 'floated-ads' ),
			array( $this, 'field_text' ),
			'floated-ads-general',
			'flads_general',
			array(
				'id'          => 'FloatedAds_margin_left',
				'std'         => 0,
				'description' => esc_html__( 'Left offset adjustment for left banner.', 'floated-ads' ),
				'type'        => 'number',
			)
		);

		add_settings_field(
			'FloatedAds_margin_right',
			esc_html__( 'Right Margin (px)', 'floated-ads' ),
			array( $this, 'field_text' ),
			'floated-ads-general',
			'flads_general',
			array(
				'id'          => 'FloatedAds_margin_right',
				'std'         => 0,
				'description' => esc_html__( 'Right offset adjustment for right banner.', 'floated-ads' ),
				'type'        => 'number',
			)
		);

		add_settings_field(
			'show_on_homepage',
			esc_html__( 'Homepage Only', 'floated-ads' ),
			array( $this, 'field_checkbox' ),
			'floated-ads-general',
			'flads_general',
			array(
				'id'          => 'show_on_homepage',
				'description' => esc_html__( 'Only show banners on the homepage.', 'floated-ads' ),
			)
		);
	}

	/**
	 * Get default options.
	 *
	 * @return array
	 */
	private function get_default_options() {
		return array(
			'main_content_width'     => 1220,
			'container_min_width'    => 1000,
			'FloatedAds_margin_top'  => 0,
			'FloatedAds_margin_left' => 0,
			'FloatedAds_margin_right' => 0,
			'show_on_homepage'       => 0,
			'left_banner_active'     => 1,
			'left_banner_sticky'     => 1,
			'left_banner_image_state' => array(),
			'left_banner_code_state'  => array(),
			'right_banner_active'    => 1,
			'right_banner_sticky'    => 0,
			'right_banner_image_state' => array(),
			'right_banner_code_state'  => array(),
			'show_mobile_banner'     => 0,
			'mobile_banner_image_state' => array(),
			'mobile_banner_code_state'  => array(),
		);
	}

	/**
	 * Get plugin option value.
	 *
	 * @param string $key     Option key.
	 * @param mixed  $default Default value.
	 * @return mixed
	 */
	public static function get_option( $key, $default = '' ) {
		if ( null === self::$options ) {
			self::$options = get_option( self::OPTION_GROUP, array() );
		}
		return isset( self::$options[ $key ] ) ? self::$options[ $key ] : $default;
	}

	/**
	 * Sanitize options before saving.
	 *
	 * @param array $input Raw input.
	 * @return array
	 */
	public function sanitize_options( $input ) {
		// Start with existing saved options so we don't lose data on partial saves.
		$existing = get_option( self::OPTION_GROUP, array() );
		$defaults = $this->get_default_options();
		$output   = array_merge( $defaults, $existing );

		if ( ! is_array( $input ) ) {
			return $output;
		}

		// Numeric fields.
		$numeric_fields = array(
			'main_content_width',
			'container_min_width',
			'FloatedAds_margin_top',
			'FloatedAds_margin_left',
			'FloatedAds_margin_right',
		);
		foreach ( $numeric_fields as $field ) {
			$output[ $field ] = isset( $input[ $field ] ) ? absint( $input[ $field ] ) : 0;
		}

		// Checkboxes.
		$checkbox_fields = array(
			'show_on_homepage',
			'left_banner_active',
			'left_banner_sticky',
			'right_banner_active',
			'right_banner_sticky',
			'show_mobile_banner',
		);
		foreach ( $checkbox_fields as $field ) {
			$output[ $field ] = ! empty( $input[ $field ] ) ? 1 : 0;
		}

		/**
		 * Conditional fields (image_state and code_state) have a hidden <input type="hidden" value="0">
		 * before the checkbox. If the checkbox is unchecked, only the hidden 0 is submitted and
		 * the sub-fields (image url, code textarea, etc.) are NOT submitted at all.
		 *
		 * We must check that:
		 *   1. 'enabled' is present AND its value is 'on' (not 0 from the hidden field)
		 *   2. The sub-field data is actually present in the input before overwriting
		 */

		// Left banner image state.
		$left_img_enabled = isset( $input['left_banner_image_state']['enabled'] ) && 'on' === $input['left_banner_image_state']['enabled'];
		if ( $left_img_enabled ) {
			$output['left_banner_image_state']['enabled'] = 'on';
			$output['left_banner_image_state']['left_banner_image'] = array(
				'id'  => isset( $input['left_banner_image_state']['left_banner_image']['id'] ) ? absint( $input['left_banner_image_state']['left_banner_image']['id'] ) : '',
				'src' => isset( $input['left_banner_image_state']['left_banner_image']['src'] ) ? esc_url_raw( $input['left_banner_image_state']['left_banner_image']['src'] ) : '',
			);
			$output['left_banner_image_state']['left_banner_image_link'] = isset( $input['left_banner_image_state']['left_banner_image_link'] ) ? esc_url_raw( $input['left_banner_image_state']['left_banner_image_link'] ) : '#';
		} elseif ( isset( $input['left_banner_image_state']['enabled'] ) && '0' === $input['left_banner_image_state']['enabled'] ) {
			// Explicitly disabled – remove the enabled flag but keep nested data intact.
			unset( $output['left_banner_image_state']['enabled'] );
		}

		// Left banner code state.
		$left_code_enabled = isset( $input['left_banner_code_state']['enabled'] ) && 'on' === $input['left_banner_code_state']['enabled'];
		if ( $left_code_enabled ) {
			$output['left_banner_code_state']['enabled'] = 'on';
			$output['left_banner_code_state']['left_banner_code'] = isset( $input['left_banner_code_state']['left_banner_code'] ) ? wp_kses_post( stripslashes( $input['left_banner_code_state']['left_banner_code'] ) ) : '';
			$output['left_banner_code_state']['left_banner_code_width'] = isset( $input['left_banner_code_state']['left_banner_code_width'] ) ? absint( $input['left_banner_code_state']['left_banner_code_width'] ) : 160;
			$output['left_banner_code_state']['left_banner_code_height'] = isset( $input['left_banner_code_state']['left_banner_code_height'] ) ? absint( $input['left_banner_code_state']['left_banner_code_height'] ) : 600;
		} elseif ( isset( $input['left_banner_code_state']['enabled'] ) && '0' === $input['left_banner_code_state']['enabled'] ) {
			unset( $output['left_banner_code_state']['enabled'] );
		}

		// Right banner image state.
		$right_img_enabled = isset( $input['right_banner_image_state']['enabled'] ) && 'on' === $input['right_banner_image_state']['enabled'];
		if ( $right_img_enabled ) {
			$output['right_banner_image_state']['enabled'] = 'on';
			$output['right_banner_image_state']['right_banner_image'] = array(
				'id'  => isset( $input['right_banner_image_state']['right_banner_image']['id'] ) ? absint( $input['right_banner_image_state']['right_banner_image']['id'] ) : '',
				'src' => isset( $input['right_banner_image_state']['right_banner_image']['src'] ) ? esc_url_raw( $input['right_banner_image_state']['right_banner_image']['src'] ) : '',
			);
			$output['right_banner_image_state']['right_banner_image_link'] = isset( $input['right_banner_image_state']['right_banner_image_link'] ) ? esc_url_raw( $input['right_banner_image_state']['right_banner_image_link'] ) : '#';
		} elseif ( isset( $input['right_banner_image_state']['enabled'] ) && '0' === $input['right_banner_image_state']['enabled'] ) {
			unset( $output['right_banner_image_state']['enabled'] );
		}

		// Right banner code state.
		$right_code_enabled = isset( $input['right_banner_code_state']['enabled'] ) && 'on' === $input['right_banner_code_state']['enabled'];
		if ( $right_code_enabled ) {
			$output['right_banner_code_state']['enabled'] = 'on';
			$output['right_banner_code_state']['right_banner_code'] = isset( $input['right_banner_code_state']['right_banner_code'] ) ? wp_kses_post( stripslashes( $input['right_banner_code_state']['right_banner_code'] ) ) : '';
			$output['right_banner_code_state']['right_banner_code_width'] = isset( $input['right_banner_code_state']['right_banner_code_width'] ) ? absint( $input['right_banner_code_state']['right_banner_code_width'] ) : 160;
			$output['right_banner_code_state']['right_banner_code_height'] = isset( $input['right_banner_code_state']['right_banner_code_height'] ) ? absint( $input['right_banner_code_state']['right_banner_code_height'] ) : 600;
		} elseif ( isset( $input['right_banner_code_state']['enabled'] ) && '0' === $input['right_banner_code_state']['enabled'] ) {
			unset( $output['right_banner_code_state']['enabled'] );
		}

		// Mobile banner image state.
		$mobile_img_enabled = isset( $input['mobile_banner_image_state']['enabled'] ) && 'on' === $input['mobile_banner_image_state']['enabled'];
		if ( $mobile_img_enabled ) {
			$output['mobile_banner_image_state']['enabled'] = 'on';
			$output['mobile_banner_image_state']['mobile_banner_image'] = array(
				'id'  => isset( $input['mobile_banner_image_state']['mobile_banner_image']['id'] ) ? absint( $input['mobile_banner_image_state']['mobile_banner_image']['id'] ) : '',
				'src' => isset( $input['mobile_banner_image_state']['mobile_banner_image']['src'] ) ? esc_url_raw( $input['mobile_banner_image_state']['mobile_banner_image']['src'] ) : '',
			);
			$output['mobile_banner_image_state']['mobile_banner_image_link'] = isset( $input['mobile_banner_image_state']['mobile_banner_image_link'] ) ? esc_url_raw( $input['mobile_banner_image_state']['mobile_banner_image_link'] ) : '#';
		} elseif ( isset( $input['mobile_banner_image_state']['enabled'] ) && '0' === $input['mobile_banner_image_state']['enabled'] ) {
			unset( $output['mobile_banner_image_state']['enabled'] );
		}

		// Mobile banner code state.
		$mobile_code_enabled = isset( $input['mobile_banner_code_state']['enabled'] ) && 'on' === $input['mobile_banner_code_state']['enabled'];
		if ( $mobile_code_enabled ) {
			$output['mobile_banner_code_state']['enabled'] = 'on';
			$output['mobile_banner_code_state']['mobile_banner_code'] = isset( $input['mobile_banner_code_state']['mobile_banner_code'] ) ? wp_kses_post( stripslashes( $input['mobile_banner_code_state']['mobile_banner_code'] ) ) : '';
			$output['mobile_banner_code_state']['mobile_banner_code_width'] = isset( $input['mobile_banner_code_state']['mobile_banner_code_width'] ) ? absint( $input['mobile_banner_code_state']['mobile_banner_code_width'] ) : 160;
			$output['mobile_banner_code_state']['mobile_banner_code_height'] = isset( $input['mobile_banner_code_state']['mobile_banner_code_height'] ) ? absint( $input['mobile_banner_code_state']['mobile_banner_code_height'] ) : 600;
		} elseif ( isset( $input['mobile_banner_code_state']['enabled'] ) && '0' === $input['mobile_banner_code_state']['enabled'] ) {
			unset( $output['mobile_banner_code_state']['enabled'] );
		}

		return $output;
	}

	/**
	 * Render a text/number field.
	 *
	 * @param array $args Field arguments.
	 */
	public function field_text( $args ) {
		$value = self::get_option( $args['id'], $args['std'] );
		$type  = isset( $args['type'] ) ? $args['type'] : 'text';
		?>
		<input type="<?php echo esc_attr( $type ); ?>" 
			name="<?php echo esc_attr( self::OPTION_GROUP . '[' . $args['id'] . ']' ); ?>" 
			id="<?php echo esc_attr( $args['id'] ); ?>" 
			value="<?php echo esc_attr( $value ); ?>" 
			class="regular-text flads-input" 
			min="0" />
		<?php if ( ! empty( $args['description'] ) ) : ?>
			<p class="description flads-description"><?php echo wp_kses_post( $args['description'] ); ?></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render a checkbox field.
	 *
	 * @param array $args Field arguments.
	 */
	public function field_checkbox( $args ) {
		$value = self::get_option( $args['id'], 0 );
		?>
		<label class="flads-toggle">
			<input type="hidden" name="<?php echo esc_attr( self::OPTION_GROUP . '[' . $args['id'] . ']' ); ?>" value="0" />
			<input type="checkbox" 
				name="<?php echo esc_attr( self::OPTION_GROUP . '[' . $args['id'] . ']' ); ?>" 
				id="<?php echo esc_attr( $args['id'] ); ?>" 
				value="1" 
				<?php checked( $value, 1 ); ?> 
				class="flads-checkbox" />
			<span class="flads-toggle-slider"></span>
		</label>
		<?php if ( ! empty( $args['description'] ) ) : ?>
			<p class="description flads-description"><?php echo wp_kses_post( $args['description'] ); ?></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render the image upload field with conditional visibility.
	 *
	 * @param array $args Field arguments.
	 */
	public function field_image_upload( $args ) {
		$option = self::get_option( $args['id'], array() );

		// The image data is stored as a nested array: [ 'id' => ..., 'src' => ... ]
		// under the img_src_key (e.g., 'left_banner_image').
		$image_data = isset( $option[ $args['img_src_key'] ] ) ? $option[ $args['img_src_key'] ] : array();
		if ( is_array( $image_data ) ) {
			$src    = isset( $image_data['src'] ) ? $image_data['src'] : '';
			$img_id = isset( $image_data['id'] ) ? $image_data['id'] : '';
		} else {
			$src    = $image_data;
			$img_id = isset( $option[ $args['img_id_key'] ] ) ? $option[ $args['img_id_key'] ] : '';
		}
		$has_image = ! empty( $src );
		?>
		<div class="flads-image-upload-wrapper">
			<div class="flads-image-preview" id="<?php echo esc_attr( $args['id'] ); ?>_preview" style="<?php echo $has_image ? '' : 'display:none;'; ?>">
				<img src="<?php echo $has_image ? esc_url( $src ) : ''; ?>" alt="" style="max-width:200px;max-height:200px;" />
				<button type="button" class="button flads-remove-image" data-field="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Remove', 'floated-ads' ); ?></button>
			</div>
			<div class="flads-image-upload" id="<?php echo esc_attr( $args['id'] ); ?>_upload" style="<?php echo $has_image ? 'display:none;' : ''; ?>">
				<button type="button" class="button flads-upload-image" data-field="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Upload Image', 'floated-ads' ); ?></button>
			</div>
			<input type="hidden" 
				name="<?php echo esc_attr( self::OPTION_GROUP . '[' . $args['id'] . '][' . $args['img_src_key'] . ']' ); ?>" 
				id="<?php echo esc_attr( $args['id'] . '_' . $args['img_src_key'] ); ?>" 
				value="<?php echo esc_attr( $src ); ?>" />
			<input type="hidden" 
				name="<?php echo esc_attr( self::OPTION_GROUP . '[' . $args['id'] . '][' . $args['img_id_key'] . ']' ); ?>" 
				id="<?php echo esc_attr( $args['id'] . '_' . $args['img_id_key'] ); ?>" 
				value="<?php echo esc_attr( $img_id ); ?>" />
		</div>
		<?php
	}

	/**
	 * Render the conditional toggle field (checkbox + sub-fields).
	 *
	 * @param array $args Field arguments.
	 */
	public function field_conditional( $args ) {
		$option = self::get_option( $args['id'], array() );
		$enabled = isset( $option['enabled'] ) && 'on' === $option['enabled'];
		?>
		<div class="flads-conditional-wrapper">
			<label class="flads-toggle flads-conditional-toggle">
				<input type="hidden" name="<?php echo esc_attr( self::OPTION_GROUP . '[' . $args['id'] . '][enabled]' ); ?>" value="0" />
				<input type="checkbox" 
					name="<?php echo esc_attr( self::OPTION_GROUP . '[' . $args['id'] . '][enabled]' ); ?>" 
					class="flads-conditional-checkbox flads-checkbox" 
					value="on" 
					<?php checked( $enabled, true ); ?> />
				<span class="flads-toggle-slider"></span>
				<span class="flads-toggle-label"><?php echo esc_html( $args['name'] ); ?></span>
			</label>

			<div class="flads-conditional-fields" id="<?php echo esc_attr( $args['id'] ); ?>_fields" style="<?php echo $enabled ? '' : 'display:none;'; ?>">
				<?php foreach ( $args['sub_fields'] as $sub_field ) : ?>
					<div class="flads-conditional-field">
						<?php if ( 'image' === $sub_field['type'] ) : ?>
							<label><?php echo esc_html( $sub_field['name'] ); ?></label>
							<?php
							$this->field_image_upload( array(
								'id'         => $args['id'],
								'img_src_key' => $sub_field['img_src_key'],
								'img_id_key'  => $sub_field['img_id_key'],
							) );
							?>
						<?php elseif ( 'text' === $sub_field['type'] ) : ?>
							<label for="<?php echo esc_attr( $args['id'] . '_' . $sub_field['id'] ); ?>"><?php echo esc_html( $sub_field['name'] ); ?></label>
							<?php
							$sub_value = isset( $option[ $sub_field['id'] ] ) ? $option[ $sub_field['id'] ] : ( isset( $sub_field['std'] ) ? $sub_field['std'] : '' );
							?>
							<input type="text" 
								name="<?php echo esc_attr( self::OPTION_GROUP . '[' . $args['id'] . '][' . $sub_field['id'] . ']' ); ?>" 
								id="<?php echo esc_attr( $args['id'] . '_' . $sub_field['id'] ); ?>" 
								value="<?php echo esc_attr( $sub_value ); ?>" 
								class="regular-text" />
							<?php if ( ! empty( $sub_field['desc'] ) ) : ?>
								<p class="description"><?php echo wp_kses_post( $sub_field['desc'] ); ?></p>
							<?php endif; ?>
						<?php elseif ( 'number' === $sub_field['type'] ) : ?>
							<label for="<?php echo esc_attr( $args['id'] . '_' . $sub_field['id'] ); ?>"><?php echo esc_html( $sub_field['name'] ); ?></label>
							<?php
							$sub_value = isset( $option[ $sub_field['id'] ] ) ? $option[ $sub_field['id'] ] : ( isset( $sub_field['std'] ) ? $sub_field['std'] : '' );
							?>
							<input type="number" 
								name="<?php echo esc_attr( self::OPTION_GROUP . '[' . $args['id'] . '][' . $sub_field['id'] . ']' ); ?>" 
								id="<?php echo esc_attr( $args['id'] . '_' . $sub_field['id'] ); ?>" 
								value="<?php echo esc_attr( $sub_value ); ?>" 
								class="small-text" min="0" />
						<?php elseif ( 'textarea' === $sub_field['type'] ) : ?>
							<label for="<?php echo esc_attr( $args['id'] . '_' . $sub_field['id'] ); ?>"><?php echo esc_html( $sub_field['name'] ); ?></label>
							<?php
							$sub_value = isset( $option[ $sub_field['id'] ] ) ? $option[ $sub_field['id'] ] : ( isset( $sub_field['std'] ) ? $sub_field['std'] : '' );
							?>
							<textarea 
								name="<?php echo esc_attr( self::OPTION_GROUP . '[' . $args['id'] . '][' . $sub_field['id'] . ']' ); ?>" 
								id="<?php echo esc_attr( $args['id'] . '_' . $sub_field['id'] ); ?>" 
								class="large-text flads-code-textarea" 
								rows="5"><?php echo esc_textarea( $sub_value ); ?></textarea>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the admin settings page.
	 */
	public function render_admin_page() {
		?>
		<div class="wrap flads-wrap">
			<div class="flads-header">
				<h1 class="flads-page-title">
					<span class="dashicons dashicons-align-wide flads-icon"></span>
					<?php esc_html_e( 'FloatedAds', 'floated-ads' ); ?>
					<span class="flads-version">v<?php echo esc_html( FLADS_VERSION ); ?></span>
				</h1>
				<p class="flads-subtitle"><?php esc_html_e( 'Configure your floated banner ads.', 'floated-ads' ); ?></p>
			</div>

			<div class="flads-notices">
				<?php settings_errors( self::OPTION_GROUP ); ?>
			</div>

			<div class="flads-body">
				<nav class="flads-tabs" id="flads-tab-nav">
					<button type="button" class="flads-tab active" data-tab="general">
						<span class="dashicons dashicons-admin-generic"></span>
						<?php esc_html_e( 'General', 'floated-ads' ); ?>
					</button>
					<button type="button" class="flads-tab" data-tab="left_banner">
						<span class="dashicons dashicons-align-pull-left"></span>
						<?php esc_html_e( 'Left Banner', 'floated-ads' ); ?>
					</button>
					<button type="button" class="flads-tab" data-tab="right_banner">
						<span class="dashicons dashicons-align-pull-right"></span>
						<?php esc_html_e( 'Right Banner', 'floated-ads' ); ?>
					</button>
					<button type="button" class="flads-tab" data-tab="mobile_banner">
						<span class="dashicons dashicons-smartphone"></span>
						<?php esc_html_e( 'Mobile Banner', 'floated-ads' ); ?>
					</button>
				</nav>

				<div class="flads-content">
					<form method="post" action="options.php" class="flads-form">
						<?php
						settings_fields( self::OPTION_GROUP );
						// Render ALL tabs so all fields are submitted together.
						// Only the active tab is shown via JS/CSS.
						$this->render_all_tabs();
						submit_button( esc_html__( 'Save Settings', 'floated-ads' ), 'primary flads-submit' );
						?>
					</form>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render ALL tab contents (rendered in DOM, hidden via JS/CSS).
	 */
	private function render_all_tabs() {
		$tabs = array(
			'general'       => esc_html__( 'General', 'floated-ads' ),
			'left_banner'   => esc_html__( 'Left Banner', 'floated-ads' ),
			'right_banner'  => esc_html__( 'Right Banner', 'floated-ads' ),
			'mobile_banner' => esc_html__( 'Mobile Banner', 'floated-ads' ),
		);

		$first = true;
		foreach ( $tabs as $key => $label ) {
			$style = $first ? '' : 'display:none;';
			$first = false;
			?>
			<div class="flads-tab-panel" id="flads-tab-<?php echo esc_attr( $key ); ?>" style="<?php echo esc_attr( $style ); ?>">
				<table class="form-table flads-table">
					<tbody>
						<?php
						switch ( $key ) {
							case 'general':
								$this->render_general_tab();
								break;
							case 'left_banner':
								$this->render_left_banner_tab();
								break;
							case 'right_banner':
								$this->render_right_banner_tab();
								break;
							case 'mobile_banner':
								$this->render_mobile_banner_tab();
								break;
						}
						?>
					</tbody>
				</table>
			</div>
			<?php
		}
	}

	/**
	 * Render tab content based on active tab.
	 *
	 * @param string $tab Active tab.
	 */
	private function render_tab_content( $tab ) {
		?>
		<table class="form-table flads-table">
			<tbody>
				<?php
				switch ( $tab ) {
					case 'general':
						$this->render_general_tab();
						break;
					case 'left_banner':
						$this->render_left_banner_tab();
						break;
					case 'right_banner':
						$this->render_right_banner_tab();
						break;
					case 'mobile_banner':
						$this->render_mobile_banner_tab();
						break;
				}
				?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Render general settings tab.
	 */
	private function render_general_tab() {
		do_settings_fields( 'floated-ads-general', 'flads_general' );
	}

	/**
	 * Render left banner tab.
	 */
	private function render_left_banner_tab() {
		?>
		<tr>
			<th scope="row"><?php esc_html_e( 'Active', 'floated-ads' ); ?></th>
			<td>
				<?php
				$this->field_checkbox( array(
					'id'          => 'left_banner_active',
					'description' => esc_html__( 'Show the left banner area.', 'floated-ads' ),
				) );
				?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Sticky', 'floated-ads' ); ?></th>
			<td>
				<?php
				$this->field_checkbox( array(
					'id'          => 'left_banner_sticky',
					'description' => esc_html__( 'Keep the banner fixed in position while scrolling.', 'floated-ads' ),
				) );
				?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Image Banner', 'floated-ads' ); ?></th>
			<td>
				<?php
				$this->field_conditional( array(
					'id'   => 'left_banner_image_state',
					'name' => esc_html__( 'Use image banner?', 'floated-ads' ),
					'sub_fields' => array(
						array(
							'type'        => 'image',
							'name'        => esc_html__( 'Upload Banner Image', 'floated-ads' ),
							'img_src_key' => 'left_banner_image',
							'img_id_key'  => 'left_banner_image',
						),
						array(
							'type' => 'text',
							'id'   => 'left_banner_image_link',
							'name' => esc_html__( 'Banner Link URL', 'floated-ads' ),
							'std'  => '#',
							'desc' => esc_html__( 'Leave as # if no link is needed.', 'floated-ads' ),
						),
					),
				) );
				?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Custom Code', 'floated-ads' ); ?></th>
			<td>
				<?php
				$this->field_conditional( array(
					'id'   => 'left_banner_code_state',
					'name' => esc_html__( 'Use custom code banner?', 'floated-ads' ),
					'sub_fields' => array(
						array(
							'type' => 'textarea',
							'id'   => 'left_banner_code',
							'name' => esc_html__( 'Banner Code', 'floated-ads' ),
							'std'  => esc_html__( 'You can use any banner code here including Google AdSense code.', 'floated-ads' ),
						),
						array(
							'type' => 'number',
							'id'   => 'left_banner_code_width',
							'name' => esc_html__( 'Banner Width (px)', 'floated-ads' ),
							'std'  => 160,
						),
						array(
							'type' => 'number',
							'id'   => 'left_banner_code_height',
							'name' => esc_html__( 'Banner Height (px)', 'floated-ads' ),
							'std'  => 600,
						),
					),
				) );
				?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Render right banner tab.
	 */
	private function render_right_banner_tab() {
		?>
		<tr>
			<th scope="row"><?php esc_html_e( 'Active', 'floated-ads' ); ?></th>
			<td>
				<?php
				$this->field_checkbox( array(
					'id'          => 'right_banner_active',
					'description' => esc_html__( 'Show the right banner area.', 'floated-ads' ),
				) );
				?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Sticky', 'floated-ads' ); ?></th>
			<td>
				<?php
				$this->field_checkbox( array(
					'id'          => 'right_banner_sticky',
					'description' => esc_html__( 'Keep the banner fixed in position while scrolling.', 'floated-ads' ),
				) );
				?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Image Banner', 'floated-ads' ); ?></th>
			<td>
				<?php
				$this->field_conditional( array(
					'id'   => 'right_banner_image_state',
					'name' => esc_html__( 'Use image banner?', 'floated-ads' ),
					'sub_fields' => array(
						array(
							'type'        => 'image',
							'name'        => esc_html__( 'Upload Banner Image', 'floated-ads' ),
							'img_src_key' => 'right_banner_image',
							'img_id_key'  => 'right_banner_image',
						),
						array(
							'type' => 'text',
							'id'   => 'right_banner_image_link',
							'name' => esc_html__( 'Banner Link URL', 'floated-ads' ),
							'std'  => '#',
							'desc' => esc_html__( 'Leave as # if no link is needed.', 'floated-ads' ),
						),
					),
				) );
				?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Custom Code', 'floated-ads' ); ?></th>
			<td>
				<?php
				$this->field_conditional( array(
					'id'   => 'right_banner_code_state',
					'name' => esc_html__( 'Use custom code banner?', 'floated-ads' ),
					'sub_fields' => array(
						array(
							'type' => 'textarea',
							'id'   => 'right_banner_code',
							'name' => esc_html__( 'Banner Code', 'floated-ads' ),
							'std'  => esc_html__( 'You can use any banner code here including Google AdSense code.', 'floated-ads' ),
						),
						array(
							'type' => 'number',
							'id'   => 'right_banner_code_width',
							'name' => esc_html__( 'Banner Width (px)', 'floated-ads' ),
							'std'  => 160,
						),
						array(
							'type' => 'number',
							'id'   => 'right_banner_code_height',
							'name' => esc_html__( 'Banner Height (px)', 'floated-ads' ),
							'std'  => 600,
						),
					),
				) );
				?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Render mobile banner tab.
	 */
	private function render_mobile_banner_tab() {
		?>
		<tr>
			<th scope="row"><?php esc_html_e( 'Active', 'floated-ads' ); ?></th>
			<td>
				<?php
				$this->field_checkbox( array(
					'id'          => 'show_mobile_banner',
					'description' => esc_html__( 'Show footer banner on mobile devices and tablets.', 'floated-ads' ),
				) );
				?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Image Banner', 'floated-ads' ); ?></th>
			<td>
				<?php
				$this->field_conditional( array(
					'id'   => 'mobile_banner_image_state',
					'name' => esc_html__( 'Use image banner?', 'floated-ads' ),
					'sub_fields' => array(
						array(
							'type'        => 'image',
							'name'        => esc_html__( 'Upload Banner Image', 'floated-ads' ),
							'img_src_key' => 'mobile_banner_image',
							'img_id_key'  => 'mobile_banner_image',
						),
						array(
							'type' => 'text',
							'id'   => 'mobile_banner_image_link',
							'name' => esc_html__( 'Banner Link URL', 'floated-ads' ),
							'std'  => '#',
							'desc' => esc_html__( 'Leave as # if no link is needed.', 'floated-ads' ),
						),
					),
				) );
				?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Custom Code', 'floated-ads' ); ?></th>
			<td>
				<?php
				$this->field_conditional( array(
					'id'   => 'mobile_banner_code_state',
					'name' => esc_html__( 'Use custom code banner?', 'floated-ads' ),
					'sub_fields' => array(
						array(
							'type' => 'textarea',
							'id'   => 'mobile_banner_code',
							'name' => esc_html__( 'Banner Code', 'floated-ads' ),
							'std'  => esc_html__( 'You can use any banner code here including Google AdSense code.', 'floated-ads' ),
						),
						array(
							'type' => 'number',
							'id'   => 'mobile_banner_code_width',
							'name' => esc_html__( 'Banner Width (px)', 'floated-ads' ),
							'std'  => 160,
						),
						array(
							'type' => 'number',
							'id'   => 'mobile_banner_code_height',
							'name' => esc_html__( 'Banner Height (px)', 'floated-ads' ),
							'std'  => 600,
						),
					),
				) );
				?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_admin_assets( $hook ) {
		if ( 'toplevel_page_floated-ads' !== $hook ) {
			return;
		}

		// Enqueue WordPress media scripts.
		wp_enqueue_media();
		wp_enqueue_editor();

		// Enqueue styles.
		wp_enqueue_style(
			'flads-admin',
			FLADS_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			FLADS_VERSION
		);

		// Enqueue scripts.
		wp_enqueue_script(
			'flads-admin',
			FLADS_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery', 'wp-i18n' ),
			FLADS_VERSION,
			true
		);

		wp_localize_script(
			'flads-admin',
			'fladsAdmin',
			array(
				'mediaTitle'   => esc_html__( 'Select Banner Image', 'floated-ads' ),
				'mediaButton'  => esc_html__( 'Use as Banner', 'floated-ads' ),
				'removeText'   => esc_html__( 'Remove', 'floated-ads' ),
				'uploadText'   => esc_html__( 'Upload Image', 'floated-ads' ),
				'ajax_url'     => admin_url( 'admin-ajax.php' ),
				'nonce'        => wp_create_nonce( 'flads_media_nonce' ),
			)
		);
	}

	/**
	 * Enqueue frontend assets.
	 */
	public function enqueue_frontend_assets() {
		$options = self::get_options();

		// Check if homepage only.
		if ( ! empty( $options['show_on_homepage'] ) && ! is_front_page() && ! is_home() ) {
			return;
		}

		$left_active  = ! empty( $options['left_banner_active'] );
		$right_active = ! empty( $options['right_banner_active'] );
		$mobile_active = ! empty( $options['show_mobile_banner'] );

		if ( ! $left_active && ! $right_active && ! $mobile_active ) {
			return;
		}

		// Enqueue frontend styles.
		wp_enqueue_style(
			'flads-frontend',
			FLADS_PLUGIN_URL . 'assets/css/frontend.css',
			array(),
			FLADS_VERSION
		);

		// Enqueue frontend script.
		wp_enqueue_script(
			'flads-frontend',
			FLADS_PLUGIN_URL . 'assets/js/frontend.js',
			array(),
			FLADS_VERSION,
			true
		);

		// Pass data to the frontend script.
		wp_localize_script(
			'flads-frontend',
			'fladsData',
			$this->get_frontend_js_data()
		);
	}

	/**
	 * Get all options.
	 *
	 * @return array
	 */
	private static function get_options() {
		if ( null === self::$options ) {
			self::$options = get_option( self::OPTION_GROUP, array() );
		}
		return self::$options;
	}

	/**
	 * Get frontend JS data.
	 *
	 * @return array
	 */
	private function get_frontend_js_data() {
		$options = self::get_options();
		$data    = array(
			'restUrl'          => rest_url( 'floated-ads/v1/banners' ),
			'nonce'            => wp_create_nonce( 'wp_rest' ),
			'mainContentWidth' => isset( $options['main_content_width'] ) ? absint( $options['main_content_width'] ) : 1220,
			'minScreenWidth'   => isset( $options['container_min_width'] ) ? absint( $options['container_min_width'] ) : 1000,
			'marginTop'        => isset( $options['FloatedAds_margin_top'] ) ? absint( $options['FloatedAds_margin_top'] ) : 0,
			'marginLeft'       => isset( $options['FloatedAds_margin_left'] ) ? absint( $options['FloatedAds_margin_left'] ) : 0,
			'marginRight'      => isset( $options['FloatedAds_margin_right'] ) ? absint( $options['FloatedAds_margin_right'] ) : 0,
			'isMobile'         => wp_is_mobile() ? 1 : 0,
			'banners'          => $this->get_banners_config(),
		);

		return $data;
	}

	/**
	 * Get banners configuration for frontend.
	 *
	 * @return array
	 */
	private function get_banners_config() {
		$options = self::get_options();
		$banners = array();

		// Left banner.
		if ( ! empty( $options['left_banner_active'] ) ) {
			$left_image_enabled = isset( $options['left_banner_image_state']['enabled'] ) && 'on' === $options['left_banner_image_state']['enabled'];
			$left_code_enabled  = isset( $options['left_banner_code_state']['enabled'] ) && 'on' === $options['left_banner_code_state']['enabled'];

			$left = array(
				'position' => 'left',
				'active'   => true,
				'sticky'   => ! empty( $options['left_banner_sticky'] ),
			);

			if ( $left_image_enabled && ! empty( $options['left_banner_image_state']['left_banner_image']['src'] ) ) {
				$left['type'] = 'image';
				$left['src']  = esc_url( $options['left_banner_image_state']['left_banner_image']['src'] );
				$left['link'] = esc_url( $options['left_banner_image_state']['left_banner_image_link'] );
				$attachment_id = isset( $options['left_banner_image_state']['left_banner_image']['id'] ) ? absint( $options['left_banner_image_state']['left_banner_image']['id'] ) : 0;
				if ( $attachment_id ) {
					$image_data = wp_get_attachment_image_src( $attachment_id, 'full' );
					if ( $image_data ) {
						$left['width']  = $image_data[1];
						$left['height'] = $image_data[2];
					}
				}
			} elseif ( $left_code_enabled ) {
				$left['type']   = 'code';
				$left['code']   = isset( $options['left_banner_code_state']['left_banner_code'] ) ? $options['left_banner_code_state']['left_banner_code'] : '';
				$left['width']  = isset( $options['left_banner_code_state']['left_banner_code_width'] ) ? absint( $options['left_banner_code_state']['left_banner_code_width'] ) : 160;
				$left['height'] = isset( $options['left_banner_code_state']['left_banner_code_height'] ) ? absint( $options['left_banner_code_state']['left_banner_code_height'] ) : 600;
			}

			$banners['left'] = $left;
		}

		// Right banner.
		if ( ! empty( $options['right_banner_active'] ) ) {
			$right_image_enabled = isset( $options['right_banner_image_state']['enabled'] ) && 'on' === $options['right_banner_image_state']['enabled'];
			$right_code_enabled  = isset( $options['right_banner_code_state']['enabled'] ) && 'on' === $options['right_banner_code_state']['enabled'];

			$right = array(
				'position' => 'right',
				'active'   => true,
				'sticky'   => ! empty( $options['right_banner_sticky'] ),
			);

			if ( $right_image_enabled && ! empty( $options['right_banner_image_state']['right_banner_image']['src'] ) ) {
				$right['type'] = 'image';
				$right['src']  = esc_url( $options['right_banner_image_state']['right_banner_image']['src'] );
				$right['link'] = esc_url( $options['right_banner_image_state']['right_banner_image_link'] );
				$attachment_id = isset( $options['right_banner_image_state']['right_banner_image']['id'] ) ? absint( $options['right_banner_image_state']['right_banner_image']['id'] ) : 0;
				if ( $attachment_id ) {
					$image_data = wp_get_attachment_image_src( $attachment_id, 'full' );
					if ( $image_data ) {
						$right['width']  = $image_data[1];
						$right['height'] = $image_data[2];
					}
				}
			} elseif ( $right_code_enabled ) {
				$right['type']   = 'code';
				$right['code']   = isset( $options['right_banner_code_state']['right_banner_code'] ) ? $options['right_banner_code_state']['right_banner_code'] : '';
				$right['width']  = isset( $options['right_banner_code_state']['right_banner_code_width'] ) ? absint( $options['right_banner_code_state']['right_banner_code_width'] ) : 160;
				$right['height'] = isset( $options['right_banner_code_state']['right_banner_code_height'] ) ? absint( $options['right_banner_code_state']['right_banner_code_height'] ) : 600;
			}

			$banners['right'] = $right;
		}

		// Mobile banner.
		if ( ! empty( $options['show_mobile_banner'] ) ) {
			$mobile_image_enabled = isset( $options['mobile_banner_image_state']['enabled'] ) && 'on' === $options['mobile_banner_image_state']['enabled'];
			$mobile_code_enabled  = isset( $options['mobile_banner_code_state']['enabled'] ) && 'on' === $options['mobile_banner_code_state']['enabled'];

			$mobile = array(
				'position' => 'mobile',
				'active'   => true,
			);

			if ( $mobile_image_enabled && ! empty( $options['mobile_banner_image_state']['mobile_banner_image']['src'] ) ) {
				$mobile['type'] = 'image';
				$mobile['src']  = esc_url( $options['mobile_banner_image_state']['mobile_banner_image']['src'] );
				$mobile['link'] = esc_url( $options['mobile_banner_image_state']['mobile_banner_image_link'] );
				$attachment_id = isset( $options['mobile_banner_image_state']['mobile_banner_image']['id'] ) ? absint( $options['mobile_banner_image_state']['mobile_banner_image']['id'] ) : 0;
				if ( $attachment_id ) {
					$image_data = wp_get_attachment_image_src( $attachment_id, 'full' );
					if ( $image_data ) {
						$mobile['width']  = $image_data[1];
						$mobile['height'] = $image_data[2];
					}
				}
			} elseif ( $mobile_code_enabled ) {
				$mobile['type']   = 'code';
				$mobile['code']   = isset( $options['mobile_banner_code_state']['mobile_banner_code'] ) ? $options['mobile_banner_code_state']['mobile_banner_code'] : '';
				$mobile['width']  = isset( $options['mobile_banner_code_state']['mobile_banner_code_width'] ) ? absint( $options['mobile_banner_code_state']['mobile_banner_code_width'] ) : 160;
				$mobile['height'] = isset( $options['mobile_banner_code_state']['mobile_banner_code_height'] ) ? absint( $options['mobile_banner_code_state']['mobile_banner_code_height'] ) : 600;
			}

			$banners['mobile'] = $mobile;
		}

		return $banners;
	}

	/**
	 * Render banner HTML in footer via AJAX (legacy support).
	 */
	public function render_banner_html() {
		$options = self::get_options();

		// Check if homepage only.
		if ( ! empty( $options['show_on_homepage'] ) && ! is_front_page() && ! is_home() ) {
			return;
		}

		// Left banner.
		if ( ! empty( $options['left_banner_active'] ) ) {
			$this->render_single_banner( 'left', $options );
		}

		// Right banner.
		if ( ! empty( $options['right_banner_active'] ) ) {
			$this->render_single_banner( 'right', $options );
		}

		// Mobile banner.
		if ( ! empty( $options['show_mobile_banner'] ) ) {
			$this->render_single_banner( 'mobile', $options );
		}
	}

	/**
	 * Render a single banner HTML.
	 *
	 * @param string $side    Banner position (left/right/mobile).
	 * @param array  $options Plugin options.
	 */
	private function render_single_banner( $side, $options ) {
		$active_key     = 'left' === $side ? 'left_banner_active' : ( 'right' === $side ? 'right_banner_active' : 'show_mobile_banner' );
		$image_state_key = $side . '_banner_image_state';
		$code_state_key  = $side . '_banner_code_state';
		$sticky_key      = $side . '_banner_sticky';

		$image_enabled = isset( $options[ $image_state_key ]['enabled'] ) && 'on' === $options[ $image_state_key ]['enabled'];
		$code_enabled  = isset( $options[ $code_state_key ]['enabled'] ) && 'on' === $options[ $code_state_key ]['enabled'];

		if ( ! $image_enabled && ! $code_enabled ) {
			return;
		}

		if ( 'mobile' === $side ) {
			// Mobile banner rendered by JS.
			return;
		}

		$div_id  = 'left' === $side ? 'divAdLeft' : 'divAdRight';
		$sticky  = isset( $options[ $sticky_key ] ) && ! empty( $options[ $sticky_key ] );
		$classes = 'flads-banner flads-banner-' . esc_attr( $side ) . ( $sticky ? ' flads-sticky' : '' );
		?>
		<div id="<?php echo esc_attr( $div_id ); ?>" class="<?php echo esc_attr( $classes ); ?>" style="display:none;">
			<?php if ( $image_enabled && ! empty( $options[ $image_state_key ][ $side . '_banner_image' ]['src'] ) ) : ?>
				<?php $link = isset( $options[ $image_state_key ][ $side . '_banner_image_link' ] ) ? $options[ $image_state_key ][ $side . '_banner_image_link' ] : '#'; ?>
				<?php if ( '#' !== $link ) : ?>
					<a href="<?php echo esc_url( $link ); ?>" target="_blank" rel="noopener noreferrer">
				<?php endif; ?>
					<img src="<?php echo esc_url( $options[ $image_state_key ][ $side . '_banner_image' ]['src'] ); ?>" alt="" />
				<?php if ( '#' !== $link ) : ?>
					</a>
				<?php endif; ?>
			<?php elseif ( $code_enabled && ! empty( $options[ $code_state_key ][ $side . '_banner_code' ] ) ) : ?>
				<?php echo wp_kses_post( $options[ $code_state_key ][ $side . '_banner_code' ] ); ?>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Register REST API routes.
	 */
	public function register_rest_routes() {
		register_rest_route( 'floated-ads/v1', '/banners', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_banners_rest' ),
			'permission_callback' => '__return_true',
		) );
	}

	/**
	 * REST API callback to get banners data.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function get_banners_rest( $request ) {
		return new WP_REST_Response( array(
			'success' => true,
			'data'    => $this->get_banners_config(),
		), 200 );
	}
}

// Initialize the plugin.
FloatedAds::init();