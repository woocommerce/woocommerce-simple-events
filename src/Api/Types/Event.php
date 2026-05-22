<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\Types;

use Automattic\WooCommerce\Api\Attributes\ArrayOf;
use Automattic\WooCommerce\Api\Attributes\Deprecated;
use Automattic\WooCommerce\Api\Attributes\Description;
use Automattic\WooCommerce\Api\Attributes\HiddenFromMetadataQuery;
use Automattic\WooCommerce\Api\Attributes\Ignore;
use Automattic\WooCommerce\Api\Attributes\Internal;
use Automattic\WooCommerceSimpleEvents\Api\Enums\EventStatus;
use Automattic\WooCommerceSimpleEvents\Api\Interfaces\ScheduledItem;
use Automattic\WooCommerceSimpleEvents\Api\Attributes\OwnerOrScope;
use Automattic\WooCommerceSimpleEvents\Api\Attributes\RequiresScope;

/**
 * An event open for registration. Exercises a broad slice of the
 * infrastructure:
 *
 *  - Implements the {@see ScheduledItem} interface (via the trait), which
 *    contributes the `id` and (custom-scalar) `date` fields.
 *  - Carries an `EventStatus` enum field plus a `raw_status` escape hatch.
 *  - Holds a list of nested {@see Session} objects (`#[ArrayOf]` with a
 *    class element type).
 *  - Demonstrates `#[Deprecated]`, `#[Ignore]`, and the `#[Internal]`
 *    metadata marker.
 *  - Keeps the field-level authorization gates (`waitlist_size`,
 *    `revenue`, `internal_notes`) that the granular-auth work introduced.
 */
#[Description( 'An event open for registration.' )]
class Event {
	use ScheduledItem;

	#[Description( 'Human-readable name.' )]
	public string $name;

	#[Description( 'Detailed description shown on the public event page.' )]
	public string $description;

	#[Description( 'Current lifecycle status.' )]
	public EventStatus $status;

	#[Description( 'Raw status string as stored. Useful when status is OTHER.' )]
	public string $raw_status;

	#[Description( 'Venue (free-form text).' )]
	public string $venue;

	/**
	 * Deprecated alias of {@see self::$venue}. Demonstrates `#[Deprecated]`:
	 * the field stays in the schema but is flagged with a reason in
	 * introspection so clients migrate off it.
	 */
	#[Description( 'Where the event takes place.' )]
	#[Deprecated( 'Use venue instead.' )]
	public string $location;

	#[Description( 'Maximum number of attendees the venue can accept.' )]
	public int $capacity;

	#[Description( 'Talks scheduled for this event.' )]
	#[ArrayOf( Session::class )]
	public array $sessions;

	#[Description( 'Login of the user who created the event.' )]
	public string $organizer_login;

	/**
	 * Internal ordering hint. Demonstrates the `#[Internal]` metadata
	 * marker: the field is still queryable, but is tagged `internal` in
	 * `_apiMetadata` and its description is prefixed with `[Internal] `.
	 */
	#[Description( 'Internal sort order.' )]
	#[Internal]
	public int $sequence;

	/**
	 * Demonstrates the `$_parent` opt-in slot via {@see OwnerOrScope}: the
	 * event's own organizer can always read this; otherwise the caller
	 * needs the `events:waitlist` scope (held by `manager`).
	 *
	 * Declared non-null on purpose: when the gate denies, the null
	 * propagates up to the nearest nullable parent — the whole `event`
	 * object becomes null. Contrast with `revenue` below.
	 */
	#[Description( 'Number of people on the waitlist for this event.' )]
	#[OwnerOrScope( 'events:waitlist' )]
	public int $waitlist_size;

	/**
	 * Straightforward scope-equality field gate; only principals holding
	 * `events:revenue` (finance) can read it. Nullable, so a deny degrades
	 * to a single null field rather than nulling the whole object.
	 */
	#[Description( 'Total revenue collected from registrations for this event.' )]
	#[RequiresScope( 'events:revenue' )]
	public ?float $revenue;

	/**
	 * Gated at runtime by `#[RequiresScope('events:internal_notes')]` and
	 * independently hidden from the `_apiMetadata` discovery channel by
	 * `#[HiddenFromMetadataQuery]`.
	 */
	#[Description( 'Free-form notes used internally by event managers.' )]
	#[RequiresScope( 'events:internal_notes' )]
	#[HiddenFromMetadataQuery]
	public string $internal_notes;

	/**
	 * Server-only bookkeeping never exposed in the schema. Demonstrates
	 * `#[Ignore]`: the property exists on the PHP object but the builder
	 * skips it entirely, so it appears in neither the schema nor
	 * introspection.
	 */
	#[Ignore]
	public ?string $audit_token = null;
}
