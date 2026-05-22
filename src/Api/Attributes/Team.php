<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\Attributes;

use Attribute;
use Automattic\WooCommerce\Api\Attributes\Metadata;

/**
 * Plugin-defined metadata attribute: records the team that owns a schema
 * element.
 *
 * Demonstrates how a plugin ships its own metadata category without any
 * infrastructure change — subclass the core `#[Metadata]` attribute and
 * fix the entry name. Applying `#[Team('events-platform')]` attaches a
 * `team => 'events-platform'` entry, discoverable through the
 * `_apiMetadata` query alongside core's `internal` / `experimental`
 * categories.
 */
#[Attribute( Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE )]
final class Team extends Metadata {
	/**
	 * @param string $team Identifier of the owning team.
	 */
	public function __construct( string $team ) {
		parent::__construct( 'team', $team );
	}
}
