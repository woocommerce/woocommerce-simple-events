<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\Queries;

use Automattic\WooCommerce\Api\Attributes\ConnectionOf;
use Automattic\WooCommerce\Api\Attributes\Description;
use Automattic\WooCommerce\Api\Attributes\Experimental;
use Automattic\WooCommerce\Api\Attributes\Name;
use Automattic\WooCommerce\Api\Attributes\PublicAccess;
use Automattic\WooCommerce\Api\Pagination\Connection;
use Automattic\WooCommerce\Api\Pagination\Edge;
use Automattic\WooCommerce\Api\Pagination\PageInfo;
use Automattic\WooCommerce\Api\Pagination\PaginationParams;
use Automattic\WooCommerceSimpleEvents\Api\Store;
use Automattic\WooCommerceSimpleEvents\Api\Types\Event;

/**
 * Cursor-paginated list of events.
 *
 * Demonstrates a Relay-style connection: `#[ConnectionOf(Event::class)]`
 * on a query that returns a {@see Connection}, with `PaginationParams`
 * (which carries `#[Unroll]`) expanding into the standard
 * `first` / `last` / `after` / `before` arguments. The whole query is
 * also marked `#[Experimental]`, attaching an `experimental` metadata
 * entry and prefixing its description with `[Experimental] ` in
 * introspection.
 *
 * The connection is built over the full event set and handed to
 * `Connection::slice()`, which applies the cursor window; a real data
 * source would push the limits into the query and use
 * `Connection::pre_sliced()` instead.
 */
#[Name( 'eventsConnection' )]
#[Description( 'List events with cursor-based pagination.' )]
#[Experimental]
#[PublicAccess]
class ListEventsConnection {
	#[ConnectionOf( Event::class )]
	public function execute( PaginationParams $pagination ): Connection {
		$edges = array();
		foreach ( array_values( Store::all_events() ) as $event ) {
			$edge         = new Edge();
			$edge->cursor = base64_encode( (string) $event->id );
			$edge->node   = $event;
			$edges[]      = $edge;
		}

		$page_info                    = new PageInfo();
		$page_info->has_next_page     = false;
		$page_info->has_previous_page = false;
		$page_info->start_cursor      = $edges ? $edges[0]->cursor : null;
		$page_info->end_cursor        = $edges ? $edges[ count( $edges ) - 1 ]->cursor : null;

		$connection              = new Connection();
		$connection->edges       = $edges;
		$connection->nodes       = array_map( static fn( Edge $e ) => $e->node, $edges );
		$connection->page_info   = $page_info;
		$connection->total_count = count( $edges );

		return $connection->slice(
			array(
				'first'  => $pagination->first,
				'last'   => $pagination->last,
				'after'  => $pagination->after,
				'before' => $pagination->before,
			)
		);
	}
}
