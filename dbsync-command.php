<?php namespace timneutkens\WP_CLI_DBSYNC;

use WP_CLI;
use WP_CLI_Command;

/**
 * Sync database using simple shell pipe
 */
class DBSYNC_Command extends WP_CLI_Command {
	/**
	 * Sync database from remote to local
	 *
	 * ## OPTIONS
	 *
	 * <remote>
	 * : Name of the configured ssh remote.
	 *
	 * ## EXAMPLES
	 *
	 *     wp dbsync production
	 *
	 * @when before_wp_load
	 */
	function __invoke( $args ) {
		$host = $args[0];

		WP_CLI::log( 'Syncing: ' . $host );
		WP_CLI::log( exec( 'wp ssh db export - --host=' . escapeshellarg( $host ) . ' | wp db import -' ) );
	}
}

\WP_CLI::add_command( 'dbsync', __NAMESPACE__ . '\\DBSYNC_Command' );
