<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\Queries;

use Automattic\WooCommerce\Api\Attributes\Description;
use Automattic\WooCommerce\Api\Attributes\Name;
use Automattic\WooCommerce\Api\Attributes\PublicAccess;
use Automattic\WooCommerceSimpleEvents\Api\Store;
use Automattic\WooCommerceSimpleEvents\Api\Types\EventStats;

/**
 * Public query returning an {@see EventStats} for the given event. The
 * query itself is `#[PublicAccess]` so the type-level (trait-supplied)
 * gate on `EventStats` is exercised independently of any class-level
 * gate on the query.
 */
#[Name( 'statistics' )]
#[Description( 'Aggregate statistics for an event. Every field is manager-only via a trait-level gate.' )]
#[PublicAccess]
class GetStatistics {
	public function execute(
		#[Description( 'Identifier of the event to summarise.' )]
		int $event_id,
	): ?EventStats {
		return Store::statistics_for_event( $event_id );
	}
}
