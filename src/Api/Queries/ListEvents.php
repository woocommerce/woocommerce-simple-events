<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\Queries;

use Automattic\WooCommerce\Api\Attributes\ArrayOf;
use Automattic\WooCommerce\Api\Attributes\Description;
use Automattic\WooCommerce\Api\Attributes\Name;
use Automattic\WooCommerce\Api\Attributes\PublicAccess;
use Automattic\WooCommerceSimpleEvents\Api\Store;
use Automattic\WooCommerceSimpleEvents\Api\Types\Event;

/**
 * List all events. The query is public, so anyone can browse; the
 * sensitive fields on each {@see Event} are gated per item — selecting
 * `revenue` across the list will succeed or deny each row independently
 * based on the caller's role.
 */
#[Name( 'events' )]
#[Description( 'List every event.' )]
#[PublicAccess]
class ListEvents {
	/**
	 * @return list<Event>
	 */
	#[ArrayOf( Event::class )]
	public function execute(): array {
		return array_values( Store::all_events() );
	}
}
