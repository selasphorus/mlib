<?php

defined( 'ABSPATH' ) or die( 'Nope!' );

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin file, not much I can do when called directly.';
	exit;
}

/*********** Functions pertaining to CPT: ORGANS ***********/

function get_cpt_builder_content( $post_id = null ) {
	
	// WIP -- content, organs...
	
	// This function retrieves supplementary info -- the regular content template (content.php) handles title, content, featured image
	
    // TS/logging setup
    $do_ts = devmode_active( array("mlib", "instruments") ); 
    $do_log = false;
    $fcn_id = "[mlib-get_cpt_instrument_content]&nbsp;";
    sdg_log( "divline2", $do_log );
    
    // Init vars
	$info = "";
	$ts_info = "";
	if ( $post_id === null ) { $post_id = get_the_ID(); }
	if ( $post_id === null ) { return false; }
	
    $post_meta = get_post_meta( $post_id );
	$ts_info .= $fcn_id."<pre>post_meta: ".print_r($post_meta, true)."</pre>";
	
    if ($post_id === null) { $post_id = get_the_ID(); } 
    if ( $post_id === null ) { return false; }
    
    // If not in editmode, show content instead of acf_form -- WIP
    if ( function_exists('sdg_editmode') && !sdg_editmode() ) {
    	
    	$website = get_post_meta( $post_id, 'builder_website', true );
    	if ( $website ) { $info .= '<strong>Website</strong>: <span class="url">'.$website."</span>"; }
    	
    	$location = get_post_meta( $post_id, 'location', true );
    	if ( $location ) { $info .= '<span class="location">'.$location.'</span>'; }
    	
    	$aka = get_post_meta( $post_id, 'aka', true );
    	if ( $aka ) { $info .= '<strong>Aka:</strong>: <div class="aka wip">'.$aka."</div>"; }
    	
    	//
    	
		// Get and display post titles for "related_liturgical_dates".
		$instruments = get_field('instruments', $post_id, false); // returns array of IDs
		if ( $instruments ) {
	
			foreach ($instruments AS $instrument_id) {
				$info .= '<span class="instrument">';
				$info .= get_the_title($instrument_id);
				$info .= '</span>';
			}
	
		}
    	
    }
    
    if ( $ts_info != "" && ( $do_ts === true || $do_ts == "venues" ) ) { $info .= '<div class="troubleshooting">'.$ts_info.'</div>'; }
    
    return $info;
    
}

?>