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
	 * [--new-base-url=<newurl>]
	 * : Base url will be replaced by this option. If none given we'll try to guess it using the old database. If none is available we quit.
	 *
	 * ## EXAMPLES
	 *
	 *     wp dbsync production
	 *
	 * @when before_wp_load
	 *
	 * @param $args
	 * @param $assoc_args
	 */
	function __invoke( $args, $assoc_args ) {
		// Host is the first argument
		$host = escapeshellarg( $args[0] );

		// Just check if the user is sure he wants to whipe his whole database. Can be overriden bye --yes option
		WP_CLI::confirm(
			'Do you want to drop the current database and import a new one from ' . $host . '?',
			$assoc_args
		);

		// Read base url. If it's not available try to fall back on (soon to be removed) database siteurl
		$new_base_url = WP_CLI\Utils\get_flag_value( $assoc_args, 'new-base-url', false );

		if ( ! $new_base_url ) {
			// Decode JSON string we got from option
			$new_base_url = json_decode( exec( 'wp option get home --format=json' ) );

			// Show error and exit if there is no fallback found
			if ( ! $new_base_url ) {
				WP_CLI::error( 'No base url specified. No option to fallback on. Please specify --new-base-url' );
			}
		}

		WP_CLI::log( 'Syncing: ' . $host );

		// Reset database (does a DROP DATABASE and CREATE DATABASE)
		WP_CLI::log( exec( 'wp db reset --yes' ) );

		WP_CLI::log( 'Database has been reset. Doing import now.' );

		// Export external database to STDOUT and pipe it to local STDIN
		WP_CLI::log( exec( 'wp ssh db export - --host=' . $host . ' | wp db import -' ) );

		WP_CLI::log( 'Done importing. Doing search replace now.' );

		// Decode JSON string we got from option
		$remote_base_url = json_decode( exec( 'wp ssh option get home --format=json --host=' . $host ) );

		// Skip guid because it should never be changed.
		WP_CLI::log( exec( "wp search-replace '" . $remote_base_url . "' '" . $new_base_url . "' --skip-columns=guid" ) );
	}
}

WP_CLI::add_command( 'dbsync', __NAMESPACE__ . '\\DBSYNC_Command' );
