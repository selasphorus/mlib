<?php
/**
 * Plugin Name:       MLib ACF plugin
 * Description:       A WordPress plugin for managing a Music Library (Works/Editions) using ACF PRO Blocks, Post Types, Options Pages, Taxonomies and more.
 * //Requires at least: 6.4
 * //Requires PHP:      7.4
 * Dependencies:	  Requires WHx4 plugin for People CPT and SDG for various utility functions
 * Requires Plugins:  whx4, sdg
 * Version:           0.1
 * Author:            atc
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       mlib
 *
 * @package           mlib
 */

// TODO: generalize as "library" w/ sub-options for music?


if( !defined('ABSPATH') ) {
	exit;
}

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

// Enforce dependency on WHx4
add_action('plugins_loaded', function() {
	if( !class_exists('atc\WHx4\Core\Plugin') ) {
		add_action('admin_notices', function() {
			echo '<div class="notice notice-error"><p><strong>MLib</strong> requires the <strong>WHx4</strong> plugin to be active. The plugin has been deactivated.</p></div>';
		});

		add_action('admin_init', function() {
			deactivate_plugins(plugin_basename(__FILE__));
		});

		return;
	}
});

// WIP >> OOP

// Via Composer
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';
/*
use atc\MLib\Modules\Repertoire;
use atc\MLib\Modules\Instruments;
use atc\MLib\Modules\Builders;
use atc\MLib\Modules\Organs;
*/
add_filter( 'whx4_register_modules', function( array $modules ) {
	//$modules['music'] = Music::class;
	$modules = [
        //'repertoire'	=> Repertoire::class, // or: Music?
        //'instruments'	=> Instruments::class,
       	//'builder'		=> Builders::class,
        //'organs' 		=> Organs::class // tmp?
    ];
	return $modules;
});


// Define our handy constants.
define( 'MLIB_VERSION', '0.1.5' );
define( 'MLIB_PLUGIN_DIR', __DIR__ );
define( 'MLIB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MLIB_PLUGIN_BLOCKS', MLIB_PLUGIN_DIR . '/blocks/' );
//$plugin_path = plugin_dir_path( __FILE__ );

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
//if ( get_field('mlib_active_modules', 'option') ) { $active_modules = get_field('mlib_active_modules', 'option'); } else { $active_modules = array(); }
if ( isset($options['mlib_active_modules']) ) { $active_modules = $options['mlib_active_modules']; } else { $active_modules = array(); }

foreach ( $active_modules as $module ) {

	// Load associated functions file, if any
    $filepath = MLIB_PLUGIN_DIR.'/modules/'.$module.'.php';
    $arr_exclusions = array ( 'mdev' ); // 'instruments', 'groups', 'newsletters', 'snippets', 'logbook', 'venues', 
    if ( !in_array( $module, $arr_exclusions) ) { // skip modules w/ no associated function files
    	if ( file_exists($filepath) ) { include_once( $filepath ); } else { echo "MLib module file $filepath not found"; }
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
		$cpt_names[] = "builder"; // or "maker"?
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

/* +~+~+ Enable ACF FORM as shortcode +~+~+ */
// TBD: move this to SDG for more general use?

add_action( 'template_redirect', 'acf_form_head' ); // See https://wordpress.org/support/topic/acf-create-a-front-end-form/

add_shortcode('mlib_acf_form', 'mlib_acf_form');
function mlib_acf_form ( $atts = array() ) {

	$info = "";
	$ts_info = "";
	
	$args = shortcode_atts( array(
        'post_content' => true,
        'instruction_placement' => 'field',
        'fields' => true
    ), $atts );
    
    // Extract
	extract( $args );
	
	// Turn fields var into array, in case of multiple fields
    $arr_fields = array(); // init
    if ( strpos($fields, ',') !== false ) {
    	// comma-separated values
    	$arr_fields = array_map('trim', explode(',', $fields)); // trim to deal w/ possibility of comma followed by space and sim
    } else {
    	$arr_fields[] = $fields;
    }
    
	
	$settings = array( 'post_content' => $post_content, 'instruction_placement' => $instruction_placement, 'fields' => $arr_fields );
	//$ts_info .= "arr_fields: <pre>".print_r($arr_fields, true)."</pre>";
	//$ts_info .= "settings: <pre>".print_r($settings, true)."</pre>";
    //$info .= $ts_info;
	
	ob_start();
    acf_form( $settings );
    $info = ob_get_clean(); // one step version of ob_get_contents(); ob_end_clean();
    
    //return ob_get_clean();
    return $info;
    
}

/*
// WIP -- attempt to prevent conversion to (and addition of) curly quotes in front-end forms
// This didn't work -- apparently theme-related -- switched back to Trudy and problem went away
function my_acf_remove_curly_quotes() {
    remove_filter ('acf_the_content', 'wptexturize');
}
add_action('acf/init', 'my_acf_remove_curly_quotes');
*/

?>