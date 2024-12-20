<?php
/**
 * ArrayPress - Google Maps Static API Tester
 *
 * @package     ArrayPress\Google\MapsStatic
 * @author      David Sherlock
 * @copyright   Copyright (c) 2024, ArrayPress Limited
 * @license     GPL2+
 * @link        https://arraypress.com/
 * @since       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:         ArrayPress - Google Maps Static API Tester
 * Plugin URI:          https://github.com/arraypress/google-maps-static-plugin
 * Description:         A plugin to test and demonstrate the Google Maps Static API integration.
 * Version:             1.0.0
 * Requires at least:   6.7.1
 * Requires PHP:        7.4
 * Author:              David Sherlock
 * Author URI:          https://arraypress.com/
 * License:             GPL v2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         arraypress-maps-static
 * Domain Path:         /languages
 */

declare( strict_types=1 );

namespace ArrayPress\Google\MapsStatic;

// Exit if accessed directly
use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/vendor/autoload.php';

class Plugin {

	/**
	 * API Client instance
	 *
	 * @var Client|null
	 */
	private ?Client $client = null;

	/**
	 * Hook name for the admin page.
	 *
	 * @var string
	 */
	const MENU_HOOK = 'google_page_arraypress-google-maps-static';

	/**
	 * Plugin constructor
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'load_textdomain' ] );
		add_action( 'admin_menu', [ $this, 'add_menu_page' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );

		$api_key = get_option( 'google_maps_static_api_key' );
		if ( ! empty( $api_key ) ) {
			$this->client = new Client( $api_key );
		}
	}

	/**
	 * Load plugin text domain
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain(
			'arraypress-maps-static',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
		);
	}

	/**
	 * Registers the Google menu and timezone detection submenu page in the WordPress admin.
	 *
	 * This method handles the creation of a shared Google menu across plugins (if it doesn't
	 * already exist) and adds the Timezone Detection tool as a submenu item. It also removes
	 * the default submenu item to prevent a blank landing page.
	 *
	 * @return void
	 */
	public function add_menu_page(): void {
		// Only add the main Google menu if it doesn't exist yet
		global $admin_page_hooks;

		if ( ! isset( $admin_page_hooks['arraypress-google'] ) ) {
			add_menu_page(
				__( 'Google', 'arraypress-google-address-validation' ),
				__( 'Google', 'arraypress-google-address-validation' ),
				'manage_options',
				'arraypress-google',
				null,
				'dashicons-google',
				30
			);
		}

		// Add the address validation submenu
		add_submenu_page(
			'arraypress-google',
			__( 'Maps Static', 'arraypress-google-address-validation' ),
			__( 'Maps Static', 'arraypress-google-address-validation' ),
			'manage_options',
			'arraypress-google-maps-static',
			[ $this, 'render_test_page' ]
		);
	}

	/**
	 * Register plugin settings
	 */
	public function register_settings(): void {
		register_setting( 'maps_static_settings', 'google_maps_static_api_key' );
	}

	/**
	 * Enqueue admin assets
	 */
	public function enqueue_admin_assets( string $hook ): void {
		if ( $hook !== self::MENU_HOOK ) {
			return;
		}

		wp_enqueue_style(
			'google-maps-static-admin',
			plugins_url( 'assets/css/admin.css', __FILE__ ),
			[],
			'1.0.0'
		);
	}

	/**
	 * Render test page interface
	 */
	public function render_test_page(): void {
		$results = $this->process_form_submission();
		?>
        <div class="wrap maps-static-test">
            <h1><?php _e( 'Google Maps Static API Test', 'arraypress-maps-static' ); ?></h1>

			<?php if ( empty( get_option( 'google_maps_static_api_key' ) ) ): ?>
                <div class="notice notice-warning">
                    <p><?php _e( 'Please enter your Google Maps API key to begin testing.', 'arraypress-maps-static' ); ?></p>
                </div>
				<?php $this->render_settings_form(); ?>
			<?php else: ?>
                <div class="maps-static-container">
                    <form method="post" class="maps-static-form">
						<?php wp_nonce_field( 'maps_static_test' ); ?>

                        <h2><?php _e( 'Map Configuration', 'arraypress-maps-static' ); ?></h2>
                        <table class="form-table">
                            <!-- Basic Settings -->
                            <tr>
                                <th scope="row">
                                    <label for="map_type"><?php _e( 'Map Type', 'arraypress-maps-static' ); ?></label>
                                </th>
                                <td>
                                    <select name="map_type" id="map_type">
                                        <option value="roadmap"><?php _e( 'Roadmap', 'arraypress-maps-static' ); ?></option>
                                        <option value="satellite"><?php _e( 'Satellite', 'arraypress-maps-static' ); ?></option>
                                        <option value="terrain"><?php _e( 'Terrain', 'arraypress-maps-static' ); ?></option>
                                        <option value="hybrid"><?php _e( 'Hybrid', 'arraypress-maps-static' ); ?></option>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="zoom"><?php _e( 'Zoom Level', 'arraypress-maps-static' ); ?></label>
                                </th>
                                <td>
                                    <input type="number" id="zoom" name="zoom" min="0" max="21" value="14">
                                    <p class="description"><?php _e( 'Zoom level from 0 (world) to 21 (street)', 'arraypress-maps-static' ); ?></p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="width"><?php _e( 'Width', 'arraypress-maps-static' ); ?></label>
                                </th>
                                <td>
                                    <input type="number" id="width" name="width" value="600" min="1" max="2048">
                                    <p class="description"><?php _e( 'Map width in pixels (max 2048px)', 'arraypress-maps-static' ); ?></p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="height"><?php _e( 'Height', 'arraypress-maps-static' ); ?></label>
                                </th>
                                <td>
                                    <input type="number" id="height" name="height" value="300" min="1" max="2048">
                                    <p class="description"><?php _e( 'Map height in pixels (max 2048px)', 'arraypress-maps-static' ); ?></p>
                                </td>
                            </tr>

                            <!-- Location -->
                            <tr>
                                <th scope="row">
                                    <label for="location"><?php _e( 'Location', 'arraypress-maps-static' ); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="location" name="location" class="regular-text"
                                           value="47.6205,-122.3493"
                                           placeholder="<?php esc_attr_e( 'Enter latitude,longitude or address', 'arraypress-maps-static' ); ?>">
                                    <p class="description">
										<?php _e( 'Enter coordinates (lat,lng) or an address', 'arraypress-maps-static' ); ?>
                                    </p>
                                </td>
                            </tr>

                            <!-- Markers -->
                            <tr>
                                <th scope="row">
									<?php _e( 'Markers', 'arraypress-maps-static' ); ?>
                                </th>
                                <td>
                                    <div class="marker-inputs">
                                        <div class="marker-input">
                                            <input type="text" name="markers[]" class="regular-text"
                                                   placeholder="<?php esc_attr_e( 'Enter marker location', 'arraypress-maps-static' ); ?>">
                                            <button type="button" class="button add-marker">+</button>
                                            <button type="button" class="button remove-marker">-</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>

						<?php submit_button( __( 'Generate Map', 'arraypress-maps-static' ) ); ?>
                    </form>

					<?php if ( $results['image_url'] ): ?>
                        <div class="map-preview">
                            <h3><?php _e( 'Generated Map', 'arraypress-maps-static' ); ?></h3>
                            <img src="<?php echo esc_url( $results['image_url'] ); ?>" alt="Static Map">

                            <div class="map-url">
                                <h4><?php _e( 'Map URL', 'arraypress-maps-static' ); ?></h4>
                                <textarea class="widefat" rows="3"
                                          onclick="this.select()"><?php echo esc_url( $results['image_url'] ); ?></textarea>
                            </div>
                        </div>
					<?php endif; ?>
                </div>

                <div class="maps-static-section">
					<?php $this->render_settings_form(); ?>
                </div>
			<?php endif; ?>
        </div>

        <script>
            jQuery(document).ready(function ($) {
                $('.add-marker').on('click', function () {
                    var newMarker = $('.marker-input:first').clone();
                    newMarker.find('input').val('');
                    $('.marker-inputs').append(newMarker);
                });

                $(document).on('click', '.remove-marker', function () {
                    if ($('.marker-input').length > 1) {
                        $(this).closest('.marker-input').remove();
                    }
                });
            });
        </script>
		<?php
	}

	/**
	 * Render settings form
	 */
	private function render_settings_form(): void {
		?>
        <h2><?php _e( 'Settings', 'arraypress-maps-static' ); ?></h2>
        <form method="post" class="maps-static-form">
			<?php wp_nonce_field( 'maps_static_api_key' ); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="google_maps_static_api_key"><?php _e( 'API Key', 'arraypress-maps-static' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="google_maps_static_api_key" id="google_maps_static_api_key"
                               class="regular-text"
                               value="<?php echo esc_attr( get_option( 'google_maps_static_api_key' ) ); ?>"
                               placeholder="<?php esc_attr_e( 'Enter your Google Maps API key...', 'arraypress-maps-static' ); ?>">
                        <p class="description">
							<?php _e( 'Your Google Maps API key. Required for making API requests.', 'arraypress-maps-static' ); ?>
                        </p>
                    </td>
                </tr>
            </table>
			<?php submit_button(
				empty( get_option( 'google_maps_static_api_key' ) )
					? __( 'Save Settings', 'arraypress-maps-static' )
					: __( 'Update Settings', 'arraypress-maps-static' ),
				'primary',
				'submit_api_key'
			); ?>
        </form>
		<?php
	}

	/**
	 * Process form submissions
	 *
	 * @return array Results containing generated image URL
	 */
	private function process_form_submission(): array {
		$results = [
			'image_url' => null
		];

		if ( isset( $_POST['submit_api_key'] ) ) {
			check_admin_referer( 'maps_static_api_key' );
			$api_key = sanitize_text_field( $_POST['google_maps_static_api_key'] );
			update_option( 'google_maps_static_api_key', $api_key );
			$this->client = new Client( $api_key );
		}

		if ( ! $this->client ) {
			return $results;
		}

		if ( isset( $_POST['submit'] ) ) {
			check_admin_referer( 'maps_static_test' );

			try {
				// Set map options
				$width  = (int) $_POST['width'];
				$height = (int) $_POST['height'];
				$this->client->set_size( $width, $height );

				if ( ! empty( $_POST['map_type'] ) ) {
					$this->client->set_map_type( $_POST['map_type'] );
				}

				if ( ! empty( $_POST['zoom'] ) ) {
					$this->client->set_zoom( (int) $_POST['zoom'] );
				}

				$location = sanitize_text_field( $_POST['location'] );
				$options  = [ 'center' => $location ];

				// Generate map URL based on location and markers
				if ( ! empty( $_POST['markers'] ) && is_array( $_POST['markers'] ) ) {
					$markers = array_filter( $_POST['markers'], 'strlen' );
					if ( ! empty( $markers ) ) {
						$marker_config = [
							[
								'locations' => array_map( 'sanitize_text_field', $markers )
							]
						];
						$url           = $this->client->markers( $marker_config, $options );
					} else {
						$url = $this->client->location( $location );
					}
				} else {
					$url = $this->client->location( $location );
				}

				if ( is_wp_error( $url ) ) {
					throw new Exception( $url->get_error_message() );
				}

				$results['image_url'] = $url;

			} catch ( Exception $e ) {
				add_settings_error(
					'maps_static_test',
					'map_error',
					$e->getMessage()
				);
			}
		}

		return $results;
	}

}

new Plugin();