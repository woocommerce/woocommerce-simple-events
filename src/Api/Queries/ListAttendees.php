<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\Queries;

use Automattic\WooCommerce\Api\Attributes\ArrayOf;
use Automattic\WooCommerce\Api\Attributes\Description;
use Automattic\WooCommerce\Api\Attributes\Name;
use Automattic\WooCommerce\Api\Attributes\PublicAccess;
use Automattic\WooCommerceSimpleEvents\Api\Store;
use Automattic\WooCommerceSimpleEvents\Api\Types\Attendee;

/**
 * List the attendees for a given event. The query itself is public so the
 * per-field gates on {@see Attendee} can be exercised across roles:
 * organizers see names and emails, finance sees what each attendee paid,
 * anonymous callers see only the public id / event_id pair.
 */
#[Name( 'attendees' )]
#[Description( 'List attendees for an event.' )]
#[PublicAccess]
class ListAttendees {
	/**
	 * @return list<Attendee>
	 */
	#[ArrayOf( Attendee::class )]
	public function execute(
		#[Description( 'Identifier of the event whose attendees to list.' )]
		int $event_id,
	): array {
		return Store::attendees_for_event( $event_id );
	}
}
