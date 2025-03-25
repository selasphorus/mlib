<?php

defined( 'ABSPATH' ) or die( 'Nope!' );

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin file, not much I can do when called directly.';
	exit;
}

/*********** Functions pertaining to CPT: ORGANS ***********/

// WIP -- not for production use -- see organs.php
function get_cpt_instrument_content( $post_id = null ) {
	
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
    	
    	$builder_str = get_arr_str(get_post_meta( $post_id, 'builder', true )); //$builder = get_field( 'builder', $post_id ); //
    	$info .= '<strong>builder(s)</strong>: <div class="xxx wip">'.$builder_str."</div>";
    	
    	$model = get_post_meta( $post_id, 'model', true );
    	if ( $model ) { $info .= '<strong>Model</strong>: <div class="xxx wip">'.$model."</div>"; }
    	
    	$build_year = get_post_meta( $post_id, 'build_year', true );
    	if ( $build_year ) { $info .= '<strong>Build Year:</strong>: <div class="xxx wip">'.$build_year."</div>"; }
    	
    	$opus_num = get_post_meta( $post_id, 'opus_num', true );
    	if ( $opus_num ) { $info .= '<strong>Opus Num.</strong>: <div class="xxx wip">'.$opus_num."</div>"; }
    	
    }
    
    if ( $ts_info != "" && ( $do_ts === true || $do_ts == "venues" ) ) { $info .= '<div class="troubleshooting">'.$ts_info.'</div>'; }
    
    return $info;
    
}

?>