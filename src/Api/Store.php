<?php

declare(strict_types=1);

namespace Automattic\WooCommerceSimpleEvents\Api;

use Automattic\WooCommerceSimpleEvents\Api\Enums\EventStatus;
use Automattic\WooCommerceSimpleEvents\Api\Types\Attendee;
use Automattic\WooCommerceSimpleEvents\Api\Types\Event;
use Automattic\WooCommerceSimpleEvents\Api\Types\Session;

/**
 * Object-cache-backed store for the simple-events plugin.
 *
 * State (events, attendees, and the two id counters) lives in WordPress's
 * object cache under the `wcse` group, so mutations performed in one
 * request are visible to the next. The plugin assumes the host site has a
 * persistent object-cache backend wired in (Redis, Memcached, …); under
 * the default in-memory cache, state still works *within* a request but
 * is reset between requests — same observable behaviour as the previous
 * static-property implementation.
 *
 * Seeded on first access if the cache holds nothing yet:
 *  - id=1 "PHP Conference"  organized by `organizer`
 *  - id=2 "WordCamp Madrid" organized by `manager`
 *
 * Three attendees split across the two events. Re-seed by flushing the
 * object cache (`wp cache flush` from wp-cli).
 *
 * Concurrency: the read-modify-write sequence for mutations is not
 * atomic. Two concurrent registrations may share an id; acceptable for a
 * demo plugin, not for production.
 */
final class Store {
	private const GROUP            = 'wcse';
	private const KEY_EVENTS       = 'events';
	private const KEY_ATTENDEES    = 'attendees';
	private const KEY_NEXT_EVENT   = 'next_event_id';
	private const KEY_NEXT_ATTENDEE = 'next_attendee_id';

	/**
	 * Read every persisted slice in one pass, seeding the cache on first
	 * access so callers never see a partially-populated state.
	 *
	 * @return array{events: array<int, Event>, attendees: array<int, Attendee>, next_event_id: int, next_attendee_id: int}
	 */
	private static function load(): array {
		$events = wp_cache_get( self::KEY_EVENTS, self::GROUP );
		if ( false === $events ) {
			return self::seed();
		}
		return array(
			'events'           => is_array( $events ) ? $events : array(),
			'attendees'        => wp_cache_get( self::KEY_ATTENDEES, self::GROUP ) ?: array(),
			'next_event_id'    => (int) ( wp_cache_get( self::KEY_NEXT_EVENT, self::GROUP ) ?: 1 ),
			'next_attendee_id' => (int) ( wp_cache_get( self::KEY_NEXT_ATTENDEE, self::GROUP ) ?: 1 ),
		);
	}

	/**
	 * @return array{events: array<int, Event>, attendees: array<int, Attendee>, next_event_id: int, next_attendee_id: int}
	 */
	private static function seed(): array {
		$e1                  = new Event();
		$e1->id              = 1;
		$e1->name            = 'PHP Conference';
		$e1->description     = 'Annual gathering for PHP developers.';
		$e1->date            = '2026-09-15T09:00:00+00:00';
		$e1->status          = EventStatus::Published;
		$e1->raw_status      = 'published';
		$e1->venue           = 'Madrid, Spain';
		$e1->location        = 'Madrid, Spain';
		$e1->capacity        = 200;
		$e1->sessions        = self::make_sessions(
			array(
				array( 101, '2026-09-15T10:00:00+00:00', 'Keynote: the state of PHP', 'Ada Lovelace' ),
				array( 102, '2026-09-15T11:30:00+00:00', 'Async PHP in production', null ),
			)
		);
		$e1->organizer_login = 'organizer';
		$e1->sequence        = 1;
		$e1->waitlist_size   = 12;
		$e1->revenue         = 4980.0;
		$e1->internal_notes  = 'Renegotiate catering before invoicing.';

		$e2                  = new Event();
		$e2->id              = 2;
		$e2->name            = 'WordCamp Madrid';
		$e2->description     = 'Community meet-up for WordPress contributors.';
		$e2->date            = '2026-11-04T10:00:00+00:00';
		$e2->status          = EventStatus::Published;
		$e2->raw_status      = 'published';
		$e2->venue           = 'Madrid, Spain';
		$e2->location        = 'Madrid, Spain';
		$e2->capacity        = 350;
		$e2->sessions        = self::make_sessions(
			array(
				array( 201, '2026-11-04T11:00:00+00:00', 'Block themes deep dive', 'Grace Hopper' ),
			)
		);
		$e2->organizer_login = 'manager';
		$e2->sequence        = 2;
		$e2->waitlist_size   = 0;
		$e2->revenue         = 12250.5;
		$e2->internal_notes  = 'Confirm sponsor logos one week out.';

		$a1              = new Attendee();
		$a1->id          = 1;
		$a1->event_id    = 1;
		$a1->name        = 'Ada Lovelace';
		$a1->email       = 'ada@example.test';
		$a1->paid_amount = 75.0;

		$a2              = new Attendee();
		$a2->id          = 2;
		$a2->event_id    = 1;
		$a2->name        = 'Linus Torvalds';
		$a2->email       = 'linus@example.test';
		$a2->paid_amount = 75.0;

		$a3              = new Attendee();
		$a3->id          = 3;
		$a3->event_id    = 2;
		$a3->name        = 'Grace Hopper';
		$a3->email       = 'grace@example.test';
		$a3->paid_amount = 35.0;

		$state = array(
			'events'           => array( 1 => $e1, 2 => $e2 ),
			'attendees'        => array( 1 => $a1, 2 => $a2, 3 => $a3 ),
			'next_event_id'    => 3,
			'next_attendee_id' => 4,
		);
		self::persist( $state );
		return $state;
	}

	/**
	 * Build a list of {@see Session} objects from compact tuples.
	 *
	 * @param list<array{0:int,1:string,2:string,3:?string}> $rows Tuples of (id, ISO date, title, speaker).
	 * @return list<Session>
	 */
	private static function make_sessions( array $rows ): array {
		return array_map(
			static function ( array $row ): Session {
				$session          = new Session();
				$session->id      = $row[0];
				$session->date    = $row[1];
				$session->title   = $row[2];
				$session->speaker = $row[3];
				return $session;
			},
			$rows
		);
	}

	/**
	 * @param array{events: array<int, Event>, attendees: array<int, Attendee>, next_event_id: int, next_attendee_id: int} $state
	 */
	private static function persist( array $state ): void {
		wp_cache_set( self::KEY_EVENTS, $state['events'], self::GROUP );
		wp_cache_set( self::KEY_ATTENDEES, $state['attendees'], self::GROUP );
		wp_cache_set( self::KEY_NEXT_EVENT, $state['next_event_id'], self::GROUP );
		wp_cache_set( self::KEY_NEXT_ATTENDEE, $state['next_attendee_id'], self::GROUP );
	}

	/**
	 * @return array<int, Event>
	 */
	public static function all_events(): array {
		return self::load()['events'];
	}

	public static function get_event( int $id ): ?Event {
		return self::load()['events'][ $id ] ?? null;
	}

	/**
	 * @return list<Attendee>
	 */
	public static function attendees_for_event( int $event_id ): array {
		return array_values(
			array_filter(
				self::load()['attendees'],
				static fn( Attendee $a ): bool => $a->event_id === $event_id,
			)
		);
	}

	public static function add_attendee( int $event_id, string $name, string $email, ?float $paid_amount = null ): Attendee {
		$state = self::load();

		$attendee              = new Attendee();
		$attendee->id          = $state['next_attendee_id'];
		$attendee->event_id    = $event_id;
		$attendee->name        = $name;
		$attendee->email       = $email;
		$attendee->paid_amount = $paid_amount ?? 0.0;

		$state['attendees'][ $attendee->id ] = $attendee;
		++$state['next_attendee_id'];
		self::persist( $state );

		return $attendee;
	}

	/**
	 * Build the statistics for an event.
	 *
	 * The `$include_money` flag lets the caller skip the revenue-related
	 * aggregates when the query didn't select them — see how
	 * {@see \Automattic\WooCommerceSimpleEvents\Api\Queries\GetStatistics}
	 * derives it from the `$_query_info` selection tree. The skipped fields
	 * stay null; they are scope-gated anyway, so most callers never see them.
	 */
	public static function statistics_for_event( int $event_id, bool $include_money = true ): ?Types\EventStats {
		if ( null === self::get_event( $event_id ) ) {
			return null;
		}
		$attendees                   = self::attendees_for_event( $event_id );
		$stats                       = new Types\EventStats();
		$stats->event_id             = $event_id;
		$stats->attendees_total      = count( $attendees );
		$stats->revenue_total        = $include_money
			? array_sum( array_map( static fn( $a ) => $a->paid_amount, $attendees ) )
			: null;
		$stats->paid_attendees_count = $include_money
			? count( array_filter( $attendees, static fn( $a ) => $a->paid_amount > 0.0 ) )
			: null;
		return $stats;
	}

	/**
	 * Mark an event cancelled. Returns the updated event, or null when no
	 * event had that id.
	 */
	public static function cancel_event( int $id ): ?Event {
		$state = self::load();
		$event = $state['events'][ $id ] ?? null;
		if ( null === $event ) {
			return null;
		}
		$event->status     = EventStatus::Cancelled;
		$event->raw_status = 'cancelled';
		$state['events'][ $id ] = $event;
		self::persist( $state );
		return $event;
	}

	public static function update_event(
		int $id,
		?string $name,
		?int $capacity,
		?int $waitlist_size,
		?string $internal_notes
	): ?Event {
		$state = self::load();
		$event = $state['events'][ $id ] ?? null;
		if ( null === $event ) {
			return null;
		}
		if ( null !== $name ) {
			$event->name = $name;
		}
		if ( null !== $capacity ) {
			$event->capacity = $capacity;
		}
		if ( null !== $waitlist_size ) {
			$event->waitlist_size = $waitlist_size;
		}
		if ( null !== $internal_notes ) {
			$event->internal_notes = $internal_notes;
		}
		$state['events'][ $event->id ] = $event;
		self::persist( $state );
		return $event;
	}

	/**
	 * Delete an event by id.
	 *
	 * @return bool True when an event was removed, false when no event had that id.
	 */
	public static function delete_event( int $id ): bool {
		$state = self::load();
		if ( ! isset( $state['events'][ $id ] ) ) {
			return false;
		}
		unset( $state['events'][ $id ] );
		self::persist( $state );
		return true;
	}
}
