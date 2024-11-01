<?php /*
Plugin Name: SPDM Shortcode Slider Plugin Multisite
Description: Jquery Featured Content Slider controlled completely by shortcodes for easy template and multisite integration (Uses featured images and has 5 default layouts). 
Plugin URI: http://mecus.es
Author: _DorsVenabili
Version: 0.2
Author URI: http://dorsvenabili.com
License: GPL2
*/
/* SPDM Shortcode Slider Multisite (Wordpress Plugin)
Copyright (C) 2013
This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with this program. If not, see  <http://www.gnu.org/licenses/>.
*/

@include_once 'wp_query_multisite.php';
/* ********************************************** */
/* ************* Add options page ************** */
/* ******************************************** */
function spdm_slider_options_pg() {
add_options_page('SPDM Slider Multisite Options', 'SPDM Shortcode Slider Multisite', 'edit_pages', 'spdm-shortcode-slider-multisite/options.php');	
}
/* ********************************************** */
/* ** Add excerpt support to Page ************** */
/* ******************************************** */
add_post_type_support('page', 'excerpt');
/* ********************************************** */
/* ************ Add post-thumbnails ************ */
/* ******************************************** */
if (function_exists('add_theme_support')){ 
add_theme_support('post-thumbnails'); 
}


// Add default slider thumb sizes
function spdm_slider_add_default_imgsize(){ 
if (function_exists('add_image_size')){ 
add_image_size('default-slide-img', 600, 400 ,true);
add_image_size('default-thumb-img', 50, 50 ,true);
}}

/* ********************************************** */
/* *************** Load Scripts **************** */
/* ******************************************** */
function spdm_slider_jquery() {
// unregister default wp jquery 
wp_deregister_script('jquery');
//set our version as the new jquery
// jquery originally used wp_register_script('jquery','http://jqueryjs.googlecode.com/files/jquery-1.3.2.min.js');
wp_register_script( 'jquery', ( "http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" ), false, '1', false);
// then load it
wp_enqueue_script('jquery');
// this is Malsup's 'jQuery Cycle Plugin' http://jquery.malsup.com/cycle/
wp_enqueue_script('cycle', network_site_url() . 'wp-content/plugins/spdm-shortcode-slider-multisite/jquery.cycle.all.min.js');
}

/* wp_enqueue_script won't work if called with wp_head action because <script> tags get outputted before wp_head runs
use template_redirect instead so that it doesent remove the jquery needed in the admin pages (pop up image boxes, expanding admin panels, etc...) | to load jquery on all pages use:  init action | on only public pages use: template_redirect action  | only admin pages use: admin_print_scripts  action */ 
/* ********************************************** */
/* *************** Load CSS ******************** */
/* ******************************************** */
function spdm_slider_css() { ?>
<link rel="stylesheet" href="<?php echo network_site_url(); ?>wp-content/plugins/spdm-shortcode-slider-multisite/style.css" type="text/css" media="screen" charset="utf-8"/>
<?php	 	
}

/* ********************************************************* */
/* more options http://jquery.malsup.com/cycle/options.html */
/* ******************************************************* */
function spdm_slider_footer_scripts() { 
	global $slider_layout;
	global $slider_fx;
	global $slider_timeout; 
	global $slider_effect_speed; 
//buttons and numbers-bottom are built the same only difference is only css styling 
if(($slider_layout == 'buttons') ||($slider_layout == 'numbers-bottom')){ ?>
<script type="text/javascript" charset="utf-8">
$(function() {
            $('#slides')
			.after('<div id="nav_num">') // adds span after cycle
			.cycle({
			pager:  '#nav_num', 
            next:   '#nav_nextslide',
            prev:   '#nav_previousslide',
			fx:      '<?php	 	 echo "$slider_fx" ; ?>',
			timeout: '<?php	 	 echo "$slider_timeout;" ;?>',
			speed:   '<?php	 	 echo "$slider_effect_speed" ;?>' //be sure to remove last comma for i.e 
            });
        });
</script><?php	 	 // end numbers-bottom and buttons
} // numbers-top same as numbers-bottom except it is put BEFORE (not after) slides and has different styles
elseif($slider_layout == 'numbers-top'){?>
<script type="text/javascript" charset="utf-8">
      $(function() { 
            $('#slides')
			.before('<div id="nav_num">') // adds span after cycle
			.cycle({
			pager:  '#nav_num',
			next:   '#nav_nextslide',
            prev:   '#nav_previousslide',
			fx:      '<?php	 	 echo "$slider_fx" ; ?>',
			timeout: '<?php	 	 echo "$slider_timeout;" ;?>',
			speed:   '<?php	 	 echo "$slider_effect_speed" ;?>'
            });
        });
</script><?php	 	 // end numbers-top
}// thumbnails bottom and thumbnails side are same except for css styling
elseif( $slider_layout == 'thumbnails-bottom' || $slider_layout == 'thumbnails-side'){ ?>
<script type="text/javascript" charset="utf-8">
      $(function() {
			$('#slides')
			.cycle({ 
  			pager:  '#nav_thumb',
			pagerAnchorBuilder: function(idx, slide) { // return selector string for existing anchor 
			return '#nav_thumb li:eq(' + idx + ') a'; },
		 	next:   '#nav_nextslide',
            prev:   '#nav_previousslide',
			fx:      '<?php	 	 echo "$slider_fx" ; ?>',
			timeout: '<?php	 	 echo "$slider_timeout;" ;?>',
			speed:   '<?php	 	 echo "$slider_effect_speed" ;?>'  
	}); 
        });	
</script><?php // end thumbnails
}// plain has no numbers, buttons, or thumbs but does have default next previous arrows
elseif( $slider_layout == 'plain'){ ?>
<script type="text/javascript" charset="utf-8">
      $(function() {
            $('#slides')
			.cycle({
			next:   '#nav_nextslide',
            prev:   '#nav_previousslide'
            });
        });
</script><?php	 	 // end plain 
}// end ifelse
}// adds scripts in footer

/* ********************************************************* */
/* [spdm_slider] shortcode function with default attributes  */
/* ******************************************************* */
function spdm_slider_handler($atts, $content=null){
// if sharing values of attributes with other functions
//declare them global in each case that they are used so values can be passed
    global $blog_id;
    global $tax_slug;
	global $term_slug;
	global $cat_id;
	global $tag_id;
	global $pageorpost_ids;
	global $sites__in;
	global $sites__not_in;
	global $post_type; 
	global $order;
	global $orderby;
	global $max_slides;	
	global $hide_arrows;
	global $slider_display_slide_title;
	global $slider_display_slide_excerpt;
	global $slider_display_thumb_title;
	global $slide_img_size;
	global $thumb_img_size;
	global $slider_fx;
	global $slider_timeout;
	global $slider_effect_speed;
	global $slider_layout;
	
	//Default sites for searching in multisite. Show results from current site if not specify
	if($sites__in!=null && $sites__in!='' && $sites__not_in!=null && $sites__not_in!=''){
		$default_site = $blog_id;
	}else{
		$default_site = '';
	}
	
//shortcodes: syntax:'attribute' => 'default' | default value will get overwitten if defined in shortcode 
extract(shortcode_atts(array(
// shortcode items used for load_spdm_slider query

'cat_id'  => '', // cat id
'tag_id'  => '', //tag id
'tax_slug'  => '', //  custom tax id
'term_slug' => '', // term id
'author_id' => '', // author ids
'pageorpost_ids' => '', // page or post ids seperated by comma
'sites__in' => $default_site, // site ids seperated by comma
'sites__not_in' => '', // site ids seperated by comma	
'post_type' => 'any', // default any, other options: page, post, attachment, orcustom post type
'order' => 'DESC', // or ASC
'orderby' => 'date', // default date, none, ID ,author, title, modified, parent, rand, comment_count
'max_slides' => '4', // amt of slides 
'hide_arrows' => 'no', // defaults to no other option 'yes'
'slider_display_slide_title'  => 'yes', // other option 'no'
'slider_display_slide_excerpt'  => 'yes', // other option 'no'
'slider_display_thumb_title'  => 'yes', // other option 'no'
//image size name used in load_spdm_slider slide & thumb image source and size
'slide_img_size' => 'default-slide-img' ,// can be large, meduim, small or custom size
'thumb_img_size'  => 'default-thumb-img', // can be, thumbnail, or custom size
// slideshow options used by spdm_slider_footer_scripts
'slider_fx'  => 'fade', // slider-transitioneffect	
'slider_timeout'  => '5000', // how many milliseconds will elapse between the start of each transition
'slider_effect_speed'  => '300', // the number of milliseconds it will take to transition from one slide to the next
// layout used by load_spdm_slider query AND spdm_slider_footer_scripts specifies which footer script is loaded and what class added to div#spdm-shortcode-slider-multisite for styling control
'slider_layout' => 'numbers-bottom', // show plain (default),  thumbnails-side , thumbnails-bottom , numbers
), $atts));  
// print_r($atts); use print line to print out attributes when testing plugin

// in case someone enters an undefined layout - defaults to plain
if( ($slider_layout !== 'thumbnails-side') && ($slider_layout !=='thumbnails-bottom') && ($slider_layout!== 'numbers-bottom') && ($slider_layout!== 'numbers-top')&& ($slider_layout!== 'buttons')){ 
$slider_layout = "plain"; 
}
// this loads the actual slider
load_spdm_slider();
}// end shortcode 


/* ********************************************** */
/* *********** This is actual slider **********  */
/* ******************************************** */
function load_spdm_slider(){ 
//must re-define global terms here or they wont get transfered from the earlier shortcode function
	global $post;
	global $blog_id;
	global $tax_slug;       
	global $term_slug; 	
	global $cat_id;        
	global $tag_id; 
	global $author_id; 		
	global $pageorpost_ids;
	global $sites__in;
	global $sites__not_in;
	global $post_type; 
	global $order;
	global $orderby; 	
	global $max_slides;	
	global $slider_layout; 	
	global $hide_arrows;
	global $slider_display_slide_title; 
	global $slider_display_slide_excerpt; 	
	global $slider_display_thumb_title; 	
	global $slide_img_size; 
	global $thumb_img_size;
	

//here we set up the cat query 
if($cat_id!=null && $cat_id!=''){	

	if($sites__in!=null && $sites__in!=''){
		$spdm_query = array (
		'post_type' => $post_type,
		'posts_per_page' => $max_slides,
		'cat' => $cat_id,
		'order' => $order,
		'orderby' => $orderby,
		'sites' => array(
			'sites__in' => explode(",", $sites__in)
			)
		);
	}elseif($sites__not_in!=null && $sites__not_in!=''){
		$spdm_query = array (
		'post_type' => $post_type,
		'posts_per_page' => $max_slides,
		'cat' => $cat_id,
		'order' => $order,
		'orderby' => $orderby,
		'sites' => array(
			'sites__not_in' => explode(",", $sites__not_in)
			)
		);
	}else{
		$spdm_query = array (
		'post_type' => $post_type,
		'posts_per_page' => $max_slides,
		'cat' => $cat_id,
		'order' => $order,
		'orderby' => $orderby,
		);
	}
}
//here we set up the tag query 
elseif($tag_id!=null && $tag_id!=''){	

	if($sites__in!=null && $sites__in!=''){
		$spdm_query = array (
		'post_type' => $post_type,
		'posts_per_page' => $max_slides,
		'tag_id' => $tag_id,
		'sites' => array(
			'sites__in' => explode(",", $sites__in)
			)
		);
	}elseif($sites__not_in!=null && $sites__not_in!=''){
		$spdm_query = array (
		'post_type' => $post_type,
		'posts_per_page' => $max_slides,
		'tag_id' => $tag_id,
		'sites' => array(
			'sites__not_in' => explode(",", $sites__not_in)
			)
		);
	}else{
		$spdm_query = array (
		'post_type' => $post_type,
		'posts_per_page' => $max_slides,
		'tag_id' => $tag_id,
		);
	}
}
//here we set up the custom tax query 
elseif($tax_slug!=null && $tax_slug!='' && $term_slug!=null && $term_slug!=''){	

	if($sites__in!=null && $sites__in!=''){
		$spdm_query = array (
		'post_type' => $post_type,
		'posts_per_page' => $max_slides,
		$tax_slug => $term_slug,
		'sites' => array(
			'sites__in' => explode(",", $sites__in)
			)
		);
	}elseif($sites__not_in!=null && $sites__not_in!=''){
		$spdm_query = array (
		'post_type' => $post_type,
		'posts_per_page' => $max_slides,
		$tax_slug => $term_slug,
		'sites' => array(
			'sites__not_in' => explode(",", $sites__not_in)
			)
		);
	}else{
		$spdm_query = array (
		'post_type' => $post_type,
		'posts_per_page' => $max_slides,
		$tax_slug => $term_slug,
		);
	}
}
//here do post or page ids
elseif($pageorpost_ids!=null && $pageorpost_ids!=''){	

	if($sites__in!=null && $sites__in!=''){
		$array_sites__in = explode(",", $sites__in);
		$spdm_query = array (
		'post_type' => $post_type,
		'posts_per_page' => $max_slides,
		'sites' => array(
			'sites__in' => $array_sites__in
			)
		);
	}elseif($sites__not_in!=null && $sites__not_in!=''){
		$spdm_query = array (
		'post_type' => $post_type,
		'posts_per_page' => $max_slides,
		'post__in' =>  explode(",", $pageorpost_ids),
		'sites' => array(
			'sites__in' => $array_sites__in
			)
		);
	}else{
		$spdm_query = array (
		'post_type' => $post_type,
		'posts_per_page' => $max_slides,
		'post__in' =>  explode(",", $pageorpost_ids)
		);
	}
}
//here do post or page ids
elseif($author_id!=null && $author_id!=''){	

	if($sites__in!=null && $sites__in!=''){
		$spdm_query = array (
		'post_type' => $post_type,
		'posts_per_page' => $max_slides,
		'author' =>  $author_id,
		'sites' => array(
			'sites__in' => explode(",", $sites__in)
			)
		);
	}elseif($sites__not_in!=null && $sites__not_in!=''){
		$spdm_query = array (
		'post_type' => $post_type,
		'posts_per_page' => $max_slides,
		'author' =>  $author_id,
		'sites' => array(
			'sites__not_in' => explode(",", $sites__not_in)
			)
		);
	}else{
		$spdm_query = array (
		'post_type' => $post_type,
		'posts_per_page' => $max_slides,
		'author' =>  $author_id
		);
	}
}
//here we set up the custom tax query 
else{

	if($sites__in!=null && $sites__in!=''){
		$spdm_query = array (
		'post_type' => $post_type,
		'posts_per_page' => $max_slides,
		'sites' => array(
			'sites__in' => explode(",", $sites__in)
			)
		);
	}elseif($sites__not_in!=null && $sites__not_in!=''){
		$spdm_query = array (
		'post_type' => $post_type,
		'posts_per_page' => $max_slides,
		'sites' => array(
			'sites__not_in' => explode(",", $sites__not_in)
			)
		);
	}else{
		$spdm_query = array (
		'post_type' => $post_type,
		'posts_per_page' => $max_slides
		);
	}
} // end query if else


//Not cache for the query results
/*$spdm_query['cache_results'] = false;
$spdm_query['update_post_meta_cache'] = false;
$spdm_query['update_post_term_cache'] = false;*/



// open slider container here we give it the slider_layout value as a class for extra styling control
?><div id="spdm-shortcode-slider-multisite" class="<?php echo $slider_layout; ?>"><?php	 	 
?><div id="slides" ><?php // slider container used by jquery cycle plugin


$postcount = 0;
$featured_query = new WP_Query_Multisite($spdm_query);

while($featured_query->have_posts()) : $featured_query->the_post();

	get_blog_post( $post->site_ID, $post->ID );

	$do_not_duplicate[] = $post->ID;
	$postcount++;
	
	// open actual slide
	?><div class="slide" id="slide-<?php echo $postcount; ?>" ><?php
	 	
	// these are the hover arrows - default is not hidden
	if( $hide_arrows !== 'yes'){ 
		?><a href="#" id="nav_previousslide"></a><a href="#" id="nav_nextslide"></a><?php	 	
	}

	// get the image filename
	$thumbnail_id = get_post_thumbnail_id($post->ID);
	$thumbnail_object = get_post($thumbnail_id); //$thumbnail_src
	$thumbnail_src = wp_get_attachment_image_src($thumbnail_id, $slide_img_size);
	

	//outputs array use print_r($thumbnail_src) to see object 0 is the img src
	// also we use $slide_img_size to determine size
	?><a href="<?php echo get_blog_permalink( $post->site_ID, $post->ID ); ?>"><img src="<?php echo $thumbnail_src[0] ?>" width="<?php echo $thumbnail_src[1]; ?>" height="<?php echo $thumbnail_src[2]; ?>"/></a><?php	 	

	// if either title or excerpt is displayed show info
	if(($slider_display_slide_title == 'yes') || ($slider_display_slide_excerpt =='yes')){ 
		?><span class="info"><?php	 	  

		// if display title is yes display title
		if($slider_display_slide_title =='yes'){
			?><h3><a href="<?php echo get_blog_permalink( $post->site_ID, $post->ID );?>" title=""><?php the_title();?></a></h3><?php	 	 
		} 
		// if display excerpt is yes display excerpt
		if($slider_display_slide_excerpt == 'yes'){
			?><?php	the_excerpt();?><?php	 	 
		} 
		?></span><?php // close info
	} // end info if 

	?></div><?php // close slide

endwhile;  // end individual loop
wp_reset_postdata();

?></div><?php // close slides div before thumbs because it needs to be in seperate thumbs for 
	
	
// if thumbnails-bottom run thumbnails loop
if($slider_layout =='thumbnails-bottom'|| $slider_layout =='thumbnails-side'){
	?><ul id="nav_thumb" class="clearfix"><?php	 	
	$postcount = 0;
	$featured_query2 = new WP_Query_Multisite($spdm_query);
	while ($featured_query2->have_posts()) : $featured_query2->the_post();
		$do_not_duplicate[] = get_the_ID();
		$postcount++;
		?><li class="thumb" id="fragment-<?php echo $postcount; ?>" ><?php	 	
		$thumbnail_id = get_post_thumbnail_id($post->ID);
		$thumbnail_object= get_post($thumbail_id);
		$thumbnail_src = wp_get_attachment_image_src($thumbnail_id, $thumb_img_size);
		?><a href="#"><img src="<?php echo $thumbnail_src[0] ?>"width="<?php echo $thumbnail_src[1]; ?>" height="<?php echo $thumbnail_src[2]; ?>"/><?php	 	  
		// if display  thumb title is yes display thumb title
		if($slider_display_thumb_title == 'yes'){
			?><h6><?php	the_title();?></h6><?php	 	 
		} 
		?></a></li><?php	 	 
	endwhile;
	wp_reset_postdata();
	?>
	<li class="clear"></li>
	</ul> 
	<div class="clear"></div>
	</div><?php	// close nav_thumb
}// end thumbnails-bottom
?></div><?php //close #spdm-shortcode-slider-multisite
}//  end load_spdm_slider function

//all actions
add_action('admin_menu', 'spdm_slider_options_pg');
add_action('admin_init', 'spdm_slider_add_default_imgsize');
add_action('template_redirect', 'spdm_slider_jquery');
add_action('wp_head', 'spdm_slider_css');
add_action('wp_footer', 'spdm_slider_footer_scripts' );
add_shortcode('spdm_slider', 'spdm_slider_handler');

?>