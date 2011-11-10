<?php
if ( !defined( 'ABSPATH' ) ) exit;

//omgwtfbbq
//the only reason this work is that the activity::save checks for if ( !$this->component || !$this->type ) return false; prior to db insert/update
//we just scan it for our excluded list and null it out. if there is an id, then we know an update is occuring and just exit.

//we can hook on the type filter and null it out
function etivite_bp_activity_block_type_before_save( $type, &$a ) {
	global $bp;

	//if there is an id - we don't care... updating
	if ( $a->id )
		return $type;

	//Come and see the violence inherent in the system. Help! Help! I'm being repressed!
	if ( etivite_bp_activity_block_denied_activity_type_check( $a->type, $a->item_id, $a->secondary_item_id, $a->user_id, $a->component ) ) {
		$a->type = null;
		return null;
	}

	//otherwise continue if nothing happened
	return $type;

}
function etivite_bp_activity_block_type_reference_before_save( $type, $a ) {
	global $bp;

	//if there is an id - we don't care... updating
	if ( $a->id )
		return $type;

	//Come and see the violence inherent in the system. Help! Help! I'm being repressed!
	if ( etivite_bp_activity_block_denied_activity_type_check( $a->type, $a->item_id, $a->secondary_item_id, $a->user_id, $a->component ) ) {
		$a->type = null;
		return null;
	}

	//otherwise continue if nothing happened
	return $type;

}
//set this low pri so last to hook in - what if someone else is messing around
//add_filter('bp_activity_type_before_save', 'etivite_bp_activity_block_type_before_save', 9999, 2);

//php issue with reference to this
//Warning: Parameter 2 to etivite_bp_activity_block_type_before_save() expected to be a reference, value given in [site]\wp-includes\plugin.php on line 166
if ( version_compare(phpversion(), '5.3.0', 'ge') ) {
	add_filter('bp_activity_type_before_save', 'etivite_bp_activity_block_type_reference_before_save', 9999, 2);
} else {
	add_filter('bp_activity_type_before_save', 'etivite_bp_activity_block_type_before_save', 9999, 2);
}


function etivite_bp_activity_block_denied_activity_type_check( $type, $item_id, $secondary_id, $user_id, $component ) {

	$types = (array) maybe_unserialize( get_option( 'bp_activity_block_denied_activity_types') );

	//return in_array( $type, apply_filters( 'etivite_bp_activity_block_denied_activity_types', $types ) );
	return apply_filters( 'etivite_bp_activity_block_denied_activity_type_check', in_array( $type, apply_filters( 'etivite_bp_activity_block_denied_activity_types', $types ) ), $type, $item_id, $secondary_item_id, $user_id, $component );
	
}
?>
