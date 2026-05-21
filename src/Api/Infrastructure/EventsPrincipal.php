<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\Infrastructure;

/**
 * Principal type for the simple-events plugin.
 *
 * Authentication is HTTP Basic auth (one fixed user per role; password
 * is always "password"). The username doubles as the role: `organizer`,
 * `manager`, or `finance`. Each role expands to a list of *scopes*
 * (capability-like strings) that authorization attributes check against.
 *
 * Different roles can share scopes — e.g. `events:read` is granted to
 * all three roles, while `events:revenue` belongs to `finance` alone.
 * That lets type-level and field-level gates compose meaningfully:
 * placing `#[RequiresScope('events:read')]` on a type and
 * `#[RequiresScope('events:revenue')]` on one of its fields produces
 * the natural "any reader can see basic data; only finance sees
 * revenue" effect.
 *
 * Anonymous callers are represented by a sentinel with an empty
 * `user_login`, null `role`, and an empty `scopes` list.
 */
final class EventsPrincipal {
	/**
	 * Role → scopes mapping. Public so {@see PrincipalResolver} can read
	 * it; treat as a constant.
	 *
	 * @var array<string, list<string>>
	 */
	public const SCOPES_BY_ROLE = array(
		'organizer' => array( 'events:read', 'attendees:read_pii' ),
		'manager'   => array( 'events:read', 'events:edit', 'events:waitlist', 'events:internal_notes', 'attendees:read_pii' ),
		'finance'   => array( 'events:read', 'events:revenue', 'attendees:payment' ),
	);

	/**
	 * @param string       $user_login Login of the authenticated caller; empty for anonymous.
	 * @param ?string      $role       One of `organizer`, `manager`, `finance`, or null when anonymous.
	 * @param list<string> $scopes     Scopes granted to this principal (derived from {@see self::SCOPES_BY_ROLE}).
	 */
	public function __construct(
		public readonly string $user_login,
		public readonly ?string $role,
		public readonly array $scopes,
	) {
	}

	/**
	 * Sentinel anonymous principal: empty login, null role, no scopes.
	 */
	public static function anonymous(): self {
		return new self( '', null, array() );
	}

	/**
	 * Whether the principal carries authenticated credentials.
	 */
	public function is_authenticated(): bool {
		return '' !== $this->user_login;
	}

	/**
	 * Whether the principal holds the requested scope.
	 *
	 * @param string $scope Scope identifier (e.g. `events:read`).
	 */
	public function has_scope( string $scope ): bool {
		return in_array( $scope, $this->scopes, true );
	}

	/**
	 * Whether the principal may run native GraphQL introspection
	 * (`__schema` / `__type`) on the endpoint.
	 *
	 * Defining this method also drives the `_apiMetadata` access gate
	 * via the framework's tri-tier ladder
	 * ({@see MetadataController::can_query_metadata()}): since we don't
	 * declare a separate `can_query_metadata()`, both surfaces share
	 * this decision. Restricting to `manager` keeps schema-shape
	 * discovery (type/field names, authorization markers) away from
	 * anonymous and lower-privileged callers.
	 */
	public function can_introspect(): bool {
		return 'manager' === $this->role;
	}
}
