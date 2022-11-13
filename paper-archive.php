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
#class paperarchive
#{
	function fn_pdffiles($atts, $content = null)
	{
		$atts = shortcode_atts(array(
			'cols' => 3,
			'filetype' => 'application/pdf',
			'title' => 'true',
			'orderby' => 'title'
		), $atts, 'paperarchive' );

		$args = array(
			'post_type' => 'attachment',
			'post_mime_type' => $atts['filetype'],
			'numberposts' => -1,
			'post_status' => null,
			'orderby' => $atts['orderby'],
			'post_parent' => get_the_ID() // any parent
		); 
		
		$attachments = get_posts($args);
		
		$fileids = array();
		
		if ($attachments)
		{
			foreach($attachments as $post)
			{
				array_push($fileids,$post->ID);
			}
		}
	
		$cols = $atts['cols'];
		$displaytitle = filter_var($atts['title'], FILTER_VALIDATE_BOOLEAN);
	
		$ret="";
		$number = 0;
		for($outerloop = 1; $outerloop <= (ceil((count($fileids)/$cols))); $outerloop++)
		{
			$ret .= '<div class="wp-block-columns is-layout-flex wp-container-4">';
			for($innerloop = 1; $innerloop <= $cols; $innerloop++)
			{
				if(array_key_exists($number, $fileids))
				{
					$file = wp_get_attachment_url($fileids[$number],false);
					$thumbnail = $file.".png";
					$title = get_post($fileids[$number])->post_title;
					$ret .= '<div class="wp-block-column is-layout-flow">';
					$ret .= '<figure class="wp-block-image size-large">';
					if($displaytitle)
					{
						$ret .= '<h3><center>'.$title.'</center></h3>';
					}
					$ret .= '<a href="'.$file.'" target="_blank">';
					if(str_starts_with($atts['filetype'], 'image'))
					{
						$ret .= '<img decoding="async" src="'.$file.'" alt="'.$title.'" style="img {max-width: 267px; height: auto;}">';
					}
					else
					{
						$ret .= '<img decoding="async" src="'.$thumbnail.'" alt="'.$title.'">';
					}
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
#}
add_shortcode("paperarchive", "fn_pdffiles") ;
?>
