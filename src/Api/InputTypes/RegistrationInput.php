<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\InputTypes;

use Automattic\WooCommerce\Api\Attributes\Description;
use Automattic\WooCommerce\Api\InputTypes\TracksProvidedFields;
use Automattic\WooCommerceSimpleEvents\Api\Attributes\RequiresScope;

/**
 * Input type for {@see \Automattic\WooCommerceSimpleEvents\Api\Mutations\RegisterForEvent}.
 * The plain registration fields are public; the two sensitive ones are
 * gated input-side, exercising the new input-field authorization path.
 *
 * Gates fire only when a field is *provided*. Public callers can
 * register by sending `event_id` / `attendee_name` / `attendee_email`;
 * staff users additionally provide `paid_amount` (needs
 * `attendees:payment`) or `internal_notes` (needs
 * `events:internal_notes`) when relevant.
 */
#[Description( 'Data submitted to register an attendee for an event.' )]
class RegistrationInput {
	use TracksProvidedFields;

	#[Description( 'Identifier of the event being registered for.' )]
	public int $event_id;

	#[Description( 'Attendee name.' )]
	public string $attendee_name;

	#[Description( 'Attendee email.' )]
	public string $attendee_email;

	#[Description( 'Amount the attendee paid; only finance staff may set this on creation.' )]
	#[RequiresScope( 'attendees:payment' )]
	public ?float $paid_amount = null;

	#[Description( 'Internal notes attached to the registration; only managers may set them.' )]
	#[RequiresScope( 'events:internal_notes' )]
	public ?string $internal_notes = null;
}
