<?php
namespace MetaBox\CustomTable;

class Cache {
	private static function query_get( int $object_id, string $table ): array {
		global $wpdb;

		$row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $table WHERE `ID` = %d",
				$object_id
			),
			ARRAY_A
		);

		return is_array( $row ) ? $row : [];
	}

	/**
	 * Get a row
	 *
	 * @param int|string|null $object_id Row ID
	 * @param string          $table Table name
	 * @param bool            $force Force to get from DB, not from cache
	 *
	 * @return array
	 */
	public static function get( $object_id, string $table, bool $force = false ): array {
		global $wpdb;

		if ( ! $object_id ) {
			return [];
		}

		if ( $force ) {
			return self::query_get( $object_id, $table );
		}

		$row = wp_cache_get( $object_id, self::get_cache_group( $table ) );

		if ( false !== $row ) {
			return is_array( $row ) ? $row : [];
		}

		$row = self::query_get( $object_id, $table );

		self::set( $object_id, $table, $row );

		return $row;
	}

	/**
	 * Set a row to cache.
	 */
	public static function set( int $object_id, string $table, array $row ) {
		wp_cache_set( $object_id, $row, self::get_cache_group( $table ) );
	}

	public static function delete( ?int $object_id, string $table ) {
		if ( ! $object_id ) {
			return;
		}

		wp_cache_delete( $object_id, self::get_cache_group( $table ) );
	}

	private static function get_cache_group( string $table ): string {
		return "rwmb_{$table}_table_data";
	}
}
