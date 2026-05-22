<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\Interfaces;

use Automattic\WooCommerce\Api\Attributes\Description;
use Automattic\WooCommerce\Api\Attributes\Name;
use Automattic\WooCommerce\Api\Attributes\ScalarType;
use Automattic\WooCommerceSimpleEvents\Api\Scalars\DateTime;

/**
 * Interface trait for anything that has an id and happens at a point in
 * time.
 *
 * Demonstrates an interface: a trait placed under `Interfaces/` and
 * marked with `#[Name]` / `#[Description]` becomes a GraphQL interface,
 * and any output type that `use`s the trait implements it. Here both
 * {@see \Automattic\WooCommerceSimpleEvents\Api\Types\Event} and
 * {@see \Automattic\WooCommerceSimpleEvents\Api\Types\Session} use it, so
 * a client can select the shared fields through the interface.
 */
#[Name( 'ScheduledItem' )]
#[Description( 'An object with an id that occurs at a specific date and time.' )]
trait ScheduledItem {
	#[Description( 'Numeric identifier.' )]
	public int $id;

	#[Description( 'Date and time the item occurs, as an ISO 8601 string.' )]
	#[ScalarType( DateTime::class )]
	public ?string $date;
}
