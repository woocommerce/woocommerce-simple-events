<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\Infrastructure;

/**
 * Optional convention class: customizes the HTTP status of responses.
 *
 * ApiBuilder detects a class named `HttpStatusResolver` under the plugin's
 * `Api\Infrastructure` namespace and routes every response's status
 * through it. Core ships none, so without this class the framework's
 * per-error-code mapping applies unchanged.
 *
 * This demo lets a client force the status of a *successful* response via
 * an `X-Status-Code` header (handy for testing proxies/clients), while
 * leaving error statuses untouched. The method must never throw — any
 * throw is converted by the framework into a fixed 500 INTERNAL_ERROR.
 */
final class HttpStatusResolver {
	/**
	 * @param int              $default_status The framework-computed status (returned verbatim to defer).
	 * @param array            $output         The response body about to be sent.
	 * @param \WP_REST_Request $request        The originating request.
	 */
	public function resolve_status( int $default_status, array $output, \WP_REST_Request $request ): int {
		if ( 200 !== $default_status ) {
			return $default_status;
		}

		$override = (int) $request->get_header( 'x-status-code' );

		return ( $override >= 100 && $override <= 599 ) ? $override : $default_status;
	}
}
