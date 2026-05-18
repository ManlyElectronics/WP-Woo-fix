<?php
/**
 * Manly Fix
 *
 * Applies configurable HTML fixes to WordPress and WooCommerce page output.
 *
 * @category WordPress_Plugin
 * @package  Manly_Fix
 * @author   Manly Electronics
 * @license  GPL-3.0 https://www.gnu.org/licenses/gpl-3.0.html
 * @link     https://manlyelectronics.com.au
 *
 * PHP Version 7.2
 *
 * @wordpress-plugin
 * Plugin Name: Manly Fix
 * Description: Applies configurable HTML fixes to WordPress and WooCommerce page output. Each fix can be individually enabled or disabled.
 * Version:     1.5
 * Author:      Manly Electronics
 * Author URI:  https://manlyelectronics.com.au
 * License:     GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Requires PHP: 7.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MANLY_FIX_OPTION', 'manly_fix_options' );

/**
 * Returns plugin options merged with defaults.
 *
 * @return array
 */
function manly_fix_get_options() {
	$defaults = array(
		// WordPress / HTML fixes.
		'fix_trailing_slash'        => 1,
		'fix_intrinsic_size_css'    => 1,
		'fix_redundant_aria_hidden' => 1,
		'fix_router_style_head'     => 1,
		'fix_prefetch_as_attr'      => 1,
		'fix_remove_oembed'         => 1,
		// WooCommerce fixes.
		'fix_mini_cart_role'        => 1,
		'fix_interactivity_img_src' => 1,
	);
	return wp_parse_args( get_option( MANLY_FIX_OPTION, array() ), $defaults );
}

// Register settings.
add_action(
	'admin_init',
	function () {
		register_setting(
			'manly_fix',
			MANLY_FIX_OPTION,
			array( 'sanitize_callback' => 'manly_fix_sanitize' )
		);

		add_settings_section( 'manly_fix_wp', 'WordPress &amp; HTML Fixes', null, 'manly-fix' );

		add_settings_field( 'fix_trailing_slash', 'Remove trailing slashes', 'manly_fix_field_trailing_slash', 'manly-fix', 'manly_fix_wp' );
		add_settings_field( 'fix_intrinsic_size_css', 'Remove intrinsic size CSS', 'manly_fix_field_intrinsic_size_css', 'manly-fix', 'manly_fix_wp' );
		add_settings_field( 'fix_redundant_aria_hidden', 'Remove redundant aria-hidden', 'manly_fix_field_redundant_aria_hidden', 'manly-fix', 'manly_fix_wp' );
		add_settings_field( 'fix_router_style_head', 'Move router style to &lt;head&gt;', 'manly_fix_field_router_style_head', 'manly-fix', 'manly_fix_wp' );
		add_settings_field( 'fix_prefetch_as_attr', 'Remove invalid <code>as</code> from prefetch links', 'manly_fix_field_prefetch_as_attr', 'manly-fix', 'manly_fix_wp' );
		add_settings_field( 'fix_remove_oembed', 'Remove oEmbed discovery links', 'manly_fix_field_remove_oembed', 'manly-fix', 'manly_fix_wp' );

		if ( class_exists( 'WooCommerce' ) ) {
			add_settings_section( 'manly_fix_woo', 'WooCommerce Fixes', null, 'manly-fix' );
			add_settings_field( 'fix_mini_cart_role', 'Mini-cart role="dialog"', 'manly_fix_field_mini_cart_role', 'manly-fix', 'manly_fix_woo' );
			add_settings_field( 'fix_interactivity_img_src', 'Add placeholder src to Interactivity API images', 'manly_fix_field_interactivity_img_src', 'manly-fix', 'manly_fix_woo' );
		}
	}
);

/**
 * Sanitizes saved options.
 *
 * @param array $input Raw POST input.
 * @return array
 */
function manly_fix_sanitize( $input ) {
	return array(
		'fix_trailing_slash'        => ! empty( $input['fix_trailing_slash'] ) ? 1 : 0,
		'fix_intrinsic_size_css'    => ! empty( $input['fix_intrinsic_size_css'] ) ? 1 : 0,
		'fix_redundant_aria_hidden' => ! empty( $input['fix_redundant_aria_hidden'] ) ? 1 : 0,
		'fix_router_style_head'     => ! empty( $input['fix_router_style_head'] ) ? 1 : 0,
		'fix_prefetch_as_attr'      => ! empty( $input['fix_prefetch_as_attr'] ) ? 1 : 0,
		'fix_remove_oembed'         => ! empty( $input['fix_remove_oembed'] ) ? 1 : 0,
		'fix_mini_cart_role'        => ! empty( $input['fix_mini_cart_role'] ) ? 1 : 0,
		'fix_interactivity_img_src' => ! empty( $input['fix_interactivity_img_src'] ) ? 1 : 0,
	);
}

// --- Field renderers ---------------------------------------------------------

/**
 * Trailing slash field.
 */
function manly_fix_field_trailing_slash() {
	$o = manly_fix_get_options();
	printf(
		'<input type="checkbox" name="%s[fix_trailing_slash]" value="1" %s><br><span class="description">Removes XHTML-style trailing slashes (<code>/&gt;</code>) from void elements for HTML5.</span>',
		esc_attr( MANLY_FIX_OPTION ),
		checked( 1, $o['fix_trailing_slash'], false )
	);
}

/**
 * Intrinsic size CSS field.
 */
function manly_fix_field_intrinsic_size_css() {
	$o = manly_fix_get_options();
	printf(
		'<input type="checkbox" name="%s[fix_intrinsic_size_css]" value="1" %s><br><span class="description">Removes the <code>contain-intrinsic-size</code> CSS rule injected by WordPress for auto-sized images (fixes HTML validation errors).</span>',
		esc_attr( MANLY_FIX_OPTION ),
		checked( 1, $o['fix_intrinsic_size_css'], false )
	);
}

/**
 * Redundant aria-hidden field.
 */
function manly_fix_field_redundant_aria_hidden() {
	$o = manly_fix_get_options();
	printf(
		'<input type="checkbox" name="%s[fix_redundant_aria_hidden]" value="1" %s><br><em>Validator: &ldquo;The <code>aria-hidden</code> attribute is unnecessary for elements that use <code>hidden</code>.&rdquo;</em><br><span class="description">Removes redundant <code>aria-hidden="true"</code> from elements that already have the <code>hidden</code> attribute.</span>',
		esc_attr( MANLY_FIX_OPTION ),
		checked( 1, $o['fix_redundant_aria_hidden'], false )
	);
}

/**
 * Router style to head field.
 */
function manly_fix_field_router_style_head() {
	$o = manly_fix_get_options();
	printf(
		'<input type="checkbox" name="%s[fix_router_style_head]" value="1" %s><br><em>Validator: &ldquo;Element <code>style</code> not allowed as child of element <code>body</code> in this context.&rdquo;</em><br><span class="description">Moves the WordPress Interactivity Router animations inline style from <code>&lt;body&gt;</code> to <code>&lt;head&gt;</code>.</span>',
		esc_attr( MANLY_FIX_OPTION ),
		checked( 1, $o['fix_router_style_head'], false )
	);
}

/**
 * Prefetch as= attribute field.
 */
function manly_fix_field_prefetch_as_attr() {
	$o = manly_fix_get_options();
	printf(
		'<input type="checkbox" name="%s[fix_prefetch_as_attr]" value="1" %s><br><em>Validator: &ldquo;A <code>link</code> element with an <code>as</code> attribute must have a <code>rel</code> attribute that contains the value <code>preload</code> or the value <code>modulepreload</code>.&rdquo;</em><br><span class="description">Removes the invalid <code>as</code> attribute from <code>&lt;link rel="prefetch"&gt;</code> elements emitted by WooCommerce.</span>',
		esc_attr( MANLY_FIX_OPTION ),
		checked( 1, $o['fix_prefetch_as_attr'], false )
	);
}

/**
 * Remove oEmbed discovery links field.
 */
function manly_fix_field_remove_oembed() {
	$o = manly_fix_get_options();
	printf(
		'<input type="checkbox" name="%s[fix_remove_oembed]" value="1" %s><br><span class="description">Removes the oEmbed discovery <code>&lt;link&gt;</code> tags from the page <code>&lt;head&gt;</code>.</span>',
		esc_attr( MANLY_FIX_OPTION ),
		checked( 1, $o['fix_remove_oembed'], false )
	);
}

/**
 * Mini-cart role field.
 */
function manly_fix_field_mini_cart_role() {
	$o = manly_fix_get_options();
	printf(
		'<input type="checkbox" name="%s[fix_mini_cart_role]" value="1" %s><br><em>Validator: &ldquo;Element <code>div</code> is missing one or more of the following attributes: <code>role</code>.&rdquo;</em><br><span class="description">Adds missing <code>role="dialog"</code> to the WooCommerce mini-cart drawer div.</span>',
		esc_attr( MANLY_FIX_OPTION ),
		checked( 1, $o['fix_mini_cart_role'], false )
	);
}

/**
 * Interactivity API img src field.
 */
function manly_fix_field_interactivity_img_src() {
	$o = manly_fix_get_options();
	printf(
		'<input type="checkbox" name="%s[fix_interactivity_img_src]" value="1" %s><br><em>Validator: &ldquo;Element <code>img</code> is missing one or more of the following attributes: <code>src</code>, <code>srcset</code>.&rdquo; / &ldquo;An <code>img</code> element must have an <code>alt</code> attribute.&rdquo;</em><br><span class="description">Adds a transparent placeholder <code>src</code> and an empty <code>alt=""</code> to <code>&lt;img&gt;</code> elements that use the WordPress Interactivity API but have no static attributes. Real values are set by JavaScript at runtime.</span>',
		esc_attr( MANLY_FIX_OPTION ),
		checked( 1, $o['fix_interactivity_img_src'], false )
	);
}

// --- Settings page -----------------------------------------------------------

add_filter(
	'plugin_action_links_manly-fix/manly-fix.php',
	function ( $links ) {
		$links[] = '<a href="' . admin_url( 'options-general.php?page=manly-fix' ) . '">' . __( 'Settings', 'manly-fix' ) . '</a>';
		return $links;
	}
);

add_action(
	'admin_menu',
	function () {
		add_options_page(
			'Manly Fix',
			'Manly Fix',
			'manage_options',
			'manly-fix',
			'manly_fix_settings_page'
		);
	}
);

/**
 * Renders the settings page.
 */
function manly_fix_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'manly_fix' );
			do_settings_sections( 'manly-fix' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

// --- Hooks that run independently of the output buffer ----------------------

// Intrinsic size CSS fix (wp_head hooks, not template_redirect buffer).
add_action(
	'init',
	function () {
		$options = manly_fix_get_options();
		if ( empty( $options['fix_intrinsic_size_css'] ) ) {
			return;
		}
		add_filter( 'wp_img_tag_add_auto_sizes', '__return_false' );
		add_action(
			'wp_head',
			function () {
				remove_action( 'wp_head', 'wp_print_auto_sizes_contain_css_fix', 1 );
			},
			0
		);
	}
);

// oEmbed discovery links removal.
add_action(
	'init',
	function () {
		$options = manly_fix_get_options();
		if ( empty( $options['fix_remove_oembed'] ) ) {
			return;
		}
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	}
);

// Router style to <head> fix.
add_action(
	'wp_enqueue_scripts',
	function () {
		$options = manly_fix_get_options();
		if ( empty( $options['fix_router_style_head'] ) ) {
			return;
		}
		// 'wp-interactivity-router-animations' is a WordPress core registered handle (Interactivity API).
		// Registering with false src and enqueueing here forces it into <head> instead of the footer.
		// The 'wp-' prefix belongs to WordPress core, not this plugin -- PHPCS prefix check is a false positive.
		wp_register_style( 'wp-interactivity-router-animations', false ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- no src file, core handle.
		wp_enqueue_style( 'wp-interactivity-router-animations' );
	},
	100
);

// --- Output buffer (template_redirect) for all remaining fixes ---------------

add_action(
	'template_redirect',
	function () {
		$options = manly_fix_get_options();

		$needs_buffer = ! empty( $options['fix_trailing_slash'] )
			|| ! empty( $options['fix_redundant_aria_hidden'] )
			|| ! empty( $options['fix_prefetch_as_attr'] )
			|| ! empty( $options['fix_mini_cart_role'] )
			|| ! empty( $options['fix_interactivity_img_src'] );

		if ( ! $needs_buffer ) {
			return;
		}

		ob_start(
			function ( $html ) use ( $options ) {

				// Trailing slashes: remove from void elements.
				if ( ! empty( $options['fix_trailing_slash'] ) ) {
					$void_elements = array(
						'meta',
						'link',
						'base',
						'img',
						'input',
						'br',
						'hr',
						'source',
						'track',
						'embed',
						'area',
						'col',
						'command',
						'keygen',
						'param',
						'wbr',
					);
					foreach ( $void_elements as $tag ) {
						$html = preg_replace( '#<(' . $tag . ')(\s[^<>]*?)(\s*)/?>#i', '<$1$2>', $html );
					}
				}

				// Redundant aria-hidden: remove from elements that also have hidden.
				if ( ! empty( $options['fix_redundant_aria_hidden'] ) ) {
					$html = preg_replace( '/\s*aria-hidden="true"(\s+hidden\b)/i', '$1', $html );
				}

				// Prefetch as=: remove invalid as= from non-preload link elements.
				if ( ! empty( $options['fix_prefetch_as_attr'] ) ) {
					$html = preg_replace_callback(
						'/<link\b[^>]+>/i',
						function ( $matches ) {
							$tag = $matches[0];
							if ( ! preg_match( '/\bas=/i', $tag )
								|| preg_match( '/\brel=["\'](?:preload|modulepreload)["\']/i', $tag ) ) {
								return $tag;
							}
							return preg_replace( '/\s+as=(?:"[^"]*"|\'[^\']*\')/i', '', $tag );
						},
						$html
					);
				}

				// Mini-cart role: add role="dialog" to the WooCommerce drawer div.
				if ( class_exists( 'WooCommerce' )
					&& ! empty( $options['fix_mini_cart_role'] )
					&& strpos( $html, 'wc-block-mini-cart__drawer' ) !== false
					&& strpos( $html, 'role="dialog"' ) === false ) {
					$html = preg_replace(
						'/(class="wc-block-mini-cart__drawer[^"]*")\s*(>)/s',
						'$1 role="dialog"$2',
						$html,
						1
					);
				}

				// Interactivity img: add placeholder src/alt to Interactivity API images.
				if ( class_exists( 'WooCommerce' ) && ! empty( $options['fix_interactivity_img_src'] ) ) {
					$html = preg_replace_callback(
						'/<img\b[^>]+>/is',
						function ( $matches ) {
							$tag       = $matches[0];
							$needs_src = preg_match( '/\bdata-wp-bind--src=/i', $tag )
								&& ! preg_match( '/\bsrc\s*=/i', $tag );
							$needs_alt = preg_match( '/\bdata-wp-bind--alt=/i', $tag )
								&& ! preg_match( '/\balt\s*=/i', $tag );
							if ( ! $needs_src && ! $needs_alt ) {
								return $tag;
							}
							$inject = '';
							if ( $needs_src ) {
								$inject .= ' src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="';
							}
							if ( $needs_alt ) {
								$inject .= ' alt=""';
							}
							return substr( $tag, 0, -1 ) . $inject . '>';
						},
						$html
					);
				}

				return $html;
			}
		);
		// Explicitly close the buffer before WordPress's own shutdown handler (priority 1).
		add_action( 'shutdown', function() { ob_end_flush(); }, 0 );
	}
);
