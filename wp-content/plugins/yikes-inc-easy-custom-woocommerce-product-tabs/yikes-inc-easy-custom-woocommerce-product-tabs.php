<?php
/**
 * Plugin Name: Custom Product Tabs for WooCommerce 
 * Plugin URI: http://www.yikesinc.com
 * Description: Extend WooCommerce to add and manage custom product tabs. Create as many product tabs as needed per product.
 * Author: YIKES, Inc.
 * Author URI: http://www.yikesinc.com
 * Version: 1.6.6
 * Text Domain: yikes-inc-easy-custom-woocommerce-product-tabs
 * Domain Path: languages/
 *
 * WC requires at least: 3.0.0
 * WC tested up to: 3.5
 *
 * Copyright: (c) 2014-2015 YIKES Inc.
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * 
 * This plugin is originally a fork of SkyVerge WooCommerce Custom Product Tabs Lite
 *
 */

	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) exit;
	
	// Must include plugin.php to use is_plugin_active()
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

		new YIKES_Custom_Product_Tabs();

	} else {

		/* Deactivate the plugin, and display our error notification */
		deactivate_plugins( '/yikes-inc-easy-custom-woocommerce-product-tabs/yikes-inc-easy-custom-woocommerce-product-tabs.php' );
		add_action( 'admin_notices' , 'yikes_woo_display_admin_notice_error' );
	}
	
	/**
	* Display our error admin notice if WooCommerce is not installed + active
	*/
	function yikes_woo_display_admin_notice_error() {
		?>	
			<!-- hide the 'Plugin Activated' default message -->
			<style>
			#message.updated {
				display: none;
			}
			</style>
			<!-- display our error message -->
			<div class="error">
				<p><?php _e( 'Custom Product Tabs for WooCommerce could not be activated because WooCommerce is not installed and active.', 'yikes-inc-easy-custom-woocommerce-product-tabs' ); ?></p>
				<p><?php _e( 'Please install and activate ', 'yikes-inc-easy-custom-woocommerce-product-tabs' ); ?><a href="<?php echo admin_url( 'plugin-install.php?tab=search&type=term&s=WooCommerce' ); ?>" title="WooCommerce">WooCommerce</a><?php _e( ' before activating the plugin.', 'yikes-inc-easy-custom-woocommerce-product-tabs' ); ?></p>
			</div>
		<?php
	}
	
	/**
	* Initialize the Custom Product Tab Class
	*/
	class YIKES_Custom_Product_Tabs {

		/**
		* Construct :)
		*/
		public function __construct() {

			$this->define_constants();

			add_action( 'admin_init', array( $this, 'run_update_check' ) );

			// Require our classes
			require_once YIKES_Custom_Product_Tabs_Path . 'admin/class.yikes-woo-saved-tabs.php';
			require_once YIKES_Custom_Product_Tabs_Path . 'admin/class.yikes-woo-tabs.php';
			require_once YIKES_Custom_Product_Tabs_Path . 'admin/class.support.php';
			require_once YIKES_Custom_Product_Tabs_Path . 'public/class.yikes-woo-tabs-display.php';
			require_once YIKES_Custom_Product_Tabs_Path . 'admin/class.premium.php';
			require_once YIKES_Custom_Product_Tabs_Path . 'admin/class.export.php';

			add_action( 'admin_init', array( $this, 'init' ) );
		}

		/**
		* Define our constants
		*/
		private function define_constants() {

			/**
			* Define the text domain
			*
			* This isn't used anywhere as I don't believe you can use a constant as a text domain
			*/
			if ( ! defined( 'YIKES_Custom_Product_Tabs_Text_Domain' ) ) {
				define( 'YIKES_Custom_Product_Tabs_Text_Domain', 'yikes-inc-easy-custom-woocommerce-product-tabs' );
			}

			/**
			* Define the page slug for our plugin's custom settings page in one central location
			*/
			if ( ! defined( 'YIKES_Custom_Product_Tabs_Settings_Page' ) ) {
				define( 'YIKES_Custom_Product_Tabs_Settings_Page', 'yikes-woo-settings' );
			}

			/**
			* Define the plugin's version
			*/
			if ( ! defined( 'YIKES_Custom_Product_Tabs_Version' ) ) {
				define( 'YIKES_Custom_Product_Tabs_Version', '1.6.6' );
			}

			/**
			* Define the plugin's URI
			*/
			if ( ! defined( 'YIKES_Custom_Product_Tabs_URI' ) ) {
				define( 'YIKES_Custom_Product_Tabs_URI', plugin_dir_url( __FILE__ ) );
			}

			/**
			* Define the plugin's path
			*/
			if ( ! defined( 'YIKES_Custom_Product_Tabs_Path' ) ) {
				define( 'YIKES_Custom_Product_Tabs_Path', plugin_dir_path( __FILE__ ) );
			}

			/**
			* Define the page slug for our plugin's support page
			*/
			if ( ! defined( 'YIKES_Custom_Product_Tabs_Support_Page' ) ) {
				define( 'YIKES_Custom_Product_Tabs_Support_Page', 'yikes-woo-support' );
			}

			/**
			* Define the page slug for our plugin's premium page
			*/
			if ( ! defined( 'YIKES_Custom_Product_Tabs_Premium_Page' ) ) {
				define( 'YIKES_Custom_Product_Tabs_Premium_Page', 'yikes-woo-premium' );
			}
		}

		/**
		* Run any update scripts 
		*/
		public function run_update_check() {

			$run_onesixone_data_update = get_option( 'custom_product_tabs_onesixone_data_update' );

			// If we don't have a value for this option then run our update again
			if ( empty( $run_onesixone_data_update ) ) {
				$this->run_onesixone_data_update();
			}

		}

		private function run_onesixone_data_update() {

			/** Update Saved Tabs **/
			$saved_tabs = get_option( 'yikes_woo_reusable_products_tabs' );

			if ( ! empty( $saved_tabs ) ) {

				foreach( $saved_tabs as $tab_id => &$tab ) {

					// Set the tab slug to the sanitized tab's title
					$tab['tab_slug'] = urldecode( sanitize_title( $tab['tab_title'] ) );

					// Default these elements
					$tab['taxonomies'] = ! isset( $tab['taxonomies'] ) ? array() : $tab['taxonomies'];
					$tab['global_tab'] = ! isset( $tab['global_tab'] ) ? false : $tab['global_tab'];
					$tab['tab_name']   = ! isset( $tab['tab_name'] ) ? '' : $tab['tab_name'];
				}

				update_option( 'yikes_woo_reusable_products_tabs', $saved_tabs );

			}

			/** Update Saved Tabs Applied **/
			$saved_tabs_applied = get_option( 'yikes_woo_reusable_products_tabs_applied' );

			if ( ! empty( $saved_tabs_applied ) ) {

				foreach( $saved_tabs_applied as $product_id => &$tabs ) {

					if ( ! empty( $tabs ) ) {

						foreach( $tabs as $saved_tab_id => &$tab ) {

							if ( ! empty( $tab ) ) {

								if ( isset( $saved_tabs[ $saved_tab_id ] ) ) { 

									// Set the tab ID to the saved tab's slug
									$tab_id = $saved_tabs[ $saved_tab_id ]['tab_slug'];
									$tab['tab_id'] = $tab_id;
								}

							} else {

								// In previous versions of the plugin we were leaving some empty arrays. Clean 'em up.
								unset( $tab );
							}
						}
					} else {

						// In previous versions of the plugin we were leaving some empty arrays. Clean 'em up.
						unset( $saved_tabs_applied[ $product_id ] );
					}
				}

				update_option( 'yikes_woo_reusable_products_tabs_applied', $saved_tabs_applied );
			}

			/** Update Post Meta **/
			global $wpdb;

			// Fetch all of the post meta items where meta_key = 'yikes_woo_products_tabs'
			$yikes_woo_products_tabs = $wpdb->get_results(
				"
				SELECT * 
				FROM {$wpdb->postmeta}
				WHERE meta_key = 'yikes_woo_products_tabs' 
				"
			);

			if ( ! empty( $yikes_woo_products_tabs ) ) {

				foreach( $yikes_woo_products_tabs as $table_row ) {

					// Unserialize our tabs
					$tabs = unserialize( $table_row->meta_value );

					// If we have tabs...
					if ( ! empty( $tabs ) ) {

						foreach( $tabs as &$tab ) {

							// Set the tab slug ('id') to the sanitized tab's title
							$tab['id'] = urldecode( sanitize_title( $tab['title'] ) );
						}

						update_post_meta( $table_row->post_id, 'yikes_woo_products_tabs', $tabs );

					} else {

						// In previous versions of the plugin we were leaving some empty arrays. Clean 'em up.
						delete_post_meta( $table_row->post_id, 'yikes_woo_products_tabs' );
					}
				}
			}

			// Set a flag so we don't run this update more than once
			add_option( 'custom_product_tabs_onesixone_data_update', true );
		}


		/**
		* Run our basic plugin setup
		*/
		public function init() {

			// Default WYSIWYG to 'visual'
			add_filter( 'wp_default_editor', array( $this, 'yikes_woo_set_editor_to_visual' ), 10, 1 );

			// i18n
			add_action( 'plugins_loaded', array( $this, 'yikes_woo_load_plugin_textdomain' ) );

			// Add settings link to plugin on plugins page
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_action_links' ), 10, 1 );
		}

		/**
		* Add a link to the settings page to the plugin's action links
		*
		* @since 1.5
		*
		* @param array | $links | array of links passed from the plugin_action_links_{plugin_name} filter
		*/
		public function add_plugin_action_links( $links ) {
			$href = esc_url_raw( add_query_arg( array( 'page' => YIKES_Custom_Product_Tabs_Settings_Page ), admin_url() ) );
			$links[] = '<a href="'. $href .'">Saved Tabs</a>';
			return $links;
		}

		
		/* i18n */

		/**
		*	Register the textdomain for proper i18n / l10n
		*	@since 1.5
		*/
		public function yikes_woo_load_plugin_textdomain() {
			load_plugin_textdomain(
				'yikes-inc-easy-custom-woocommerce-product-tabs',
				false,
				YIKES_Custom_Product_Tabs_Path . 'languages/'
			);
		}

		/* End i18n */


		/* Misc. */

		/**
		* Default the wp_editor to 'Visual' tab (this helps prevent errors with dynamically generating WYSIWYG)
		*
		* @since 1.5
		*
		* @param  string | $mode | The current mode of the editor
		* @return string 'tinymce' || $mode
		*/
		public function yikes_woo_set_editor_to_visual( $mode ) {
			global $post;

			// Only continue if we're on the products page
			if ( isset( $post ) && isset( $post->post_type ) && $post->post_type !== 'product' ) {
				return $mode;
			}

			// This is funky, but only default the editor when we don't have a post (and we're on the product page)
			// This a result of calling the wp_editor via AJAX - I think
			if ( ! isset( $post ) ) {
				return apply_filters( 'yikes_woocommerce_default_editor_mode', 'tinymce' );
			} else {
				return $mode;
			}
		}

		/* End Misc. */
	}
