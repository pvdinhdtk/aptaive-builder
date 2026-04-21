=== Aptaive Builder ===
Contributors: Aptaive
Tags: mobile app, app builder, wordpress api, woocommerce, headless
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Build configurable mobile app layouts and REST APIs from WordPress and WooCommerce.

== Description ==

Aptaive Builder helps you configure mobile app data from your WordPress admin area.

Features include:

* App settings for branding and download links
* Layout configuration for home and bottom navigation
* REST API endpoints for app config, auth, products, cart, checkout, orders, posts, and pages
* WooCommerce-ready endpoints for mobile storefronts
* Account deletion endpoint for in-app account removal flows

== External Services ==

This plugin integrates with the Aptaive build platform at https://app.taive.net/.

The external service is used only when a site administrator chooses to open the `Build App` area in the plugin and continues to the Aptaive platform to submit a mobile app build request.

The plugin itself does not require the external service for local configuration such as app settings, layouts, or WordPress-side API setup. The service is only needed when requesting a build and receiving generated app files through the Aptaive platform.

Service URL: https://app.taive.net/
Terms of Service: https://app.taive.net/terms
Privacy Policy: https://app.taive.net/privacy-policy

The plugin requires a JWT secret key in `wp-config.php`:

`define('APTAIVE_JWT_SECRET', 'CHANGE_THIS_SECRET_KEY');`

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/aptaive-builder` directory, or install the plugin through the WordPress plugins screen.
2. Activate the plugin through the `Plugins` screen in WordPress.
3. Add `define('APTAIVE_JWT_SECRET', 'CHANGE_THIS_SECRET_KEY');` to your `wp-config.php`.
4. Open `Aptaive Builder` in wp-admin and configure your app.

== Development ==

This plugin includes generated admin JavaScript and CSS assets in `admin/build/`.

Public source repository: https://github.com/pvdinhdtk/aptaive-builder

Frontend source files are located in `admin/dev/src/`.
Build configuration is located in `admin/dev/package.json`.

To rebuild the admin assets:

1. Change to the `admin/dev/` directory.
2. Run `npm install`.
3. Run `npm run build`.

Node.js and Composer are only required for development and are not required on production sites.

== Frequently Asked Questions ==

= Does this plugin require WooCommerce? =

WooCommerce is required for product, cart, checkout, and order endpoints. Non-commerce content APIs can still work without WooCommerce.

= Does this plugin require Node.js or Composer on production? =

No. Runtime only needs WordPress and PHP. Development tools are not required on production.
