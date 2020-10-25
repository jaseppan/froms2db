<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://cidedot.com
 * @since             1.0.0
 * @package           Forms2db
 *
 * @wordpress-plugin
 * Plugin Name:       Forms2DB
 * Plugin URI:        https://cidedot.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Janne Seppänen
 * Author URI:        https://cidedot.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       forms2db
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'FORMS2DB_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-forms2db-activator.php
 */
function activate_forms2db() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-forms2db-activator.php';
	Forms2db_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-forms2db-deactivator.php
 */
function deactivate_forms2db() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-forms2db-deactivator.php';
	Forms2db_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_forms2db' );
register_deactivation_hook( __FILE__, 'deactivate_forms2db' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-forms2db.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_forms2db() {

	$plugin = new Forms2db();
	$plugin->run();

}
run_forms2db();
