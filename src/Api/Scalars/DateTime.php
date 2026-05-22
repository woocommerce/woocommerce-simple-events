<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api\Scalars;

use Automattic\WooCommerce\Api\Attributes\Description;

/**
 * Custom scalar for ISO 8601 date/time values.
 *
 * Demonstrates a custom scalar: a class placed under `Scalars/` becomes a
 * GraphQL custom scalar. The two static methods are the contract the
 * engine calls — `serialize()` to render a PHP value for transport and
 * `parse()` to turn a client-supplied string back into a PHP value,
 * throwing `\InvalidArgumentException` (mapped to `INVALID_ARGUMENT` /
 * 400) on bad input. Apply it to a field with `#[ScalarType(DateTime::class)]`.
 */
#[Description( 'An ISO 8601 encoded date and time string.' )]
class DateTime {
	/**
	 * Serialize a PHP value to the scalar's transport format.
	 *
	 * @param mixed $value The value to serialize.
	 */
	public static function serialize( mixed $value ): string {
		if ( $value instanceof \DateTimeInterface ) {
			return $value->format( \DateTimeInterface::ATOM );
		}
		return (string) $value;
	}

	/**
	 * Parse a value received from a client (variable or literal).
	 *
	 * @param string $value The raw string value from the client.
	 * @throws \InvalidArgumentException When the value cannot be parsed as an ISO 8601 date/time string.
	 */
	public static function parse( string $value ): \DateTimeImmutable {
		try {
			return new \DateTimeImmutable( $value );
		} catch ( \Exception $e ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Serialized as JSON in the GraphQL error response.
			throw new \InvalidArgumentException( sprintf( 'Invalid ISO 8601 date/time: %s', $e->getMessage() ), 0, $e );
		}
	}
}
