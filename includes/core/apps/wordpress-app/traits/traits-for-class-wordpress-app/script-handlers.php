<?php
/**
 * Trait:
 * Contains functions that check if ssh was successful as well as replacing tokens in the scripts before they are sent to the server.
 * Used only by the class-wordpress.php file which defines the
 * WPCD_WORDPRESS_APP class.
 *
 * @package wpcd
 */

/**
 * Trait wpcd_wpapp_script_handlers
 */
trait wpcd_wpapp_script_handlers {

	/**
	 * This interprets the ssh result and reduces to a boolean value to indicate if a command succeeded or failed.
	 *
	 * @param string $result result.
	 * @param string $command command.
	 * @param string $action action.
	 */
	public function is_ssh_successful( $result, $command, $action = '' ) {

		// If $result is not a string, return false since we can't do a proper compare to anything.
		if ( ! is_string( $result ) ) {
			return false;
		}

		switch ( $command ) {
			case 'disable_remove_site.txt':
				$return =
				( str_contains( $result, ' has been ' ) )
				||
				( str_contains( $result, ' local backups have been ' ) );
				break;
			case 'manage_https.txt':
				$return =
				( str_contains( $result, 'SSL has been ' ) )
				||
				( str_contains( $result, 'SSL Already Enabled' ) )
				||
				( str_contains( $result, 'SSL is already disabled for' ) )
				||
				( str_contains( $result, 'http2 is already enabled for domain' ) )
				||
				( str_contains( $result, 'http2 enabled for domain' ) )
				||
				( str_contains( $result, 'http2 disabled for domain' ) )
				||
				( str_contains( $result, 'http2 is already disabled for domain' ) )
				||
				( str_contains( $result, 'Successfully received certificate' ) )
				||
				( str_contains( $result, 'certificate has been successfully installed' ) );

				break;
			case 'add_remove_sftp.txt':
				$return =
				( 'sftp-add-user' === $action && str_contains( $result, 'Added SFTP user ' ) )
				||
				( 'sftp-remove-user' === $action && str_contains( $result, 'Removed SFTP user ' ) )
				||
				( 'sftp-change-password' === $action && str_contains( $result, 'Password changed for ' ) )
				||
				( 'sftp-remove-key' === $action && str_contains( $result, 'Public key removed for ' ) )
				||
				( 'sftp-remove-password' === $action && str_contains( $result, 'Password removed for ' ) )
				||
				( 'sftp-set-key' === $action && str_contains( $result, 'Public key set for ' ) );
				break;
			case 'manage_site_users.txt':
				$return =
				( 'site-user-change-password' === $action && str_contains( $result, 'Password changed for ' ) )
				||
				( 'site-user-remove-key' === $action && str_contains( $result, 'Public key removed for ' ) )
				||
				( 'site-user-remove-password' === $action && str_contains( $result, 'Password removed for ' ) )
				||
				( 'site-user-set-key' === $action && str_contains( $result, 'Public key set for ' ) );
				break;
			case 'basic_auth_misc.txt':
				$return =
				( str_contains( $result, 'Basic authentication disabled for' ) )
				||
				( str_contains( $result, 'Basic authentication enabled for' ) )
				||
				( str_contains( $result, 'Basic auth is already enabled' ) );
				break;
			case 'basic_auth_wplogin_misc.txt':
				$return =
				( str_contains( $result, 'Basic authentication disabled for' ) )
				||
				( str_contains( $result, 'Basic authentication enabled for' ) )
				||
				( str_contains( $result, 'Wp Admin auth is already enabled' ) );
				break;
			case 'toggle_https_misc.txt':
				$return =
				( str_contains( $result, 'HTTPS redirect disabled for' ) )
				||
				( str_contains( $result, 'HTTPS redirect enabled for' ) )
				||
				( str_contains( $result, 'SSL redirection is already disabled for' ) );
				break;
			case 'toggle_wp_linux_cron_misc.txt':
				$return =
				( str_contains( $result, 'System cron enabled for' ) )
				||
				( str_contains( $result, 'System cron disabled for' ) );
				break;
			case 'toggle_password_auth_misc.txt':
				$return =
				( str_contains( $result, 'SSH password auth has been enabled for user' ) )
				||
				( str_contains( $result, 'SSH password auth has been disabled for user' ) );
				break;
			case 'change_php_version_misc.txt':
				$return =
				( str_contains( $result, 'PHP version changed to' ) )
				||
				( str_contains( $result, 'PHP version remains at' ) );
				break;
			case 'change_php_option_misc.txt':
				$return = str_contains( $result, 'Successfully changed PHP value' );
				break;
			case 'toggle_php_active_misc.txt':
				$return =
				( str_contains( $result, 'has been disabled' ) )
				||
				( str_contains( $result, 'already disabled' ) )
				||
				( str_contains( $result, 'has been enabled' ) )
				||
				( str_contains( $result, 'already enabled' ) );
				break;
			case 'backup_restore.txt':
				$return =
				( str_contains( $result, 'Backup has been completed!' ) )
				||
				( str_contains( $result, 'has been restored' ) );
				break;
			case 'backup_restore_schedule.txt':
				$return =
				( str_contains( $result, 'Backup job configured!' ) )
				||
				( str_contains( $result, 'Backup job removed!' ) )
				||
				( str_contains( $result, 'Full backup job configured!' ) )
				||
				( str_contains( $result, 'Full backup job removed!' ) );
				break;
			case 'backup_restore_save_credentials.txt':
				$return = ( str_contains( $result, 'AWS credentials have been saved' ) );
				break;
			case 'change_domain_quick.txt':
				$return = ( str_contains( $result, 'changed to' ) );
				break;
			case 'change_domain_full.txt':
				$return =
				( str_contains( $result, 'changed to' ) )
				||
				( str_contains( $result, 'Dry run completed' ) );
				break;
			case 'clone_site.txt':
				$return = ( str_contains( $result, 'has been cloned' ) );
				break;
			case 'manage_phpmyadmin.txt':
				$return =
				( str_contains( $result, 'phpMyAdmin installed for' ) )
				||
				( str_contains( $result, 'phpMyAdmin updated for' ) )
				||
				( str_contains( $result, 'Access credentials have been updated' ) )
				||
				( str_contains( $result, 'phpMyAdmin has been removed for' ) );
				break;
			case 'manage_database_operation.txt':
				$return =
				( str_contains( $result, 'Mysql host is already set to localhost' ) )
				||
				( str_contains( $result, 'Database has been switched to' ) )
				||
				( str_contains( $result, 'Database has been copied' ) );
				break;
			case 'manage_tinyfilemanager.txt':
				$return =
				( str_contains( $result, 'Filemanager installed for' ) )
				||
				( str_contains( $result, 'FileManager updated for' ) )
				||
				( str_contains( $result, 'Access credentials have been updated' ) )
				||
				( str_contains( $result, 'FileManager has been removed for' ) );
				break;
			case '6g_firewall.txt':
				$return =
				( str_contains( $result, 'Enabled 6G Firewall' ) )
				||
				( str_contains( $result, 'Disabled 6G Firewall' ) );
				break;
			case '7g_firewall.txt':
				$return =
				( str_contains( $result, 'Enabled 7G Firewall' ) )
				||
				( str_contains( $result, 'Disabled 7G Firewall' ) );
				break;
			case 'manage_nginx_pagecache.txt':
				$return =
				( str_contains( $result, 'WordPress Cache has been enabled' ) )
				||
				( str_contains( $result, 'WordPress Cache has been disabled' ) )
				||
				( str_contains( $result, 'WordPress Cache has been cleared' ) );
				break;
			case 'toggle_wp_debug.txt':
				$return =
				( str_contains( $result, 'WordPress debug flags enabled' ) )
				||
				( str_contains( $result, 'WordPress debug flags disabled' ) );
				break;
			case 'multisite.txt':
				$return =
				( str_contains( $result, 'WordPress Multisite has been enabled for' ) )
				||
				( str_contains( $result, 'configuration has been set up' ) )
				||
				( str_contains( $result, 'has been deregistered' ) )
				||
				( str_contains( $result, 'SSL enabled for' ) )
				||
				( str_contains( $result, 'SSL is already disabled for' ) )
				||
				( str_contains( $result, 'HTTPS disabled for' ) );
				break;
			case 'multisite_wildcard_ssl.txt':
				$return =
				( str_contains( $result, 'Wildcard HTTPS has been configured for' ) )
				||
				( str_contains( $result, 'HTTPS disabled for' ) )
				||
				( str_contains( $result, 'SSL is already disabled for' ) );
				break;
			case 'site_sync_origin_setup.txt':
				$return =
				( str_contains( $result, 'Authentication is already set up' ) )
				||
				( str_contains( $result, 'Authentication has been set up' ) );
				break;
			case 'site_sync_destination_setup.txt':
				$return =
				( str_contains( $result, 'Setup has been completed' ) );
				break;
			case 'site_sync.txt':
				$return =
				( str_contains( $result, 'Site Sync Completed Successfully' ) )
				||
				( str_contains( $result, 'MT Site Sync Completed Successfully' ) )
				||
				( str_contains( $result, 'Site sync has been scheduled' ) );
				break;
			case 'site_sync_unschedule.txt':
				$return =
				( str_contains( $result, 'Site sync job removed' ) )
				||
				( str_contains( $result, 'Schedule Site Sync For This Site Disabled' ) )
				||
				( str_contains( $result, 'No such job configured with given domain and destination ip' ) )
				||
				( str_contains( $result, 'No syncing job is configured as cron' ) );
				break;
			case 'enable_disable_php_functions.txt':
				$return =
				( str_contains( $result, 'has been enabled' ) )
				||
				( str_contains( $result, 'has been disabled' ) );
				break;
			case 'reset_site_permissions.txt':
				$return =
				( str_contains( $result, 'Permissions have been reset for' ) );
				break;
			case 'server_redirect.txt':
				// Even though this name has "server" in it, it's mostly a site-level item.
				$return =
				( str_contains( $result, 'Redirect rule added' ) )
				||
				( str_contains( $result, 'Redirect rule has been removed' ) )
				||
				( str_contains( $result, 'All Rewrite rules have been removed' ) );
				break;
			case 'nginx_options.txt':
			case 'ols_options.txt':
				// This one is a mix of server and site level items - mostly site level items.
				$return =
				( str_contains( $result, 'already enabled' ) )
				||
				( str_contains( $result, 'already disabled' ) )
				||
				( str_contains( $result, 'Success!' ) );
				break;
			case 'ols_manage_admin_console.txt':
				$return =
				( str_contains( $result, 'Set OpenLiteSpeed Web Admin access' ) )
				||
				( str_contains( $result, 'OpenLiteSpeed WebAdmin password not changed' ) )
				||
				( str_contains( $result, 'Enabled OLS/LSWS admin port on firewall!' ) )
				||
				( str_contains( $result, 'Disabled OLS/LSWS admin port on firewall!' ) );
				break;
			case 'php_workers.txt':
				$return =
				( str_contains( $result, 'PHP Workers Updated' ) );
				break;
			case 'fail2ban_site.txt':
				// There is also a fail2ban section in the servers section below!
				$return =
				( str_contains( $result, 'Fail2ban plugin has been installed for' ) )
				||
				( str_contains( $result, 'Fail2ban Plugin has been removed from' ) );
				break;
			case 'reliable_updates.txt':
				$return =
				( str_contains( $result, 'Updates are complete' ) );
				break;
			case 'copy_site_to_existing_site.txt':
				$return =
				( str_contains( $result, 'Copy to existing site is complete' ) );
				break;
			case 'change_file_upload_size.txt':
				$return =
				( str_contains( $result, 'File upload limits have been changed for' ) );
				break;
			case 'update_wp_site_option.txt':
				$return =
				( str_contains( $result, 'Updated Option Value' ) );
				break;
			case 'change_wp_credentials.txt':
				$return =
				( str_contains( $result, 'Updated credentials for user' ) );
				break;
			case 'add_wp_user.txt':
				$return =
				( str_contains( $result, 'Added user' ) );
				break;
			case 'update_wp_config_option.txt':
				$return =
				( str_contains( $result, 'Updated WPConfig Option Value' ) );
				break;
			case 'passwordless_login.txt':
				// for this one we just want to make sure that the last line has a string that starts with http:
				list($url_array[]) = array_slice( explode( PHP_EOL, trim( $result ) ), -1, 1 );
				$return            =
				( str_contains( $url_array[0], 'http://' ) )
				||
				( str_contains( $url_array[0], 'https://' ) );
				break;
			case 'git_control_site_command.txt':
			case 'git_control_site.txt':
				$return =
				( str_contains( $result, 'Git Init Complete For Domain' ) )
				||
				( str_contains( $result, 'Git has been removed from' ) )
				||
				( str_contains( $result, 'Git sync succeeded' ) )
				||
				( str_contains( $result, 'Git branch switch and checkout succeeded' ) )
				||
				( str_contains( $result, 'Git create new branch and checkout succeeded' ) )
				||
				( str_contains( $result, 'Git commit and push succeeded' ) )
				||
				( str_contains( $result, 'Git tag and push succeeded' ) )
				||
				( str_contains( $result, 'Git pull tag succeeded' ) )
				||
				( str_contains( $result, 'Git fetch tag succeeded' ) )
				||
				( str_contains( $result, 'Version folder has been removed for' ) )
				||
				( str_contains( $result, 'All version folders have been removed for' ) )
				||
				( str_contains( $result, 'Git switch version succeeded' ) )
				||
				( str_contains( $result, 'Git credentials successfully set up for domain' ) )
				||
				( str_contains( $result, 'Git clone successful' ) )
				||
				( str_contains( $result, 'Multi-tenant: Fetch version succeeded' ) )
				||
				( str_contains( $result, 'Multi-tenant: Site conversion succeeded' ) );
				break;
			case 'mt_clone_site.txt':
				$return = ( str_contains( $result, 'has been cloned' ) && str_contains( $result, 'Git tag and push succeeded' ) && str_contains( $result, 'Multi-tenant: Fetch version succeeded' ) );
				break;
			case 'mt_convert_site.txt':
				$return = ( str_contains( $result, 'Multi-tenant: Site conversion succeeded for' ) );
				break;
			case 'renew_all_certificates.txt':
				$return = ( str_contains( $result, 'Certificate renewal attempt completed' ) );
				break;
			case 'manage_logtivity.txt':
				$return =
				( str_contains( $result, 'Logtivity installed and license activated' ) )
				||
				( str_contains( $result, 'Logtivity license activated' ) )
				||
				( str_contains( $result, 'Logtivity has been removed' ) );
				$return = $return && ( ! str_contains( $result, 'Please provide a valid API key' ) ); // If the string 'Please provide a valid API key' is in the output, the thing has failed.
				break;
			case 'manage_solidwp_security.txt':
				$return =
				( str_contains( $result, 'Solidwp installed and license activated' ) )
				||
				( str_contains( $result, 'Solidwp license activated' ) )
				||
				( str_contains( $result, 'Solidwp has been removed' ) );
				break;

			/**************************************************************
			* The items below this are SERVER items, not APP items        *
			*/
			case 'backup_restore_delete_and_prune_server.txt':
				$return =
				( str_contains( $result, 'All backups have been deleted' ) )
				||
				( str_contains( $result, 'All backups older than' ) );
				break;
			case 'install_memcached.txt':
				$return =
				( str_contains( $result, 'Memcached has been installed' ) )
				||
				( str_contains( $result, 'Memcached is already installed' ) );
				break;
			case 'manage_memcached.txt':
				$return =
				( str_contains( $result, 'Memcached server has been restarted' ) )
				||
				( str_contains( $result, 'Memcached cache has been cleared' ) )
				||
				( str_contains( $result, 'Memcached has been enabled' ) )
				||
				( str_contains( $result, 'Memcached has been disabled' ) )
				||
				( str_contains( $result, 'Memcached has been removed from the system' ) );
				break;
			case 'install_redis.txt':
				$return =
				( str_contains( $result, 'Redis has been installed' ) )
				||
				( str_contains( $result, 'Redis is already installed' ) );
				break;
			case 'manage_redis.txt':
				$return =
				( str_contains( $result, 'Redis server has been restarted' ) )
				||
				( str_contains( $result, 'Redis cache has been cleared' ) )
				||
				( str_contains( $result, 'Redis has been enabled' ) )
				||
				( str_contains( $result, 'Redis has been disabled' ) )
				||
				( str_contains( $result, 'Redis has been removed from the system' ) );
				break;
			case 'add_wp_admin.txt':
				$return =
				( str_contains( $result, 'added as an administrator to' ) );
				break;
			case 'restart_php_service.txt':
				$return =
				( str_contains( $result, 'PHP service has restarted for version' ) );
				break;
			case 'toggle_edd_nginx_rules.txt':
				$return =
				( str_contains( $result, 'Easy Digital Downloads NGINX directives enabled for' ) )
				||
				( str_contains( $result, 'Easy Digital Downloads NGINX directives disabled for' ) );
				break;
			case 'email_gateway.txt':
				$return =
				( str_contains( $result, 'The email gateway has now been configured' ) )
				||
				( str_contains( $result, 'Test email has been sent' ) )
				||
				( str_contains( $result, 'Email gateway successfully removed' ) );
				break;
			case 'run_upgrades_290.txt':
			case 'run_upgrades_460.txt':
			case 'run_upgrades_461.txt':
			case 'run_upgrades_462.txt':
			case 'run_upgrades_530.txt':
				$return =
				( str_contains( $result, 'upgrade completed' ) )
				||
				( str_contains( $result, 'Upgrade Completed' ) )
				||
				( str_contains( $result, '7G Firewall is already installed' ) );
				break;
			case 'run_upgrade_install_php_81.txt':
				$return = ( str_contains( $result, 'PHP 8.1 has been installed' ) );
				break;
			case 'run_upgrade_install_php_82.txt':
				$return = ( str_contains( $result, 'PHP 8.2 has been installed' ) );
				break;
			case 'run_upgrade_install_php_83.txt':
				$return = ( str_contains( $result, 'PHP 8.3 has been installed' ) );
				break;
			case 'run_upgrade_install_php_84.txt':
				$return = ( str_contains( $result, 'PHP 8.4 has been installed' ) );
				break;
	
			case 'run_upgrade_install_old_php_version.txt':
				$return = ( str_contains( $result, 'has been installed' ) );
				break;
			case 'run_upgrade_7g.txt':
				$return = ( str_contains( $result, 'The 7G Firewall has been upgraded' ) );
				break;
			case 'run_remove_6g.txt':
				$return = ( str_contains( $result, 'The 6G Firewall has been removed' ) );
				break;
			case 'run_upgrade_wpcli.txt':
				$return = ( str_contains( $result, 'WPCLI has been upgraded' ) );
				break;
			case 'run_upgrade_install_php_intl.txt':
				$return = ( str_contains( $result, 'PHP intl module has been installed' ) );
				break;
			case 'run_upgrade_cache_enabler_nginx_config.txt':
				$return = ( str_contains( $result, 'Cache Enabler NGINX Config Has Been Upgraded' ) );
				break;
			case 'server_status_callback.txt':
				$return =
				( str_contains( $result, 'Server status job configured' ) )
				||
				( str_contains( $result, 'Server status job removed' ) )
				||
				( str_contains( $result, 'Server status job scheduled successfully' ) )
				||
				( str_contains( $result, 'Server status job executed successfully' ) );
				break;
			case 'maldet.txt':
				$return =
				( str_contains( $result, 'Maldet has been installed' ) )
				||
				( str_contains( $result, 'LMD is already installed!' ) )
				||
				( str_contains( $result, 'clamscan and LMD uninstalled' ) )
				||
				( str_contains( $result, 'Clamscan database has been updated' ) )
				||
				( str_contains( $result, 'Malware Detection has been updated' ) )
				||
				( str_contains( $result, 'Scanning has been completed' ) )
				||
				( str_contains( $result, 'Cron has been disabled' ) )
				||
				( str_contains( $result, 'Cron has been enabled' ) )
				||
				( str_contains( $result, 'Malware data has been purged' ) )
				||
				( str_contains( $result, 'Malware services have been restarted' ) );
				break;
			case 'server_restart_callback.txt':
				$return =
				( str_contains( $result, 'Server restart callback job configured' ) )
				||
				( str_contains( $result, 'Server restart callback job removed' ) )
				||
				( str_contains( $result, 'Server restart callback job executed successfully' ) );
				break;
			case 'monitorix.txt':
				$return =
				( str_contains( $result, 'Monitorix has been installed' ) )
				||
				( str_contains( $result, 'Monitorix has been removed' ) )
				||
				( str_contains( $result, 'Monitorix has been updated' ) )
				||
				( str_contains( $result, 'has been enabled for' ) )
				||
				( str_contains( $result, 'has been disabled for' ) )
				||
				( str_contains( $result, 'SSL has been enabled for' ) )
				||
				( str_contains( $result, 'SSL is already disabled for' ) )
				||
				( str_contains( $result, 'SSL has been disabled for' ) );
				break;
			case 'netdata_install.txt':
				$return =
				( str_contains( $result, 'Netdata has been installed' ) )
				||
				( str_contains( $result, 'Netdata is already installed' ) );
				break;
			case 'netdata.txt':
				$return =
				( str_contains( $result, 'Netdata has been installed' ) )
				||
				( str_contains( $result, 'Netdata has been removed' ) )
				||
				( str_contains( $result, 'Netdata has been updated' ) )
				||
				( str_contains( $result, 'Basic Auth has been enabled for' ) )
				||
				( str_contains( $result, 'Basic Auth already enabled' ) )
				||
				( str_contains( $result, 'Basic Auth has been disabled' ) )
				||
				( str_contains( $result, 'Basic Auth has been updated' ) )
				||
				( str_contains( $result, 'SSL has been enabled for' ) )
				||
				( str_contains( $result, 'SSL was not enabled for netdata so nothing to disable' ) )
				||
				( str_contains( $result, 'SSL has been disabled for' ) )
				||
				( str_contains( $result, 'Registry already enabled ' ) )
				||
				( str_contains( $result, 'Registry enabled to' ) )
				||
				( str_contains( $result, 'Registry already pointed to ' ) )
				||
				( str_contains( $result, 'Registry pointed to' ) );
				break;
			case 'monit.txt':
				$return =
				( str_contains( $result, 'Monit has been installed' ) )
				||
				( str_contains( $result, 'Monit has been removed' ) )
				||
				( str_contains( $result, 'Monit has been updated' ) )
				||
				( str_contains( $result, 'has been enabled' ) )
				||
				( str_contains( $result, 'has been disabled' ) )
				||
				( str_contains( $result, 'SSL has been enabled for' ) )
				||
				( str_contains( $result, 'SSL has been disabled for' ) )
				||
				( str_contains( $result, 'SSL is already disabled for' ) )
				||
				( str_contains( $result, 'Monit email settings updated' ) )
				||
				( str_contains( $result, 'All monitors enabled' ) )
				||
				( str_contains( $result, 'All monitors disabled' ) )
				||
				( str_contains( $result, 'Callbacks have been enabled' ) )
				||
				( str_contains( $result, 'Callbacks have been disabled' ) )
				||
				( str_contains( $result, 'Monit has been activated' ) )
				||
				( str_contains( $result, 'Monit has been temporarily deactivated' ) );
				break;
			case 'schedule_server_reboot.txt':
				$return =
				( str_contains( $result, 'The server reboot has been scheduled' ) );
				break;
			case 'backup_config_files.txt':
				$return =
				( str_contains( $result, 'Backup cron job has been configured' ) )
				||
				( str_contains( $result, 'Cron for conf backup has been removed' ) )
				||
				( str_contains( $result, 'Backup files have been removed' ) );
				break;
			case 'goaccess.txt':
				$return =
				( str_contains( $result, 'goaccess is already installed' ) )
				||
				( str_contains( $result, 'Goaccess has been installed' ) )
				||
				( str_contains( $result, 'goaccess has been removed' ) )
				||
				( str_contains( $result, 'Goaccess has been disabled' ) )
				||
				( str_contains( $result, 'goaccess has been enabled' ) )
				||
				( str_contains( $result, 'SSL Already Enabled' ) )
				||
				( str_contains( $result, 'SSL has been enabled for' ) )
				||
				( str_contains( $result, 'SSL has been disabled' ) )
				||
				( str_contains( $result, 'SSL Not enabled for' ) )
				||
				( str_contains( $result, 'Basic Auth already enabled' ) )
				||
				( str_contains( $result, 'Basic auth has been enabled' ) )
				||
				( str_contains( $result, 'Basic Auth already disabled' ) )
				||
				( str_contains( $result, 'Auth has been updated' ) )
				||
				( str_contains( $result, 'whitelisted' ) )
				||
				( str_contains( $result, 'removed from whitelist' ) )
				||
				( str_contains( $result, 'is not whitelisted' ) )
				||
				( str_contains( $result, 'All whiteslited ips has been removed' ) );
				break;
			case 'fail2ban.txt':
				$return =
				( str_contains( $result, 'Fail2ban installation complete' ) )
				||
				( str_contains( $result, 'fail2ban has been removed' ) )
				||
				( str_contains( $result, 'fail2ban has been purged' ) )
				||
				( str_contains( $result, 'Fail2ban parameters have been successfully updated' ) )
				||
				( str_contains( $result, 'Protocol has been added' ) )
				||
				( str_contains( $result, 'The specified protocol has been removed' ) )
				||
				( str_contains( $result, 'The protocol was not enabled and therefore could not be removed' ) )
				||
				( str_contains( $result, 'Fail2ban parameters have been successfully updated' ) )
				||
				( str_contains( $result, 'Fail2ban software has been successfully updated' ) )
				||
				( str_contains( $result, 'has been unbanned' ) )
				||
				( str_contains( $result, 'has been banned' ) );
				break;
			case 'server_update.txt':
				$return =
				( str_contains( $result, 'Updates have been scheduled to run via cron' ) )
				||
				( str_contains( $result, 'Security Updates have been scheduled to run via cron' ) );
				break;
			case 'server_php_version.txt':
				$return =
				( str_contains( $result, 'Server level PHP version has been updated to' ) );
				break;
			case 'git_control_server.txt':
				$return =
				( str_contains( $result, 'Git has been installed' ) )
				||
				( str_contains( $result, 'Git has been updated' ) );
				break;
			case 'ubuntu_pro_activate.txt':
				$return =
				( str_contains( $result, 'Ubuntu Pro token has been applied to this server' ) );
				break;
			case 'ubuntu_pro_actions.txt':
				$return =
				( str_contains( $result, 'This machine is not attached to an Ubuntu Pro subscription' ) )
				||
				( str_contains( $result, 'Ubuntu Pro token has been removed from this server' ) );
				break;

			/**
			 *************************************************************
			 * The items below this are SERVER SYNC items, not APP items.
			 **************************************************************
			 */
			case 'server_sync_origin_setup.txt':
				$return =
				( str_contains( $result, 'Setup has been finished for this server. But you are not done yet' ) );
				break;
			case 'server_sync_destination_setup.txt':
				$return =
				( str_contains( $result, 'Setup has been completed!' ) );
				break;
			case 'server_sync_manage.txt':
				$return =
				( str_contains( $result, 'The syncronization job has been started' ) )
				||
				( str_contains( $result, 'The scheduled sync job has been disabled' ) )
				||
				( str_contains( $result, 'The scheduled sync job has been re-enabled' ) )
				||
				( str_contains( $result, 'The sync service has been permanently removed' ) );
				break;

		}

		/* Sometimes we get a false positive so check for some things that might indicate a generic failure. */
		if ( $return ) {
			$return = $return
				&&
				( ! str_contains( $result, 'dpkg was interrupted, you must manually run' ) )
				&&
				( ! str_contains( $result, 'Installation of required packages failed' ) );
		}
		if ( $return && ( false === boolval( wpcd_get_option( 'wordpress_app_ignore_journalctl_xe' ) ) ) ) {
			$return = $return
				&&
				( ! str_contains( $result, 'journalctl -xe' ) );
		}

		return apply_filters( 'wpcd_is_ssh_successful', $return, $result, $command, $action, $this->get_app_name() );

	}

	/**
	 * Different scripts needs different placeholders/handling.
	 *
	 * Filter Hook: wpcd_script_placeholders_{$this->get_app_name()}
	 *
	 * @param array  $array              The array of placeholders, usually empty but since this is the first param, its the one returned as the modified value.
	 * @param string $script_name        Script_name.
	 * @param string $script_version     The version of script to be used.
	 * @param array  $instance           Various pieces of data about the server or app being used. It can use the following keys. post_id: the ID of the post.
	 * @param string $command            The command being constructed.
	 * @param array  $additional         An array of any additional data we might need. It can use the following keys (non-exhaustive list):
	 *    command: The command to use (a script may have multiple commands)
	 *    domain: The domain of the site
	 *    user: The user to action.
	 *    email: The email to use.
	 *    public_key: The path to the public key
	 *    password: The password of the user.
	 */
	public function script_placeholders( $array, $script_name, $script_version, $instance, $command, $additional ) {
		$new_array    = array();
		$common_array = array(
			'SCRIPT_COMMON_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/9999-common-functions.txt',
			'SCRIPT_COMMON_NAME' => '9999-common-functions.sh',
		);
		switch ( $script_name ) {
			case 'after-server-create-run-commands.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'           => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/01-prepare_server.txt',
						'SCRIPT_NAME'          => '01-prepare_server.sh',
						'SCRIPT_LOGS'          => "{$this->get_app_name()}_prepare_server",
						'CALLBACK_URL'         => $this->get_command_url( $instance['post_id'], 'prepare_server', 'completed' ),
						'LONG_COMMAND_TIMEOUT' => wpcd_get_long_running_command_timeout(),
					),
					$common_array,
					$additional
				);
				break;
			case 'install_wordpress_site.txt':
				$command_name = $additional['command'];
				$new_array    = array_merge(
					array(
						'SIX_G_COMMANDS_URL'           => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/6G-Firewall-OLS.txt',
						'SCRIPT_SIX_G_COMMANDS_NAME'   => '6G-Firewall-OLS.txt',
						'SEVEN_G_COMMANDS_URL'         => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/7G-Firewall-OLS.txt',
						'SCRIPT_SEVEN_G_COMMANDS_NAME' => '7G-Firewall-OLS.txt',
						'SCRIPT_URL'                   => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/02-install_wordpress_site.txt',
						'SCRIPT_NAME'                  => '02-install_wordpress_site.sh',
						'SCRIPT_LOGS'                  => "{$this->get_app_name()}_{$command_name}",
						'CALLBACK_URL'                 => $this->get_command_url( $instance['post_id'], $command_name, 'completed' ),
					),
					$common_array,
					$additional
				);
				break;
			case 'disable_remove_site.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/03-disable-remove-site.txt',
						'SCRIPT_NAME' => '03-disable-remove-site.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'manage_https.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/04-manage_https.txt',
						'SCRIPT_NAME' => '04-manage_https.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'add_remove_sftp.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/06-add_remove_sftp.txt',
						'SCRIPT_NAME' => '06-add_remove_sftp.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'manage_site_users.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/11-manage_site_users.txt',
						'SCRIPT_NAME' => '11-manage_site_users.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'backup_restore.txt':
				$command_name = $additional['command'];
				$new_array    = array_merge(
					array(
						'SCRIPT_URL'   => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/08-backup.txt',
						'SCRIPT_NAME'  => '08-backup.sh',
						'SCRIPT_LOGS'  => "{$this->get_app_name()}_{$command_name}",
						'CALLBACK_URL' => $this->get_command_url( $instance['app_id'], $command_name, 'completed' ),
					),
					$common_array,
					$additional
				);
				break;
			case 'backup_restore_delete_and_prune.txt':
				$command_name = $additional['command'];
				$new_array    = array_merge(
					array(
						'SCRIPT_URL'   => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/08-backup.txt',
						'SCRIPT_NAME'  => '08-backup.sh',
						'SCRIPT_LOGS'  => "{$this->get_app_name()}_{$command_name}",
						'CALLBACK_URL' => $this->get_command_url( $instance['app_id'], $command_name, 'completed' ),
					),
					$common_array,
					$additional
				);
				break;
			case 'backup_restore_schedule.txt':
			case 'backup_restore_save_credentials.txt':
			case 'backup_restore_refresh_backup_list.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/08-backup.txt',
						'SCRIPT_NAME' => '08-backup.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'basic_auth_misc.txt':
			case 'basic_auth_wplogin_misc.txt':
			case 'toggle_https_misc.txt':
			case 'toggle_wp_linux_cron_misc.txt':
			case 'change_php_version_misc.txt':
			case 'get_diskspace_used_misc.txt':
			case 'change_php_option_misc.txt':
			case 'toggle_wp_debug.txt':
			case 'restart_php_service.txt':
			case 'toggle_password_auth_misc.txt':
			case 'toggle_php_active_misc.txt':
			case 'renew_all_certificates.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/10-misc.txt',
						'SCRIPT_NAME' => '10-misc.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'change_domain_quick.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/05-change_domain.txt',
						'SCRIPT_NAME' => '05-change_domain.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'change_domain_full.txt':
				$command_name = $additional['command'];
				$new_array    = array_merge(
					array(
						'SCRIPT_URL'   => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/05-change_domain.txt',
						'SCRIPT_NAME'  => '05-change_domain.sh',
						'SCRIPT_LOGS'  => "{$this->get_app_name()}_{$command_name}",
						'CALLBACK_URL' => $this->get_command_url( $instance['app_id'], $command_name, 'completed' ),
					),
					$common_array,
					$additional
				);
				break;
			case 'search_and_replace_db.txt':
				$command_name = $additional['command'];
				$new_array    = array_merge(
					array(
						'SCRIPT_URL'   => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/31-search_and_replace_db.txt',
						'SCRIPT_NAME'  => '31-search_and_replace_db.sh',
						'SCRIPT_LOGS'  => "{$this->get_app_name()}_{$command_name}",
						'CALLBACK_URL' => $this->get_command_url( $instance['app_id'], $command_name, 'completed' ),
					),
					$common_array,
					$additional
				);
				break;
			case 'clone_site.txt':
				$command_name = $additional['command'];
				$new_array    = array_merge(
					array(
						'SCRIPT_URL'   => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/09-clone_site.txt',
						'SCRIPT_NAME'  => '09-clone_site.sh',
						'SCRIPT_LOGS'  => "{$this->get_app_name()}_{$command_name}",
						'CALLBACK_URL' => $this->get_command_url( $instance['app_id'], $command_name, 'completed' ),
					),
					$common_array,
					$additional
				);
				break;
			case 'manage_phpmyadmin.txt':
				$command_name = $additional['command'];
				$new_array    = array_merge(
					array(
						'SCRIPT_URL'   => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/07-phpmyadmin.txt',
						'SCRIPT_NAME'  => '07-phpmyadmin.sh',
						'SCRIPT_LOGS'  => "{$this->get_app_name()}_{$command_name}",
						'CALLBACK_URL' => $this->get_command_url( $instance['app_id'], $command_name, 'completed' ),
					),
					$common_array,
					$additional
				);
				break;
			case 'manage_database_operation.txt':
				$command_name = $additional['command'];
				$new_array    = array_merge(
					array(
						'SCRIPT_URL'   => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/53-database-operation.txt',
						'SCRIPT_NAME'  => '53-database-operation.sh',
						'SCRIPT_LOGS'  => "{$this->get_app_name()}_{$command_name}",
						'CALLBACK_URL' => $this->get_command_url( $instance['app_id'], $command_name, 'completed' ),
					),
					$common_array,
					$additional
				);
				break;
			case 'manage_tinyfilemanager.txt':
				$command_name = $additional['command'];
				$new_array    = array_merge(
					array(
						'SCRIPT_URL'   => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/57-tinyfilemanager.txt',
						'SCRIPT_NAME'  => '57-tinyfilemanager.sh',
						'SCRIPT_LOGS'  => "{$this->get_app_name()}_{$command_name}",
						'CALLBACK_URL' => $this->get_command_url( $instance['app_id'], $command_name, 'completed' ),
					),
					$common_array,
					$additional
				);
				break;
			case '6g_firewall.txt':
				$new_array = array_merge(
					array(
						'SIX_G_COMMANDS_URL'         => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/6G-Firewall-OLS.txt',
						'SCRIPT_SIX_G_COMMANDS_NAME' => '6G-Firewall-OLS.txt',
						'SCRIPT_URL'                 => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/15-6g_firewall.txt',
						'SCRIPT_NAME'                => '15-6g_firewall.sh',
					),
					$common_array,
					$additional
				);
				break;
			case '7g_firewall.txt':
				$new_array = array_merge(
					array(
						'SEVEN_G_COMMANDS_URL'         => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/7G-Firewall-OLS.txt',
						'SCRIPT_SEVEN_G_COMMANDS_NAME' => '7G-Firewall-OLS.txt',
						'SCRIPT_URL'                   => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/40-7g_firewall.txt',
						'SCRIPT_NAME'                  => '40-7g_firewall.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'manage_nginx_pagecache.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/18-wp_cache.txt',
						'SCRIPT_NAME' => '18-wp_cache.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'add_wp_admin.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/10-misc.txt',
						'SCRIPT_NAME' => '10-misc.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'toggle_edd_nginx_rules.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/17-plugin_tweaks.txt',
						'SCRIPT_NAME' => '17-plugin_tweaks.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'multisite.txt':
			case 'multisite_wildcard_ssl.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/13-multisite.txt',
						'SCRIPT_NAME' => '13-multisite.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'site_sync_origin_setup.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/81-origin-site-sync.txt',
						'SCRIPT_NAME' => '81-origin-site-sync.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'site_sync_destination_setup.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/82-destination-site-sync.txt',
						'SCRIPT_NAME' => '82-destination-site-sync.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'site_sync.txt':
				$command_name = $additional['command'];
				$new_array    = array_merge(
					array(
						'SCRIPT_URL'   => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/81-origin-site-sync.txt',
						'SCRIPT_NAME'  => '81-origin-site-sync.sh',
						'SCRIPT_LOGS'  => "{$this->get_app_name()}_{$command_name}",
						'CALLBACK_URL' => $this->get_command_url( $instance['app_id'], $command_name, 'completed' ),
					),
					$common_array,
					$additional
				);
				break;
			case 'site_sync_unschedule.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/81-origin-site-sync.txt',
						'SCRIPT_NAME' => '81-origin-site-sync.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'enable_disable_php_functions.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/22-enable_disable_php_functions.txt',
						'SCRIPT_NAME' => '22-enable_disable_php_functions.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'reset_site_permissions.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/10-misc.txt',
						'SCRIPT_NAME' => '10-misc.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'server_redirect.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/33-server_redirect.txt',
						'SCRIPT_NAME' => '33-server_redirect.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'nginx_options.txt':
				// This one is a mix of server and site level items - mostly site level items.
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/34-nginx_options.txt',
						'SCRIPT_NAME' => '34-nginx_options.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'ols_options.txt':
				// This one is a mix of server and site level items - mostly site level items.
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/55-ols_options.txt',
						'SCRIPT_NAME' => '55-ols_options.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'ols_manage_admin_console.txt':
				// This one is a mix of server and site level items - mostly site level items.
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/56-ols_manage_admin_console.txt',
						'SCRIPT_NAME' => '56-ols_manage_admin_console.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'php_workers.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/39-php_workers.txt',
						'SCRIPT_NAME' => '39-php_workers.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'fail2ban_site.txt':
				// There is also a fail2ban section in the servers section below!
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/23-fail2ban.txt',
						'SCRIPT_NAME' => '23-fail2ban.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'reliable_updates.txt':
				$command_name = $additional['command'];
				$new_array    = array_merge(
					array(
						'SCRIPT_URL'         => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/50-reliable_updates.txt',
						'SCRIPT_NAME'        => '50-reliable_updates.sh',
						'SCRIPT_LOGS'        => "{$this->get_app_name()}_{$command_name}",
						'CALLBACK_URL'       => $this->get_command_url( $instance['app_id'], $command_name, 'completed' ),
						'SCRIPT_URL_BACKUP'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/08-backup.txt',
						'SCRIPT_NAME_BACKUP' => '08-backup.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'copy_site_to_existing_site.txt':
				$command_name = $additional['command'];
				$new_array    = array_merge(
					array(
						'SCRIPT_URL'         => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/25-copy_site_to_existing_site.txt',
						'SCRIPT_NAME'        => '25-copy_site_to_existing_site.sh',
						'SCRIPT_LOGS'        => "{$this->get_app_name()}_{$command_name}",
						'CALLBACK_URL'       => $this->get_command_url( $instance['app_id'], $command_name, 'completed' ),
						'SCRIPT_URL_BACKUP'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/08-backup.txt',
						'SCRIPT_NAME_BACKUP' => '08-backup.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'change_file_upload_size.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/10-misc.txt',
						'SCRIPT_NAME' => '10-misc.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'update_wp_site_option.txt':
			case 'change_wp_credentials.txt':
			case 'add_wp_user.txt':
			case 'update_wp_config_option.txt':
			case 'passwordless_login.txt';
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/30-wp_site_things.txt',
						'SCRIPT_NAME' => '30-wp_site_things.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'git_control_site_command.txt':
				$command_name = $additional['command'];
				$new_array    = array_merge(
					array(
						'SCRIPT_URL'   => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/58-git_control.txt',
						'SCRIPT_NAME'  => '58-git_control.sh',
						'SCRIPT_LOGS'  => "{$this->get_app_name()}_{$command_name}",
						'CALLBACK_URL' => $this->get_command_url( $instance['app_id'], $command_name, 'completed' ),
					),
					$common_array,
					$additional
				);
				break;
			case 'git_control_site.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/58-git_control.txt',
						'SCRIPT_NAME' => '58-git_control.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'mt_clone_site.txt':
				$command_name = $additional['command'];
				$new_array    = array_merge(
					array(
						'SCRIPT_URL'   => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/09-clone_site.txt',
						'SCRIPT_NAME'  => '09-clone_site.sh',
						'SCRIPT_URL2'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/58-git_control.txt',
						'SCRIPT_NAME2' => '58-git_control.sh',
						'SCRIPT_LOGS'  => "{$this->get_app_name()}_{$command_name}",
						'CALLBACK_URL' => $this->get_command_url( $instance['app_id'], $command_name, 'completed' ),
					),
					$common_array,
					$additional
				);
				break;
			case 'mt_convert_site.txt':
				$command_name = $additional['command'];
				$new_array    = array_merge(
					array(
						'SCRIPT_URL'   => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/58-git_control.txt',
						'SCRIPT_NAME'  => '58-git_control.sh',
						'SCRIPT_LOGS'  => "{$this->get_app_name()}_{$command_name}",
						'CALLBACK_URL' => $this->get_command_url( $instance['app_id'], $command_name, 'completed' ),
					),
					$common_array,
					$additional
				);
				break;
			case 'manage_logtivity.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/52-logtivity.txt',
						'SCRIPT_NAME' => '52-logtivity.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'manage_solidwp_security.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/51-solidsecurity.txt',
						'SCRIPT_NAME' => '51-solidsecurity.sh',
					),
					$common_array,
					$additional
				);
				break;

			/*********************************************************
			* The items below this are SERVER items, not APP items   *
			*/
			case 'backup_restore_delete_and_prune_server.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/08-backup.txt',
						'SCRIPT_NAME' => '08-backup.sh',
					),
					$common_array,
					$additional
				);
				break;

			case 'install_memcached.txt':
				$command_name = $additional['command'];
				$new_array    = array_merge(
					array(
						'SCRIPT_URL'   => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/16-memcached.txt',
						'SCRIPT_NAME'  => '16-memcached.sh',
						'SCRIPT_LOGS'  => "{$this->get_app_name()}_{$command_name}",
						'CALLBACK_URL' => $this->get_command_url( $instance['server_id'], $command_name, 'completed' ),
					),
					$common_array,
					$additional
				);
				break;
			case 'manage_memcached.txt':
				$command_name = $additional['command'];
				$new_array    = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/16-memcached.txt',
						'SCRIPT_NAME' => '16-memcached.sh',
					),
					$common_array,
					$additional
				);
				break;

			case 'install_redis.txt':
				$command_name = $additional['command'];
				$new_array    = array_merge(
					array(
						'SCRIPT_URL'   => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/12-redis.txt',
						'SCRIPT_NAME'  => '12-redis.sh',
						'SCRIPT_LOGS'  => "{$this->get_app_name()}_{$command_name}",
						'CALLBACK_URL' => $this->get_command_url( $instance['server_id'], $command_name, 'completed' ),
					),
					$common_array,
					$additional
				);
				break;
			case 'manage_redis.txt':
				$command_name = $additional['command'];
				$new_array    = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/12-redis.txt',
						'SCRIPT_NAME' => '12-redis.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'email_gateway.txt':
				$command_name = $additional['command'];
				$new_array    = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/14-mail.txt',
						'SCRIPT_NAME' => '14-mail.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'monitorix.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/21-monitorix.txt',
						'SCRIPT_NAME' => '21-monitorix.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'netdata_install.txt':
				$command_name = $additional['command'];
				$new_array    = array_merge(
					array(
						'SCRIPT_URL'   => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/43-netdata.txt',
						'SCRIPT_NAME'  => '43-netdata.sh',
						'SCRIPT_LOGS'  => "{$this->get_app_name()}_{$command_name}",
						'CALLBACK_URL' => $this->get_command_url( $instance['server_id'], $command_name, 'completed' ),
					),
					$common_array,
					$additional
				);
				break;
			case 'netdata.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/43-netdata.txt',
						'SCRIPT_NAME' => '43-netdata.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'monit.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/20-monit.txt',
						'SCRIPT_NAME' => '20-monit.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'run_upgrades_290.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/1010-upgrade_290_secure_php.txt',
						'SCRIPT_NAME' => '1010-upgrade_290_secure_php.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'run_upgrades_460.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/1020-upgrade_460_performance.txt',
						'SCRIPT_NAME' => '1020-upgrade_460_performance.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'run_upgrades_461.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/1030-upgrade_461_certbot_snap.txt',
						'SCRIPT_NAME' => '1030-upgrade_461_certbot_snap.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'run_upgrades_462.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/1040-upgrade_462_install_7g_firewall.txt',
						'SCRIPT_NAME' => '1040-upgrade_462_install_7g_firewall.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'run_upgrades_530.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/1090-upgrade_530_ols_server_fix.txt',
						'SCRIPT_NAME' => '1090-upgrade_530_ols_server_fix.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'run_upgrade_install_php_81.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/1050-upgrade_install_php_81.txt',
						'SCRIPT_NAME' => '1050-upgrade_install_php_81.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'run_upgrade_install_php_82.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/1110-upgrade_install_php_82.txt',
						'SCRIPT_NAME' => '1110-upgrade_install_php_82.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'run_upgrade_install_php_83.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/1110-upgrade_install_php_83.txt',
						'SCRIPT_NAME' => '1110-upgrade_install_php_83.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'run_upgrade_install_php_84.txt':
					$new_array = array_merge(
						array(
							'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/1110-upgrade_install_php_84.txt',
							'SCRIPT_NAME' => '1110-upgrade_install_php_84.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'run_upgrade_install_old_php_version.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/1130-upgrade_install_php.txt',
						'SCRIPT_NAME' => '1130-upgrade_install_php.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'run_upgrade_7g.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/1060-upgrade_7g_firewall.txt',
						'SCRIPT_NAME' => '1060-upgrade_7g_firewall.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'run_remove_6g.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/1120-remove_6g_firewall.txt',
						'SCRIPT_NAME' => '1120-remove_6g_firewall.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'run_upgrade_wpcli.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/1070-upgrade_wp_cli.txt',
						'SCRIPT_NAME' => '1070-upgrade_wp_cli.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'run_upgrade_install_php_intl.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/1080-upgrade_install_php_intl_module.txt',
						'SCRIPT_NAME' => '1080-upgrade_install_php_intl_module.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'run_upgrade_cache_enabler_nginx_config.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/1100-upgrade_cache_enabler.txt',
						'SCRIPT_NAME' => '1100-upgrade_cache_enabler.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'server_status_callback.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/24-server_status.txt',
						'SCRIPT_NAME' => '24-server_status.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'maldet.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/26-lmd_clamav.txt',
						'SCRIPT_NAME' => '26-lmd_clamav.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'server_restart_callback.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/28-restart_callback.txt',
						'SCRIPT_NAME' => '28-restart_callback.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'schedule_server_reboot.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/36-schedule-server-reboot.txt',
						'SCRIPT_NAME' => '36-schedule-server-reboot.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'backup_config_files.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/37-backup-configuration.txt',
						'SCRIPT_NAME' => '37-backup-configuration.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'goaccess.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/27-goaccess.txt',
						'SCRIPT_NAME' => '27-goaccess.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'fail2ban.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/23-fail2ban.txt',
						'SCRIPT_NAME' => '23-fail2ban.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'server_update.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/29-server_update.txt',
						'SCRIPT_NAME' => '29-server_update.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'server_php_version.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/38-server_php_version.txt',
						'SCRIPT_NAME' => '38-server_php_version.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'git_control_server.txt':
				$command_name = $additional['command'];
				$new_array    = array_merge(
					array(
						'SCRIPT_URL'   => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/58-git_control.txt',
						'SCRIPT_NAME'  => '58-git_control.sh',
						'SCRIPT_LOGS'  => "{$this->get_app_name()}_{$command_name}",
						'CALLBACK_URL' => $this->get_command_url( $instance['server_id'], $command_name, 'completed' ),
					),
					$common_array,
					$additional
				);
				break;
			case 'ubuntu_pro_activate.txt':
				// This one's a long running command.
				$command_name = $additional['command'];
				$new_array    = array_merge(
					array(
						'SCRIPT_URL'   => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/54-ubuntupro.txt',
						'SCRIPT_NAME'  => '54-ubuntupro.sh',
						'SCRIPT_LOGS'  => "{$this->get_app_name()}_{$command_name}",
						'CALLBACK_URL' => $this->get_command_url( $instance['server_id'], $command_name, 'completed' ),
					),
					$common_array,
					$additional
				);
				break;
			case 'ubuntu_pro_actions.txt':
				// This one's a short command.
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/54-ubuntupro.txt',
						'SCRIPT_NAME' => '54-ubuntupro.sh',
					),
					$common_array,
					$additional
				);
				break;

			/**
			 *************************************************************
			 * The items below this are SERVER SYNC items, not APP items.
			 **************************************************************
			 */
			case 'server_sync_origin_setup.txt':
				$command_name = $additional['command'];
				$new_array    = array_merge(
					array(
						'SCRIPT_URL'   => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/71-origin.txt',
						'SCRIPT_NAME'  => '71-origin.sh',
						'SCRIPT_URL2'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/wp-sync',
						'SCRIPT_NAME2' => 'wp-sync',
					),
					$common_array,
					$additional
				);
				break;
			case 'server_sync_destination_setup.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/72-destination.txt',
						'SCRIPT_NAME' => '72-destination.sh',
					),
					$common_array,
					$additional
				);
				break;
			case 'server_sync_manage.txt':
				$new_array = array_merge(
					array(
						'SCRIPT_URL'  => trailingslashit( wpcd_url ) . $this->get_scripts_folder_relative() . $script_version . '/raw/71-origin.txt',
						'SCRIPT_NAME' => '71-origin.sh',
					),
					$common_array,
					$additional
				);
				break;
		}

		$new_array = apply_filters( 'wpcd_wpapp_replace_script_tokens', $new_array, $array, $script_name, $script_version, $instance, $command, $additional );

		return array_merge( $array, $new_array );
	}

}
