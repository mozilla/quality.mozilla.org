<?php
/*
Plugin Name: Degradable HTML5 audio and video
Plugin URI: http://soukie.net/degradable-html5-audio-and-video-plugin/
Description: Shortcodes for HTML5 video and audio, with auto-inserted links to alternative file types, and degradable performance (lightweight Flash and download) | <a href="http://soukie.net/degradable-html5-audio-and-video-plugin/#instr" title="Usage instructions">How to use</a>
Author: Pavel Soukenik
Version: 1.8.2
Author URI: http://soukie.net

Copyright 2009-2010 Pavel Soukenik

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

function html5audiovideo_init(){
	wp_enqueue_script('jquery');
}
add_action('init', 'html5audiovideo_init');

function add_audioplayer_header() {
	$plpath = WP_PLUGIN_URL .'/degradable-html5-audio-and-video/incl';
	echo <<<END
<script type="text/javascript" src="$plpath/audio-player.js"></script>
<script type="text/javascript">
	AudioPlayer.setup("$plpath/player.swf", {
		/* Format the player by inserting lines here. See http://wpaudioplayer.com/standalone */
		width: 290,
		initialvolume: 80
	});
</script>
END;
}
add_action('wp_head','add_audioplayer_header');

function audio_shortcode( $atts, $content = null ) {
	extract( shortcode_atts( array(
	'src' => '',
	'id' => '',
	'class' => 'html5audio',
	'options' => 'controls autobuffer',
	'format' => 'auto',
	), $atts ) );
	static $aud_cnt = 0;
	$format = ' '.$format;
	if( $id == '') $id = 'html5audio-' . $aud_cnt++;
	if (substr($src, strlen($src)-4, 1)=='.') $src = substr($src, 0, strlen($src)-4);
	if ($format == ' auto') {
		if (substr($src, 0, 4)!='http') $filename = WP_CONTENT_DIR . substr($src, strlen(WP_CONTENT_DIR)-strrpos(WP_CONTENT_DIR, '/'));
		else $filename = WP_CONTENT_DIR . substr($src, strlen(WP_CONTENT_URL));
	}
	$fallbackpl = '<a href="'.$src.'.mp3" title="Click to open" id="f-'.$id.'">Audio MP3</a><script type="text/javascript">AudioPlayer.embed("f-'.$id.'", {soundFile: "'.$src.'.mp3"';
	if (strpos(' '.$options, 'autoplay')) $fallbackpl .= ', autostart: "yes"';
	if (strpos(' '.$options, 'loop')) $fallbackpl .= ', loop: "yes"';
	$fallbackpl .= '});</script>';
	if ($format == ' auto') {
		$format = ' ';
		if (file_exists($filename.'.wav')) $format .= 'wav ';
		if (file_exists($filename.'.m4a')) $format .= 'm4a ';
		if (file_exists($filename.'.ogg')) $format .= 'ogg ';
		if (file_exists($filename.'.oga')) $format .= 'oga ';
		if (file_exists($filename.'.mp3')) $format .= 'mp3';
	}
	if (strpos($format, 'og')) $html5incompl = false; else $html5incompl = true;
	$output = '<!-- degradable html5 audio and video plugin --><div class="audio_wrap '.$class.'">';
	if ($html5incompl) $output .= '<div style="display:none;">'.$fallbackpl.'</div>';
	$output .= '<audio ' . $options . ' id="' . $id . '" class="' . $class . '">';
	if (strpos($format, 'wav')) $output .= '<source src="'.$src.'.wav" type="audio/wav" />';
	if (strpos($format, 'm4a')) $output .= '<source src="'.$src.'.m4a" type="audio/mp4" />';
	if (strpos($format, 'oga')) $output .= '<source src="'.$src.'.oga" type="audio/ogg" />';
	if (strpos($format, 'ogg')) $output .= '<source src="'.$src.'.ogg" type="audio/ogg" />';
	if (strpos($format, 'mp3')) $output .= '<source src="'.$src.'.mp3" type="audio/mpeg" />';
	$output .= $fallbackpl . '</audio></div>';
	if ($html5incompl) $output .= '<script type="text/javascript">if (jQuery.browser.mozilla) {tempaud=document.getElementsByTagName("audio")[0]; jQuery(tempaud).remove(); jQuery("div.audio_wrap div").show()} else jQuery("div.audio_wrap div *").remove();</script>';
	return $output;
}
add_shortcode('audio', 'audio_shortcode');

function video_shortcode( $atts, $content = null ) {
	extract( shortcode_atts( array(
	'src' => '',
	'poster' => '',
	'id' => '',
	'class' => 'html5video',
	'options' => 'controls autobuffer',
	'width' => '480',
	'height' => '320',
	'format' => 'auto',
	), $atts ) );
	static $vid_cnt = 0;
	$format = ' '.$format;
	if (substr($src, strlen($src)-4, 1)=='.') $src = substr($src, 0, strlen($src)-4);
	$noogg=true;
	$fallbackext='.m4v';
	if (substr($src, 0, 4)!='http') $filename = WP_CONTENT_DIR . substr($src, strlen(WP_CONTENT_DIR)-strrpos(WP_CONTENT_DIR, '/'));
	else $filename = WP_CONTENT_DIR . substr($src, strlen(WP_CONTENT_URL));
	if ($format == ' auto') {
		if (file_exists($filename.'.ogg')) {$noogg=false; $oggext='.ogg';}
		if (file_exists($filename.'.ogv')) {$noogg=false; $oggext='.ogv';}
		if (file_exists($filename.'.flv')) $fallbackext='.flv';
		if (!file_exists($filename.'.m4v')) $nom4v=true;
	} else {
		if (strpos($format, 'ogg')) {$noogg=false; $oggext='.ogg';}
		if (strpos($format, 'ogv')) {$noogg=false; $oggext='.ogv';}
		if (strpos($format, 'flv')) $fallbackext='.flv';
		if (!strpos($format, 'm4v')) $nom4v=true;
	}
	if($poster == '') {if (file_exists($filename.'.jpg')) $poster = $src.'.jpg';}
	if($id == '') $id = 'html5video-' . $vid_cnt++;
	$plpath = WP_PLUGIN_URL .'/degradable-html5-audio-and-video/incl/videoplayer.swf';
	$fallbackpl = '<object width="'.$width.'" height="'.$height.'" type="application/x-shockwave-flash" data="'.$plpath.'?file='.$src.$fallbackext.'" id="f-'.$id.'"><param name="movie" value="'.$plpath.'?file='.$src.$fallbackext.'" />';
	$output = '<!-- degradable html5 audio and video plugin --><div class="video_wrap '.$class.'">';
if (!$nom4v) {
	if ($noogg) $output .= '<div style="display:none;">'.$fallbackpl.'</object></div>';
	$output .= '<video width="'.$width.'" height="'.$height.'" '.$options;
	if( $poster != '') $output .= ' poster="' . $poster . '"';
	$output .= ' id="' . $id . '" class="' . $class . '">';
	$output .= '<source src="'.$src.'.m4v" type="video/mp4" />';
	if (!$noogg) $output .= '<source src="'.$src.$oggext.'" type="video/ogg" />';
	$output .= $fallbackpl . '<p>Could not use HTML&nbsp;5 or <em>Flash</em> for playback. You can download the file as <a href="'.$src.'.m4v">MPEG4/H.264</a> or <a href="'.$src.$oggext.'">Ogg Theora</a> file.</p></object></video></div>';
	if ($noogg) $output .= '<script type="text/javascript">if (jQuery.browser.mozilla) {tempvid=document.getElementsByTagName("video")[0]; jQuery(tempvid).remove(); jQuery("div.video_wrap div").show()} else jQuery("div.video_wrap div object").remove();</script>';
}
else {
$output .= $fallbackpl.'</div>';
}
	return $output;
}
add_shortcode('video', 'video_shortcode');

?>