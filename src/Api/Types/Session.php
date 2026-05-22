<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\Types;

use Automattic\WooCommerce\Api\Attributes\Description;
use Automattic\WooCommerceSimpleEvents\Api\Attributes\Team;
use Automattic\WooCommerceSimpleEvents\Api\Interfaces\ScheduledItem;

/**
 * A single talk/session within an event's programme.
 *
 * Implements the {@see ScheduledItem} interface (via the trait), so it
 * shares the `id` and `date` fields with {@see Event}. Carries a custom
 * `#[Team]` metadata marker at the type level — a plugin-defined metadata
 * category (see {@see Team}) discoverable through `_apiMetadata`.
 */
#[Description( 'A talk or session in an event programme.' )]
#[Team( 'events-platform' )]
class Session {
	use ScheduledItem;

	#[Description( 'Session title.' )]
	public string $title;

	#[Description( 'Name of the speaker, if announced.' )]
	public ?string $speaker;
}
