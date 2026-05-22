<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\Enums;

use Automattic\WooCommerce\Api\Attributes\Description;

/**
 * Lifecycle status of an event.
 *
 * Demonstrates an enum type: a class placed under `Enums/` becomes a
 * GraphQL enum, with case names converted from PascalCase to
 * SCREAMING_SNAKE_CASE (e.g. `Published` becomes `PUBLISHED`). The
 * trailing `Other` case mirrors WooCommerce core's convention for
 * keeping forward compatibility with values a plugin might add: the
 * type carries a `raw_status` string alongside the enum so callers can
 * still read the underlying value when the enum reports `OTHER`.
 */
#[Description( 'The lifecycle status of an event.' )]
enum EventStatus: string {
	#[Description( 'Created but not yet open for registration.' )]
	case Draft = 'draft';

	#[Description( 'Open for registration.' )]
	case Published = 'published';

	#[Description( 'Called off; no further registrations are accepted.' )]
	case Cancelled = 'cancelled';

	#[Description( 'Already took place.' )]
	case Completed = 'completed';

	#[Description( 'A status not covered by the standard values. Inspect raw_status for the underlying value.' )]
	case Other = 'other';
}
