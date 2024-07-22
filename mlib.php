<?php
/**
 * Plugin Name:       MLib ACF plugin
 * Description:       A WordPress plugin for managing a Music Library (Works/Editions) using ACF PRO Blocks, Post Types, Options Pages, Taxonomies and more.
 * Dependencies:	  Requires WHx4 plugin for People CPT
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Version:           0.1
 * Author:            ACF
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       mlib
 *
 * @package           mlib
 */

// Define our handy constants.
define( 'MLIB_VERSION', '0.1.5' );
define( 'MLIB_PLUGIN_DIR', __DIR__ );
define( 'MLIB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MLIB_PLUGIN_BLOCKS', MLIB_PLUGIN_DIR . '/blocks/' );

/* +~+~+ *** +~+~+ */

// Function to check for dev/admin user
function mlib_queenbee() {
	$current_user = wp_get_current_user();
	$username = $current_user->user_login;
	$useremail = $current_user->user_email;
	//
    if ( $username == 'stcdev' || $useremail == "birdhive@gmail.com" ) {
    	return true;
    } else {
    	return false;
    }
}

/* +~+~+ ACF +~+~+ */

// Set custom load & save JSON points for ACF sync
require 'includes/acf-json.php';

// Register a default "Site Settings" Options Page
require 'includes/acf-settings-page.php';

// Restrict access to ACF Admin screens
require 'includes/acf-restrict-access.php';
	
// Load ACF field groups hard-coded as PHP
require 'includes/acf-field-groups.php';

// Post types, taxonomies, field groups
require 'includes/cpts.php';

// Load custom post types
//require 'posttypes.php';

// Load custom taxonomies
//require 'taxonomies.php';

/* +~+~+ Optional Modules +~+~+ */

// Get plugin options -- WIP
// Get plugin options to determine which modules are active
$options = get_option( 'mlib_settings' );
if ( get_field('mlib_active_modules', 'option') ) { $active_modules = get_field('mlib_active_modules', 'option'); } else { $active_modules = array(); }

foreach ( $active_modules as $module ) {

	// Load associated functions file, if any
    $filepath = $plugin_path . 'modules/'.$module.'.php';
    $arr_exclusions = array ( 'instruments' ); // , 'groups', 'newsletters', 'snippets', 'logbook', 'venues', 
    if ( !in_array( $module, $arr_exclusions) ) { // skip modules w/ no associated function files
    	if ( file_exists($filepath) ) { include_once( $filepath ); } else { echo "module file $filepath not found"; }
    }
    
    // Add module options page for adding featured image, page-top content, &c.
    $cpt_names = array(); // array because some modules include multiple post types
    
    // Which post types are associated with this module? Build array
	// Deal w/ modules whose names don't perfectly match their CPT names
	if ( $module == "music" ) {
		$primary_cpt = "repertoire";
		$cpt_names[] = "repertoire";
		$cpt_names[] = "edition";
	} else if ( $module == "instruments" ) {
		$primary_cpt = "instrument";
		$cpt_names[] = "instrument";
	} else if ( $module == "organs" ) {
		$primary_cpt = "organ";
		$cpt_names[] = "organ";
		$cpt_names[] = "builder";
	} else {
		$cpt_name = $module;
		// Make it singular -- remove trailing "s"
		if ( substr($cpt_name, -1) == "s" && $cpt_name != "press" ) { $cpt_name = substr($cpt_name, 0, -1); }
		$primary_cpt = $cpt_name;
		$cpt_names[] = $cpt_name;
	}
    
	if ( function_exists('acf_add_options_page') ) {
		// Add module options page
    	acf_add_options_sub_page(array(
			'page_title'	=> ucfirst($module).' Module Options',
			'menu_title'    => ucfirst($module).' Module Options',//'menu_title'    => 'Archive Options', //ucfirst($cpt_name).
			'menu_slug' 	=> $module.'-module-options',
			'parent_slug'   => 'edit.php?post_type='.$primary_cpt,
		));
	}

}

?>