<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\InputTypes;

use Automattic\WooCommerce\Api\Attributes\Description;
use Automattic\WooCommerce\Api\InputTypes\TracksProvidedFields;
use Automattic\WooCommerceSimpleEvents\Api\Attributes\RequiresScope;

/**
 * Input type for {@see \Automattic\WooCommerceSimpleEvents\Api\Mutations\UpdateEvent}.
 * Class-level `#[RequiresScope('events:edit')]` makes the whole input
 * editor-only — every field's input-side gate AND-composes with the
 * class-level gate, so providing any property of this input requires
 * the `events:edit` scope (held only by `manager`). The mutation class
 * itself is public; this input class carries the authorization.
 */
#[Description( 'Patchable fields on an event.' )]
#[RequiresScope( 'events:edit' )]
class UpdateEventInput {
	use TracksProvidedFields;

	#[Description( 'New event name.' )]
	public ?string $name = null;

	#[Description( 'New capacity for the venue.' )]
	public ?int $capacity = null;

	#[Description( 'New waitlist size.' )]
	public ?int $waitlist_size = null;

	#[Description( 'Replacement internal notes.' )]
	public ?string $internal_notes = null;
}
