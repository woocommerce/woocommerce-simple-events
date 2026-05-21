<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\Attributes;

use Attribute;
use Automattic\WooCommerceSimpleEvents\Api\Infrastructure\EventsPrincipal;

/**
 * Plugin-supplied authorization attribute that grants either to the
 * resource's owner OR to anyone holding the declared scope. Exercises
 * the `$_parent` opt-in slot: the field-level resolver passes the
 * enclosing object as `$_parent`, and the attribute reads its
 * `organizer_login` to decide whether the caller is the owner.
 *
 * Use on output-type properties only; class-level placement would
 * receive `$_parent = null` from the resolver and degenerate to a plain
 * scope check.
 */
#[Attribute( Attribute::TARGET_PROPERTY )]
final class OwnerOrScope {
	/**
	 * @param string $scope Fallback scope that grants regardless of ownership.
	 */
	public function __construct(
		public readonly string $scope,
	) {
	}

	/**
	 * Grant when the principal owns the parent object OR holds the scope.
	 *
	 * @param EventsPrincipal $principal The resolved request principal.
	 * @param mixed           $_parent   The enclosing object being resolved; null at the root.
	 */
	public function authorize( EventsPrincipal $principal, mixed $_parent ): bool {
		if ( $principal->has_scope( $this->scope ) ) {
			return true;
		}
		if ( ! $principal->is_authenticated() ) {
			return false;
		}
		if ( is_object( $_parent ) && property_exists( $_parent, 'organizer_login' ) ) {
			return $_parent->organizer_login === $principal->user_login;
		}
		return false;
	}
}
