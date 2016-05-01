# WP-CLI DB Sync
*Remote to local database import tool for wp-cli*

This is a wp-cli command version of [this gist](https://gist.github.com/timneutkens/32c9bcd7d8576e663958ed4b1389c1dc)

## Installation
`composer require timneutkens/wp-cli-dbsync:^1.0.0`

## Configuration
This command uses [wp-cli-ssh](https://github.com/xwp/wp-cli-ssh).

To get started use their instructions to setup a remote host:

Add an `ssh` section to your `wp-cli.yml`/`wp-cli.local.yml`, as seen in the [sample config](wp-cli.sample.yml).
You indicate the `ssh` command templates for each host you want to connect to. The template variable `%cmd%` is 
replaced with the full command to run on the server; the `%pseudotty%` template variable is replaced 
with `-t`/`-T` depending on whether you're on a TTY or piping the command output.

For a step-by-step guide, please refer to the [wiki](https://github.com/x-team/wp-cli-ssh/wiki/Configuring-the-plugin).


Now you can run the following command:

`wp dbsync <host here>`

The command will import the database. After that it will search replace the old home url to the new home url using `wp search-replace`.
The new home url will be guessed using `wp option get home`.
Beware that if you define `WP_HOME` this command will return that url instead of the url set in the `wp_options` table.
Optionally you can provide `--new-base-url=http://example.com` to override this behaviour.

Replace `<host here>` with the host you just setup in your `wp-cli.yml`/`wp-cli.local.yml`.
