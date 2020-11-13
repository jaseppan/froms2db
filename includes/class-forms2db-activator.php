<?php

/**
 * Fired during plugin activation
 *
 * @link       https://cidedot.com
 * @since      1.0.0
 *
 * @package    Forms2db
 * @subpackage Forms2db/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Forms2db
 * @subpackage Forms2db/includes
 * @author     Janne Seppänen <j.v.seppanen@gmail.com>
 */
class Forms2db_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		global $wpdb;
		global $jal_db_version;
		$charset_collate = $wpdb->get_charset_collate();
	
		$sql .= "CREATE TABLE {$wpdb->prefix}forms2db_data (
					id INT NOT NULL AUTO_INCREMENT,
					post_id INT NOT NULL,
					form_id INT NOT NULL,
					user_id INT,
					status VARCHAR (256),
					form_key VARCHAR (256),
					form_data VARCHAR (16000),
					PRIMARY KEY (id) );";

		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	
		add_option( 'jal_db_version', $jal_db_version );

	}

}
