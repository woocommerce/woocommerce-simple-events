<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\Attributes;

use Attribute;
use Automattic\WooCommerceSimpleEvents\Api\Infrastructure\EventsPrincipal;

/**
 * Plugin-supplied authorization attribute: grants when the caller's
 * principal carries the declared scope.
 *
 * Scopes are a small lookup table — see {@see EventsPrincipal::SCOPES_BY_ROLE}
 * for the role → scopes mapping. Different roles can share scopes, so a
 * type-level `#[RequiresScope('events:read')]` and a field-level
 * `#[RequiresScope('events:revenue')]` compose naturally via AND: the
 * caller has to hold both scopes, which any of the three roles holding
 * `events:read` can claim, but only `finance` holds `events:revenue` on
 * top.
 *
 * Targets class (query / mutation / type) and property (output field,
 * input field, or trait-declared property).
 */
#[Attribute( Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY )]
final class RequiresScope {
	/**
	 * @param string $scope Required scope identifier (e.g. `events:read`).
	 */
	public function __construct(
		public readonly string $scope,
	) {
	}

	/**
	 * Grant when the principal holds the required scope.
	 *
	 * @param EventsPrincipal $principal The resolved request principal.
	 */
	public function authorize( EventsPrincipal $principal ): bool {
		return $principal->has_scope( $this->scope );
	}
}
