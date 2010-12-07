<?php
/**
 * @package Akismet
 */
// Widget stuff
function widget_akismet_register() {
	if ( function_exists('register_sidebar_widget') ) :
	function widget_akismet($args) {
		extract($args);
		$options = get_option('widget_akismet');
		$count = number_format_i18n(get_option('akismet_spam_count'));
		?>
			<?php echo $before_widget; ?>
				<?php echo $before_title . $options['title'] . $after_title; ?>
				<div id="akismetwrap"><div id="akismetstats"><a id="aka" href="http://akismet.com" title=""><?php printf( __( '%1$s %2$sspam comments%3$s %4$sblocked by%5$s<br />%6$sAkismet%7$s' ), '<span id="akismet1"><span id="akismetcount">' . $count . '</span>', '<span id="akismetsc">', '</span></span>', '<span id="akismet2"><span id="akismetbb">', '</span>', '<span id="akismeta">', '</span></span>' ); ?></a></div></div>
			<?php echo $after_widget; ?>
	<?php
	}

	function widget_akismet_style() {
		$plugin_dir = '/wp-content/plugins';
		if ( defined( 'PLUGINDIR' ) )
			$plugin_dir = '/' . PLUGINDIR;

		?>
<style type="text/css">
#aka,#aka:link,#aka:hover,#aka:visited,#aka:active{color:#fff;text-decoration:none}
#aka:hover{border:none;text-decoration:none}
#aka:hover #akismet1{display:none}
#aka:hover #akismet2,#akismet1{display:block}
#akismet2{display:none;padding-top:2px}
#akismeta{font-size:16px;font-weight:bold;line-height:18px;text-decoration:none}
#akismetcount{display:block;font:15px Verdana,Arial,Sans-Serif;font-weight:bold;text-decoration:none}
#akismetwrap #akismetstats{background:url(<?php echo get_option('siteurl'), $plugin_dir; ?>/akismet/akismet.gif) no-repeat top left;border:none;color:#fff;font:11px 'Trebuchet MS','Myriad Pro',sans-serif;height:40px;line-height:100%;overflow:hidden;padding:8px 0 0;text-align:center;width:120px}
</style>
		<?php
	}

	function widget_akismet_control() {
		$options = $newoptions = get_option('widget_akismet');
		if ( isset( $_POST['akismet-submit'] ) && $_POST["akismet-submit"] ) {
			$newoptions['title'] = strip_tags(stripslashes($_POST["akismet-title"]));
			if ( empty($newoptions['title']) ) $newoptions['title'] = __('Spam Blocked');
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_akismet', $options);
		}
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
	?>
				<p><label for="akismet-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="akismet-title" name="akismet-title" type="text" value="<?php echo $title; ?>" /></label></p>
				<input type="hidden" id="akismet-submit" name="akismet-submit" value="1" />
	<?php
	}

	if ( function_exists( 'wp_register_sidebar_widget' ) ) {
		wp_register_sidebar_widget( 'akismet', 'Akismet', 'widget_akismet', null, 'akismet');
		wp_register_widget_control( 'akismet', 'Akismet', 'widget_akismet_control', null, 75, 'akismet');
	} else {
		register_sidebar_widget('Akismet', 'widget_akismet', null, 'akismet');
		register_widget_control('Akismet', 'widget_akismet_control', null, 75, 'akismet');
	}
	if ( is_active_widget('widget_akismet') )
		add_action('wp_head', 'widget_akismet_style');
	endif;
}

add_action('init', 'widget_akismet_register');

// Counter for non-widget users
function akismet_counter() {
	$plugin_dir = '/wp-content/plugins';
	if ( defined( 'PLUGINDIR' ) )
		$plugin_dir = '/' . PLUGINDIR;

?>
<style type="text/css">
#akismetwrap #aka,#aka:link,#aka:hover,#aka:visited,#aka:active{color:#fff;text-decoration:none}
#aka:hover{border:none;text-decoration:none}
#aka:hover #akismet1{display:none}
#aka:hover #akismet2,#akismet1{display:block}
#akismet2{display:none;padding-top:2px}
#akismeta{font-size:16px;font-weight:bold;line-height:18px;text-decoration:none}
#akismetcount{display:block;font:15px Verdana,Arial,Sans-Serif;font-weight:bold;text-decoration:none}
#akismetwrap #akismetstats{background:url(<?php echo get_option('siteurl'), $plugin_dir; ?>/akismet/akismet.gif) no-repeat top left;border:none;color:#fff;font:11px 'Trebuchet MS','Myriad Pro',sans-serif;height:40px;line-height:100%;overflow:hidden;padding:8px 0 0;text-align:center;width:120px}
</style>
<?php
$count = number_format_i18n(get_option('akismet_spam_count'));
?>
<div id="akismetwrap"><div id="akismetstats"><a id="aka" href="http://akismet.com" title=""><div id="akismet1"><span id="akismetcount"><?php echo $count; ?></span> <span id="akismetsc"><?php _e('spam comments') ?></span></div> <div id="akismet2"><span id="akismetbb"><?php _e('blocked by') ?></span><br /><span id="akismeta">Akismet</span></div></a></div></div>
<?php
}
