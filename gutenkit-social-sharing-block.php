<?php
/**
 * Plugin Name: Gutenberg Social Sharing Block by GutenKit
 * Plugin URI: https://gutenkit.com
 * Description: Easily add a social sharing block to the upcoming Gutenberg editor. <strong>This is a beta release.</strong>
 * Author: @@pkg.author
 * Author URI: https://richtabor.com
 * Version: @@pkg.version
 * Text Domain: @@textdomain
 * Domain Path: languages
 * Requires at least: @@pkg.requires
 * Tested up to: @@pkg.tested_up_to
 *
 * The following was made possible in part by the Gutenberg Boilerplate
 * Check it out - https://github.com/ahmadawais/Gutenberg-Boilerplate
 *
 * GutenKit is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * GutenKit is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GutenKit. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package   @@pkg.title for Gutenberg
 * @author    @@pkg.author
 * @license   @@pkg.license
 */

/**
 * Main GutenKit Lite Click to Tweet Block
 *
 * @since 1.0.0
 */
class Gutenkit_Lite_Social_Sharing_Block {

	/**
	 * This plugin's instance.
	 *
	 * @var Gutenkit_Lite_Social_Sharing_Block
	 */
	private static $instance;

	/**
	 * Registers the plugin.
	 */
	public static function register() {
		if ( null === self::$instance ) {
			self::$instance = new Gutenkit_Lite_Social_Sharing_Block();
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
	 * The Constructor.
	 */
	private function __construct() {
		$this->_version = '@@pkg.version';
		$this->_slug    = 'gutenkit-lite-social-sharing-block';
		$this->_dir     = untrailingslashit( plugin_dir_path( __FILE__ ) );
		$this->_url     = untrailingslashit( plugins_url( '/', __FILE__ ) );

		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		add_action( 'init', array( $this, 'register_block' ) );
	}

	/**
	 * Add actions to enqueue assets.
	 *
	 * @access public
	 */
	public function plugins_loaded() {
		add_action( 'enqueue_block_assets', array( $this, 'enqueue_block_assets' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
	}

	/**
	 * Register the block.
	 *
	 * @access public
	 */
	public function register_block() {
		register_block_type( 'gutenkit/social-sharing', array(
			'attributes'      => array(
				'twitter'         => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'facebook'        => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'pinterest'       => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'linkedin'        => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'tumblr'          => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'align'           => array(
					'type'    => 'string',
					'default' => 'left',
				),
				'align'           => array(
					'type'    => 'string',
					'default' => 'left',
				),
				'backgroundColor' => array(
					'type' => 'string',
				),

			),
			'render_callback' => array( $this, 'render_block' ),
		) );
	}

	/**
	 * Renders the `gutenkit/social-sharing` block on server.
	 *
	 * @param array $attributes The block attributes.
	 */
	public function render_block( $attributes ) {

		// Get the featured image.
		if ( has_post_thumbnail() ) {
			$thumbnail_id = get_post_thumbnail_id( $post->ID );
			$thumbnail    = $thumbnail_id ? current( wp_get_attachment_image_src( $thumbnail_id, 'large', true ) ) : '';
		} else {
			$thumbnail = null;
		}

		// Generate the Twitter URL.
		$twitter_url = '
			http://twitter.com/share?
			text=' . get_the_title() . '
			&url=' . get_the_permalink() . '
		';

		// Generate the Facebook URL.
		$facebook_url = '
			https://www.facebook.com/sharer/sharer.php?
			u=' . get_the_permalink() . '
			&title=' . get_the_title() . '
		';

		// Generate the LinkedIn URL.
		$linkedin_url = '
			https://www.linkedin.com/shareArticle?mini=true
			&url=' . get_the_permalink() . '
			&title=' . get_the_title() . '
		';

		// Generate the Pinterest URL.
		$pinterest_url = '
			https://pinterest.com/pin/create/button/?
			&url=' . get_the_permalink() . '
			&description=' . get_the_title() . '
			&media=' . esc_url( $thumbnail ) . '
		';

		// Generate the Tumblr URL.
		$tumblr_url = '
			https://tumblr.com/share/link?
			url=' . get_the_permalink() . '
			&name=' . get_the_title() . '
		';

		// Apply filters, so that they may be easily modified.
		$twitter_url   = apply_filters( 'gutenkit_twitter_share_url', $twitter_url );
		$facebook_url  = apply_filters( 'gutenkit_facebook_share_url', $facebook_url );
		$pinterest_url = apply_filters( 'gutenkit_pinterest_share_url', $pinterest_url );
		$linkedin_url  = apply_filters( 'gutenkit_linkedin_share_url', $linkedin_url );
		$tumblr_url    = apply_filters( 'gutenkit_tumblr_share_url', $tumblr_url );

		// Start the markup output.
		$markup = '';
		$class  = 'wp-block-gutenkit-social-sharing';
		$style  = is_array( $attributes ) && isset( $attributes['align'] ) ? "style=text-align:{$attributes['align']}" : false;
		$color  = is_array( $attributes ) && isset( $attributes['backgroundColor'] ) ? "style=background-color:{$attributes['backgroundColor']}" : false;

		if ( isset( $attributes['twitter'] ) && $attributes['twitter'] ) {
			$markup .= sprintf(
				'<a href="%1$s" class="wp-block-gutenkit-social-sharing__button button--twitter icon--gutenkit" title="%2$s" %3$s><span class="screen-reader-text">%2$s</span></a>',
				esc_url( $twitter_url ),
				esc_html__( 'Share on Twitter', '@@textdomain' ),
				esc_attr( $color )
			);
		}

		if ( isset( $attributes['facebook'] ) && $attributes['facebook'] ) {
			$markup .= sprintf(
				'<a href="%1$s" class="wp-block-gutenkit-social-sharing__button button--facebook icon--gutenkit" title="%2$s" %3$s><span class="screen-reader-text">%2$s</span></a>',
				esc_url( $facebook_url ),
				esc_html__( 'Share on Facebook', '@@textdomain' ),
				esc_attr( $color )
			);
		}

		if ( isset( $attributes['pinterest'] ) && $attributes['pinterest'] ) {
			$markup .= sprintf(
				'<a href="%1$s" class="wp-block-gutenkit-social-sharing__button button--pinterest icon--gutenkit" title="%2$s" %3$s><span class="screen-reader-text">%2$s</span></a>',
				esc_url( $pinterest_url ),
				esc_html__( 'Share on Pinterest', '@@textdomain' ),
				esc_attr( $color )
			);
		}

		if ( isset( $attributes['linkedin'] ) && $attributes['linkedin'] ) {
			$markup .= sprintf(
				'<a href="%1$s" class="wp-block-gutenkit-social-sharing__button button--linkedin icon--gutenkit" title="%2$s" %3$s><span class="screen-reader-text">%2$s</span></a>',
				esc_url( $linkedin_url ),
				esc_html__( 'Share on LinkedIn', '@@textdomain' ),
				esc_attr( $color )
			);
		}

		if ( isset( $attributes['tumblr'] ) && $attributes['tumblr'] ) {
			$markup .= sprintf(
				'<a href="%1$s" class="wp-block-gutenkit-social-sharing__button button--tumblr icon--gutenkit" title="%2$s" %3$s><span class="screen-reader-text">%2$s</span></a>',
				esc_url( $tumblr_url ),
				esc_html__( 'Share on Tumblr', '@@textdomain' ),
				esc_attr( $color )
			);
		}

		// Render block content.
		$block_content = sprintf(
			'<div class="%1$s" %2$s><p>%3$s</p></div>',
			esc_attr( $class ),
			esc_attr( $style ),
			$markup
		);

		return $block_content;
	}

	/**
	 * Enqueue block assets for use within Gutenberg, as well as on the front end.
	 *
	 * @access public
	 */
	public function enqueue_block_assets() {

		// Styles.
		wp_enqueue_style(
			$this->_slug,
			$this->_url . '/block/build/style.css',
			array( 'wp-blocks' ),
			$this->_version
		);
	}

	/**
	 * Enqueue block assets for use within Gutenberg.
	 *
	 * @access public
	 */
	public function enqueue_block_editor_assets() {

		// Scripts.
		wp_enqueue_script(
			$this->_slug,
			$this->_url . '/block/build/index.js',
			array( 'wp-blocks', 'wp-i18n', 'wp-element' ),
			$this->_version
		);

		// Styles.
		wp_enqueue_style(
			$this->_slug . '-editor',
			$this->_url . '/block/build/editor.css',
			array( 'wp-edit-blocks' ),
			$this->_version
		);
	}
}

Gutenkit_Lite_Social_Sharing_Block::register();
