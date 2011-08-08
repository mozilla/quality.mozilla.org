// rate posts jquery, called from the user rating click
function rfp_rate_js( post_id, direction, rater ) {

	if ( post_id != '' ) {
		jQuery( '#rfp-rate-'+post_id+' .counter' ).text( '...' );
		jQuery( '#rfp-rate-'+post_id+' b' ).hide();
		
		jQuery.post( ajaxurl, {
			action: 'rfp_rate',
			post_id: post_id, 
			direction: direction, 
			rater: rater 
		},
		function( data ){	
			datasplit = data.split( '|' );
			jQuery( '#rfp-rate-'+post_id+' .counter' ).text( datasplit[0] ); // the new (or old) rating
			jQuery( '#rfp-rate-'+post_id+' i' ).show().text( datasplit[1] ).animate({opacity:1},2000).fadeOut('slow'); //status message
		});
	}
}


// if a post is hidden, add a 'click to show' link
jQuery(document).ready( function() {
	jQuery( '.rfp-hide' ).append( '<div class="rfp-show">Click to show this hidden item</div>' ).click( function() {
		jQuery( this ).removeClass( 'rfp-hide' );
		jQuery( '.rfp-show', this ).hide();	  // using a nice way to select children of this
	});
});






//THIS CODE IS DEPRECEIATED IN BP VERSION 1.2.4+ AND POST RATING PLUGIN VERSION 1.4
/*
jQuery(document).ready( function() {
	if ( typeof(rfp_alter_posts_legacy) != "undefined" ) {
		jQuery.getJSON( blogUrl + "/wp-content/plugins/buddypress-rate-forum-posts/rate.php", { topic_id: topic_id }, function( json ){
			if (json) { 
				jQuery.each( json, function( post_id, rfp_class ){	
					jQuery( '#post-'+post_id ).addClass( rfp_class ); // apply the css class
				});
			}
		});
	}
});
*/
