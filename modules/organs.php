<?php

defined( 'ABSPATH' ) or die( 'Nope!' );

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin file, not much I can do when called directly.';
	exit;
}

/*********** Functions pertaining to CPT: ORGANS ***********/

function get_cpt_organ_content( $post_id = null ) {
	
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
    
    //
	if ( function_exists('sdg_editmode') && sdg_editmode() ) {
		
		//$settings = array( 'post_content' => true, 'instruction_placement' => 'field', 'fields' => array( 'builder', 'model', 'opus_num', 'build_year', 'build_location', 'num_manuals', 'num_divisions', 'num_ranks', 'num_stops', 'num_pipes', 'num_registers', 'num_other', 'action_type', 'venue_filename', 'venue_name', 'organ_sum_html', 'organ_html', 'specs_html', 'stops_summary' ) );
		//$info .= acf_form( $settings );
		$acf_fields = 'builder, model, opus_num, build_year, build_location, num_manuals, num_divisions, num_ranks, num_stops, num_pipes, num_registers, num_other, action_type, venue_filename, venue_name, organ_sum_html, organ_html, specs_html, stops_summary';
		$info .= do_shortcode( '[mlib_acf_form fields="'.$acf_fields.'"]' );
		
	} else {
    	
    	// If not in editmode, show content instead of acf_form -- WIP
    	
    	// TODO: use this summary_str as a model for re-titling organ posts
    	$summary_str = "";
    	
    	$builder_str = get_arr_str(get_post_meta( $post_id, 'builder', true )); //$builder = get_field( 'builder', $post_id ); //
    	$summary_str .= $builder_str;
    	
    	$opus_num = get_post_meta( $post_id, 'opus_num', true );
    	//if ( $opus_num ) { $info .= '<strong>Opus Num.</strong>: <div class="xxx wip">'.$opus_num."</div>"; }
    	if ( $opus_num ) { $summary_str .= '&nbsp;<span class="opus_num">'.$opus_num.'</span>'; }
    	
    	$model = get_post_meta( $post_id, 'model', true );
    	if ( $model ) { $summary_str .= ' / <span class="model">'.$model."</span>"; }
    	
    	$build_year = get_post_meta( $post_id, 'build_year', true );
    	//if ( $build_year ) { $info .= '<strong>Build Year:</strong>: <div class="xxx wip">'.$build_year."</div>"; }
    	if ( $build_year ) { $summary_str .= '&nbsp;(<span class="build_year">'.$build_year.'</span>)'; }
    	
    	$info .= '<h2 class="builder">'.$summary_str."</h2>";
    	
    	/*
    	$organ_sum_html = get_post_meta( $post_id, 'organ_sum_html', true );
    	if ( $organ_sum_html ) { $info .= '<div class="organ_sum_html">'.$organ_sum_html.'</div>'; }
    	//
    	$organ_html = get_post_meta( $post_id, 'organ_html', true );
    	if ( $organ_html ) { $info .= '<div class="organ_html">'.$organ_html.'</div>'; }
    	*/
    	
    	$organs_divisions = get_post_meta( $post_id, 'organs_divisions', true );
    	if ( $organs_divisions ) { $info .= '<div class="organs_divisions">'.$organs_divisions.'</div>'; }
    	//
    	//$organ_html = get_post_meta( $post_id, 'organ_html', true );
    	//if ( $organ_html ) { $info .= '<div class="organ_html">'.$organ_html.'</div>'; }
    	
    	// Specs WIP
    	$specs_html = get_post_meta( $post_id, 'specs_html', true );
    	if ( $specs_html ) { $info .= '<div class="specs_html">'.$specs_html.'</div>'; }
    	
    	$info .= "<hr />";
    	
    }
    
    if ( $ts_info != "" && ( $do_ts === true || $do_ts == "venues" ) ) { $info .= '<div class="troubleshooting">'.$ts_info.'</div>'; }
    
    return $info;
    
}

?>