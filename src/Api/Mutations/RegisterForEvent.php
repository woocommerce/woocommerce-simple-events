<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\Mutations;

use Automattic\WooCommerce\Api\ApiException;
use Automattic\WooCommerce\Api\Attributes\Description;
use Automattic\WooCommerce\Api\Attributes\Name;
use Automattic\WooCommerce\Api\Attributes\PublicAccess;
use Automattic\WooCommerceSimpleEvents\Api\InputTypes\RegistrationInput;
use Automattic\WooCommerceSimpleEvents\Api\Store;
use Automattic\WooCommerceSimpleEvents\Api\Types\Attendee;

/**
 * Public mutation. Anyone can register; the input-side gates on
 * {@see RegistrationInput} prevent unauthenticated callers from providing
 * `paid_amount` (finance) or `internal_notes` (manager).
 */
#[Name( 'registerForEvent' )]
#[Description( 'Register an attendee for an event.' )]
#[PublicAccess]
class RegisterForEvent {
	public function execute( RegistrationInput $input ): Attendee {
		if ( null === Store::get_event( $input->event_id ) ) {
			throw new ApiException( sprintf( 'Unknown event id %d.', $input->event_id ) );
		}
		return Store::add_attendee(
			$input->event_id,
			$input->attendee_name,
			$input->attendee_email,
			$input->was_provided( 'paid_amount' ) ? $input->paid_amount : null,
		);
	}
}
