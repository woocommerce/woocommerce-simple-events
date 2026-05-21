<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\Authorization;

use Automattic\WooCommerceSimpleEvents\Api\Attributes\RequiresScope;

/**
 * Trait that brings a class-level `#[RequiresScope('events:read')]` to
 * whatever type uses it. The dual-API engine walks the using class's
 * traits when collecting class-level authorization usages, so the
 * trait's attribute propagates into every field gate of the using type
 * without per-property repetition.
 *
 * `events:read` is the broad "any authenticated event consumer" scope —
 * granted to every role. Used by
 * {@see \Automattic\WooCommerceSimpleEvents\Api\Types\EventStats} to
 * gate the entire statistics type behind authentication, with
 * additional per-field scopes layered on top for the sensitive fields
 * (revenue_total, paid_attendees_count).
 */
#[RequiresScope( 'events:read' )]
trait EventReadableTrait {
}
