<?php
/**
Plugin Name: Paper Archive
Plugin URI: https://www.wuesti.eu/wp-paperarchive
Description: Lists PDF files from media gallery based on a conected page.
Version: 1.2
Author: Ulf Wüstefeld
Author URI: https://www.wuesti.eu
License: GPLv2
Update URI: https://www.wuesti.eu/paper-archive/paper-archive.json
*/

/* shortcode */
function fn_pdffiles($atts, $content = null)
{
	extract(shortcode_atts(array("cols" => 3,"filetype" => 'application/pdf'), $atts));
 
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
