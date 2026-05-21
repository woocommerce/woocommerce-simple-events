<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\Queries;

use Automattic\WooCommerce\Api\Attributes\Description;
use Automattic\WooCommerce\Api\Attributes\Name;
use Automattic\WooCommerce\Api\Attributes\PublicAccess;
use Automattic\WooCommerceSimpleEvents\Api\Store;
use Automattic\WooCommerceSimpleEvents\Api\Types\Event;

/**
 * Fetch a single event by id. The query itself is public; sensitive
 * fields on {@see Event} carry their own per-field gates.
 */
#[Name( 'event' )]
#[Description( 'Fetch a single event by id.' )]
#[PublicAccess]
class GetEvent {
	public function execute(
		#[Description( 'Identifier of the event to fetch.' )]
		int $id,
	): ?Event {
		return Store::get_event( $id );
	}
}
