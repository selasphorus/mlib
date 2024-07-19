<?php

defined( 'ABSPATH' ) or die( 'Nope!' );

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

// Get plugin options to determine which modules are active
$options = get_option( 'mlib_settings' );
if ( get_field('mlib_active_modules', 'option') ) { $active_modules = get_field('mlib_active_modules', 'option'); } else { $active_modules = array(); }
//if ( isset($options['options_mlib_active_modules']) ) { $active_modules = $options['options_mlib_active_modules']; } else { $active_modules = array(); }

/*** Taxonomies for REPERTOIRE and EDITIONS ***/

if ( in_array('music', $active_modules ) ) {

	/*** Taxonomies for REPERTOIRE ***/
	
	// Custom Taxonomy: Occasion
	function register_taxonomy_occasion() {
		$labels = array(
			'name'              => _x( 'Occasions', 'taxonomy general name' ),
			'singular_name'     => _x( 'Occasion', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Occasions' ),
			'all_items'         => __( 'All Occasions' ),
			'parent_item'       => __( 'Parent Occasion' ),
			'parent_item_colon' => __( 'Parent Occasion:' ),
			'edit_item'         => __( 'Edit Occasion' ),
			'update_item'       => __( 'Update Occasion' ),
			'add_new_item'      => __( 'Add New Occasion' ),
			'new_item_name'     => __( 'New Occasion Name' ),
			'menu_name'         => __( 'Occasions' ),
		);
		$args = array(
			'labels'            => $labels,
			'description'          => '',
			'public'               => true,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => [ 'slug' => 'occasion' ],
		);
		if ( mlib_custom_caps() ) {
			$cap = 'music';
			$args['capabilities'] = array(
				'manage_terms'  =>   'manage_'.$cap.'_terms',
				'edit_terms'    =>   'edit_'.$cap.'_terms',
				'delete_terms'  =>   'delete_'.$cap.'_terms',
				'assign_terms'  =>   'assign_'.$cap.'_terms',
			);
		}
		register_taxonomy( 'occasion', [ 'repertoire' ], $args );
	}
	add_action( 'init', 'register_taxonomy_occasion' );

	// Custom Taxonomy: Repertoire Category
	function register_taxonomy_repertoire_category() {
		$labels = array(
			'name'              => _x( 'Rep Categories', 'taxonomy general name' ),
			'singular_name'     => _x( 'Repertoire Category', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Rep Categories' ),
			'all_items'         => __( 'All Rep Categories' ),
			'parent_item'       => __( 'Parent Rep Category' ),
			'parent_item_colon' => __( 'Parent Rep Category:' ),
			'edit_item'         => __( 'Edit Rep Category' ),
			'update_item'       => __( 'Update Rep Category' ),
			'add_new_item'      => __( 'Add New Rep Category' ),
			'new_item_name'     => __( 'New Rep Category Name' ),
			'menu_name'         => __( 'Rep Categories' ),
		);
		$args = array(
			'labels'            => $labels,
			'description'          => '',
			'public'               => true,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => [ 'slug' => 'repertoire_category' ],
		);
		if ( mlib_custom_caps() ) {
			$cap = 'music';
			$args['capabilities'] = array(
				'manage_terms'  =>   'manage_'.$cap.'_terms',
				'edit_terms'    =>   'edit_'.$cap.'_terms',
				'delete_terms'  =>   'delete_'.$cap.'_terms',
				'assign_terms'  =>   'assign_'.$cap.'_terms',
			);
		}
		register_taxonomy( 'repertoire_category', [ 'repertoire' ], $args );
	}
	add_action( 'init', 'register_taxonomy_repertoire_category' );

	/*** Taxonomies for EDITIONS ***/

	// Custom Taxonomy: Instrument
	function register_taxonomy_instrument() {
		$labels = array(
			'name'              => _x( 'Instruments', 'taxonomy general name' ),
			'singular_name'     => _x( 'Instrument', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Instruments' ),
			'all_items'         => __( 'All Instruments' ),
			'parent_item'       => __( 'Parent Instrument' ),
			'parent_item_colon' => __( 'Parent Instrument:' ),
			'edit_item'         => __( 'Edit Instrument' ),
			'update_item'       => __( 'Update Instrument' ),
			'add_new_item'      => __( 'Add New Instrument' ),
			'new_item_name'     => __( 'New Instrument Name' ),
			'menu_name'         => __( 'Instrument' ),
		);
		$args = array(
			'labels'            => $labels,
			'description'          => '',
			'public'               => true,
			//'publicly_queryable'   => true, // inherit from 'public'
			'hierarchical'      => true, // make it hierarchical (like categories)
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => [ 'slug' => 'instrument' ],
			//'default_term'         => array( 'name', 'slug', 'description' ),
		);
		if ( mlib_custom_caps() ) {
			$cap = 'music';
			$args['capabilities'] = array(
				'manage_terms'  =>   'manage_'.$cap.'_terms',
				'edit_terms'    =>   'edit_'.$cap.'_terms',
				'delete_terms'  =>   'delete_'.$cap.'_terms',
				'assign_terms'  =>   'assign_'.$cap.'_terms',
			);
		}
		register_taxonomy( 'instrument', [ 'edition' ], $args );
	}
	add_action( 'init', 'register_taxonomy_instrument' );

	// Custom Taxonomy: Key
	function register_taxonomy_key() {
		$labels = array(
			'name'              => _x( 'Keys', 'taxonomy general name' ),
			'singular_name'     => _x( 'Key', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Keys' ),
			'all_items'         => __( 'All Keys' ),
			'parent_item'       => __( 'Parent Key' ),
			'parent_item_colon' => __( 'Parent Key:' ),
			'edit_item'         => __( 'Edit Key' ),
			'update_item'       => __( 'Update Key' ),
			'add_new_item'      => __( 'Add New Key' ),
			'new_item_name'     => __( 'New Key Name' ),
			'menu_name'         => __( 'Key' ),
		);
		$args = array(
			'labels'            => $labels,
			'description'          => '',
			'public'               => true,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => [ 'slug' => 'key' ],
		);
		if ( mlib_custom_caps() ) {
			$cap = 'music';
			$args['capabilities'] = array(
				'manage_terms'  =>   'manage_'.$cap.'_terms',
				'edit_terms'    =>   'edit_'.$cap.'_terms',
				'delete_terms'  =>   'delete_'.$cap.'_terms',
				'assign_terms'  =>   'assign_'.$cap.'_terms',
			);
		}
		register_taxonomy( 'key', [ 'edition' ], $args );
	}
	add_action( 'init', 'register_taxonomy_key' );

	// Custom Taxonomy: Soloist
	function register_taxonomy_soloist() {
		$labels = array(
			'name'              => _x( 'Soloists', 'taxonomy general name' ),
			'singular_name'     => _x( 'Soloist', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Soloists' ),
			'all_items'         => __( 'All Soloists' ),
			'parent_item'       => __( 'Parent Soloist' ),
			'parent_item_colon' => __( 'Parent Soloist:' ),
			'edit_item'         => __( 'Edit Soloist' ),
			'update_item'       => __( 'Update Soloist' ),
			'add_new_item'      => __( 'Add New Soloist' ),
			'new_item_name'     => __( 'New Soloist Name' ),
			'menu_name'         => __( 'Soloist' ),
		);
		$args = array(
			'labels'            => $labels,
			'description'          => '',
			'public'               => true,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => [ 'slug' => 'soloist' ],
		);
		if ( mlib_custom_caps() ) {
			$cap = 'music';
			$args['capabilities'] = array(
				'manage_terms'  =>   'manage_'.$cap.'_terms',
				'edit_terms'    =>   'edit_'.$cap.'_terms',
				'delete_terms'  =>   'delete_'.$cap.'_terms',
				'assign_terms'  =>   'assign_'.$cap.'_terms',
			);
		}
		register_taxonomy( 'soloist', [ 'edition' ], $args );
	}
	add_action( 'init', 'register_taxonomy_soloist' ); // TMP disabled until I figure out how to add fields: Abbreviation (abbr) & Sort Num (sort_num)

	// Custom Taxonomy: Voicing
	function register_taxonomy_voicing() {
		$labels = array(
			'name'              => _x( 'Voicings', 'taxonomy general name' ),
			'singular_name'     => _x( 'Voicing', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Voicings' ),
			'all_items'         => __( 'All Voicings' ),
			'parent_item'       => __( 'Parent Voicing' ),
			'parent_item_colon' => __( 'Parent Voicing:' ),
			'edit_item'         => __( 'Edit Voicing' ),
			'update_item'       => __( 'Update Voicing' ),
			'add_new_item'      => __( 'Add New Voicing' ),
			'new_item_name'     => __( 'New Voicing Name' ),
			'menu_name'         => __( 'Voicing' ),
		);
		$args = array(
			'labels'            => $labels,
			'description'          => '',
			'public'               => true,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => [ 'slug' => 'voicing' ],
		);
		if ( mlib_custom_caps() ) {
			$cap = 'music';
			$args['capabilities'] = array(
				'manage_terms'  =>   'manage_'.$cap.'_terms',
				'edit_terms'    =>   'edit_'.$cap.'_terms',
				'delete_terms'  =>   'delete_'.$cap.'_terms',
				'assign_terms'  =>   'assign_'.$cap.'_terms',
			);
		}
		register_taxonomy( 'voicing', [ 'edition' ], $args );
	}
	add_action( 'init', 'register_taxonomy_voicing' );

	// Custom Taxonomy: Library Tag
	function register_taxonomy_library_tag() {	
		$labels = array(
			'name'              => _x( 'Library Tags', 'taxonomy general name' ),
			'singular_name'     => _x( 'Library Tag', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Library Tags' ),
			'all_items'         => __( 'All Library Tags' ),
			'parent_item'       => __( 'Parent Library Tag' ),
			'parent_item_colon' => __( 'Parent Library Tag:' ),
			'edit_item'         => __( 'Edit Library Tag' ),
			'update_item'       => __( 'Update Library Tag' ),
			'add_new_item'      => __( 'Add New Library Tag' ),
			'new_item_name'     => __( 'New Library Tag Name' ),
			'menu_name'         => __( 'Library Tags' ),
		);
		$args = array(
			'labels'            => $labels,
			'description'          => '',
			'public'               => true,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_in_menu'      => 'edit.php?post_type=repertoire',
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => [ 'slug' => 'library_tag' ],
		);
		if ( mlib_custom_caps() ) {
			$cap = 'music';
			$args['capabilities'] = array(
				'manage_terms'  =>   'manage_'.$cap.'_terms',
				'edit_terms'    =>   'edit_'.$cap.'_terms',
				'delete_terms'  =>   'delete_'.$cap.'_terms',
				'assign_terms'  =>   'assign_'.$cap.'_terms',
			);
		}	
		register_taxonomy( 'library_tag', [ 'edition' ], $args );
	}
	add_action( 'init', 'register_taxonomy_library_tag' );

	/*** Taxonomies for PUBLICATIONS ***/

	// Custom Taxonomy: Publication Category
	function register_taxonomy_publication_category() {
		$labels = array(
			'name'              => _x( 'Publication Categories', 'taxonomy general name' ),
			'singular_name'     => _x( 'Publication Category', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Publication Categories' ),
			'all_items'         => __( 'All Publication Categories' ),
			'parent_item'       => __( 'Parent Publication Category' ),
			'parent_item_colon' => __( 'Parent Publication Category:' ),
			'edit_item'         => __( 'Edit Publication Category' ),
			'update_item'       => __( 'Update Publication Category' ),
			'add_new_item'      => __( 'Add New Publication Category' ),
			'new_item_name'     => __( 'New Publication Category Name' ),
			'menu_name'         => __( 'Publication Categories' ),
		);
		$args = array(
			'labels'            => $labels,
			'description'          => '',
			'public'               => true,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => [ 'slug' => 'publication_category' ],
		);
		if ( mlib_custom_caps() ) {
			$cap = 'music';
			$args['capabilities'] = array(
				'manage_terms'  =>   'manage_'.$cap.'_terms',
				'edit_terms'    =>   'edit_'.$cap.'_terms',
				'delete_terms'  =>   'delete_'.$cap.'_terms',
				'assign_terms'  =>   'assign_'.$cap.'_terms',
			);
		}
		register_taxonomy( 'publication_category', [ 'publication' ], $args );
	}
	add_action( 'init', 'register_taxonomy_publication_category' );

}

/*** Taxonomies for ORGANS ***/
// TODO: generalize as instrument/instrument_tag

if ( in_array('organs', $active_modules ) ) {

	// Custom Taxonomy: Action Type
	function register_taxonomy_action_type() {
		$labels = array(
			'name'              => _x( 'Action Types', 'taxonomy general name' ),
			'singular_name'     => _x( 'Action Type', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Action Types' ),
			'all_items'         => __( 'All Action Types' ),
			'parent_item'       => __( 'Parent Action Type' ),
			'parent_item_colon' => __( 'Parent Action Type:' ),
			'edit_item'         => __( 'Edit Action Type' ),
			'update_item'       => __( 'Update Action Type' ),
			'add_new_item'      => __( 'Add New Action Type' ),
			'new_item_name'     => __( 'New Action Type Name' ),
			'menu_name'         => __( 'Action Types' ),
		);
		$args = array(
			'labels'            => $labels,
			'description'          => '',
			'public'               => true,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => [ 'slug' => 'action_type' ],
		);
		if ( mlib_custom_caps() ) {
			$cap = 'organ';
			$args['capabilities'] = array(
				'manage_terms'  =>   'manage_'.$cap.'_terms',
				'edit_terms'    =>   'edit_'.$cap.'_terms',
				'delete_terms'  =>   'delete_'.$cap.'_terms',
				'assign_terms'  =>   'assign_'.$cap.'_terms',
			);
		}
		register_taxonomy( 'action_type', [ 'organ' ], $args );
	}
	add_action( 'init', 'register_taxonomy_action_type' );
	
	// Custom Taxonomy: Organ Tag
	function register_taxonomy_organ_tag() {
		$cap = 'organ';
		$labels = array(
			'name'              => _x( 'Organ Tags', 'taxonomy general name' ),
			'singular_name'     => _x( 'Organ Tag', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Organ Tags' ),
			'all_items'         => __( 'All Organ Tags' ),
			'parent_item'       => __( 'Parent Organ Tag' ),
			'parent_item_colon' => __( 'Parent Organ Tag:' ),
			'edit_item'         => __( 'Edit Organ Tag' ),
			'update_item'       => __( 'Update Organ Tag' ),
			'add_new_item'      => __( 'Add New Organ Tag' ),
			'new_item_name'     => __( 'New Organ Tag Name' ),
			'menu_name'         => __( 'Organ Tags' ),
		);
		$args = array(
			'labels'            => $labels,
			'description'          => '',
			'public'               => true,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'capabilities'         => array(
				'manage_terms'  =>   'manage_'.$cap.'_terms',
				'edit_terms'    =>   'edit_'.$cap.'_terms',
				'delete_terms'  =>   'delete_'.$cap.'_terms',
				'assign_terms'  =>   'assign_'.$cap.'_terms',
			),
			'query_var'         => true,
			'rewrite'           => [ 'slug' => 'organ_tag' ],
		);
		register_taxonomy( 'organ_tag', [ 'organ' ], $args );
	}
	add_action( 'init', 'register_taxonomy_organ_tag' );
	
}

?>