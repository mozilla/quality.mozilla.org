<?php

function create_bph_menu() {
	add_submenu_page('bp-general-settings','BP Humanity','BP Humanity','administrator','bph-settings','bph_admin_page');
	add_action('admin_init', 'register_bph_settings');
}
	add_action('admin_menu', 'create_bph_menu');
	
function register_bph_settings() {	
	$input_field_names = array(
	'title',
	'question',
	'answers'
	);
foreach ($input_field_names as $field_name){ register_setting( 'bph_settings', $field_name ); }
}

function bph_admin_page() {
//Set Defaults
$default_title = "Security Question";
$default_question = 'What is the sum of 4 and 5. Please spell out the answer ~ Ex: seven';
$default_answers = 'nine';

if(get_option('title') == ''){ update_option('title', $default_title); }
if(get_option('question') == ''){ update_option('question',$default_question); }
if(get_option('answers') == ''){ update_option('answers',$default_answers); }

//Variables
$title = get_option('title');
$question = get_option('question');
$answers = get_option('answers');
?>

  <div class="wrap">
  <!-- BPH Form -->
  <form name="BPHForm" method="post" action="options.php">
  <h2>Buddpress Humanity Settings</h2>
    <?php settings_fields('bph_settings'); ?>
    <!-- Title -->
    <p>
    <label style="font-weight:bold;">Title:</label><br />
    <input style="overflow:auto;" name="title" value="<?php echo $title;?>"/><br />
    <span class="help">Enter a title for your security question</span>
    </p>
    <!-- Question -->
    <p>
    <label style="font-weight:bold;">Question:</label><br />
    <textarea style="overflow:auto;" name="question" cols="72" rows="2"><?php echo $question;?></textarea><br />
    <span>Enter your question here</span>
    </p>
    <!-- Answers -->
    <p>
    <label style="font-weight:bold;">Answers:</label><br />
    <textarea style="overflow:auto;" name="answers" cols="72" rows="2"><?php echo $answers;?></textarea><br />
    <span>Enter answers as a comma separated list</span>
    </p>
    <p><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>
  </form>
</div><!--/.wrap-->
<?php } 

function bph_field_value() {
	echo get_bph_field_value();
}
	function get_bph_field_value() {
		return apply_filters( 'get_bph_field_value', $_POST['bph_field'] );
	}
	
function bph_check_validation(){	
	global $bp;
	$bph_field_raw = $_POST['bph_field'];
	$bph_field_txt = strtolower($bph_field_raw);
	$answer_match = false;
	$answer_cs_list_raw = get_option('answers');
	$answer_cs_list = strtolower($answer_cs_list_raw);
	$accepted_answers = explode(',', $answer_cs_list);
	
	foreach ($accepted_answers as $answer){
		if ($answer == $bph_field_txt){
			$answer_match = true;
		}	
	}
	if($answer_match == false){
		$bp->signup->errors['bph_field'] = __('Sorry, please answer the question again','buddypress');
		}
		
	if (empty($bph_field_txt) || $bph_field_txt == '') {
		$bp->signup->errors['bph_field'] = __('This is a required field','buddypress');
	}
	return;
}
function bph_show_input_field(){?>
	<div style="float:left;clear:left;width:48%;margin:12px 0;" class="bph_container">
    <h4><?php echo get_option('title')?></h4>
    <p style="font-size:14px"><?php echo get_option('question')?></p>
    <?php do_action('bp_bph_field_errors') ?>
    <input type="text" name="bph_field" id="bph_field" value="<?php bph_field_value() ?>" /><br />
    
	</div><?php
}

add_action('bp_signup_validate', 'bph_check_validation');
add_action('bp_before_registration_submit_buttons', 'bph_show_input_field');
?>