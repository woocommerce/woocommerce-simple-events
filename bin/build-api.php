<?php
/**
 * Build script for the plugin's GraphQL API. Delegates to the WooCommerce Dual
 * API plugin's ApiBuilder::run_for_plugin(). That plugin is located next to
 * this repo in the same parent directory by convention (which is also where it
 * is when both are installed under wp-content/plugins); set WC_DUAL_API_PATH to
 * override.
 */

declare(strict_types=1);

if ( PHP_SAPI !== 'cli' ) {
	http_response_code( 403 );
	exit;
}

$plugin_root   = dirname( __DIR__ );
$dual_api_path = getenv( 'WC_DUAL_API_PATH' ) ?: dirname( $plugin_root ) . '/woocommerce-dual-api';

if ( ! is_file( $dual_api_path . '/vendor/autoload.php' ) ) {
	fwrite( STDERR, "WooCommerce Dual API plugin not found at {$dual_api_path} (or its Composer autoloader is missing). Set WC_DUAL_API_PATH to the plugin's directory.\n" );
	exit( 1 );
}
require_once $dual_api_path . '/vendor/autoload.php';

use Automattic\WooCommerce\Api\Infrastructure\DesignTime\ApiBuilder;

if ( ! method_exists( ApiBuilder::class, 'run_for_plugin' ) ) {
	fwrite( STDERR, "The WooCommerce Dual API plugin at {$dual_api_path} lacks ApiBuilder::run_for_plugin. Update it, or point WC_DUAL_API_PATH at a newer copy.\n" );
	exit( 1 );
}

ApiBuilder::run_for_plugin( $plugin_root, 'Automattic\\WooCommerceSimpleEvents' );
