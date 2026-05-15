<?php

defined( 'ABSPATH' ) or die( 'Nope!' );

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin file, not much I can do when called directly.';
    exit;
}

/*********** Functions pertaining to CPT: ORGANS ***********/

// WIP -- not for production use -- see organs.php
function get_cpt_instrument_content( $post_id = null )
{
    $logCtx = ['mlib', 'instruments'];
    // This function retrieves supplementary info -- the regular content template (content.php) handles title, content, featured image
    
    // Init vars
    $info = "";
    if ( $post_id === null ) { $post_id = get_the_ID(); }
    if ( $post_id === null ) { return false; }
    
    $post_meta = get_post_meta( $post_id );
    wxc_log("post_meta", $post_meta, $logCtx);
    
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
    
    return $info; 
}
