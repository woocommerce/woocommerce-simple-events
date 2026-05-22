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
 *
 * Demonstrates the `$_query_info` infrastructure parameter: the resolver
 * passes the current selection tree, and the query skips the (notionally
 * expensive) revenue aggregates when the client didn't select them. The
 * tree's top-level keys are the selected field names of the result.
 */
#[Name( 'statistics' )]
#[Description( 'Aggregate statistics for an event. Sensitive fields are gated to finance.' )]
#[PublicAccess]
class GetStatistics {
	public function execute(
		#[Description( 'Identifier of the event to summarise.' )]
		int $event_id,
		?array $_query_info = null,
	): ?EventStats {
		$include_money = ! is_array( $_query_info )
			|| isset( $_query_info['revenue_total'] )
			|| isset( $_query_info['paid_attendees_count'] );

		return Store::statistics_for_event( $event_id, $include_money );
	}
}
