<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\Infrastructure;

use Automattic\WooCommerce\Api\InvalidTokenException;

/**
 * Custom principal resolver for the simple-events plugin.
 *
 * HTTP Basic auth, single hardcoded password (`password`), one fixed
 * user per role. The username doubles as the role and must be one of
 * `organizer`, `manager`, or `finance`; anything else is rejected as an
 * invalid token. No credentials → anonymous principal.
 *
 * The role is expanded into a list of *scopes* via
 * {@see EventsPrincipal::SCOPES_BY_ROLE}. Authorization attributes check
 * scopes (not roles directly), so a single principal can carry several
 * permissions and different roles can share permissions.
 *
 * The build script captures this method's return type and uses it as
 * the plugin's principal type; every authorization attribute and
 * `_principal` parameter is type-checked against {@see EventsPrincipal}.
 */
class PrincipalResolver {
	private const PASSWORD = 'password';

	public function resolve_principal( \WP_REST_Request $request ): EventsPrincipal {
		unset( $request );

		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		$user     = $_SERVER['PHP_AUTH_USER'] ?? null;
		$password = $_SERVER['PHP_AUTH_PW'] ?? null;
		// phpcs:enable

		if ( null === $user || null === $password ) {
			return EventsPrincipal::anonymous();
		}

		if ( self::PASSWORD !== $password || ! isset( EventsPrincipal::SCOPES_BY_ROLE[ $user ] ) ) {
			throw new InvalidTokenException();
		}

		return new EventsPrincipal( $user, $user, EventsPrincipal::SCOPES_BY_ROLE[ $user ] );
	}
}
