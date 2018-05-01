<?php
/**
 * Plugin Name: Gutenberg Social Block by CoBlocks
 * Plugin URI: https://coblocks.com
 * Description: @@pkg.description
 * Author: Rich Tabor from CoBlocks
 * Author URI: https://coblocks.com
 * Tags: gutenberg, editor, block, sharing, social
 * Version: 1.1.0
 * Text Domain: '@@pkg.name'
 * Domain Path: languages
 * Tested up to: @@pkg.tested_up_to
 *
 * @@pkg.title is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with @@pkg.title. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package   @@pkg.title
 * @author    @@pkg.author
 * @license   @@pkg.license
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main @@pkg.title Class
 *
 * @since 1.0.0
 */
class CoBlocks_Social {

	/**
	 * This plugin's instance.
	 *
	 * @var CoBlocks_Social
	 */
	private static $instance;

	/**
	 * Registers the plugin.
	 */
	public static function register() {
		if ( null === self::$instance ) {
			self::$instance = new CoBlocks_Social();
			self::$instance->includes();
		}
	}

	/**
	 * The base directory path (without trailing slash).
	 *
	 * @var string $_url
	 */
	private $_dir;

	/**
	 * The base URL path (without trailing slash).
	 *
	 * @var string $_url
	 */
	private $_url;

	/**
	 * The Plugin version.
	 *
	 * @var string $_version
	 */
	private $_version;

	/**
	 * The Plugin version.
	 *
	 * @var string $_slug
	 */
	private $_slug;

	/**
	 * The plugin's store.
	 *
	 * @var string $_store
	 */
	private $_store;

	/**
	 * This plugin.
	 *
	 * @var string $_plugin
	 */
	private $_plugin;

	/**
	 * The Constructor.
	 */
	private function __construct() {

		$this->_version = '@@pkg.version';
		$this->_slug    = 'coblocks';
		$this->_store   = 'https://coblocks.com';
		$this->_plugin  = $this->_slug . '-social-block-plugin';
		$this->_dir     = untrailingslashit( plugin_dir_path( '/', __FILE__ ) );
		$this->_url     = untrailingslashit( plugins_url( '/', __FILE__ ) );

		add_action( 'init', array( $this, 'block_assets' ) );
		add_action( 'init', array( $this, 'editor_assets' ) );
		add_action( 'plugins_loaded', array( $this, 'load_dynamic_blocks' ) );

		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
		add_filter( 'plugin_action_links_' . plugin_basename( plugin_dir_path( __FILE__ ) . 'class-coblocks-social.php' ), array( $this, 'plugin_action_links' ) );
	}

	/**
	 * Check if CoBlocks lite or pro is activated.
	 *
	 * @access public
	 */
	public function has_coblocks() {

		if ( class_exists( 'CoBlocks' ) || class_exists( 'CoBlocks_Pro' ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Include classes
	 *
	 * @return void
	 */
	public function includes() {
		require_once 'includes/class-coblocks-social-notices.php';
	}

	/**
	 * Register server-side code for individual blocks.
	 *
	 * @access public
	 */
	public function load_dynamic_blocks() {

		// Return early if CoBlocks Lite or Pro is active.
		if ( $this->has_coblocks() ) {
			return;
		}

		foreach ( glob( dirname( __FILE__ ) . '/src/blocks/*/index.php' ) as $block_logic ) {
			require $block_logic;
		}
	}

	/**
	 * Enqueue block assets for use within Gutenberg.
	 *
	 * @access public
	 */
	public function block_assets() {

		// Styles.
		wp_register_style(
			$this->_slug . '-frontend',
			$this->_url . '/dist/blocks.style.build.css',
			array( 'wp-blocks' ),
			$this->_version
		);
	}

	/**
	 * Enqueue block assets for use within Gutenberg.
	 *
	 * @access public
	 */
	public function editor_assets() {

		// Styles.
		wp_register_style(
			$this->_slug . '-editor',
			$this->_url . '/dist/blocks.editor.build.css',
			array( 'wp-edit-blocks' ),
			$this->_version
		);

		// Scripts.
		wp_register_script(
			$this->_slug . '-editor',
			$this->_url . '/dist/blocks.build.js',
			array( 'wp-blocks', 'wp-i18n', 'wp-element' ),
			$this->_version
		);
	}

	/**
	 * Returns the URL to upgrade the plugin to the pro version.
	 * Can be overridden by theme developers to use their affiliate
	 * link using the coblocks_affiliate_id filter.
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function get_affiliate_id() {

		$id = array( 'ref' => apply_filters( 'coblocks_affiliate_id', null ) );

		return $id;
	}

	/**
	 * Returns a URL that points to the Beaver Builder store.
	 *
	 * @since 1.0.0
	 * @param string|string $path A URL path to append to the store URL.
	 * @param array|array   $params An array of key/value params to add to the query string.
	 * @return string
	 */
	public function get_store_url( $path = '', $params = array() ) {

		$id = $this->get_affiliate_id();

		$params = array_merge( $params, $id );

		$url = trailingslashit( $this->_store . '/' . $path ) . '?' . http_build_query( $params, '', '&#038;' );

		return $url;
	}

	/**
	 * Add a link next to the Activate/Deactivate action
	 *
	 * @param       array|array $actions The plugin.
	 * @return      array
	 */
	public function plugin_action_links( $actions ) {

		$title = esc_html__( 'Get CoBlocks', '@@textdomain' );

		$url = $this->get_store_url(
			'support',
			array(
				'utm_medium'   => $this->_plugin,
				'utm_source'   => 'plugins-page',
				'utm_campaign' => 'plugins-action-link',
				'utm_content'  => 'Get CoBlocks',
			)
		);

		// Use the WordPress.org URL for now.
		$url = 'https://wordpress.org/plugins/coblocks';

		// Merge and display each link.
		return array_merge(
			array( 'url' => sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $url ), $title ) ),
			$actions
		);
	}

	/**
	 * Plugin row meta links
	 *
	 * @param array|array   $input already defined meta links.
	 * @param string|string $file plugin file path and name being processed.
	 * @return array $input
	 */
	public function plugin_row_meta( $input, $file ) {

		if ( 'social-sharing-block-gutenberg/class-coblocks-social.php' !== $file ) {
			return $input;
		}

		$url = $this->get_store_url(
			'extensions',
			array(
				'utm_medium'   => $this->_plugin,
				'utm_source'   => 'plugins-page',
				'utm_campaign' => 'plugins-row',
				'utm_content'  => 'extensions',
			)
		);

		// Use the WordPress.org URL for now.
		$url = 'https://wordpress.org/plugins/coblocks';

		$links = array(
			'<a href="' . esc_url( $url ) . '" target="_blank">' . esc_html__( 'More blocks', '@@textdomain' ) . '</a>',
		);

		$input = array_merge( $input, $links );

		return $input;
	}
}

CoBlocks_Social::register();
