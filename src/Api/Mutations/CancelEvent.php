<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\Mutations;

use Automattic\WooCommerce\Api\Attributes\Description;
use Automattic\WooCommerce\Api\Attributes\Name;
use Automattic\WooCommerceSimpleEvents\Api\Attributes\RequiresScope;
use Automattic\WooCommerceSimpleEvents\Api\Infrastructure\EventsPrincipal;
use Automattic\WooCommerceSimpleEvents\Api\Store;
use Automattic\WooCommerceSimpleEvents\Api\Types\Event;

/**
 * Cancel an event.
 *
 * Demonstrates composing a command's own `authorize()` method with an
 * attribute gate via the `$_preauthorized` slot. The class-level
 * `#[RequiresScope('events:edit')]` grants managers up front; the
 * `authorize()` method then widens that to also allow the event's own
 * organizer (who lacks `events:edit`). `$_preauthorized` carries the
 * attribute decision, so the method only does extra work when the
 * attribute alone wouldn't grant.
 */
#[Name( 'cancelEvent' )]
#[Description( 'Cancel an event. Allowed for managers (events:edit) or the event organizer.' )]
#[RequiresScope( 'events:edit' )]
class CancelEvent {
	/**
	 * @param int             $id             Identifier of the event to cancel.
	 * @param bool            $_preauthorized Whether the attribute gates already grant access.
	 * @param EventsPrincipal $_principal     The resolved request principal.
	 */
	public function authorize( int $id, bool $_preauthorized, EventsPrincipal $_principal ): bool {
		if ( $_preauthorized ) {
			return true;
		}
		if ( ! $_principal->is_authenticated() ) {
			return false;
		}
		$event = Store::get_event( $id );
		return null !== $event && $event->organizer_login === $_principal->user_login;
	}

	public function execute(
		#[Description( 'Identifier of the event to cancel.' )]
		int $id,
	): ?Event {
		return Store::cancel_event( $id );
	}
}
