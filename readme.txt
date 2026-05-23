=== Manly Fix ===
Contributors: dimitriaus
Tags: html5, validation, woocommerce, trailing-slash, markup
Requires at least: 4.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.5.1
License: GPL v3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

HTML validatator errors fix in WordPress and WooCommerce.

== Description ==

Applies configurable HTML fixes to WordPress and WooCommerce page output. Each fix can be individually enabled or disabled via **Settings → Manly Fix**.

=== WordPress & HTML Fixes ===

* **Remove trailing slashes** - Removes XHTML-style trailing slashes (`/>`) from void elements (`<meta>`, `<link>`, `<img>`, `<input>`, etc.) for clean HTML5 output.
  *Validator: "Info: Trailing slash on void elements has no effect and interacts badly with unquoted attribute values."*

* **Remove intrinsic size CSS** - Removes the `contain-intrinsic-size` CSS rule injected by WordPress for auto-sized images.
  *Validator: "CSS: contain-intrinsic-size: Property contain-intrinsic-size doesn't exist."*

* **Remove redundant aria-hidden** - Removes `aria-hidden="true"` from elements that already carry the `hidden` attribute.
  *Validator: "Attribute aria-hidden is unnecessary for elements that have attribute hidden."*

* **Move router style to `<head>`** - Moves the WordPress Interactivity Router animations inline `<style>` tag from `<body>` to `<head>`.
  *Validator: "Element style not allowed as child of element body in this context. (Suppressing further errors from this subtree.)"*

* **Remove invalid `as` from prefetch links** - Removes the invalid `as` attribute from `<link rel="prefetch">` elements emitted by WooCommerce.
  *Validator: "A `link` element with an `as` attribute must have a `rel` attribute that contains the value `preload` or the value `modulepreload`."*

=== WooCommerce Fixes ===

These fixes are only shown when WooCommerce is active.

* **Mini-cart role="dialog"** - Adds missing `role="dialog"` to the WooCommerce mini-cart drawer div.
  *Validator: "Element div is missing one or more of the following attributes: role."*

* **Add placeholder src to Interactivity API images** - Adds a transparent placeholder `src` and an empty `alt=""` to `<img>` elements that use the WordPress Interactivity API but have no static attributes. Real values are set by JavaScript at runtime.
  *Validator: "Element `img` is missing one or more of the following attributes: `src`, `srcset`." / "An `img` element must have an `alt` attribute."*

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Settings → Manly Fix** to enable or disable individual fixes

== Frequently Asked Questions ==

= Does this plugin affect page performance? =
No. The plugin uses output buffering at the `template_redirect` hook, processing the entire page once before output. On typical sites, the performance impact is negligible.

= Can I choose which fixes are applied? =
Yes. Go to **Settings → Manly Fix** and toggle each fix on or off.

= Are the WooCommerce fixes applied if WooCommerce is not installed? =
No. The WooCommerce fixes section only appears in settings and only runs when WooCommerce is active.

= Is this compatible with all WordPress versions? =
Yes, the plugin uses standard WordPress hooks and PHP functions compatible with WordPress 4.0+.

== Changelog ==

= 1.5.1 =
* Updated: Requires PHP bumped to 7.4 (WordPress 7.0 minimum); Tested up to 7.0.

= 1.5 =
* Merged all Manly Woo Fix functionality into this plugin
* Added WooCommerce fixes: mini-cart role="dialog", Interactivity API image placeholder src/alt
* Added WordPress fixes: redundant aria-hidden, router style to head, prefetch as= attribute
* WooCommerce fixes section is only shown/applied when WooCommerce is active

= 1.4 =
* Added "Remove intrinsic size CSS" fix (absorbs remove-contain-intrinsic-size plugin)

= 1.3 =
* Added Settings page (Settings → Manly Fix) with toggles for each fix

= 1.2 =
* Added direct file access protection
* Improved code documentation

= 1.1 =
* Initial public release
* Support for 18 HTML5 void elements
* Output buffering implementation

== Support ==

For issues, feature requests, or contributions, please contact Manly Electronics.
