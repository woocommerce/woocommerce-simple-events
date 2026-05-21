<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\Types;

use Automattic\WooCommerce\Api\Attributes\Description;
use Automattic\WooCommerce\Api\Attributes\HiddenFromMetadataQuery;
use Automattic\WooCommerceSimpleEvents\Api\Attributes\OwnerOrScope;
use Automattic\WooCommerceSimpleEvents\Api\Attributes\RequiresScope;

/**
 * An event in the simple-events demo. Output-type fields exercise the
 * field-level authorization surface: most fields are public, but a few
 * sensitive ones are gated to specific scopes or to the event's
 * organizer.
 */
#[Description( 'An event open for registration.' )]
class Event {
	#[Description( 'Numeric identifier.' )]
	public int $id;

	#[Description( 'Human-readable name.' )]
	public string $name;

	#[Description( 'Detailed description shown on the public event page.' )]
	public string $description;

	#[Description( 'Start date / time as an ISO 8601 string.' )]
	public string $date;

	#[Description( 'Venue (free-form text).' )]
	public string $venue;

	#[Description( 'Maximum number of attendees the venue can accept.' )]
	public int $capacity;

	#[Description( 'Login of the user who created the event.' )]
	public string $organizer_login;

	/**
	 * Demonstrates the `$_parent` opt-in slot via {@see OwnerOrScope}:
	 * the event's own organizer can always read this; otherwise the
	 * caller needs the `events:waitlist` scope (held by `manager`).
	 *
	 * Declared **non-null** (`int`) on purpose: when the gate denies, the
	 * field's null has nowhere to land, so per the GraphQL spec it
	 * propagates up to the nearest nullable parent — the whole `event`
	 * object becomes null. Contrast with `revenue` below, which is
	 * nullable and degrades to a single null field instead.
	 */
	#[Description( 'Number of people on the waitlist for this event.' )]
	#[OwnerOrScope( 'events:waitlist' )]
	public int $waitlist_size;

	/**
	 * Demonstrates a straightforward scope-equality field gate. Only
	 * principals holding `events:revenue` (i.e. `finance`) can read it.
	 *
	 * Declared **nullable** (`?float`): when the gate denies, the field
	 * resolves to null while its siblings still resolve and the enclosing
	 * `event` object survives — the graceful-degradation pattern, and the
	 * recommended default for gated fields.
	 */
	#[Description( 'Total revenue collected from registrations for this event.' )]
	#[RequiresScope( 'events:revenue' )]
	public ?float $revenue;

	/**
	 * Demonstrates the per-target `_apiMetadata` opt-out via the stock
	 * `#[HiddenFromMetadataQuery]` marker. The runtime gate is supplied
	 * by `#[RequiresScope('events:internal_notes')]` and still fires
	 * (only `manager` can read); the marker independently keeps the
	 * field out of the discovery endpoint.
	 *
	 * Like `waitlist_size`, declared **non-null** (`string`), so a deny
	 * propagates the null up to the enclosing `event` object.
	 */
	#[Description( 'Free-form notes used internally by event managers.' )]
	#[RequiresScope( 'events:internal_notes' )]
	#[HiddenFromMetadataQuery]
	public string $internal_notes;
}
