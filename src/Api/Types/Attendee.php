<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\Types;

use Automattic\WooCommerce\Api\Attributes\Description;
use Automattic\WooCommerceSimpleEvents\Api\Attributes\RequiresScope;

/**
 * A registration record. The whole type is gated to authenticated
 * event consumers by a class-level `#[RequiresScope('events:read')]`
 * applied directly — so anonymous callers see nothing on an attendee,
 * not even the public identifier pair. Contrast with
 * {@see \Automattic\WooCommerceSimpleEvents\Api\Types\EventStats},
 * which gets the same scope through a trait; both patterns are
 * equivalent at the build level.
 *
 * The per-field gates layered on top of that floor restrict PII to
 * `attendees:read_pii` (organizer + manager) and payment to
 * `attendees:payment` (finance), AND-composed with the type-level
 * `events:read` requirement.
 *
 * Per-item gating naturally falls out of resolving a list of
 * attendees — each item's field gates are evaluated independently.
 */
#[Description( 'A registration for an event.' )]
#[RequiresScope( 'events:read' )]
class Attendee {
	#[Description( 'Numeric identifier.' )]
	public ?int $id;

	#[Description( 'Identifier of the event this attendee registered for.' )]
	public ?int $event_id;

	#[Description( 'Attendee name as captured during registration.' )]
	#[RequiresScope( 'attendees:read_pii' )]
	public ?string $name;

	#[Description( 'Attendee email captured during registration.' )]
	#[RequiresScope( 'attendees:read_pii' )]
	public ?string $email;

	#[Description( 'Amount the attendee paid for their ticket.' )]
	#[RequiresScope( 'attendees:payment' )]
	public ?float $paid_amount;
}
