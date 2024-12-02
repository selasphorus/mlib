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
//if ( isset($options['options_mlib_active_modules']) ) { $active_modules = unserialize($options['options_mlib_active_modules']); } else { $active_modules = array(); }

function mlib_custom_caps() {
	$use_custom_caps = false;
	if ( isset($options['use_custom_caps']) && !empty($options['use_custom_caps']) ) {
		$use_custom_caps = true;
	}
	return $use_custom_caps;
}

/*** MUSIC LIBRARY ***/

// TODO: generalize as "library" w/ sub-options for music?
if ( in_array('music', $active_modules ) ) {

	// Repertoire, aka Musical Work
	function register_post_type_repertoire() {

		if ( mlib_custom_caps() ) { $caps = array('musicwork', 'repertoire'); } else { $caps = "post"; }
		
		$labels = array(
			'name' => __( 'Musical Works', 'mlib' ),
			'singular_name' => __( 'Musical Work', 'mlib' ),
			'add_new' => __( 'New Musical Work', 'mlib' ),
			'add_new_item' => __( 'Add New Musical Work', 'mlib' ),
			'edit_item' => __( 'Edit Musical Work', 'mlib' ),
			'new_item' => __( 'New Musical Work', 'mlib' ),
			'view_item' => __( 'View Musical Work', 'mlib' ),
			'search_items' => __( 'Search Musical Works', 'mlib' ),
			'not_found' =>  __( 'No Musical Works Found', 'mlib' ),
			'not_found_in_trash' => __( 'No Musical Works found in Trash', 'mlib' ),
		);
	
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable'=> true,
			'show_ui' 			=> true,
			'show_in_menu'     	=> true,
			'query_var'        	=> true,
			'rewrite'			=> array( 'slug' => 'repertoire' ), // permalink structure slug
			'capability_type'	=> $caps,
			'map_meta_cap'		=> true,
			'has_archive' 		=> true,
			'hierarchical'		=> false,
			'menu_icon'			=> 'dashicons-book',
			'menu_position'		=> null,
			'supports' 			=> array( 'title', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'revisions', 'page-attributes' ), //'editor', 
			'taxonomies'		=> array( 'repertoire_category', 'occasion', 'post_tag', 'admin_tag' ), //, 'season'
			'show_in_rest'		=> true, // false = use classic, not block editor
		);

		register_post_type( 'repertoire', $args );
	
	}
	add_action( 'init', 'register_post_type_repertoire' );

	// Edition
	function register_post_type_edition() {

		if ( mlib_custom_caps() ) { $caps = "edition"; } else { $caps = "post"; }
		
		$labels = array(
			'name' => __( 'Editions', 'mlib' ),
			'singular_name' => __( 'Edition', 'mlib' ),
			'add_new' => __( 'New Edition', 'mlib' ),
			'add_new_item' => __( 'Add New Edition', 'mlib' ),
			'edit_item' => __( 'Edit Edition', 'mlib' ),
			'new_item' => __( 'New Edition', 'mlib' ),
			'view_item' => __( 'View Edition', 'mlib' ),
			'search_items' => __( 'Search Editions', 'mlib' ),
			'not_found' =>  __( 'No Editions Found', 'mlib' ),
			'not_found_in_trash' => __( 'No Editions found in Trash', 'mlib' ),
		);
	
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable'=> true,
			'show_ui'  			=> true,
			'show_in_menu' 		=> 'edit.php?post_type=repertoire',
			'query_var'			=> true,
			'rewrite'			=> array( 'slug' => 'editions' ), // permalink structure slug
			'capability_type'	=> $caps,
			'map_meta_cap'		=> true,
			'has_archive' 		=> true,
			'hierarchical'		=> false,
			//'menu_icon'			=> 'dashicons-book',
			'menu_position'		=> null,
			'supports' 			=> array( 'title', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'revisions', 'page-attributes' ), //'editor', 
			'taxonomies'		=> array( 'instrument', 'key', 'soloist', 'voicing', 'library_tag', 'admin_tag' ),
			'show_in_rest'		=> true,    
		);

		register_post_type( 'edition', $args );
	
	}
	add_action( 'init', 'register_post_type_edition' );

	// Publisher
	function register_post_type_publisher() {

		if ( mlib_custom_caps() ) { $caps = "publisher"; } else { $caps = "post"; }
		
		$labels = array(
			'name' => __( 'Publishers', 'mlib' ),
			'singular_name' => __( 'Publisher', 'mlib' ),
			'add_new' => __( 'New Publisher', 'mlib' ),
			'add_new_item' => __( 'Add New Publisher', 'mlib' ),
			'edit_item' => __( 'Edit Publisher', 'mlib' ),
			'new_item' => __( 'New Publisher', 'mlib' ),
			'view_item' => __( 'View Publisher', 'mlib' ),
			'search_items' => __( 'Search Publishers', 'mlib' ),
			'not_found' =>  __( 'No Publishers Found', 'mlib' ),
			'not_found_in_trash' => __( 'No Publishers found in Trash', 'mlib' ),
		);
	
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable'=> true,
			'show_ui'  			=> true,
			'show_in_menu' 		=> 'edit.php?post_type=publication',
			'query_var'			=> true,
			'rewrite'			=> array( 'slug' => 'publishers' ), // permalink structure slug
			'capability_type'	=> $caps,
			'map_meta_cap'		=> true,
			'has_archive' 		=> true,
			'hierarchical'		=> false,
			//'menu_icon'			=> 'dashicons-book',
			'menu_position'		=> null,
			'supports' 			=> array( 'title', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'revisions', 'page-attributes' ), //'editor', 
			'taxonomies'		=> array( 'admin_tag' ),
			'show_in_rest'		=> true,    
		);

		register_post_type( 'publisher', $args );
	
	}
	add_action( 'init', 'register_post_type_publisher' );

	// Publication
	function register_post_type_publication() {

		if ( mlib_custom_caps() ) { $caps = "publication"; } else { $caps = "post"; }
		
		$labels = array(
			'name' => __( 'Publications', 'mlib' ),
			'singular_name' => __( 'Publication', 'mlib' ),
			'add_new' => __( 'New Publication', 'mlib' ),
			'add_new_item' => __( 'Add New Publication', 'mlib' ),
			'edit_item' => __( 'Edit Publication', 'mlib' ),
			'new_item' => __( 'New Publication', 'mlib' ),
			'view_item' => __( 'View Publication', 'mlib' ),
			'search_items' => __( 'Search Publications', 'mlib' ),
			'not_found' =>  __( 'No Publications Found', 'mlib' ),
			'not_found_in_trash' => __( 'No Publications found in Trash', 'mlib' ),
		);
	
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable'=> true,
			'show_ui' 			=> true,
			'show_in_menu'     	=> true,
			'query_var'        	=> true,
			'rewrite'			=> array( 'slug' => 'publications' ), // permalink structure slug
			'capability_type'	=> $caps,
			'map_meta_cap'		=> true,
			'has_archive' 		=> true,
			'hierarchical'		=> false,
			'menu_icon'			=> 'dashicons-book-alt',
			'menu_position'		=> null,
			'supports' 			=> array( 'title', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'revisions', 'page-attributes' ), //'editor', 
			'taxonomies'		=> array( 'publication_category', 'admin_tag' ),
			'show_in_rest'		=> true,    
		);

		register_post_type( 'publication', $args );
	
	}
	add_action( 'init', 'register_post_type_publication' );

}

if ( in_array('mdev', $active_modules ) ) {

	// Music List
	function register_post_type_music_list() {

		if ( mlib_custom_caps() ) { $caps = array('music_list', 'music_lists'); } else { $caps = "post"; }
		
		$labels = array(
			'name' => __( 'Music Lists', 'mlib' ),
			'singular_name' => __( 'Music List', 'mlib' ),
			'add_new' => __( 'New Music List', 'mlib' ),
			'add_new_item' => __( 'Add New Music List', 'mlib' ),
			'edit_item' => __( 'Edit Music List', 'mlib' ),
			'new_item' => __( 'New Music List', 'mlib' ),
			'view_item' => __( 'View Music List', 'mlib' ),
			'search_items' => __( 'Search Music Lists', 'mlib' ),
			'not_found' =>  __( 'No Music Lists Found', 'mlib' ),
			'not_found_in_trash' => __( 'No Music Lists found in Trash', 'mlib' ),
		);
	
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable'=> true,
			'show_ui'          	=> true,
			'show_in_menu'		=> true,
			'query_var'			=> true,
			'rewrite'			=> array( 'slug' => 'music-lists' ), // permalink structure slug
			'capability_type'	=> $caps,
			'map_meta_cap'		=> true,
			'has_archive' 		=> true,
			'hierarchical'		=> false,
			//'menu_icon'			=> 'dashicons-book',
			'menu_position'		=> null,
			'supports' 			=> array( 'title', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'revisions', 'page-attributes' ), //'editor', 
			'taxonomies'		=> array( 'admin_tag' ),
			'show_in_rest'		=> true,    
		);

		register_post_type( 'music_list', $args );
	
	}
	add_action( 'init', 'register_post_type_music_list' );

}

/*** INSTRUMENT/ORGAN LIBRARY ***/

/*** ORGANS ***/
// >>> MLIB
// TODO: generalize as "instruments"?
if ( in_array('organs', $active_modules ) ) {

	// Organ
	function register_post_type_organ() {

		if ( mlib_custom_caps() ) { $caps = array('organ', 'organs'); } else { $caps = "post"; }
		
		$labels = array(
			'name' => __( 'Organs', 'mlib' ),
			'singular_name' => __( 'Organ', 'mlib' ),
			'add_new' => __( 'New Organ', 'mlib' ),
			'add_new_item' => __( 'Add New Organ', 'mlib' ),
			'edit_item' => __( 'Edit Organ', 'mlib' ),
			'new_item' => __( 'New Organ', 'mlib' ),
			'view_item' => __( 'View Organ', 'mlib' ),
			'search_items' => __( 'Search Organs', 'mlib' ),
			'not_found' =>  __( 'No Organs Found', 'mlib' ),
			'not_found_in_trash' => __( 'No Organs found in Trash', 'mlib' ),
		);
	
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable'=> true,
			'show_ui' 			=> true,
			'show_in_menu'     	=> true,
			'query_var'        	=> true,
			'rewrite'			=> array( 'slug' => 'dborgans' ), // permalink structure slug
			'capability_type'	=> $caps,
			'map_meta_cap'		=> true,
			'has_archive' 		=> true,
			'hierarchical'		=> false,
			'menu_icon'			=> 'dashicons-playlist-audio',
			'menu_position'		=> null,
			'supports' 			=> array( 'title', 'author', 'thumbnail', 'editor', 'excerpt', 'custom-fields', 'revisions', 'page-attributes' ), //
			'taxonomies'		=> array( 'action_type', 'organ_tag', 'admin_tag' ),
			'show_in_rest'		=> false, // i.e. false = use classic, not block editor
		);

		register_post_type( 'organ', $args );
	
	}
	add_action( 'init', 'register_post_type_organ' );

	// Organ Builder
	function register_post_type_builder() {

		if ( mlib_custom_caps() ) { $caps = array('organ', 'organs'); } else { $caps = "post"; }
		
		$labels = array(
			'name' => __( 'Builders', 'mlib' ),
			'singular_name' => __( 'Builder', 'mlib' ),
			'add_new' => __( 'New Builder', 'mlib' ),
			'add_new_item' => __( 'Add New Builder', 'mlib' ),
			'edit_item' => __( 'Edit Builder', 'mlib' ),
			'new_item' => __( 'New Builder', 'mlib' ),
			'view_item' => __( 'View Builder', 'mlib' ),
			'search_items' => __( 'Search Builders', 'mlib' ),
			'not_found' =>  __( 'No Builders Found', 'mlib' ),
			'not_found_in_trash' => __( 'No Builders found in Trash', 'mlib' ),
		);
	
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable'=> true,
			'show_ui'  			=> true,
			'show_in_menu' 		=> 'edit.php?post_type=organ',
			'query_var'			=> true,
			'rewrite'			=> array( 'slug' => 'builders' ), // permalink structure slug
			'capability_type'	=> $caps,
			'map_meta_cap'		=> true,
			'has_archive' 		=> true,
			'hierarchical'		=> false,
			//'menu_icon'			=> 'dashicons-welcome-write-blog',
			'menu_position'		=> null,
			'supports' 			=> array( 'title', 'author', 'thumbnail', 'editor', 'excerpt', 'custom-fields', 'revisions', 'page-attributes' ), //
			'taxonomies'		=> array( 'admin_tag' ),
			'show_in_rest'		=> false, // i.e. false = use classic, not block editor
		);

		register_post_type( 'builder', $args );
	
	}
	add_action( 'init', 'register_post_type_builder' );

	// Division
	function register_post_type_division() {

		if ( mlib_custom_caps() ) { $caps = array('organ', 'organs'); } else { $caps = "post"; }
		
		$labels = array(
			'name' => __( 'Divisions', 'mlib' ),
			'singular_name' => __( 'Division', 'mlib' ),
			'add_new' => __( 'New Division', 'mlib' ),
			'add_new_item' => __( 'Add New Division', 'mlib' ),
			'edit_item' => __( 'Edit Division', 'mlib' ),
			'new_item' => __( 'New Division', 'mlib' ),
			'view_item' => __( 'View Division', 'mlib' ),
			'search_items' => __( 'Search Divisions', 'mlib' ),
			'not_found' =>  __( 'No Divisions Found', 'mlib' ),
			'not_found_in_trash' => __( 'No Divisions found in Trash', 'mlib' ),
		);
	
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable'=> true,
			'show_ui'  			=> true,
			'show_in_menu' 		=> 'edit.php?post_type=organ',
			'query_var'			=> true,
			'rewrite'			=> array( 'slug' => 'divisions' ), // permalink structure slug
			'capability_type'	=> $caps,
			'map_meta_cap'		=> true,
			'has_archive' 		=> true,
			'hierarchical'		=> false,
			//'menu_icon'			=> 'dashicons-welcome-write-blog',
			'menu_position'		=> null,
			'supports' 			=> array( 'title', 'author', 'thumbnail', 'editor', 'excerpt', 'custom-fields', 'revisions', 'page-attributes' ), //
			'taxonomies'		=> array( 'admin_tag' ),
			'show_in_rest'		=> false, // i.e. false = use classic, not block editor
		);

		register_post_type( 'division', $args );
	
	}
	add_action( 'init', 'register_post_type_division' );

	// Manual
	function register_post_type_manual() {

		if ( mlib_custom_caps() ) { $caps = array('organ', 'organs'); } else { $caps = "post"; }
		
		$labels = array(
			'name' => __( 'Manuals', 'mlib' ),
			'singular_name' => __( 'Manual', 'mlib' ),
			'add_new' => __( 'New Manual', 'mlib' ),
			'add_new_item' => __( 'Add New Manual', 'mlib' ),
			'edit_item' => __( 'Edit Manual', 'mlib' ),
			'new_item' => __( 'New Manual', 'mlib' ),
			'view_item' => __( 'View Manual', 'mlib' ),
			'search_items' => __( 'Search Manuals', 'mlib' ),
			'not_found' =>  __( 'No Manuals Found', 'mlib' ),
			'not_found_in_trash' => __( 'No Manuals found in Trash', 'mlib' ),
		);
	
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable'=> true,
			'show_ui'  			=> true,
			'show_in_menu' 		=> 'edit.php?post_type=organ',
			'query_var'			=> true,
			'rewrite'			=> array( 'slug' => 'manuals' ), // permalink structure slug
			'capability_type'	=> $caps,
			'map_meta_cap'		=> true,
			'has_archive' 		=> true,
			'hierarchical'		=> false,
			//'menu_icon'			=> 'dashicons-welcome-write-blog',
			'menu_position'		=> null,
			'supports' 			=> array( 'title', 'author', 'thumbnail', 'editor', 'excerpt', 'custom-fields', 'revisions', 'page-attributes' ), //
			'taxonomies'		=> array( 'admin_tag' ),
			'show_in_rest'		=> false, // i.e. false = use classic, not block editor
		);

		register_post_type( 'manual', $args );
	
	}
	add_action( 'init', 'register_post_type_manual' );

	// Stop
	function register_post_type_stop() {

		if ( mlib_custom_caps() ) { $caps = array('organ', 'organs'); } else { $caps = "post"; }
		
		$labels = array(
			'name' => __( 'Stops', 'mlib' ),
			'singular_name' => __( 'Stop', 'mlib' ),
			'add_new' => __( 'New Stop', 'mlib' ),
			'add_new_item' => __( 'Add New Stop', 'mlib' ),
			'edit_item' => __( 'Edit Stop', 'mlib' ),
			'new_item' => __( 'New Stop', 'mlib' ),
			'view_item' => __( 'View Stop', 'mlib' ),
			'search_items' => __( 'Search Stops', 'mlib' ),
			'not_found' =>  __( 'No Stops Found', 'mlib' ),
			'not_found_in_trash' => __( 'No Stops found in Trash', 'mlib' ),
		);
	
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable'=> true,
			'show_ui'  			=> true,
			'show_in_menu' 		=> 'edit.php?post_type=organ',
			'query_var'			=> true,
			'rewrite'			=> array( 'slug' => 'stops' ), // permalink structure slug
			'capability_type'	=> $caps,
			'map_meta_cap'		=> true,
			'has_archive' 		=> true,
			'hierarchical'		=> false,
			//'menu_icon'			=> 'dashicons-welcome-write-blog',
			'menu_position'		=> null,
			'supports' 			=> array( 'title', 'author', 'thumbnail', 'editor', 'excerpt', 'custom-fields', 'revisions', 'page-attributes' ), //
			'taxonomies'		=> array( 'admin_tag' ),
			'show_in_rest'		=> false, // i.e. false = use classic, not block editor
		);

		register_post_type( 'stop', $args );
	
	}
	add_action( 'init', 'register_post_type_stop' );
	
	// Rank -- TBD

}

/*
// TODO: change "person" to "individual", to better include plants and animals? w/ ACF field groups based on category/species
if ( in_array('people', $active_modules ) ) { // && !post_type_exists('person')
	
	// Person
	function register_post_type_person() {

		//if ( mlib_custom_caps() ) { $caps = array('person', 'people'); } else { $caps = "post"; }
		if ( mlib_custom_caps() ) { $caps = "person"; } else { $caps = "post"; }
		
		$labels = array(
			'name' => __( 'People', 'mlib' ),
			'singular_name' => __( 'Person', 'mlib' ),
			'add_new' => __( 'New Person', 'mlib' ),
			'add_new_item' => __( 'Add New Person', 'mlib' ),
			'edit_item' => __( 'Edit Person', 'mlib' ),
			'new_item' => __( 'New Person', 'mlib' ),
			'view_item' => __( 'View Person', 'mlib' ),
			'search_items' => __( 'Search People', 'mlib' ),
			'not_found' =>  __( 'No People Found', 'mlib' ),
			'not_found_in_trash' => __( 'No People found in Trash', 'mlib' ),
		);
	
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable'=> true,
			'show_ui' 			=> true,
			'show_in_menu'     	=> true,
			'query_var'        	=> true,
			'rewrite'			=> array( 'slug' => 'people' ), // permalink structure slug
			'capability_type'	=> $caps,
			'map_meta_cap'		=> true,
			'has_archive' 		=> true,
			'hierarchical'		=> false,
			'menu_icon'			=> 'dashicons-groups',
			'menu_position'		=> null,
			'supports' 			=> array( 'title', 'author', 'editor', 'excerpt', 'revisions', 'thumbnail', 'custom-fields', 'page-attributes' ),
			'taxonomies'		=> array( 'person_category', 'person_title', 'admin_tag' ), //, 'person_tag', 'people_category'
			'show_in_rest'		=> false, // false = use classic, not block editor
			'delete_with_user' 	=> false,
		);

		register_post_type( 'person', $args );
	
	}
	add_action( 'init', 'register_post_type_person' );

	// Group
	// TODO: Figure out how to allow for individual sites to customize labels -- e.g. "Ensembles" for STC(?)
	function register_post_type_group() {

		if ( mlib_custom_caps() ) { $caps = "group"; } else { $caps = "post"; }
		
		$labels = array(
			'name' => __( 'Groups', 'mlib' ),
			'singular_name' => __( 'Group', 'mlib' ),
			'add_new' => __( 'New Group', 'mlib' ),
			'add_new_item' => __( 'Add New Group', 'mlib' ),
			'edit_item' => __( 'Edit Group', 'mlib' ),
			'new_item' => __( 'New Group', 'mlib' ),
			'view_item' => __( 'View Group', 'mlib' ),
			'search_items' => __( 'Search Groups', 'mlib' ),
			'not_found' =>  __( 'No Groups Found', 'mlib' ),
			'not_found_in_trash' => __( 'No Group found in Trash', 'mlib' ),
		);
	
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable'=> true,
			'show_ui' 			=> true,
			'show_in_menu'     	=> true,
			'query_var'        	=> true,
			'rewrite'			=> array( 'slug' => 'groups' ), // permalink structure slug
			'capability_type'	=> $caps,
			'map_meta_cap' 		=> true,
			'has_archive'  		=> true,
			'hierarchical' 		=> true,
			//'menu_icon'			=> 'dashicons-groups',
			'menu_position'		=> null,
			'supports' 			=> array( 'title', 'author', 'thumbnail', 'editor', 'excerpt', 'custom-fields', 'revisions', 'page-attributes' ), // 
			'taxonomies'		=> array( 'admin_tag', 'group_category' ),
			'show_in_rest'		=> false,    
		);

		register_post_type( 'group', $args );
	
	}
	add_action( 'init', 'register_post_type_group' );
	
	if ( mlib_queenbee() ) {
		// Roster -- WIP
		function register_post_type_roster() {

			if ( mlib_custom_caps() ) { $caps = "roster"; } else { $caps = "post"; }
			
			$labels = array(
				'name' => __( 'Rosters', 'mlib' ),
				'singular_name' => __( 'Roster', 'mlib' ),
				'add_new' => __( 'New Roster', 'mlib' ),
				'add_new_item' => __( 'Add New Roster', 'mlib' ),
				'edit_item' => __( 'Edit Roster', 'mlib' ),
				'new_item' => __( 'New Roster', 'mlib' ),
				'view_item' => __( 'View Roster', 'mlib' ),
				'search_items' => __( 'Search Rosters', 'mlib' ),
				'not_found' =>  __( 'No Rosters Found', 'mlib' ),
				'not_found_in_trash' => __( 'No Rosters found in Trash', 'mlib' ),
			);
		
			$args = array(
				'labels' => $labels,
				'public' => true,
				'publicly_queryable'=> true,
				'show_ui' 			=> true,
				'show_in_menu'     	=> true,
				'query_var'        	=> true,
				'rewrite'			=> array( 'slug' => 'rosters' ), // permalink structure slug
				'capability_type'	=> $caps,
				'map_meta_cap' 		=> true,
				'has_archive'  		=> true,
				'hierarchical' 		=> true,
				//'menu_icon'			=> 'dashicons-groups',
				'menu_position'		=> null,
				'supports' 			=> array( 'title', 'author', 'thumbnail', 'editor', 'excerpt', 'custom-fields', 'revisions', 'page-attributes' ), // 
				'taxonomies'		=> array( 'admin_tag' ), //, 'group_category'
				'show_in_rest'		=> false,    
			);
	
			register_post_type( 'roster', $args );
		
		}
		add_action( 'init', 'register_post_type_roster' );
	}
}
*/

// ACF Bi-directional fields
// WIP!
// https://www.advancedcustomfields.com/resources/bidirectional-relationships/

// WIP?
if ( in_array('music', $active_modules ) ) {
	add_filter('acf/update_value/name=repertoire_editions', 'bidirectional_acf_update_value', 10, 3);
	/*if ( function_exists('is_dev_site') && is_dev_site() ) {
		//add_action('acf/save_post', 'acf_update_related_field_on_save'); // WIP
	}*/
}

if ( !function_exists( 'bidirectional_acf_update_value' ) ) {
	function bidirectional_acf_update_value( $value, $post_id, $field  ) {	

		// vars
		$field_name = $field['name'];
		$field_key = $field['key'];
		$global_name = 'is_updating_' . $field_name;
		
		// bail early if this filter was triggered from the update_field() function called within the loop below
		// - this prevents an infinite loop
		if( !empty($GLOBALS[ $global_name ]) ) return $value;
		
		
		// set global variable to avoid inifite loop
		// - could also remove_filter() then add_filter() again, but this is simpler
		$GLOBALS[ $global_name ] = 1;
		
		
		// loop over selected posts and add this $post_id
		if( is_array($value) ) {
		
			foreach( $value as $post_id2 ) {
				
				// load existing related posts
				$value2 = get_field($field_name, $post_id2, false);
				
				// allow for selected posts to not contain a value
				if( empty($value2) ) {
					$value2 = array();
				}
				
				// bail early if the current $post_id is already found in selected post's $value2
				if( in_array($post_id, $value2) ) continue;
				
				// append the current $post_id to the selected post's 'related_posts' value
				$value2[] = $post_id;
				
				// update the selected post's value (use field's key for performance)
				update_field($field_key, $value2, $post_id2);
				
			}
		
		}
		
		// find posts which have been removed
		$old_value = get_field($field_name, $post_id, false);
		
		if ( is_array($old_value) ) {
			
			foreach( $old_value as $post_id2 ) {
				
				// bail early if this value has not been removed
				if( is_array($value) && in_array($post_id2, $value) ) continue;
				
				// load existing related posts
				$value2 = get_field($field_name, $post_id2, false);
				
				// bail early if no value
				if( empty($value2) ) continue;
				
				// find the position of $post_id within $value2 so we can remove it
				$pos = array_search($post_id, $value2);
				
				// remove
				unset( $value2[ $pos] );
				
				// update the un-selected post's value (use field's key for performance)
				update_field($field_key, $value2, $post_id2);
				
			}
			
		}
		
		// reset global varibale to allow this filter to function as per normal
		$GLOBALS[ $global_name ] = 0;	
		
		// return
		return $value;
		
	}
}

// WIP!
if ( !function_exists( 'acf_update_related_field_on_save' ) ) {
	function acf_update_related_field_on_save ( $post_id ) {	
	
		// TODO: figure out how to handle repeater field sub_fields -- e.g. repertoire_events << event program_items
		
		// Get newly saved values -- all fields
		//$values = get_fields( $post_id );
	
		// Check the current (updated) value of a specific field.
		$rows = get_field('program_items', $post_id);
		if ( $rows ) {
			foreach( $rows as $row ) {
				if ( isset($row['program_item'][0]) ) {
					foreach ( $row['program_item'] as $program_item_obj_id ) {
						$item_post_type = get_post_type( $program_item_obj_id );
						if ( $item_post_type == 'repertoire' ) {
							$rep_related_events = get_field('related_events', $program_item_obj_id);
							if ( $rep_related_events ) {
								// Check to see if post_id is already saved to rep record
							} else {
								// No related_events set yet, so add the post_id
								//update_field('related_events', $post_id, $program_item_obj_id );
							}
						}	
					}
				}
			}
		}
		
	}
}

/*
// WIP
add_action( 'acf/init', 'mlib_bidirectional_field_updates' );
function mlib_bidirectional_field_updates () {
	if ( in_array('events', $active_modules ) ) {
		add_filter('acf/update_value/name=series_events', 'bidirectional_acf_update_value', 10, 3);
	}
}
*/
?>