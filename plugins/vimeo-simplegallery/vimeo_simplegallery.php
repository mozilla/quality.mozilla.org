<?php
/*
Plugin Name: Vimeo SimpleGallery
Plugin URI: http://www.stianandreassen.com/plugins/vimeo-simplegallery/
Description: A Vimeo Gallery Plugin, that let's you add a gallery of videos to any Page or Post. Use [vimeogallery]{vimeo-links}[/vimeogallery], with a list of vimeo-links separated with linebreaks to add a gallery. <strong>See <a href="options-general.php?page=vimeo-gallery">Settings</a> for Usage.</strong>
Author: Stian Andreassen
Author URI: http://www.stianandreassen.com
Version: 0.2
*/

global $vimeo_gallery_count, $vimeo_gallery_ID;
$vimeo_gallery_count = 0;
$vimeo_gallery_ID = 0;

add_action('wp_head', 'vimeo_gallery_css');

// ADD OPTIONS PAGE
add_action('admin_menu', 'vimeogallery_admin');
function vimeogallery_admin() {
  add_options_page('Vimeo SimpleGallery', 'Vimeo SimpleGallery', '10', 'vimeo-gallery', 'vimeogallery_options');
  
  if($_REQUEST['action'] == 'update_vimeo_gallery'){
  	delete_option('vimeo_gallery_option');
	add_option('vimeo_gallery_option', array('thickbox' => $_REQUEST['vimeogallery_thickbox'], 'openlinks' => $_REQUEST['vimeogallery_openlinks'], 'css' => $_REQUEST['vimeogallery_css'], 'showtitles' => $_REQUEST['vimeogallery_showtitles'], 'title' => $_REQUEST['vimeogallery_title']));
  }
}

// Add settings link on plugin page  
function vimeogallery_settings_link($links) {  
$settings_link = '<a href="options-general.php?page=vimeo-gallery">Settings</a>';  
array_push($links, $settings_link);  
return $links;  
}  

// SHOW OPTIONS
function vimeogallery_options(){
?>
<style type="text/css">
<!--
	#plugins td, #plugins th {
		border-bottom: none;
		padding: 10px;
	}
	
	#plugins th {
		text-align: right;
	}
	
	#plugins tr {
		vertical-align: top;
	}
	
	#thickboxstatus {
		float: left;
		width: 250px;
		border: 1px solid #dbdbdb;
		padding: 20px;
		margin-left: 20px;
		background: #ececec;
	}
	
	#vimeo_settings {
		float: left;
	}
	
	.vimeo_code {
		font-family: Consolas, Monaco, Courier, monospace;
		margin-left: 20px;
	}
	
	.vimeo_highlight {
		background: #dedede;
	}
	
-->
</style>
<div id="wpbody-content">
	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br /></div>
		<h2>Vimeo SimpleGallery Settings</h2>
		<br />
		<?php
	    if ( $_REQUEST['update'] ) echo '<div id="message" class="updated fade"><p><strong>Settings saved.</strong></p></div>';
		$vimeo_gallery_options = get_option('vimeo_gallery_option');
		?>
		
		<div id="vimeo_settings">
		<form method="POST">
		<table class="widefat" id="plugins" style="width: 400px; clear: none;">
			<tbody>
				<tr>
					<th scope="row">Use&nbsp;JS:</th>
					<td>
						<input type="radio" name="vimeogallery_thickbox" value="shadowbox"<?php if($vimeo_gallery_options['thickbox'] == 'shadowbox') echo' checked'; ?>> Shadowbox <br />
						<input type="radio" name="vimeogallery_thickbox" value="thickbox"<?php if($vimeo_gallery_options['thickbox'] == 'thickbox') echo' checked'; ?>> Thickbox <br />
						<input type="radio" name="vimeogallery_thickbox" value="none"<?php if($vimeo_gallery_options['thickbox'] == 'none') echo' checked'; ?>> None | <input type="checkbox" name="vimeogallery_openlinks"<?php if($vimeo_gallery_options['openlinks']) echo' checked'; ?>> Open links in new window/tab<br />
						<span style="color: #999;">Opens videos in a box on your page &ndash; requires either <a href="http://wordpress.org/extend/plugins/thickbox/">Thickbox</a> or <a href="http://wordpress.org/extend/plugins/shadowbox-js/">Shadowbox</a> installed. <br /><b>Select &#171;None&#187; if you want the links to go directly to the Vimeo-page</b></span>
					</td>
				</tr>
				<tr>
					<th scope="row">Use&nbsp;CSS:</th><td><input type="checkbox" name="vimeogallery_css" value="usecss"<?php if($vimeo_gallery_options['css'] == 'usecss') echo' checked'; ?>> <span style="color: #999;">Use CSS included with plugin<br /><b>Disable if you want to use your own CSS</b></span></td>
				</tr>
				<tr>
					<th scope="row">Show&nbsp;Vimeo&nbsp;Titles</th><td><input type="checkbox" name="vimeogallery_showtitles" value="show"<?php if($vimeo_gallery_options['showtitles'] == 'show') echo' checked'; ?>> <span style="color: #999;">Automatically fetches titles from Vimeo</span></td>
				</tr>
				<tr>
					<th scope="row">Display titles:</th><td><input type="radio" name="vimeogallery_title" value="above"<?php if($vimeo_gallery_options['title'] == 'above') echo' checked'; ?>> Above thumbnails<br /><input type="radio" name="vimeogallery_title" value="below"<?php if($vimeo_gallery_options['title'] == 'below') echo' checked'; ?>> Below thumbnails</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
		<input name="update" type="submit" value="<?php _e('Update Settings'); ?>" />
		<input type="hidden" name="action" value="update_vimeo_gallery" />
		</p>
		</form>
		</div>
		
		<div id="thickboxstatus">
		<h3>Thickbox/Shadowbox status:</h3>		
		<p>
		<?php 
			if(function_exists('is_thickbox_enabled'))
			echo '<a href="http://wordpress.org/extend/plugins/thickbox/">Thickbox</a> is installed and activated';
			else
			echo 'Thickbox is NOT installed and/or not actived. Please download <a href="http://wordpress.org/extend/plugins/thickbox/">Thickbox</a>, install and activate it if you want to use it with this plugin.'; 
		?>
		</p>
		<p>
		<?php 
			if(class_exists('Shadowbox'))
			echo '<a href="http://wordpress.org/extend/plugins/shadowbox-js/">Shadowbox</a> is installed and activated';
			else
			echo 'Shadowbox is NOT installed and/or not actived. Please download <a href="http://wordpress.org/extend/plugins/shadowbox-js/">Shadowbox</a>, install and activate it if you want to use it with this plugin.'; 
		?>
		</p>
		</div>

		<h2 style="clear: both;">Usage</h2>
		<p>To embed a gallery in a Post or a Page use the following code:</p>
		<p class="vimeo_code">
			&#91;vimeogallery&#93;<br />
			http://vimeo.com/14815990<br />
			http://vimeo.com/13470805<br />
			http://vimeo.com/11147001<br />
			http://vimeo.com/14736551<br />
			&#91;/vimeogallery&#93;<br />
		</p>
		
		<p>You can override the default settings of the plugin:</p>

		<p class="vimeo_code">
			&#91;vimeogallery <span class="vimeo_highlight">js=none showtitles=true title=above</span>&#93;<br />
			http://vimeo.com/14815990<br />
			http://vimeo.com/13470805<br />
			http://vimeo.com/11147001<br />
			http://vimeo.com/14736551<br />
			&#91;/vimeogallery&#93;<br />
		</p>
		
		<p>
			<b>js</b>: shadowbox|thickbox|none<br />
			<b>showtitles</b>: true|false<br />
			<b>title</b>: above|below
		
		<h2>Donate</h2>
		<p>Like this plugin? Please donate to support development.</p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="10406928">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
	</div>
</div>
<?php
}

// GET ONLY URL IF AUTO EMBED IS ON
function getVimeoAttribute($attrib, $tag){
  //get attribute from html tag
  $re = '/'.$attrib.'=["\']?([^"\' ]*)["\' ]/is';
  preg_match($re, $tag, $match);
  if($match){
    return urldecode($match[1]);
  }else {
    return false;
  }
}

// GET VIMEO INFO
function getVimeoInfo($id) {
	if (!function_exists('curl_init')) die('CURL is not installed!');
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://vimeo.com/api/v2/video/$id.php");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	$output = unserialize(curl_exec($ch));
	$output = $output[0];
	curl_close($ch);
	return $output;
}


// OUTPUT THE GALLERY
function show_vimeogallery( $atts, $vimeolinks = null ) {
$atts = $atts;
$vimeolinks = explode("\n", $vimeolinks);
array_pop($vimeolinks);
array_shift($vimeolinks);

$vimeooptions = get_option('vimeo_gallery_option');
// CHECK IF USER OVERRIDES DEFAULT SETTINGS
if($atts['js']=='shadowbox') $vimeooptions['thickbox'] = 'shadowbox';
if($atts['js']=='thickbox') $vimeooptions['thickbox'] = 'thickbox';
if($atts['js']=='none') $vimeooptions['thickbox'] = 'none';
if($atts['showtitles']=='false') $vimeooptions['showtitles'] = 'false';
if($atts['showtitles']=='true') $vimeooptions['showtitles'] = 'show';
if($atts['title']=='above') $vimeooptions['title'] = 'above';
if($atts['title']=='below') $vimeooptions['title'] = 'below';


global $vimeo_gallery_count, $vimeo_gallery_ID;
$x = $vimeo_gallery_count;
$vimeo_gallery_ID++;
$vimeo_gallery_options = get_option('vimeo_gallery_option');

$showgallery = ('<div id="vimeo_gallery_'.$vimeo_gallery_ID.'" class="vimeo_gallery"><div class="vimeo_gallery_divider"></div><br />'."\n");
	foreach ( $vimeolinks as $thumbnails ):
		$x++;

		if(get_option('embed_autourls')=='1'){
		$vimeoID = getVimeoAttribute('src', $thumbnails);
		$vimeoID = str_replace('http://player.vimeo.com/video/', '', $vimeoID);
		}
		else{		
		$vimeoID = str_replace('http://vimeo.com/', '', $thumbnails);		
		}
		$vimeoID = str_replace('<br />', '', $vimeoID);
		
		$vimeoinfo = getVimeoInfo($vimeoID);
		
		// IF USE SHADOWBOX
		if($vimeooptions['thickbox'] == 'shadowbox') {
			$showgallery .= '<div id="vimeo_gallery_item_'.$x.'" class="vimeo_gallery_item">'."\n";
			if($vimeooptions['title'] == 'above' && $vimeoinfo['title'] && $vimeooptions['showtitles'] == 'show' ) $showgallery .= ('<p>'.$vimeoinfo['title'] .'</p>');
			$showgallery .= '<a rel="shadowbox[Mixed];width='.$vimeoinfo['width'].';height='.$vimeoinfo['height'].'"  href="http://player.vimeo.com/video/'.$vimeoID.'" title="'.$vimeoinfo['title'] .'"><img src="'.$vimeoinfo['thumbnail_medium'].'" border="0"></a><br />';
			if($vimeooptions['title'] == 'below' && $vimeoinfo['title'] && $vimeooptions['showtitles'] == 'show' ) $showgallery .= ('<p>'.$vimeoinfo['title'].'</p>');
			$showgallery .='</div>'."\r";
		}

		// IF USE THICKBOX
		elseif($vimeooptions['thickbox'] == 'thickbox') {
			$showgallery .= '<div id="vimeo_gallery_item_'.$x.'" class="vimeo_gallery_item">'."\n";
			if($vimeooptions['title'] == 'above' && $vimeoinfo['title'] && $vimeooptions['showtitles'] == 'show' ) $showgallery .= ('<p>'.$vimeoinfo['title'] .'</p>');
			$showgallery .= '<a href="#TB_inline?height='.($vimeoinfo['height']+30).'&width='.($vimeoinfo['width']+10).'&inlineId=vimeoplayer-'.$x.'" class="thickbox"><img src="'.$vimeoinfo['thumbnail_medium'].'" border="0"></a><br />';
			if($vimeooptions['title'] == 'below' && $vimeoinfo['title'] && $vimeooptions['showtitles'] == 'show' ) $showgallery .= ('<p>'.$vimeoinfo['title'].'</p>');
			$showgallery .='</div>'."\r";
		}

		// IF GO TO VIMEO.COM
		else {
			$showgallery .= '<div id="vimeo_gallery_item_'.$x.'" class="vimeo_gallery_item">'."\n";
			if($vimeooptions['title'] == 'above' && $vimeoinfo['title'] && $vimeooptions['showtitles'] == 'show' ) $showgallery .= ('<p>'.$vimeoinfo['title'] .'</p>');
			$showgallery .= '<a href="http://vimeo.com/'.$vimeoID.'"';
			if($vimeo_gallery_options['openlinks']) $showgallery .= ' target="_blank"';
			$showgallery .= '><img src="'.$vimeoinfo['thumbnail_medium'].'" border="0"></a><br />';
			if($vimeooptions['title'] == 'below' && $vimeoinfo['title'] && $vimeooptions['showtitles'] == 'show' ) $showgallery .= ('<p>'.$vimeoinfo['title'].'</p>');
			$showgallery .= '</div>'."\r";
		}
		$vimeoID = '';
		$vimeoinfo = '';
	endforeach;
	$showgallery .= ('<div class="vimeo_gallery_divider"></div><br clear="all" /></div>');

	if($vimeooptions['thickbox'] == 'thickbox'):	
		$x = $vimeo_gallery_count;
		reset($vimeolinks);
		foreach ( $vimeolinks as $videolink ):
			$x++;

			if(get_option('embed_autourls')=='1'){
			$vimeoID = getVimeoAttribute('src', $thumbnails);
			$vimeoID = str_replace('http://player.vimeo.com/video/', '', $vimeoID);
			}
			else{		
			$vimeoID = str_replace('http://vimeo.com/', '', $videolink);		
			}
			$vimeoID = str_replace('<br />', '', $vimeoID);
		
			$vimeoinfo = getVimeoInfo($vimeoID);
		

			$showgallery .= '
			<div id="vimeoplayer-'.$x.'" style="display: none; text-align: center;"><br />
			<iframe src="http://player.vimeo.com/video/'.$vimeoID.'" width="'.$vimeoinfo['width'].'" height="'.$vimeoinfo['height'].'" frameborder="0"></iframe>
			</div>
			';
		endforeach;
	endif;

$vimeo_gallery_count = $x;
return $showgallery;
}
add_shortcode('vimeogallery', 'show_vimeogallery');

// ON INSTALL PLUGIN
function vimeogallery_install(){
		add_option('vimeo_gallery_option', array('thickbox' => 'shadowbox', 'css' => 'usecss', 'title' => 'below', 'showtitles' => 'show' ));

}

// ON UNINSTALL PLUGIN
function vimeogallery_uninstall(){
		delete_option('vimeo_gallery_option');

}

// ADD CSS TO FRONTEND AND SOME JQUERY FOR CENTERING
function vimeo_gallery_css(){
global $vimeo_gallery_ID;
$vimeooptions = get_option('vimeo_gallery_option');
if($vimeooptions['css'] == 'usecss')
	echo("\n".'<link rel="stylesheet" href="'.get_bloginfo('home').'/wp-content/plugins/vimeo-simplegallery/vimeo_simplegallery.css" type="text/css" media="screen" />');
?>

<script type="text/javascript"> 
jQuery(document).ready(function($) {
	totalwidth = ($(".vimeo_gallery").width());
	numberdivs = totalwidth/135;
	numberdivs = (numberdivs < 0 ? -1 : 1) * Math.floor(Math.abs(numberdivs))
	spacing = totalwidth - (135*numberdivs);
	$(".vimeo_gallery").css({ 'margin-left' : (spacing/2), 'margin-right' : (spacing/2) });
});
</script>
<?php
}

// LOAD WORDPRESS’ BUILT-IN JQUERY
function vimeo_gallery_init() {
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
}
add_action('init', 'vimeo_gallery_init');

// HOOK IT UP TO WORDPRESS
register_activation_hook(__FILE__,'vimeogallery_install');	
register_deactivation_hook(__FILE__,'vimeogallery_uninstall');	
add_filter("plugin_action_links_$plugin", 'vimeogallery_settings_link' ); 

?>
