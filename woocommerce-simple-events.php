<?php
/**
 * Plugin Name: WooCommerce Simple Events
 * Description: Test plugin exercising the WooCommerce dual-code GraphQL infrastructure's granular (field-level) authorization. Exposes a tiny event-registration API at /wp-json/wc/graphql/simple-events.
 * Version: 0.1.0
 * Requires PHP: 8.1
 * Requires Plugins: woocommerce
 *
 * @package Automattic\WooCommerceSimpleEvents
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use \Automattic\WooCommerce\Api\Infrastructure\Main as WooCommerceApiMain;

if ( is_file( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

add_action(
	'plugins_loaded',
	static function () {
		if ( method_exists( WooCommerceApiMain::class, 'register_graphql_endpoint' ) ) {
			WooCommerceApiMain::register_graphql_endpoint(
				__DIR__,
				'wc',
				'/graphql/simple-events'
			);
		}
	}
);
