<?php

defined( 'ABSPATH' ) or die( 'Nope!' );

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin file, not much I can do when called directly.';
	exit;
}


/*********** CPT: REPERTOIRE (aka Musical Works) ***********/

/* ~~~ Admin/Dev functions ~~~ */
function update_repertoire_events( $rep_id = null, $run_slow_queries = false, $arr_event_ids = array() ) {
	
	$info = "";
	$updates = false;
	
	$info .= "About to run update_repertoire_events for rep item with ID:".$rep_id."<br />";
	
	// get the repertoire_events field contents for the rep item
	$repertoire_events = get_field('repertoire_events', $rep_id, false);
	
	if ( !empty($repertoire_events) ) {
		//$info .= "This rep item currently has the following repertoire_events: <pre>".print_r($repertoire_events,true)."</pre>";
		if ( !is_array($repertoire_events) ) { $repertoire_events = explode( ", ",$repertoire_events ); } // If it's not an array already, make it one
		$info .= "This rep item currently has [".count($repertoire_events)."] repertoire_events<br />";	
	} else {
		$info .= "This rep item currently has no repertoire_events.<br />";
		$repertoire_events = array(); // No repertoire_events set yet, so prep an empty array
	}
	
	// Check to see if any event_ids were submitted and proceed accordingly
	if ( empty($arr_event_ids) && $run_slow_queries == true ) {
	
		// No event_ids were submitted -> run a query to find ALL event_ids for events with programs containing the rep_id		
		$related_events = get_related_events ( "program_item", $rep_id );
		$arr_event_ids = $related_events['event_posts'];
	
		if ( empty($arr_event_ids) ) {
			$info .= "No related events were found using the get_related_events fcn.<br />"; // tft
		}
    
	}

	// Check event_ids to see if they're already in the repertoire_events array and add them if not
	foreach($arr_event_ids as $event_id) {
		if ( !in_array( $event_id, $repertoire_events ) ) {
			$repertoire_events[] = $event_id;
			$updates = true;
		} else {
			$info .= "The event_id [$event_id] is already in the array.<br />";	
		}
	}
	
	// If changes have been made, then update the repertoire_events field with the modified array of event_id values
	if ( $updates == true ) {
		if ( update_field('repertoire_events', $repertoire_events, $rep_id ) ) {
			$info .= "Success! repertoire_events field updated<br />";
			$info .= "Updated repertoire_events: <pre>".print_r($repertoire_events,true)."</pre>";
		} else {
			$info .= "phooey. update failed.<br />";
		}
	} else {
		$info .= "No update needed.<br />";
	}
	
	$info .= "+++++<br /><br />";
	
	return $info;
	
}

// WIP fcn to update to new bidirectional field: repertoire_litdates
/*function update_repertoire_litdates( $rep_id = null, $run_slow_queries = false, $arr_litdate_ids = array() ) {
	
	$info = "";
	$updates = false;
	
	$info .= "About to update repertoire_litdates for rep item with ID:".$rep_id."<br />";
	
	// get the repertoire_litdates field contents for the rep item
	$repertoire_litdates = get_field('repertoire_litdates', $rep_id, false);
	
	if ( !empty($repertoire_litdates) ) {
		$info .= "This rep item currently has the following repertoire_litdates: <pre>".print_r($repertoire_litdates,true)."</pre>";								
		if ( !is_array($repertoire_litdates) ) { $repertoire_litdates = explode( ", ",$repertoire_litdates ); } // If it's not an array already, make it one		
	} else {
		$info .= "This rep item currently has no repertoire_litdates.<br />";
		$repertoire_litdates = array(); // No repertoire_litdates set yet, so prep an empty array
	}
	
	// Check to see if any litdate_ids were submitted and proceed accordingly
	if ( empty($arr_litdate_ids) && $run_slow_queries == true ) {
	
		// No litdate_ids were submitted -> run a query to find ALL litdate_ids for litdates with programs containing the rep_id		
		$related_litdates = get_related_litdates ( "program_item", $rep_id );
		$arr_litdate_ids = $related_litdates['litdate_posts'];
	
		if ( empty($arr_litdate_ids) ) {
			$info .= "No related litdates were found using the get_related_litdates fcn.<br />"; // tft
		}
    
	}

	// Check litdate_ids to see if they're already in the repertoire_litdates array and add them if not
	foreach($arr_litdate_ids as $litdate_id) {
		if ( !in_array( $litdate_id, $repertoire_litdates ) ) {
			$repertoire_litdates[] = $litdate_id;
			$updates = true;
		} else {
			$info .= "The litdate_id [$litdate_id] is already in the array.<br />";	
		}
	}
	
	// If changes have been made, then update the repertoire_litdates field with the modified array of litdate_id values
	if ( $updates == true ) {
		if ( update_field('repertoire_litdates', $repertoire_litdates, $rep_id ) ) {
			$info .= "Success! repertoire_litdates field updated<br />";
			$info .= "Updated repertoire_litdates: <pre>".print_r($repertoire_litdates,true)."</pre>";
		} else {
			$info .= "phooey. update failed.<br />";
		}
	} else {
		$info .= "No update needed.<br />";
	}
	
	$info .= "+++++<br /><br />";
	
	return $info;
	
}*/


/* ~~~ Display functions ~~~ */

function get_cpt_repertoire_content( $post_id = null ) {
	
	// TS/logging setup
    $do_ts = devmode_active(); 
    $do_log = false;
    sdg_log( "divline2", $do_log );
    
	// Init vars
    $arr_info = array();
    $info = "";
    $ts_info = "";
    
	if ($post_id === null) { $post_id = get_the_ID(); }
	//$ts_info .="[get_cpt_repertoire_content] post_id: $post_id<br />";
	
    $arr_rep_info = get_rep_info( $post_id, 'display', true, true ); // get_rep_info( $post_id = null, $format = 'display', $show_authorship = true, $show_title = true )
	$rep_info = $arr_rep_info['info'];
	$ts_info .= $arr_rep_info['ts_info'];
						
	if ( $rep_info ) {
        //$info .= "<h3>The Work:</h3>";
        $info .= $rep_info;
    }
    
    // Related Events
    $repertoire_events = get_field('repertoire_events', $post_id, false);
	if ( empty($repertoire_events) && is_dev_site() ) {
		// Field repertoire_events is empty -> check to see if updates are in order
		$ts_info .= '<!-- '.update_repertoire_events( $post_id ).' -->';
	}
    
    if ( $repertoire_events ) { 
        //global $post;
        //-- STC
        $info .= "<h3>Performances at Saint Thomas Church:</h3>";
        $x = 1;
        foreach($repertoire_events as $event_post_id) { 
            //setup_postdata($event_post);
            //$ts_info .= "[$x] event_post: <pre>".print_r($event_post, true)."</pre>"; // tft
            //$event_post_id = $event_post->ID;
            
            // TODO: modify to show title & event date as link text
            $event_title = get_the_title($event_post_id);
            $date_str = get_post_meta( $event_post_id, '_event_start_date', true );
            if ( $date_str ) { $event_title .= ", ".$date_str; }
            $info .= make_link( get_the_permalink($event_post_id), $event_title, null, null, "_blank" ) . "<br />";
            
            $x++;
        }
    } else {
        if ( devmode_active() ) { 
            $info .= "<p>No related events were found.</p>"; // tft
        }
    }
    
    wp_reset_query();
    
    // Related Editions
    $related_editions = get_field('related_editions', $post_id, false);
    
    if ( $related_editions &&
        ( ( is_dev_site() && current_user_can('read_repertoire') ) || current_user_can('read_music') ) 
       ) {
       	//-- STC
        $info .= "<h3>Edition(s) in the Saint Thomas Library:</h3>";
        //$ts_info .= "<pre>related_editions: ".print_r($related_editions, true)."</pre>";
        foreach ( $related_editions as $edition_id ) {
            //$ts_info .= "edition_id: ".$edition_id."<br />";
            $info .= make_link( get_the_permalink($edition_id), get_the_title($edition_id) ) . "<br />";
        }        
    }
    
    // Possible Duplicate Posts
    /*$dupes = get_possible_duplicate_posts ( $post_id );
    $duplicate_posts = $dupes['posts'];
    $duplicate_posts_info = $dupes['info'];
    
    if ( $duplicate_posts ) { 
        
        $ts_info .= "<h3>Possible Duplicate(s):</h3>";
        $x = 1;
        foreach($duplicate_posts as $duplicate_post) { 
        
            setup_postdata($duplicate_post);
            //$ts_info .= "[$x] duplicate_post: <pre>".print_r($duplicate_post, true)."</pre>"; // tft
            $duplicate_post_id = $duplicate_post->ID;
            
            $ts_info .= make_link( get_the_permalink($duplicate_post_id), $duplicate_post->post_title, null, null, "_blank" ) . "<br />";
            
            // TODO: build in merge options
                        
            $x++;
        }
    } else {
        if ( devmode_active() ) { 
            $ts_info .= "<p>No duplicate posts were found.</p>"; // tft
        }
    }*/
    
    //$ts_info .= "test"; // tft
    //if ( $do_ts === true || $do_ts == "mlib" ) { $ts_info = '<div class="troubleshooting">'.$ts_info.'</div>'; }
	
	$arr_info['info'] = $info;
    if ( $do_ts === true || $do_ts == "mlib" ) { $arr_info['ts_info'] = $ts_info; } else { $arr_info['ts_info'] = null; }
    
    return $arr_info;
}

/*********** CPT: EDITION ***********/
function get_cpt_edition_content( $post_id = null ) {
    
    // TS/logging setup
    $do_ts = devmode_active(); 
    $do_log = false;
    sdg_log( "divline2", $do_log );
	
	// Init vars
	$info = "";
	$ts_info = "";
	if ($post_id === null) { $post_id = get_the_ID(); }
	
	$ts_info .= "<!-- edition post_id: $post_id -->";
    
    // Musical Work
    if ( get_field( 'repertoire_editions', $post_id )  ) {
        $repertoire_editions = get_field( 'repertoire_editions', $post_id );
        //$info .= '<pre>'.print_r($repertoire_editions, true).'</pre>';
        foreach ( $repertoire_editions as $musical_work_id ) {
            $info .= "<h3>".get_the_title($musical_work_id)."</h3>";
            //$info .= "<h3>".$musical_work->post_title."</h3>";
        }
    } elseif ( get_field( 'musical_work', $post_id )  ) {
        //$ts_info .= '<p class="devinfo">'."This record requires an update. It is using the old musical_work field and should be updated to use the new bidirectional repertoire_editions field.".'</p>';
        $ts_info .= '<!-- NB: This record requires an update. It is using the old musical_work field and should be updated to use the new bidirectional repertoire_editions field -->';
        $musical_works = get_field( 'musical_work', $post_id );
        //$info .= '<pre>'.print_r($musical_works, true).'</pre>';
        foreach ( $musical_works as $musical_work ) {
            $info .= "<h3>".$musical_work->post_title."</h3>";
        }
    } else {
        $ts_info .= "<!-- No musical_work found for edition with id: $post_id -->";
    }
    
    // TODO: use get_rep_info to make more refined view?
    
    /*$rep_info = get_rep_info( $post_id, 'display', true, true ); // get_rep_info( $post_id = null, $format = 'display', $show_authorship = true, $show_title = true )
	if ( $rep_info ) {
        $info .= "<h3>The Work:</h3>";
        $info .= $rep_info;
    }*/
    
    $info .= '<table class="edition_info">';
    //$info .= '<tr><td class="label"></td><td></td></tr>';
        
    // Publication Info
    if ( get_field( 'editor', $post_id )  ) {
        $editors = get_field( 'editor', $post_id );
        //$info .= '<tr><td><pre>'.print_r($editors, true).'</pre></td></tr>';
        foreach ( $editors as $editor ) {
            $info .= '<tr><td class="label">Editor</td><td>'.$editor->post_title.'</td></tr>';
        }
    }
    if ( get_field( 'publisher', $post_id )  ) {
        $publishers = get_field( 'publisher', $post_id );
        foreach ( $publishers as $publisher ) {
            $info .= '<tr><td class="label">Publisher</td><td>'.$publisher->post_title.'</td></tr>';
        }
    }
    if ( get_field( 'publication', $post_id )  ) {
        $publications = get_field( 'publication', $post_id );
        //$info .= '<tr><td class="label">Publication</td><td><pre>'.print_r($publications, true).'</pre></td></tr>'; // tft
        foreach ( $publications as $publication ) {
            if ( is_object($publication) ) { $publication_title = $publication->post_title; } else { $publication_title = get_the_title($publication); }
            $info .= '<tr><td class="label">Publication</td><td>'.$publication_title.'</td></tr>';
        }
    }
    if ( get_field( 'publication_date', $post_id )  ) {
        $publication_date = get_field( 'publication_date', $post_id );
        $info .= '<tr><td class="label">Publication Date</td><td>'.$publication_date.'</td></tr>';
    }
    
    // Choir Forces
    if ( get_field( 'choir_forces', $post_id )  ) {
        $choir_forces = get_field( 'choir_forces', $post_id );
        //$info .= '<tr><td class="label">Choir Forces</td><td><pre>'.print_r($choir_forces, true).'</pre></td></tr>';
        foreach ( $choir_forces as $choir ) {
            if ( is_array($choir) ) { $choir_label = $choir['label']; } else { $choir_label = $choir; }
            $info .= '<tr><td class="label">Choir Forces</td><td>'.$choir_label.'</td></tr>';
        }
    }
    
    
    // TODO: streamline this to process array of taxonomies
        
    // Get and display term names for "voicing"
    $voicings = wp_get_post_terms( $post_id, 'voicing', array( 'fields' => 'names' ) );
    $voicings_str = "";
    if ( count($voicings) > 0 ) {
        foreach ( $voicings as $voicing ) {
            //$voicings_str .= '<span class="voicing">';
            //$voicings_str .= '<pre>'.print_r($voicing, true).'</pre>';
            $voicings_str .= $voicing;
            //$voicings_str .= '</span>';
        }
    } else {
        $voicings_str = '<span class="fyi">N/A</span>';
    }
    $info .= '<tr><td class="label">Voicing</td><td>'.$voicings_str.'</td></tr>';
    
    // Get and display term names for "soloists"
    $soloists = wp_get_post_terms( $post_id, 'soloist', array( 'fields' => 'names' ) );
    $soloists_str = "";
    #$info .= print_r($soloists, true); // tft
    if ( count($soloists) > 0 ) {
        foreach ( $soloists as $soloist ) {
            $soloists_str .= $soloist;
        }
    } else {
        $soloists_str = '<span class="fyi">N/A</span>';
    }
    $info .= '<tr><td class="label">Soloists</td><td>'.$soloists_str.'</td></tr>';

    // Get and display term names for "instruments"
    $instruments = wp_get_post_terms( $post_id, 'instrument', array( 'fields' => 'names' ) );
    $instruments_str = "";
    #$info .= print_r($instruments, true); // tft
    if ( count($instruments) > 0 ) {
        foreach ( $instruments as $instrument ) {
            $instruments_str .= $instrument;
        }
    } else {
        $instruments_str = '<span class="fyi">N/A</span>';
    }
    $info .= '<tr><td class="label">Instruments</td><td>'.$instruments_str.'</td></tr>';
    
    // Get and display term names for "keys"
    $keys = wp_get_post_terms( $post_id, 'key', array( 'fields' => 'names' ) );
    $keys_str = "";
    #$info .= print_r($keys, true); // tft
    if ( count($keys) > 0 ) {
        foreach ( $keys as $key ) {
            $keys_str .= $key;
        }
    } else {
        $keys_str = '<span class="fyi">N/A</span>';
    }
    $info .= '<tr><td class="label">Key(s)</td><td>'.$keys_str.'</td></tr>';
    
    // WIP -- still to add:
    // library tags
        
    // Library Info
    //if ( current_user_can('music') ) {
    if ( current_user_can('read_music') ) { // Why is this generating an error?

        if ( $box_num = get_field( 'box_num', $post_id ) ) {            
            $info .= '<tr><td class="label">Call Num</td><td>'.$box_num.'</td></tr>';
        }
        if ( $library_notes = get_field( 'library_notes', $post_id ) ) {
            $info .= '<tr><td class="label">Library Notes</td><td>'.$library_notes.'</td></tr>';
        }
        if ( $scores = get_field( 'scores', $post_id ) ) {
            //$info .= '<tr><td class="label">Score(s)</td><td>'.$scores.'</td></tr>';
        }

    }

    $info .= '</table>';
    
    if ( $do_ts ) { $info .= $ts_info; }
    
	return $info;
	
}

// Function to determine if rep work is of anonymous or unknown authorship
function is_anon( $post_id = null ) {
    
    // Init vars
	if ($post_id === null) { $post_id = get_the_ID(); }
	if ( empty($post_id) ) { return null; }
    $info = "";
    $composers_str = "";
    $anon = false;
    
    // Do nothing if post_id is empty or this is not a rep record
    if ( $post_id === null || get_post_type( $post_id ) != 'repertoire' ) { return null; }
    
    $composers = get_field('composer', $post_id, false);
    if ( $composers ) {
    	foreach ( $composers as $composer ) {
			if ( $composer ) { $composers_str .= get_the_title($composer); }
		}
    }    
    
    if ( $composers_str == '[Unknown]' || $composers_str == 'Unknown' || $composers_str == 'Anonymous' || $composers_str == 'Plainsong' ) {
        $anon = true;
    }
    
    return $anon;
}

// Stringify an array of person ids or objects, with formatting options
// TODO: better documentation
// TODO: add option to make_link for each name
function str_from_persons_array ( $args = array() ) {
    
    // TS/logging setup
    $do_ts = devmode_active(); 
    $do_log = false;
    sdg_log( "divline2", $do_log );
    sdg_log( "function called: str_from_persons_array", $do_log );
    
    // Init vars
    $arr_info = array();
    $info = "";
    $ts_info = "";
    
    // Defaults
	$defaults = array(
		'arr_persons'     	=> array(),
		'person_category' 	=> null,
		'post_id' 			=> null,
		'format'    		=> 'display', // other possible values include: "post_title", "edition_title" -- ??
		'arr_of'    		=> 'objects',
		'abbr'    			=> false,
		'links'    			=> false,
	);

	// Parse & Extract args
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
    
    //sdg_log( "[str_from_persons] arr_persons: ".print_r($arr_persons, true), $do_log );
    sdg_log( "[ssfpa] person_category: ".$person_category, $do_log );
    sdg_log( "[ssfpa] post_id: ".$post_id, $do_log );
    sdg_log( "[ssfpa] format: ".$format, $do_log );
    sdg_log( "[ssfpa] arr_of: ".$arr_of, $do_log );
    sdg_log( "[ssfpa] abbr: ".(int)$abbr, $do_log );
    sdg_log( "[ssfpa] links: ".(int)$links, $do_log );
    
    $ts_info .= "<!-- [ssfpa] format: $format -->";
    $ts_info .= "<!-- [ssfpa] person_category: $person_category -->";
    $ts_info .= "<!-- [ssfpa] arr_persons: ".print_r($arr_persons, true)." -->";
    
    foreach ( $arr_persons AS $person_id ) {

        //$info .= "<pre>person: ".print_r($person, true)."</pre>"; // tft
        
        /*if ( $arr_of == "objects" ) {
            if ( isset($person['ID']) ) { $person_id = $person['ID']; } else { $person_id = null; }
        } else {
            $person_id = $person;
        }*/
        sdg_log( "[ssfpa] person_id: ".$person_id, $do_log );
        $ts_info .= "<!-- [ssfpa] person_id: ".$person_id." -->";
        
        // Set up display args to pass to fcn get_person_display_name
        if ( $abbr || has_term( 'psalms', 'repertoire_category', $post_id ) && !has_term( 'motets', 'repertoire_category', $post_id ) && !has_term( 'anthems', 'repertoire_category', $post_id ) ) { 
        	$name_abbr = "abbr";
        } else {
        	$name_abbr = "full";
        }
        
        $override = "none";
        $use_post_title = false;
        $show_prefix = false;
        $show_suffix = false;
        $show_job_title = false;
        $show_dates = false;
        $styled = true;
        	
        if ( $person_category == "composers" || $person_category == "arrangers" ) {
        	//
        }
        
        if ( ( $format == "post_title" || $format == "edition_title" ) && ( $person_category == "composers" || $person_category == "arrangers" ) ) { 
			$show_dates = true;
			$styled = false; // don't add person_dates span/style for post_titles
		} else if ( $abbr !== true ) {
			$show_dates = true;
			$styled = true; // add dates with span/style
		}
        
        if ( $links ) {
        	// TODO: verify post_type == person?
			$person_url = esc_url( get_permalink( $person_id ) );
			if ( $person_url ) { $display_args['url'] = $person_url; }
		} else {
			$person_url = null;
		}
		
		$display_args = array( 'person_id' => $person_id, 'override' => $override, 'name_abbr' => $name_abbr, 'show_prefix' => $show_prefix, 'show_suffix' => $show_suffix, 'show_job_title' => $show_job_title, 'show_dates' => $show_dates, 'url' => $person_url, 'styled' => $styled );
        
        // Get the display_name
        $arr_person_name = get_person_display_name( $display_args );
        $person_name = $arr_person_name['info'];            
        $info .= $person_name;
        $ts_info .= $arr_person_name['ts_info'];

        if (count($arr_persons) > 1) { $info .= ", "; }

    } // END foreach $arr_persons

    // Trim trailing comma and space
    if ( substr($info, -2) == ", " ) {
        $info = substr($info, 0, -2); // trim off trailing comma
    }
    
    $arr_info['info'] = $info;
	if ( $do_ts ) { $arr_info['ts_info'] = $ts_info; } else { $arr_info['ts_info'] = null; }
	
	return $arr_info;
    
}

// Retrieve properly formatted authorship info for Repertoire records
// Authorship: Composers, Arrangers, Transcriber, Librettists, &c.
// $format options include: display; post_title; ....? (TODO: better info here)
function get_authorship_info ( $args = array() ) {

	// TS/logging setup
	$do_ts = devmode_active(); 
    $do_log = false;
    sdg_log( "divline2", $do_log ); 
    sdg_log( "function called: get_authorship_info", $do_log );
    
    // Defaults
	$defaults = array(
		'data'     		=> array(),
		'format'    	=> 'post_title',
		'abbr'    		=> false,
		'is_single_work'=> false,
		'show_title'    => false,
		'links'    		=> false,
	);

	// Parse & Extract args
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	/*
    sdg_log( "[authorship_info] data: ".print_r($data, true), $do_log );
    sdg_log( "[authorship_info] format: ".$format, $do_log );
    sdg_log( "[authorship_info] is_single_work: ".$is_single_work, $do_log );
    sdg_log( "[authorship_info] show_title: ".$show_title, $do_log );
    sdg_log( "[authorship_info] abbr: ".(int)$abbr, $do_log );
    */
    
    // Init vars
    $arr_info = array();
    $authorship_info = "";
    $ts_info = "";
    //
    $rep_title = "";
    $composers = array();
    $arrangers = array();
    $transcribers = array();
    $translators = array();
    $librettists = array();
    //
    $anon_info = "";
    $is_anon = false;
    $is_hymn = false;
    $is_psalm = false;
    //
    if ( $format == "post_title" || $format == "edition_title" ) {
    	$html = false;
    } else {
    	$html = true;
    }
    
    // Get info either via post_id, if set, or from data array
    if ( isset($data['post_id']) ) {
        
        sdg_log( "[authorship_info] get info from data['post_id']", $do_log );
        $ts_info .= "<!-- [authorship_info] get info from data['post_id'] -->";
        
        $post_id = $data['post_id'];
        ///$ts_info .= "<!-- [authorship_info] post_id: ".$post_id." -->";
        
        if ( isset($data['rep_title']) && $data['rep_title'] != "" ) {
            $ts_info .= "<!-- [authorship_info] rep_title from data['rep_title'] -->";
            $rep_title = $data['rep_title'];
        } else {
       		$ts_info .= "<!-- [authorship_info] rep_title from post_id -->";
            $title_clean = get_post_meta( $post_id, 'title_clean' );
            if ( $title_clean != "" ) {
                $rep_title = $title_clean;
            } else {
                $rep_title = get_the_title( $post_id );
            }
        }
        
        $is_anon = is_anon($post_id);
        ///if ( $format == 'display' && $is_anon == true ) { $ts_info .= "<!-- anon: true -->"; } else { $ts_info .= "<!-- anon: false -->"; }

        // Taxonomies
        if ( has_term( 'hymns', 'repertoire_category', $post_id ) ) { $is_hymn = true; }
        if ( has_term( 'psalms', 'repertoire_category', $post_id ) ) { $is_psalm = true; }
        
        // Get postmeta
        $composers_str = "";
        $composers = get_field('composer', $post_id, false); // Can't use get_post_meta for ACF relationship fields because stored value is array
        if ( $composers ) { 
            $persons_args = array( 'arr_persons' => $composers, 'person_category' => 'composers', 'post_id' => $post_id, 'format' => $format, 'arr_of' => 'objects', 'abbr' => false, 'links' => $links );
            $arr_composers_str = str_from_persons_array ( $persons_args );
            $composers_str = $arr_composers_str['info'];
            $ts_composers = $arr_composers_str['ts_info'];
            $ts_info .= $ts_composers;
            //args: $arr_persons, $person_category = null, $post_id = null, $format = 'display', $arr_of = "objects", $abbr = false ) {
        }
        $display_composer = $composers_str;
        //
        $arrangers = get_field('arranger', $post_id, false);
        $transcribers = get_field('transcriber', $post_id, false);
        $librettists = get_field('librettist', $post_id, false);
        $translators = get_field('translator', $post_id, false);
        //
        $anon_info = get_post_meta( $post_id, 'anon_info', true ); // post_meta ok for text fields... but is it better/faster? TODO: RS //$anon_info = get_field( $post_id, 'anon_info', false );//

        // TODO: streamline this -- maybe along the lines of is_anon?
        if ( $format == 'display' ) { $ts_info .= "<!-- [authorship_info] display_composer: ".$display_composer." -->"; } // tft
        if ( $display_composer == 'Plainsong' ) { 
            $plainsong = true;
            if ( $anon_info == "" ) {
                // TODO: change composer to "Anonymous" - ??
                //$anon_info = "Plainsong"; // TMP
            }                
        } else {
            $plainsong = false;
        }
        
        ///if ( $format == 'display') { $ts_info .= "<!-- anon_info: ".$anon_info." -->"; } // tft
        
        $arr_of = 'objects';
        
    } else {
        
        sdg_log( "[authorship_info] get info from data without post_id", $do_log );
        $ts_info .= "<!-- [authorship_info] get info from data without post_id -->";
        
        $post_id = null;
        //$is_hymn
        //$is_psalm
        
        if ( isset($data['rep_title']) ) { $rep_title = $data['rep_title']; } else { $rep_title = ""; }
        
        if ( isset($data['composers']) ) { $composers = $data['composers']; }
        if ( isset($data['arrangers']) ) { $arrangers = $data['arrangers']; }
        if ( isset($data['transcribers']) ) { $transcribers = $data['transcribers']; }
        if ( isset($data['anon_info']) ) { $anon_info = $data['anon_info']; } else { $anon_info = ""; }
        if ( isset($data['is_hymn']) ) { $is_hymn = $data['is_hymn']; }
        if ( isset($data['is_psalm']) ) { $is_psalm = $data['is_psalm']; }
        
        $arr_of = 'ids';
        
    }
    if ( $rep_title == "" || empty($rep_title) || $rep_title == "Responses" ) { $show_title = false; }
    $ts_info .= "<!-- [authorship_info] rep_title: ".print_r($rep_title,true)." -->";
    
    sdg_log( "[authorship_info] anon_info: ".$anon_info, $do_log );
    //sdg_log( "[authorship_info] rep_title: ".print_r($rep_title, true), $do_log );
    
    // Build the authorship_info string
    
    // 1. Composer(s)
    if ( !empty($composers) ) { //
        
        sdg_log( "[authorship_info] composers: ".print_r($composers, true), $do_log );
        
        $persons_args = array( 'arr_persons' => $composers, 'person_category' => 'composers', 'post_id' => $post_id, 'format' => $format, 'arr_of' => $arr_of, 'abbr' => $abbr, 'links' => $links );
        sdg_log( "[authorship_info] persons_args: ".print_r($persons_args, true), $do_log );
        $ts_info .= "<!-- [authorship_info] persons_args: <pre>".print_r($persons_args, true)."</pre> -->";
        $arr_composers_str = str_from_persons_array ( $persons_args );
        $composer_info = $arr_composers_str['info'];
        $ts_composers = $arr_composers_str['ts_info'];
        $ts_info .= $ts_composers;
                
        // TODO: check instead by ID? Would be more accurate and would allow for comments to be returned by fcn str_from_persons_array
        // Redundant: TODO: instead use is_anon fcn? Any reason why not to do this?
        if ( $composer_info == '[Unknown]' || $composer_info == 'Unknown' || $composer_info == 'Anonymous' || $composer_info == 'Plainsong' ) { //
            $is_anon = true;
            sdg_log( "[authorship_info] is_anon.", $do_log);
        } else {
            sdg_log( "[authorship_info] NOT is_anon.", $do_log);
        }
        if ( $composer_info == "Unknown" || ( $composer_info == "Anonymous" && $anon_info == "" ) ) { 
            $composer_info = "";
        }
        
        sdg_log( "[authorship_info] composer_info: ".$composer_info, $do_log );
        sdg_log( "[authorship_info] anon_info: ".$anon_info, $do_log );
        
        ///$ts_info .= "<!-- composer_info: ".$composer_info." -->";
        ///$ts_info .= "<!-- anon_info: ".$anon_info." -->";
        
        if ( $composer_info != "" || $anon_info != "" ) {

            if ( $is_anon == true ) { // || $composer_info == 'Plainsong'
                if ( $anon_info != "" ) {
                	$show_anon = "";
                    // 1a. "Anonymous/anon_info"
                    //sdg_log( "[authorship_info] is_anon + anon_info", $do_log );
                    if ( $format == "post_title" || $format == "edition_title" || $format == "concert_item" ) {
                        if ( $composer_info != "" ) {
                            $show_anon .= "/";
                        }
                        $show_anon .= $anon_info;
                        //$composer_info .= "Anonymous/".$anon_info.""; // ???
                    } else if ( $is_single_work !== true && $anon_info != "Plainsong" && $is_psalm == false ) {
                        $show_anon .= " (".$anon_info.")";
                    } else if ( $is_psalm == true && $composer_info == "Plainsong" ) {
                        $show_anon .= "/".$anon_info;
                        // TODO: make this same as all plainsong? Or keep variation for Psalms only?
                    } else {
                        $show_anon .= $anon_info;
                    }
                    if ( $html ) { $show_anon = '<span class="anon_info">'.$show_anon.'</span>'; }
                    if ( !empty( $show_anon) ) { $composer_info .= $show_anon; }
                }
            }

            if ( $is_single_work !== true && $composer_info != "" ) {

                // 1b. Composer name(s)
                if ( $format == "post_title" && $composer_info != "Unknown" && $composer_info != "" ) { // && $composer_info != "Anonymous"
                    $composer_info = " -- ".$composer_info;
                } else if ( $is_psalm && $format != "concert_item" ) { // has_term( 'psalms', 'repertoire_category', $post_id )
                    $composer_info = " (".$composer_info.")";
                } else if ( $plainsong == true ) {
                    if ( $show_title == true ) {
                        $composer_info = " &mdash; ".$composer_info;
                    }
                } else if ( $is_anon == true && $show_title == true ) {
                    $composer_info = " &mdash; ".$composer_info;
                } else if ( $rep_title != "" && $show_title == true ) {
                    $composer_info = ", by ".$composer_info;
                } else if ( $format != "edition_title" && $format != "concert_item"  ) {
                    $composer_info = "by ".$composer_info;
                }
                if ( $html ) { $composer_info = '<span class="composer">'.$composer_info.'</span>'; }
            }

        }

        if ( $is_single_work == true && $composer_info != "" ) {
            if ( $is_anon == false ) { 
                $authorship_info .= "Composer(s): ";
            } else if ( $plainsong == true || stripos($composer_info, 'tone') || stripos($composer_info, 'Plainsong') || stripos($anon_info, 'Plainsong') || $anon_info == 'Plainsong' ) { // also "mode"? -- 
                // TODO after upgrade to PHP 8: str_contains ( string $haystack , string $needle )
                $authorship_info .= "Tone/Mode: ";
            } else {
                $authorship_info .= "Authorship: ";
            }
            $authorship_info .= $composer_info."<br />";
        } else {
            $authorship_info .= $composer_info;
        }
        
    } else {
        $composer_info = "";
    }

    // 2. Arranger(s)
    if ( !empty($arrangers) ) {

        $persons_args = array( 'arr_persons' => $arrangers, 'person_category' => 'arrangers', 'post_id' => $post_id, 'format' => $format, 'arr_of' => $arr_of, 'abbr' => $abbr, 'links' => $links );
        $arr_arrangers_info = str_from_persons_array ( $persons_args );
        $arrangers_info = $arr_arrangers_info['info'];
        $ts_arrangers = $arr_arrangers_info['ts_info'];
        $ts_info .= $ts_arrangers;

        if ( $is_single_work == true && $arrangers_info != "") {
            $authorship_info .= "Arranger(s): ".$arrangers_info."<br />";
        } else {
            if ( $authorship_info != "" ) {
                //$authorship_info .= ", ";
            } else if ( $format != 'edition_title' && $format != "concert_item" ) {
                //$authorship_info .= " -- ";
            }
            if ( $authorship_info != "" ) { $authorship_info .= ", "; } else if ( $format != "concert_item" ) { $authorship_info .= " -- "; }
            if ( $html ) { 
            	$authorship_info .= '<span class="arranger">arr. '.$arrangers_info.'</span>';
            } else {
            	$authorship_info .= "arr. ".$arrangers_info;
            }
            
        }

    }

	// TODO: consolidate the following three blocks into a single loop for transcribers, librettists, translators (poss also arrangers)
	
    // 3. Transcriber(s)
    if ( !empty($transcribers) ) {

        $persons_args = array( 'arr_persons' => $transcribers, 'person_category' => 'transcribers', 'post_id' => $post_id, 'format' => $format, 'arr_of' => $arr_of, 'abbr' => $abbr, 'links' => $links );
        $arr_transcribers_info = str_from_persons_array ( $persons_args );
        $transcribers_info = $arr_transcribers_info['info'];
        $ts_transcribers = $arr_transcribers_info['ts_info'];
        $ts_info .= $ts_transcribers;

        if ( $transcribers_info != "" ) {
            if ( $is_single_work == true ) {
                $authorship_info .= "Transcriber(s): ".$transcribers_info."<br />";
            } else {
                if ( $authorship_info != "" ) {
                    //$authorship_info .= ", ";
                } else if ( $format != 'edition_title' && $format != "concert_item" ) {
                    $authorship_info .= " -- ";
                }
                if ( $html ) { 
					$authorship_info .= '<span class="transcriber">transcr. '.$transcribers_info.'</span>';
				} else {
					if ( $authorship_info != "" ) { $authorship_info .= ", "; } else { $authorship_info .= " -- "; }
					$authorship_info .= "transcr. ".$transcribers_info;
				}
            }
        }
        
    }

    // 4. Librettist(s)
    if ( !empty($librettists) && $format != "post_title" && $format != "edition_title" && $format != "concert_item" ) {
        
        $persons_args = array( 'arr_persons' => $librettists, 'person_category' => 'librettists', 'post_id' => $post_id, 'format' => $format, 'arr_of' => $arr_of, 'abbr' => $abbr, 'links' => $links );
        $arr_librettists_info = str_from_persons_array ( $persons_args );
        $librettists_info = $arr_librettists_info['info'];
        $ts_librettists = $arr_librettists_info['ts_info'];
        $ts_info .= $ts_librettists;

        if ( $is_single_work == true && $librettists_info != "") {
            $authorship_info .= "Librettist(s): ".$librettists_info."<br />";
        } else {
        	if ( $html ) { 
				$authorship_info .= '<span class="librettist">text by '.$librettists_info.'</span>';
			} else {
				if ( $authorship_info != "" ) { $authorship_info .= ", "; } else { $authorship_info .= " -- "; }
				$authorship_info .= "text by ".$librettists_info;
			}            
        }

    }

    // 5. Translator(s)
    if ( !empty($translators) && $format != "post_title" ) {

        $persons_args = array( 'arr_persons' => $translators, 'person_category' => 'translators', 'post_id' => $post_id, 'format' => $format, 'arr_of' => $arr_of, 'abbr' => $abbr, 'links' => $links );
        $arr_translators_info = str_from_persons_array ( $persons_args );
        $translators_info = $arr_translators_info['info'];
        $ts_translators = $arr_translators_info['ts_info'];
        $ts_info .= $ts_translators;
		
        if ( $is_single_work == true && $translators_info != "") {
            $authorship_info .= "Translator(s): ".$translators_info."<br />";
        } else {
        	if ( $html ) { 
				$authorship_info .= '<span class="librettist">transl. '.$translators_info.'</span>';
			} else {
				if ( $authorship_info != "" ) { $authorship_info .= ", "; } else { $authorship_info .= " -- "; }
				$authorship_info .= "transl. ".$translators_info;
			}
        }

    }
    
    $arr_info['info'] = $authorship_info;
    if ( $do_ts ) { $arr_info['ts_info'] = $ts_info; } else { $arr_info['ts_info'] = null; }
    
    return $arr_info;
    
}

// Excerpted From
function get_excerpted_from( $post_id = null ) {

	// TS/logging setup
	$do_ts = devmode_active(); 
    $do_log = false;
    sdg_log( "divline2", $do_log ); 
    
    // Init vars
    $arr_info = array();
    $excerpted_from = "";
    $ts_info = "";
    
    if ( $post_id == null ) { return null; }    
    //$ts_info .= "<!-- seeking excerpted_from info for post_id: $post_id -->"; // tft
    
    $excerpted_from_post = get_field('excerpted_from', $post_id, false);
    
    if ( $excerpted_from_post ) {
        
        //$ts_info .= "<!-- excerpted_from_post: ".print_r($excerpted_from_post, true)." -->";
        
        $excerpted_from_id = $excerpted_from_post[0]; // TODO: deal w/ possibility that there may be multiple values in the array
        
        $ts_info .= "<!-- excerpted_from_id: $excerpted_from_id -->";
        
        $excerpted_from_title_clean = get_post_meta( $excerpted_from_id, 'title_clean', true );
        if ( $excerpted_from_title_clean ) {
            $excerpted_from = $excerpted_from_title_clean;
        } else {
            $excerpted_from = get_the_title($excerpted_from_id);
        }
        
    } else if ( $excerpted_from_txt = get_post_meta( $post_id, 'excerpted_from_txt', true ) ) {
        $ts_info .= "<!-- excerpted_from_txt: $excerpted_from_txt -->";
        $excerpted_from = $excerpted_from_txt;
    } else {
        $excerpted_from = null;
    }
    
    $arr_info['info'] = $excerpted_from;
    if ( $do_ts ) { $arr_info['ts_info'] = $ts_info; } else { $arr_info['ts_info'] = null; }
    
    return $arr_info;
    
}

// Retrieve full rep title and associated info. 
// Return formats include 'display' (for front end), 'txt' (for back end(, and 'sanitized' (for DB matching)
// TODO: streamline, pass args instead of separate parameters, build in more formatting options
function get_rep_info( $post_id = null, $format = 'display', $show_authorship = true, $show_title = true, $full_title = false ) {
	
	// TS/logging setup
	$do_ts = devmode_active(); 
    $do_log = false;
    sdg_log( "divline2", $do_log );
    sdg_log( "function called: get_rep_info", $do_log );
    
	// Init vars
    $arr_info = array();
    $info = "";
    $ts_info = "";    
	if ( $post_id === null ) { $post_id = get_the_ID(); }
    
    sdg_log( "[get_rep_info] post_id: ".$post_id, $do_log );
    sdg_log( "[get_rep_info] format: ".$format, $do_log );
    sdg_log( "[get_rep_info] show_authorship: ".$show_authorship, $do_log );
    sdg_log( "[get_rep_info] show_title: ".$show_title, $do_log );
    
    // Do nothing if post_id is empty or this is not a rep record
    if ( $post_id === null || get_post_type( $post_id ) != 'repertoire' ) { return null; }
    
    if ( $show_authorship == 'true' ) { $show_authorship = true; } else { $show_authorship = false; }    
    if ( $show_title == 'true' ) { $show_title = true; }
    if ( is_singular('repertoire') ) { $is_single_work = true; } else { $is_single_work = false; }
	//if ( $format == 'display') { $info = "<!-- post_id: $post_id -->"; } // tft
        
    $post_title = get_the_title( $post_id );
    $title_clean = get_post_meta( $post_id, 'title_clean', true );
    // TODO: if title_clean is empty, then $new_title_clean = make_clean_title( $post_id ) &c. ?
    $title_for_matching = get_post_meta( $post_id, 'title_for_matching', true );
    $catalog_number = get_post_meta( $post_id, 'catalog_number', true );
    $opus_number = get_post_meta( $post_id, 'opus_number', true );
    $tune_name = get_post_meta( $post_id, 'tune_name', true );
    // Consider getting all post_meta at once as array? -- $post_metas = get_post_meta(get_the_ID());

    if ( $title_clean != "" ) { $title = $title_clean; } else { $title = $post_title; $title_clean = $title; }
    
    // Hymn nums, where relevant
    if ( has_term( 'hymns', 'repertoire_category', $post_id) && $catalog_number != "" ) {
        $title = $catalog_number." &ndash; ".$title;
    }
    
    // Psalms
    if ( $is_single_work == false && $full_title == false ) {
        
        if (substr($title,0,6) == "Psalm ") {
            $title = substr($title,6);
        } else if (substr($title,0,7) == "Psalms ") {
            $title = substr($title,7);
        } else {
            //if ( $format == 'display') { $info .= "<!-- ".substr($title,0,6)." -->"; }
        }
        
    }
    
    // Psalms: Anglican Chant
    // TODO: If title starts w/ number and includes words 'Anglican Chant' and has category 'Anglican Chant' and/or 'Psalms', then fix the post_title by prepending 'Psalm'
    
    if (  $show_title == false || // ACF field option per program row
        ( $format == 'display' && $title == "Responses" ) // Responses -- don't display title in event programs, &c. -- front end display
       ){ //|| has_term( 'responses', 'repertoire_category', $post_id )
        $title = "";
        $show_title = false;
    }
    
    if ( $is_single_work == true && $title != "") {
        $info .= "Title: ".$title."<br />";
    }
    
    $arr_excerpted_from = get_excerpted_from( $post_id );
    $excerpted_from = $arr_excerpted_from['info'];
    if ( $format == 'display' ) { $info .= $arr_excerpted_from['ts_info']; }
       
    if ( $excerpted_from != "" ) {
        if ( $is_single_work == true ) {
            $info .= "Excerpted from: ".$excerpted_from."<br />";
        } else {
            $title .= ", from &lsquo;".$excerpted_from."&rsquo;";
            //$title .= ", from <em>".$excerpted_from."</em>";
        }        
    }
    
    // Catalog & Opus numbers
    if ( $catalog_number != "" && !has_term( 'hymns', 'repertoire_category', $post_id ) ) {
        if ( $is_single_work == true ) {
            $info .= "Catalog No.: ".$title."<br />";
        } else {
            $title .= ", ".$catalog_number;
        }        
    }
    if ( $opus_number != "" ) {
        if ( $is_single_work == true ) {
            $info .= "Opus No.: ".$title."<br />";
        } else {
            $title .= ", ".$opus_number;
        }
    }
    
    // Tune Name
    if ( $tune_name != "" ) {
        if ( $is_single_work == true ) {
            $info .= "Tune name: ".$tune_name."<br />";
        } else {
            $title .= " &ndash; ".$tune_name;
        }
	}
    
    // Add the assembled title to the info to be returned
    if ( $is_single_work == false ) {
        $info .= $title;
    }
    
    // Display authorship info
    if ( $show_authorship == true ) { // && $is_single_work == false
        
        $authorship_arr = array( 'post_id' => $post_id, 'rep_title' => $title );
        $authorship_args = array( 'data' => $authorship_arr, 'format' => $format, 'abbr' => false, 'is_single_work' => $is_single_work, 'show_title' => $show_title );
        $arr_authorship_info = get_authorship_info ( $authorship_args );
        $authorship_info = $arr_authorship_info['info'];
        if ( $title == "" && substr($authorship_info, 0, 2) == ", " ) { $authorship_info = substr($authorship_info, 2); } // trim leading comma and space
    
        $ts_info .= $arr_authorship_info['ts_info'];
        
        // ( $data = array(), $format = 'post_title', $abbr = false, $is_single_work = false, $show_title = true ) 
        if ( $authorship_info != "Unknown" ) {
            if ( $format == 'display' ) { $info .= '<span class="authorship">'; }
            $info .= $authorship_info;
            if ( $format == 'display' ) { $info .= '</span>'; }
        }

    } // END if ( $show_authorship == true ):
    
    if ( $format == 'sanitized' ) { 
        $info = super_sanitize_title( $info );
    } else if ( $format == 'txt' ) { 
        //$info = super_sanitize_title( $info );
    } else if ( $is_single_work == true ) {
        $ts_info .= "<!-- test -->";
    } else {
        $info = make_link( get_the_permalink( $post_id ), $info, $title_clean, 'subtle', '_blank' );
    }
	
	$arr_info['info'] = $info;
	if ( $do_ts ) { $arr_info['ts_info'] = $ts_info; } else { $arr_info['ts_info'] = null; }
	
	return $arr_info;
	
} // END function get_rep_info

function get_rep_meta_info ( $post_id = null ) {

	// TS/logging setup
	$do_ts = devmode_active(); 
    $do_log = false;
    sdg_log( "divline2", $do_log );
    sdg_log( "function called: get_rep_meta_info", $do_log );
    
	// Init vars
    $arr_info = array();
    $info = "";
    $ts_info = "";    
	if ( $post_id === null ) { $post_id = get_the_ID(); }
	
	// Get and display term names for "repertoire_category".
	$rep_categories = wp_get_post_terms( $post_id, 'repertoire_category', array( 'fields' => 'names' ) );
	if ( count($rep_categories) > 0 ) {
		foreach ( $rep_categories as $category ) {
			if ( $category != "Choral Works" ) {
				$info .= '<span class="category rep_category">';
				$info .= $category;
				$info .= '</span>';
			}                
		}
		//$info .= "Categories: ";
		//$info .= implode(", ",$rep_categories);
		//$info .= "<br />";
	}

	// Get and display term names for "season".
	//$seasons = wp_get_post_terms( $post_id, 'season', array( 'fields' => 'names' ) );
	$seasons = get_field('season', $post_id, false); // returns array of IDs
	if ( is_array($seasons) && count($seasons) > 0 ) {
		foreach ( $seasons as $season ) {
			$info .= '<span class="season">';
			$info .= ucfirst($season);
			$info .= '</span>';
		}
		//$info .= implode(", ",$seasons);
	}
	
	// Get and display post titles for "related_liturgical_dates".
	$repertoire_litdates = get_field('repertoire_litdates', $post_id, false); // returns array of IDs
	if ( $repertoire_litdates ) {

		foreach ($repertoire_litdates AS $litdate_id) {
			$info .= '<span class="liturgical_date">';
			$info .= get_the_title($litdate_id);
			$info .= '</span>';
		}

	}
	// Old version of field.
	$related_liturgical_dates = get_field('related_liturgical_dates', $post_id, false);
	if ( $related_liturgical_dates ) {

		foreach ($related_liturgical_dates AS $litdate_id) {
			$info .= '<span class="liturgical_date_old devinfo">';
			$info .= get_the_title($litdate_id);
			$info .= '</span>';
		}

	}
	
	// Get and display term names for "occasion".
	$occasions = wp_get_post_terms( $post_id, 'occasion', array( 'fields' => 'names' ) );
	if ( count($occasions) > 0 ) {
		foreach ( $occasions as $occasion ) {
			$info .= '<span class="occasion">';
			$info .= $occasion;
			$info .= '</span>';
		}
		//$info .= implode(", ",$occasions);
	}

	// Get and display term names for "voicing".
	$voicings = wp_get_post_terms( $post_id, 'voicing', array( 'fields' => 'names' ) );
	if ( count($voicings) > 0 ) {
		foreach ( $voicings as $voicing ) {
			$info .= '<span class="voicing devinfo">';
			$info .= $voicing;
			$info .= '</span>';
		}
	}

	// Get and display term names for "instrument".
	$instruments = wp_get_post_terms( $post_id, 'instrument', array( 'fields' => 'names' ) );
	if ( count($instruments) > 0 ) {
		foreach ( $instruments as $instrument ) {
			$info .= '<span class="instrumentation devinfo">';
			$info .= $instrument;
			$info .= '</span>';
		}
	}
	
	return $info;
		
}

function get_author_ids ( $post_id = null, $include_composers = true ) {
    
    $arr_ids = array();
	//if ($post_id === null) { $post_id = get_the_ID(); }
    
    // Do nothing if post_id is empty or this is not a rep record
    if ( $post_id === null || get_post_type( $post_id ) != 'repertoire' ) { return "no post_id"; } //return null; }
	
    // Get postmeta
    $composers = get_field('composer', $post_id, false);
    $arrangers = get_field('arranger', $post_id, false);
    $transcribers = get_field('transcriber', $post_id, false);

    if ( is_array($composers) ) { array_merge($arr_ids, $composers); }
    if ( is_array($arrangers) ) { array_merge($arr_ids, $arrangers); }
    if ( is_array($transcribers) ) { array_merge($arr_ids, $transcribers); }
    //if ( $arrangers ) { $arr_ids[] = $arrangers; }
    //if ( $transcribers ) { $arr_ids[] = $transcribers; }
    
    //$arr_ids[] = $composers;
    //if ( $composers ) { $arr_ids[] = $composers; }
    //if ( $arrangers ) { $arr_ids[] = $arrangers; }
    //if ( $transcribers ) { $arr_ids[] = $transcribers; }
    //array_merge($arr_ids, $composers, $arrangers, $transcribers);
    //array_merge($arr_ids, $arrangers);
    //array_merge($arr_ids, $transcribers);
    
    return $arr_ids;
    
}

function get_composer_ids ( $post_id = null ) {
    
    $arr_ids = array();
	//if ($post_id === null) { $post_id = get_the_ID(); }
    
    // Do nothing if post_id is empty or this is not a rep record
    if ( $post_id === null || get_post_type( $post_id ) != 'repertoire' ) { return "no post_id"; } //return null; }
	
    $composers = get_field('composer', $post_id, false);
    if ( !is_array($composers) ) { return null; }
    foreach ($composers AS $composer_id) {
        $arr_ids[] = $composer_id;
    }

    return $arr_ids;
    
}

/*** Choirplanner ***/

// WIP to replace pods w/ ACF
// TODO: generalize to make this not so repertoire-specific?
// https://www.advancedcustomfields.com/resources/creating-wp-archive-custom-field-filter/

/*
function match_group_field ( $field_groups, $field_name ) {
    
    $field = null;
    
    // Loop through the field_groups and their fields to look for a match (by field name)
    foreach ( $field_groups as $group ) {

        $group_key = $group['key'];
        //$info .= "group: <pre>".print_r($group,true)."</pre>"; // tft
        $group_title = $group['title'];
        $group_fields = acf_get_fields($group_key); // Get all fields associated with the group
        //$field_info .= "<hr /><strong>".$group_title."/".$group_key."] ".count($group_fields)." group_fields</strong><br />"; // tft

        $i = 0;
        foreach ( $group_fields as $group_field ) {

            $i++;

            if ( $group_field['name'] == $field_name ) {

                // field exists, i.e. the post_type is associated with a field matching the $field_name
                $field = $group_field;
                // field_object parameters include: key, label, name, type, id -- also potentially: 'post_type' for relationship fields, 'sub_fields' for repeater fields, 'choices' for select fields, and so on

                //$field_info .= "Matching field found for field_name $field_name!<br />"; // tft
                //$field_info .= "<pre>".print_r($group_field,true)."</pre>"; // tft

                /*
                $field_info .= "[$i] group_field: <pre>".print_r($group_field,true)."</pre>"; // tft
                $field_info .= "[$i] group_field: ".$group_field['key']."<br />";
                $field_info .= "label: ".$group_field['label']."<br />";
                $field_info .= "name: ".$group_field['name']."<br />";
                $field_info .= "type: ".$group_field['type']."<br />";
                if ( $group_field['type'] == "relationship" ) { $field_info .= "post_type: ".print_r($group_field['post_type'],true)."<br />"; }
                if ( $group_field['type'] == "select" ) { $field_info .= "choices: ".print_r($group_field['choices'],true)."<br />"; }
                $field_info .= "<br />";
                //$field_info .= "[$i] group_field: ".$group_field['key']."/".$group_field['label']."/".$group_field['name']."/".$group_field['type']."/".$group_field['post_type']."<br />";
                */
                /*

                break;
            }

        }

        if ( $field ) { 
            //$field_info .= "break.<br />";
            break;  // Once the field has been matched to a post_type field, there's no need to continue looping
        }

    } // END foreach ( $field_groups as $group )
    
    return $field;
}
*/


//
function format_search_results ( $post_ids, $search_type = "choirplanner" ) {
    
    // init
    $info = ""; 
    $ts_info = "";
    
    $ts_info .= "+~+~+~+~+~+~+~+~+~+~ format_search_results +~+~+~+~+~+~+~+~+~+~<br />";
    
    // TODO: generalize -- this is currently very specific to display of repertoire/editions info
    //if ( $search_type = "choirplanner" ) { }
    
    // TODO: untangle results if search was run for multiple post types
    // First match repertoire to editions? merge data somehow? 
    // deal w/ rep with no related editions
    // For repertoire-type posts, ...
    // For edition-type posts, ...
    
    //$posts = $posts->posts; // Retrieves an array of WP_Post Objects
    $rep_ids = array();
    foreach ( $post_ids as $post_id ) {
            
        //$info .= '<pre>'.print_r($post, true).'</pre>';
        //$info .= '<div class="troubleshooting">post: <pre>'.print_r($post, true).'</pre></div>';
        $post_type = get_post_type($post_id);
        //$ts_info .= 'post_id: '.$post_id."<br />";
        //$ts_info .= 'post_type: '.$post_type."<br />";
        if ( $post_type == "edition" ) {
            // Get the related repertoire record(s)
            if ( $repertoire_editions = get_field( 'repertoire_editions', $post_id ) ) { //  && !empty($repertoire_editions)
                $ts_info .= 'repertoire_editions for edition with post_id '.$post_id.': <pre>'.print_r($repertoire_editions, true).'</pre>';
                foreach ( $repertoire_editions as $musical_work ) {
                    if ( is_object($musical_work) ) {
                        $rep_ids[] = $musical_work->ID;
                    } else {
                        $rep_ids[] = $musical_work;
                    }
                }
            } elseif ( $musical_works = get_field( 'musical_work', $post_id )  ) {
                $ts_info .= 'musical_works for edition with post_id '.$post_id.': <pre>'.print_r($musical_works, true).'</pre>';
                $ts_info .= '<span class="devinfo">'."[$post_id] This record requires an update. It is using the old musical_work field and should be updated to use the new bidirectional repertoire_editions field.</span><br />";
                foreach ( $musical_works as $musical_work ) {
                    if ( is_object($musical_work) ) {
                        $rep_ids[] = $musical_work->ID;
                    } else {
                        $rep_ids[] = $musical_work;
                    }
                }           
            } else {
                $ts_info .= '<span class="devinfo">No musical_work found for edition with id: '.$post_id.'</span><br />';
            }
            //$rep_ids[] = $rep_post_id;
        } else if ( $post_type == "repertoire" ) {
            $rep_ids[] = $post_id;
        }
    }
    
    //$ts_info .= 'rep_ids: <pre>'.print_r($rep_ids, true).'</pre>';
    
    $rep_ids = array_unique($rep_ids);
    //$info .= 'array_unique rep_ids: <pre>'.print_r($rep_ids, true).'</pre>';
    //$info .= "<br />+++++++++++<br />";
    
    $info .= "<p>Num matching posts found: [".count($rep_ids)."]</p>";
    $limit = 100; // tft -- limit num of posts to display, lest search is broken and it tried to display thousands of records at once...
    if ( count($rep_ids) > $limit ) {
    	$info .= "<p>To keep page load times under control, only the first ".$limit." results are displayed.<br />You might want to try narrowing your search by adding additional terms or filters.</p>";
    }
    
    $info .= '<form id="cp_merge" method="get" action="/merge-records/" target="_blank">';
    //$info .= '<form id="cp_merge" method="post" action="/merge-records/" target="_blank">'; // This works fine, but ids are lost on refresh of merge page. Pass them via GET instead for more flexibility.
    //$info .= '<form action="'.htmlspecialchars($_SERVER['PHP_SELF']).'" class="sdg_search_form '.$form_type.'">';
    $info .= '<table class="choirplanner search_results">';
    $info .= '<tr>';
    $info .= '<th class="actions" style="width: 2rem;"></th>'; // TODO: replace inline style w/ proper class definition
    $info .= '<th>Musical Work</th><th>Editions</th>';
    $info .= '</tr>';
    
    $i = 0;
    foreach ( $rep_ids as $rep_id ) {
        
        //$ts_info .= 'rep_id: <pre>'.print_r($rep_id, true).'</pre>';
        
        $post_id = $rep_id;
        $post_title = get_the_title($post_id);

        $title = get_field('title_clean', $post_id, false);
        if ( empty($title)) { $title = $post_title; }
        
        $info .= '<tr>';
        //
        $info .= '<td class="actions">';
        $info .= '<input type="checkbox" id="merge-'.$post_id.'" name="ids[]" value="'.$post_id.'" />'; // If using form action POST
        //$info .= '<input type="checkbox" id="merge-'.$post_id.'" name="merge-'.$post_id.'" value="'.$post_id.'" />';
        $info .= '</td>';
        //
        $info .= '<td class="repertoire">';
        $info .= '<div class="rep_item">';
        $info .= make_link( esc_url( get_permalink($post_id) ), $title, "TEST", null, '_blank' );
        $info .= "&nbsp;";
        $authorship_args = array( 'data' => array( 'post_id' => $post_id ), 'format' => 'display', 'abbr' => false, 'is_single_work' => false, 'show_title' => false, 'links' => true );
        $arr_authorship_info = get_authorship_info ( $authorship_args );
		$authorship_info = $arr_authorship_info['info'];
		$ts_info .= $arr_authorship_info['ts_info'];
        $info .= $authorship_info;
        /*
        $info .= " by ";
        // Composer(s)
        $composers = get_field('composer', $post_id, false);
        if ( $composers ) {
            foreach ( $composers AS $person_id ) {
                //$info .= "<pre>".print_r($composer, true)."</pre>";
                $composer_name = get_the_title($person_id);
                $composer_url = esc_url( get_permalink( $person_id ) );
                $info .= make_link( $composer_url, $composer_name, null, null, '_blank' );
            }
        }
        */
        $info .= ' <span class="devinfo">['.$post_id.']</span>';
        
        // Excerpted from
        $arr_excerpted_from = get_excerpted_from( $post_id );
    	$excerpted_from = $arr_excerpted_from['info'];
        if ( $excerpted_from ) { $info .= '<br /><span class="excerpted_from">Excerpted from: '.$excerpted_from.'</span>'; }
        
        // Tune Name
        $tune_name = get_field('tune_name', $post_id, false);
        if ( $tune_name ) { $info .= '<br /><span class="tune_name">Tune: '.$tune_name.'</span>'; }
        
        $info .= '</div>';

        // Get rep-specific info: rep categories, etc
        $rep_info = get_rep_meta_info($post_id);
		if ( $rep_info != "" ) { $info .= "<br />".$rep_info; }
		
		// Get and display note of num event programs which include this work, if any
		// Get Related Events        
		// New way
		$repertoire_events = get_field('repertoire_events', $post_id, false);
		if ( is_array($repertoire_events) && count($repertoire_events) > 0 ) {
			$info .= '<br /><span class="nb orange">This work appears in ['.count($repertoire_events).'] event program(s).</span>';
		} else {
			// Field repertoire_events is empty -> check to see if updates are in order
			if ( is_dev_site() ) {
				$info .= '<p class="troubleshooting">';
				$info .= update_repertoire_events( $post_id, false );
				$info .= '</p>';
			} else if ( $i < 5 ) {  // On live site, for now, limit number of records that are processed, because the queries may be slow
				$info .= '<p class="troubleshooting">{'.$i.'}'.update_repertoire_events( $post_id, false ).'</p>';
			}			
		}
	
		// Old way
		/*
		$related_events = get_related_events ( "program_item", $post_id );
		$event_post_ids = $related_events['event_posts'];

		if ( $event_post_ids ) {
			$info .= '<br /><span class="nb orange">This work appears in ['.count($event_post_ids).'] event program(s).</span>';
		}
		*/
	
        $info .= '</td>';

        // Related Editions
        $related_editions = get_field('repertoire_editions', $post_id, false);
        if ( empty($related_editions) ) {
            $related_editions = get_field('related_editions', $post_id, false);
        }
        //$info .= 'related_editions: <pre>'.print_r($related_editions, true).'</pre>';
        
        $info .= '<td class="editions">';
        
        if ( empty($related_editions) ) {

            $editions = '<div class="edition_info">';
            $editions .= "<span>No editions found in library database.</span>";
            $editions .= '</div>';

        } else {

            $editions = ""; // init
            $i = 1; // init counter

            foreach ( $related_editions AS $edition_id ) {

                //$info .= "<pre>".print_r($edition, true)."</pre>";
                
                $editions .= '<div class="edition_info">';
                $editions .= '<span class="counter">';
                $edition_url = esc_url( get_permalink( $edition_id ) );
                $editions .= make_link( $edition_url, '('.$i.')', null, null, '_blank' );
                //$editions .= '('.$i.')';
                $editions .= '</span>';

                // Publication Info
                $publication = get_field('publication', $edition_id);
                if ( $publication ){
                    $editions .= '<span class="publication">'; // publisher
                    $editions .= get_the_title($publication[0]);
                    //$editions .= the_field('publication', $edition_id); // nope
                    //$editions .= print_r($publication, true); // WIP 05/31/22 -- returns ID
                    $editions .= '</span>';
                    // todo -- link to publication?
                }
                
                $publisher = get_field('publisher', $edition_id);
                if ( $publisher ){
                    if ( $publication ){
                        $editions .= "/";
                    }
                    $editions .= '<span class="publisher">';
                    $pub_abbr = get_field('abbr', $publisher[0], false);
                    if ( $pub_abbr ) { $publisher = $pub_abbr; } else { $publisher = get_the_title($publisher[0]); }
                    $editions .= $publisher;
                    $editions .= '</span>';
                    //$editions .= make_link( $publisher_url, $publisher );
                }

                // Choir Forces
                $choir_forces = get_field('choir_forces', $edition_id);
                //$choir_forces = get_field_object('choir_forces', $edition_id);
                if ( $choir_forces ) {

                    /*$editions .= "choir_forces for edition $edition_id: <pre>".print_r($choir_forces, true)."</pre>";
                    $editions .= '<span class="choir_forces">';
                    if ( isset($choir_forces['label']) ) { $editions .= $choir_forces['label']; } else { $editions .= $choir_forces; }
                    $editions .= '</span>';*/
                    foreach ( $choir_forces as $choir ) {
                        //$choir = ucwords(str_replace("_"," ",$choir));
                        //$choir = str_replace("Mens","Men's",$choir);
                        $editions .= '<span class="choir_forces">';
                        //$editions .= print_r($choir, true);
                        if ( isset($choir['label']) ) { $editions .= $choir['label']; } else { $editions .= $choir; }
                        //$editions .= $choir['label'];
                        $editions .= '</span>';
                    }

                        /*if ( is_array($choir_forces) ) {
                            $editions .= "<pre>".print_r($choir_forces, true)."</pre>";
                            foreach ( $choir_forces as $choir ) {
                                if ( $choir == "Men-s Voices" ) { $choir = "Men's Voices"; }
                                $editions .= '<span class="choir_forces">';
                                $editions .= $choir;
                                $editions .= '</span>';
                            }
                        } else {
                            if ( $choir_forces == "Men-s Voices" ) { $choir_forces = "Men's Voices"; }
                            $editions .= '<span class="choir_forces">';
                            $editions .= $choir_forces;
                            $editions .= '</span>';
                        }*/
                        //$editions .= $edition_pod->display('choir_forces');

                }

                // Voicings
                $voicings_obj_list = get_the_terms( $edition_id, 'voicing' );
                // todo -- link to voicings?
                if ( $voicings_obj_list ) {
                    $voicings_str = join(', ', wp_list_pluck($voicings_obj_list, 'name'));
                    if ( !empty($voicings_str)) {
                        $editions .= '<span class="voicing">'.$voicings_str.'</span>';
                    }
                }

                // Soloists
                $soloists_obj_list = get_the_terms( $edition_id, 'soloist' );
                if ( $soloists_obj_list ) {
                    $soloists_str = join(', ', wp_list_pluck($soloists_obj_list, 'name'));
                    if ( !empty($soloists_str)) {
                        $editions .= '<span class="soloists">'.$soloists_str.'</span>';
                    }
                }

                // Instrumentation
                $instruments_obj_list = get_the_terms( $edition_id, 'instrument' );
                if ( $instruments_obj_list ) {
                    $instruments_str = join(', ', wp_list_pluck($instruments_obj_list, 'name'));
                    if ( !empty($instruments_str)) {
                        $editions .= '<span class="instrumentation">'.$instruments_str.'</span>';
                    }
                }

                // Keys
                $keys_obj_list = get_the_terms( $edition_id, 'key' );
                if ( $keys_obj_list ) {
                    $keys_str = join(', ', wp_list_pluck($keys_obj_list, 'name'));
                    if ( !empty($keys_str)) {
                        $editions .= '<span class="keys">'.$keys_str.'</span>';
                    }
                }

                $editions .= '</div>';

                // Box Num
                $box_num = get_field('box_num', $edition_id);
                if ( $box_num ){
                    $editions .= '<div class="box_num">';
                    $editions .= $box_num;
                    //$editions .= make_link( $edition_url, $box_num );
                    $editions .= '</div>';
                }

                $editions .= "<br />";

                $i++;
            } // foreach ($related_editions AS $edition)

            if ( substr($editions, -6) == '<br />' ) { $editions = substr($editions, 0, -6); } // trim off trailing OR
            
        }
        
        $info .= $editions;
        
        $info .= '</td>';
        $info .= '</tr>';
        
        $i++;
        if ( $i >= $limit ) { break; }
        
    } // END foreach ( $posts as $post )
    
    $info .= "</table>";
    
    // Users with the appropriate permissions can merge duplicate records
    // Also check to see if there are at least two records -- otherwise there's nothing to merge!
    if ( count($rep_ids) > 2 && ( current_user_can('read_repertoire') || current_user_can('read_music') ) ) {
    	$info .= '<input type="submit" value="Merge Selected">';
    }
    
	$info .= "</form>";
	
	if ( $do_ts === true || $do_ts == "mlib" ) { $info .= '<div class="troubleshooting">'.$ts_info.'</div>'; }
    
    return $info;
    
}


/*********** CPT: GROUP ***********/
function get_cpt_group_content() {
	
	$info = "";
	$post_id = get_the_ID();
	$info .= "group post_id: $post_id<br />";
	
	return $info;
	
}

?>