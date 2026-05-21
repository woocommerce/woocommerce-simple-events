<?php
/**
 * Build script for the plugin's GraphQL API. Delegates to WooCommerce's
 * ApiBuilder::run_for_plugin(). WooCommerce is located next to the plugin
 * repo in the same parent directory by convention; set WC_PATH to override
 * (point it at WooCommerce's plugins/woocommerce subtree).
 */

declare(strict_types=1);

if ( PHP_SAPI !== 'cli' ) {
	http_response_code( 403 );
	exit;
}

$plugin_root = dirname( __DIR__ );
$wc_path     = getenv( 'WC_PATH' ) ?: dirname( $plugin_root ) . '/woocommerce/plugins/woocommerce';

if ( ! is_file( $wc_path . '/vendor/autoload.php' ) ) {
	fwrite( STDERR, "WooCommerce not found at {$wc_path}. Set WC_PATH to its plugins/woocommerce path.\n" );
	exit( 1 );
}
require_once $wc_path . '/vendor/autoload.php';

use Automattic\WooCommerce\Api\Infrastructure\DesignTime\ApiBuilder;

if ( ! method_exists( ApiBuilder::class, 'run_for_plugin' ) ) {
	fwrite( STDERR, "WooCommerce at {$wc_path} lacks ApiBuilder::run_for_plugin. Update it, or point WC_PATH at a newer tree.\n" );
	exit( 1 );
}

ApiBuilder::run_for_plugin( $plugin_root, 'Automattic\\WooCommerceSimpleEvents' );
