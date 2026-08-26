<?php
/**
 * Plugin Name:       MLib-OOP
 * Description:       A WordPress plugin for managing a Music Library (Works/Editions) using ACF PRO Blocks, Post Types, Options Pages, Taxonomies and more.
 * //Requires at least: 6.4
 * //Requires PHP:      7.4
 * Dependencies:      Requires WHx4 plugin for People CPT etc.
 * Requires Plugins:  whx4
 * Version:           0.1.260826
 * Author:            atc
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       mlib
 *
 * @package           mlib
 */

// TODO: generalize as "library" w/ sub-options for music?
// NB: this is the barest skeleton of an OOP version of the plugin and is in no way currently useable

if( !defined('ABSPATH') ) {
    exit;
}

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

// Define our handy constants.
define( 'MLIB_VERSION', '0.1.5' );
define( 'MLIB_PLUGIN_DIR', __DIR__ );
define( 'MLIB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MLIB_PLUGIN_BLOCKS', MLIB_PLUGIN_DIR . '/blocks/' );
//$plugin_path = plugin_dir_path( __FILE__ );

// WIP >> OOP
/*
// Via Composer
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

use atc\MLib\Modules\Repertoire;
use atc\MLib\Modules\Instruments;
use atc\MLib\Modules\Builders;
use atc\MLib\Modules\Organs;
*/
/*add_filter( 'whx4_register_modules', function( array $modules ) {
    //$modules['music'] = Music::class;
    $modules = [
        //'repertoire'    => Repertoire::class, // or: Music?
        //'instruments'    => Instruments::class,
           //'builder'        => Builders::class,
        //'organs'         => Organs::class // tmp?
    ];
    return $modules;
});*/

