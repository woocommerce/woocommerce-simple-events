<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\Mutations;

use Automattic\WooCommerce\Api\Attributes\Description;
use Automattic\WooCommerce\Api\Attributes\Name;
use Automattic\WooCommerceSimpleEvents\Api\Attributes\RequiresScope;
use Automattic\WooCommerceSimpleEvents\Api\Store;

/**
 * Delete an event. Unlike {@see UpdateEvent} — which is `#[PublicAccess]`
 * and relies on its input type's gate — this mutation is gated at the
 * *operation* level: `#[RequiresScope('events:edit')]` on the class itself
 * (a scope held only by `manager`) means an unauthorized caller is denied
 * before `execute()` runs, with a bare authorization error (no `subject`
 * payload). This is the operation-level counterpart to the field-, input-,
 * and type-level gates the other operations demonstrate.
 */
#[Name( 'deleteEvent' )]
#[Description( 'Delete an event. Requires the events:edit scope (manager).' )]
#[RequiresScope( 'events:edit' )]
class DeleteEvent {
	/**
	 * @return bool True when an event was removed, false when no event had that id.
	 */
	public function execute(
		#[Description( 'Identifier of the event to delete.' )]
		int $id,
	): bool {
		return Store::delete_event( $id );
	}
}
