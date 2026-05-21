<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\Types;

use Automattic\WooCommerce\Api\Attributes\Description;
use Automattic\WooCommerceSimpleEvents\Api\Attributes\RequiresScope;
use Automattic\WooCommerceSimpleEvents\Api\Authorization\EventReadableTrait;

/**
 * Aggregate statistics for a single event. Exercises type-level + per-
 * field authorization composition:
 *
 *  - The class-level gate comes from {@see EventReadableTrait} — every
 *    field gate AND-composes with `#[RequiresScope('events:read')]`, so
 *    any authenticated event consumer (`organizer`, `manager`,
 *    `finance`) can read at least the public summary fields.
 *  - The sensitive fields `revenue_total` and `paid_attendees_count`
 *    add their own per-field scope on top; the resulting AND grants
 *    only to `finance`, the role that holds both scopes.
 */
#[Description( 'Aggregate statistics for an event.' )]
class EventStats {
	use EventReadableTrait;

	#[Description( 'Identifier of the event the statistics summarise.' )]
	public ?int $event_id;

	#[Description( 'Total number of registered attendees.' )]
	public ?int $attendees_total;

	#[Description( 'Total revenue collected from registrations.' )]
	#[RequiresScope( 'events:revenue' )]
	public ?float $revenue_total;

	#[Description( 'Number of attendees whose paid_amount is greater than zero.' )]
	#[RequiresScope( 'attendees:payment' )]
	public ?int $paid_attendees_count;
}
