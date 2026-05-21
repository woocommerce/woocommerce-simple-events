<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\Mutations;

use Automattic\WooCommerce\Api\ApiException;
use Automattic\WooCommerce\Api\Attributes\Description;
use Automattic\WooCommerce\Api\Attributes\Name;
use Automattic\WooCommerce\Api\Attributes\PublicAccess;
use Automattic\WooCommerceSimpleEvents\Api\InputTypes\UpdateEventInput;
use Automattic\WooCommerceSimpleEvents\Api\Store;
use Automattic\WooCommerceSimpleEvents\Api\Types\Event;

/**
 * Patch mutation. The mutation class itself is public; the manager-only
 * gate lives on {@see UpdateEventInput} at the class level. Anonymous
 * callers can syntactically invoke the mutation, but the input-side
 * gate fires for every provided input field — so any non-empty input
 * requires the `manager` role.
 */
#[Name( 'updateEvent' )]
#[Description( 'Patch an existing event.' )]
#[PublicAccess]
class UpdateEvent {
	public function execute(
		#[Description( 'Identifier of the event to patch.' )]
		int $id,
		UpdateEventInput $input,
	): Event {
		$event = Store::update_event(
			$id,
			$input->was_provided( 'name' ) ? $input->name : null,
			$input->was_provided( 'capacity' ) ? $input->capacity : null,
			$input->was_provided( 'waitlist_size' ) ? $input->waitlist_size : null,
			$input->was_provided( 'internal_notes' ) ? $input->internal_notes : null,
		);
		if ( null === $event ) {
			throw new ApiException( sprintf( 'Unknown event id %d.', $id ) );
		}
		return $event;
	}
}
