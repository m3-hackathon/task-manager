<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/m3-hackathon/task-manager
 * @since             1
 * @package           Task_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       Task Manager
 * Plugin URI:        https://github.com/m3-hackathon/
 * Description:       Task Manager is a Task Management Module/Tools for wordpress, where you can create, manage, assign user, update and delete different tasks. It has all features of Task Management Application.
 * Version:           3.6.34
 * Domain Path:       /languages
 * Requires at least: 4.8
 * Tags: task manager, tasks, manager
 * Tested up to: 6.1.1
 * Author:            Louis Sanchez
 * Author URI:        https://louie-sanchez.vercel.app/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       task-manager
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/task-manager-activator.php
 */
function activate_task_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/task-manager-activator.php';
	Task_Manager_Activator::activate();
	Task_Manager_Activator::createTables();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/task-manager-deactivator.php
 */
function deactivate_task_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/task-manager-deactivator.php';
	Task_Manager_Deactivator::deactivate();
}


/**
 * The code that runs during plugin uninstall.
 * This action is documented in includes/task-manager-uninstall.php
 */
function uninstall_task_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/task-manager-uninstall.php';
	Task_Manager_Uninstall::uninstall();
}

register_activation_hook( __FILE__, 'activate_task_manager' );
register_deactivation_hook( __FILE__, 'deactivate_task_manager' );
register_uninstall_hook( __FILE__, 'uninstall_task_manager' );



/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/task-manager.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_task_manager() {

	$plugin = new Task_Manager();
	$plugin->run();

}
run_task_manager();

require plugin_dir_path( __FILE__ ) .'puc_dir/puc.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://gitlab.com/RobinHossain/task-manager',
    __FILE__,
    'task-manager'
);
//        $myUpdateChecker->setBranch('master');
$myUpdateChecker->setAuthentication('glpat-ycEchykGz9ar-16i6n-e');
