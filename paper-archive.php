<?php
/**
Plugin Name: Paper Archive
Plugin URI: https://www.wuesti.eu/wp-paperarchive
Description: Lists PDF files from media gallery based on a conected page.
Version: 1.1
Author: Ulf Wüstefeld
Author URI: https://www.wuesti.eu
License: GPLv2
Update URI: https://www.wuesti.eu/paper-archive/paper-archive.json
*/

/* update */
if( ! function_exists( 'my_plugin_check_for_updates' ) ){
    
    function my_plugin_check_for_updates( $update, $plugin_data, $plugin_file ){
        
        static $response = false;
        
        if( empty( $plugin_data['UpdateURI'] ) || ! empty( $update ) )
            return $update;
        
        if( $response === false )
            $response = wp_remote_get( $plugin_data['UpdateURI'] );
        
        if( empty( $response['body'] ) )
            return $update;
        
        $custom_plugins_data = json_decode( $response['body'], true );
        
        if( ! empty( $custom_plugins_data[ $plugin_file ] ) )
            return $custom_plugins_data[ $plugin_file ];
        else
            return $update;
        
    }
    
    add_filter('update_plugins_wuesti.eu', 'my_plugin_check_for_updates', 10, 3);
    
}

/* shortcode */
function fn_pdffiles($atts, $content = null)
{
	extract(shortcode_atts(array("width" => 640,"height" => 480,"src" => 'https://www.google.de/maps/@51.6691329,6.6196191,17.5z'), $atts));
 
	$args = array(
		'post_type' => 'attachment',
		'post_mime_type' => 'application/pdf',
		'numberposts' => -1,
		/*'category' => 'schuetzenzeitung',*/
		'post_status' => null,
		'orderby' => 'title',
		'post_parent' => get_the_ID() // any parent
    ); 
	
	$attachments = get_posts($args);
	
	$pdffileids = array();
	
	if ($attachments)
	{
		foreach($attachments as $post)
		{
			array_push($pdffileids,$post->ID);
		}
	}


	$cols = 3;

	$ret="";
	$number = 0;
	for($outerloop = 1; $outerloop <= (ceil((count($pdffileids)/$cols))); $outerloop++)
	{
		$ret .= '<div class="wp-block-columns is-layout-flex wp-container-4">';
		for($innerloop = 1; $innerloop <= $cols; $innerloop++)
		{
			if(array_key_exists($number, $pdffileids))
			{
				/*$file = get_attachment_link($pdffileids[$number],false);*/
				$file = wp_get_attachment_url($pdffileids[$number],false);
				$thumbnail = $file.".png";
				$title = get_post($pdffileids[$number])->post_title;
				$ret .= '<div class="wp-block-column is-layout-flow">';
				$ret .= '<figure class="wp-block-image size-large">';
				/*$ret .= get_post($pdffileids[$number])->post_mime_type;*/
				$ret .= '<h3><center>'.$title.'</center></h3><a href="'.$file.'" target="_blank">';
				$ret .= '<img decoding="async" src="'.$thumbnail.'" alt="'.$title.'">';
				$ret .= '</a>';
				$ret .= '</figure>';
				$ret .= '</div>';
			}
			else
			{
				$ret .= '<div class="wp-block-column is-layout-flow"></div>';
			}
			$number++;
		}
		
		$ret .= '</div>';
	}
	return $ret;
}

add_shortcode("paperarchive", "fn_pdffiles") ;
?>
