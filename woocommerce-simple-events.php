<?php
/**
 * Plugin Name: WooCommerce Simple Events
 * Description: Test plugin exercising the WooCommerce Dual API plugin's engine end to end. Exposes a small event-registration API at /wp-json/wc/graphql/simple-events.
 * Version: 0.2.0
 * Requires PHP: 8.1
 * Requires Plugins: woocommerce, woocommerce-dual-api
 *
 * @package Automattic\WooCommerceSimpleEvents
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use \Automattic\WooCommerce\Api\Infrastructure\Main as DualApiMain;

if ( is_file( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

add_action(
	'plugins_loaded',
	static function () {
		// The class is provided by the WooCommerce Dual API plugin; when that
		// plugin is inactive (or dormant) this is a silent no-op.
		if ( method_exists( DualApiMain::class, 'register_graphql_endpoint' ) ) {
			DualApiMain::register_graphql_endpoint(
				__DIR__,
				'wc',
				'/graphql/simple-events'
			);
		}
	}
);
