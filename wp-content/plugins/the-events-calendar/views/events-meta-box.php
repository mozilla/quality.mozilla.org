<script type="text/javascript" charset="utf-8">
	jQuery(document).ready(function(){
		// Register event handler for the event toggle
		jQuery("input[name='isEvent']").click(function(){ 
			if ( jQuery(this).val() == 'yes' ) {
				jQuery("#eventDetails").slideDown(200);
				jQuery("#eventBriteTicketing").slideDown(200);
			} else {
				jQuery("#eventDetails").slideUp(200);
				jQuery("#eventBriteTicketing").slideUp(200);
			}
		});
		// toggle time input
		jQuery('#allDayCheckbox').click(function(){
			jQuery(".timeofdayoptions").toggle();
			jQuery("#EventTimeFormatDiv").toggle();
		});
		if( jQuery('#allDayCheckbox').attr('checked') == true ) {
			jQuery(".timeofdayoptions").addClass("hide")
			jQuery("#EventTimeFormatDiv").addClass("hide");
		}
		// Set the initial state of the event detail and EB ticketing div
		jQuery("input[name='isEvent']").each(function(){
			if( jQuery(this).val() == 'no' && jQuery(this).attr('checked') == true ) {
				jQuery('#eventDetails, #eventBriteTicketing').hide();
			} else if( jQuery(this).val() == 'yes' && jQuery(this).attr('checked') == true ) {
				jQuery('#eventDetails, #eventBriteTicketing').show();
			}
		});
		
		//show state/province input based on first option in countries list, or based on user input of country
		function spShowHideCorrectStateProvinceInput(country) {
			if (country == 'US') {
				jQuery("#USA").removeClass("hide");
				jQuery("#International").addClass("hide");
			}
			else {
				jQuery("#International").removeClass("hide");
				jQuery("#USA").addClass("hide");				
			}
		}
		
		spShowHideCorrectStateProvinceInput(jQuery("#EventCountry > option:first").attr('label'));
		
		jQuery("#EventCountry").change(function() {
			var t = jQuery(this);
			var value = t.val();
			if( t.find('option[label="US"]').val() == value ) spShowHideCorrectStateProvinceInput('US');
			else spShowHideCorrectStateProvinceInput(null);
		});
		
		var spDaysPerMonth = [29,31,28,31,30,31,30,31,31,30,31,30,31];
		
		// start and end date select sections
		var spStartDays = [ jQuery('#28StartDays'), jQuery('#29StartDays'), jQuery('#30StartDays'), jQuery('#31StartDays') ];
		var spEndDays = [ jQuery('#28EndDays'), jQuery('#29EndDays'), jQuery('#30EndDays'), jQuery('#31EndDays') ];
				
		jQuery("select[name='EventStartMonth'], select[name='EventEndMonth']").change(function() {
			var startEnd = jQuery(this).attr("name");
			// get changed select field
			if( startEnd == 'EventStartMonth' ) startEnd = 'Start';
			else startEnd = 'End';
			// show/hide date lists according to month
			var chosenMonth = jQuery(this).attr("value");
			if( chosenMonth.charAt(0) == '0' ) chosenMonth = chosenMonth.replace('0', '');
			else chosenMonth = chosenMonth;
			// leap year
			var remainder = jQuery("select[name='Event" + startEnd + "Year']").attr("value") % 4;
			if( chosenMonth == 2 && remainder == 0 ) chosenMonth = 0;
			// preserve selected option
			var currentDateField = jQuery("select[name='Event" + startEnd + "Day']");

			jQuery('.event' + startEnd + 'DateField').remove();
			if( startEnd == "Start") {
				var selectObject = spStartDays[ spDaysPerMonth[ chosenMonth ] - 28 ];
				selectObject.val( currentDateField.val() );
				jQuery("select[name='EventStartMonth']").after( selectObject );
			} else {
				var selectObject = spEndDays[ spDaysPerMonth[ chosenMonth ] - 28 ];
				selectObject.val( currentDateField.val() );
				jQuery('select[name="EventEndMonth"]').after( selectObject );
			}
		});
		
		jQuery("select[name='EventStartMonth'], select[name='EventEndMonth']").change();
		
		jQuery("select[name='EventStartYear']").change(function() {
			jQuery("select[name='EventStartMonth']").change();
		});
		
		jQuery("select[name='EventEndYear']").change(function() {
			jQuery("select[name='EventEndMonth']").change();
		});
				
	});
</script>
<style type="text/css">
	.eventForm td {
		padding:6px 6px 0 0;
		font-size:11px;
		vertical-align:middle;
	}
	.eventForm select, .eventForm input {
		font-size:11px;
	}
	.eventForm .hide {
		display:none;
	}
	.eventForm h4 {
		font-size:1.2em;
		margin:2em 0 1em;
	}
	.eventForm h4.event-time {
		margin-top: 0;
	}
	.notice {
		background-color: rgb(255, 255, 224);
		border: 1px solid rgb(230, 219, 85);
		margin: 5px 0 15px;
	}
	#EventInfo {
		border-color:#dfdfdf;
		background-color:#F9F9F9;
		border-width:1px;
		border-style:solid;
		-moz-border-radius:3px;
		-khtml-border-radius:3px;
		-webkit-border-radius:3px;
		border-radius:3px;
		margin: 0;
		width:100%;
		border-style:solid;
		border-spacing:0;
		padding: 10px;
	}
	#eventIntro {
	  margin: 10px 0 25px 0;
	}
	
	.form-table form input {border:none;}
	<?php if( eventsGetOptionValue('donateHidden', false) ) : ?>
		#mainDonateRow {display: none;}
	<?php endif; ?>
	#submitLabel {display: block;}
	#submitLabel input {
		display: block;
		padding: 0;
	}
	<?php if( class_exists( 'Eventbrite_for_The_Events_Calendar' ) ) : ?>
		.eventBritePluginPlug {display:none;}
	<?php endif; ?>
</style>
<div id="eventIntro">
<?php do_action('sp_events_errors', $postId ); ?>
	<p>
		<?php _e('Is this post an event?',$this->pluginDomain); ?>&nbsp;
		<input tabindex="2001" type='radio' name='isEvent' value='yes' <?php echo $isEventChecked; ?> />&nbsp;<b><?php _e('Yes', $this->pluginDomain); ?></b>
		<input tabindex="2002" type='radio' name='isEvent' value='no' <?php echo $isNotEventChecked; ?> />&nbsp;<b><?php _e('No', $this->pluginDomain); ?></b>
	</p>
</div>
<div id='eventDetails' class="inside eventForm">
	<?php do_action('sp_events_detail_top', $postId ); ?>
	<table cellspacing="0" cellpadding="0" id="EventInfo">
		<tr>
			<td colspan="2" class="snp_sectionheader"><h4 class="event-time"><?php _e('Event Time &amp; Date', $this->pluginDomain); ?></h4></td>
		</tr>
		<tr>
			<td><?php _e('All day event?', $this->pluginDomain); ?></td>
			<td><input tabindex="2007" type='checkbox' id='allDayCheckbox' name='EventAllDay' value='yes' <?php echo $isEventAllDay; ?> /></td>
		</tr>
		<tr>
			<td style="width:125px;"><?php _e('Start Date / Time:',$this->pluginDomain); ?></td>
			<td>
				<select tabindex="2010" name='EventStartMonth'>
					<?php echo $startMonthOptions; ?>
				</select>
				<?php foreach( $startDayOptions as $key => $val ) : ?>
					<select id="<?php echo $key; ?>StartDays" class="eventStartDateField" tabindex="2011" name='EventStartDay'>
						<?php echo $val; ?>
					</select>
				<?php endforeach; ?>
				<select tabindex="2012" name='EventStartYear'>
					<?php echo $startYearOptions; ?>
				</select>
				<span class='timeofdayoptions'>
					<?php _e('@',$this->pluginDomain); ?>
					<select tabindex="2013" name='EventStartHour'>
						<?php echo $startHourOptions; ?>
					</select>
					<select tabindex="2014" name='EventStartMinute'>
						<?php echo $startMinuteOptions; ?>
					</select>
					<?php if ( !strstr( get_option( 'time_format', The_Events_Calendar::TIMEFORMAT ), 'H' ) ) : ?>
						<select tabindex="2015" name='EventStartMeridian'>
							<?php echo $startMeridianOptions; ?>
						</select>
					<?php endif; ?>
				</span>
			</td>
		</tr>
		<tr>
			<td><?php _e('End Date / Time:',$this->pluginDomain); ?></td>
			<td>
				<select tabindex="2016" name='EventEndMonth'>
					<?php echo $endMonthOptions; ?>
				</select>
				<?php foreach( $endDayOptions as $key => $val ) : ?>
					<select id="<?php echo $key; ?>EndDays" class="eventEndDateField" tabindex="2017" name='EventEndDay'>
						<?php echo $val; ?>
					</select>
				<?php endforeach; ?>
				<select tabindex="2018" name='EventEndYear'>
					<?php echo $endYearOptions; ?>
				</select>
				<span class='timeofdayoptions'>
					<?php _e('@',$this->pluginDomain); ?>
					<select class="spEventsInput"tabindex="2019" name='EventEndHour'>
						<?php echo $endHourOptions; ?>
					</select>
					<select tabindex="2020" name='EventEndMinute'>
						<?php echo $endMinuteOptions; ?>
					</select>
					<?php if ( !strstr( get_option( 'time_format', The_Events_Calendar::TIMEFORMAT ), 'H' ) ) : ?>
						<select tabindex="2021" name='EventEndMeridian'>
							<?php echo $endMeridianOptions; ?>
						</select>
					<?php endif; ?>
				</span>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="snp_sectionheader"><h4><?php _e('Event Location Details', $this->pluginDomain); ?></h4></td>
		</tr>
		<tr>
			<td><?php _e('Venue:',$this->pluginDomain); ?></td>
			<td>
				<input tabindex="2022" type='text' name='EventVenue' size='25'  value='<?php echo $_EventVenue; ?>' />
			</td>
		</tr>
		<tr>
			<td><?php _e('Country:',$this->pluginDomain); ?></td>
			<td>
				<select tabindex="2023" name="EventCountry" id="EventCountry">
					<?php 
					$this->constructCountries();
				     foreach ($this->countries as $abbr => $fullname) {
				       print ("<option label=". $abbr . " value=\"$fullname\" ");
				       if ($_EventCountry == $fullname) { 
				         print ('selected="selected" ');
				       }
				       print (">$fullname</option>\n");
				     }
				     ?>
			     </select>
			</td>
		</tr>
		<tr>
			<td><?php _e('Address:',$this->pluginDomain); ?></td>
			<td><input tabindex="2024" type='text' name='EventAddress' size='25' value='<?php echo $_EventAddress; ?>' /></td>
		</tr>
		<tr>
			<td><?php _e('City:',$this->pluginDomain); ?></td>
			<td><input tabindex="2025" type='text' name='EventCity' size='25' value='<?php echo $_EventCity; ?>' /></td>
		</tr>
		<tr id="International" <?php if($_EventCountry == 'United States' || $_EventCountry == '' ){echo('class="hide"'); } ?>>
			<td><?php _e('Province:',$this->pluginDomain); ?></td>
			<td><input tabindex="2026" type='text' name='EventProvince' size='10' value='<?php echo $_EventProvince; ?>' /></td>
		</tr>
		<tr id="USA" <?php if($_EventCountry !== 'United States'){echo('class="hide"');} ?>>
			<td><?php _e('State:',$this->pluginDomain); ?></td>
			<td>
				<select tabindex="2027" name="EventState">
				    <option value=""><?php _e('Select a State:',$this->pluginDomain); ?></option> 
					<?php $states = array (
						"AL" => __("Alabama", $this->pluginDomain),
						"AK" => __("Alaska", $this->pluginDomain),
						"AZ" => __("Arizona", $this->pluginDomain),
						"AR" => __("Arkansas", $this->pluginDomain),
						"CA" => __("California", $this->pluginDomain),
						"CO" => __("Colorado", $this->pluginDomain),
						"CT" => __("Connecticut", $this->pluginDomain),
						"DE" => __("Delaware", $this->pluginDomain),
						"DC" => __("District of Columbia", $this->pluginDomain),
						"FL" => __("Florida", $this->pluginDomain),
						"GA" => __("Georgia", $this->pluginDomain),
						"HI" => __("Hawaii", $this->pluginDomain),
						"ID" => __("Idaho", $this->pluginDomain),
						"IL" => __("Illinois", $this->pluginDomain),
						"IN" => __("Indiana", $this->pluginDomain),
						"IA" => __("Iowa", $this->pluginDomain),
						"KS" => __("Kansas", $this->pluginDomain),
						"KY" => __("Kentucky", $this->pluginDomain),
						"LA" => __("Louisiana", $this->pluginDomain),
						"ME" => __("Maine", $this->pluginDomain),
						"MD" => __("Maryland", $this->pluginDomain),
						"MA" => __("Massachusetts", $this->pluginDomain),
						"MI" => __("Michigan", $this->pluginDomain),
						"MN" => __("Minnesota", $this->pluginDomain),
						"MS" => __("Mississippi", $this->pluginDomain),
						"MO" => __("Missouri", $this->pluginDomain),
						"MT" => __("Montana", $this->pluginDomain),
						"NE" => __("Nebraska", $this->pluginDomain),
						"NV" => __("Nevada", $this->pluginDomain),
						"NH" => __("New Hampshire", $this->pluginDomain),
						"NJ" => __("New Jersey", $this->pluginDomain),
						"NM" => __("New Mexico", $this->pluginDomain),
						"NY" => __("New York", $this->pluginDomain),
						"NC" => __("North Carolina", $this->pluginDomain),
						"ND" => __("North Dakota", $this->pluginDomain),
						"OH" => __("Ohio", $this->pluginDomain),
						"OK" => __("Oklahoma", $this->pluginDomain),
						"OR" => __("Oregon", $this->pluginDomain),
						"PA" => __("Pennsylvania", $this->pluginDomain),
						"RI" => __("Rhode Island", $this->pluginDomain),
						"SC" => __("South Carolina", $this->pluginDomain),
						"SD" => __("South Dakota", $this->pluginDomain),
						"TN" => __("Tennessee", $this->pluginDomain),
						"TX" => __("Texas", $this->pluginDomain),
						"UT" => __("Utah", $this->pluginDomain),
						"VT" => __("Vermont", $this->pluginDomain),
						"VA" => __("Virginia", $this->pluginDomain),
						"WA" => __("Washington", $this->pluginDomain),
						"WV" => __("West Virginia", $this->pluginDomain),
						"WI" => __("Wisconsin", $this->pluginDomain),
						"WY" => __("Wyoming", $this->pluginDomain),
					);
				      foreach ($states as $abbr => $fullname) {
				        print ("<option value=\"$abbr\" ");
				        if ($_EventState == $abbr) { 
				          print ('selected="selected" '); 
				        }
				        print (">$fullname</option>\n");
				      }
				      ?>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php _e('Postal Code:',$this->pluginDomain); ?></td>
			<td><input tabindex="2028" type='text' name='EventZip' size='6' value='<?php echo $_EventZip; ?>' /></td>
		</tr>
		<tr>
			<td><?php _e('Phone:',$this->pluginDomain); ?></td>
			<td><input tabindex="2030" type='text' name='EventPhone' size='14' value='<?php echo $_EventPhone; ?>' /></td>
		</tr>
        <tr>
			<td colspan="2" class="snp_sectionheader"><h4><?php _e('Event Cost', $this->pluginDomain); ?></h4></td>
		</tr>
		<tr>
			<td><?php _e('Cost:',$this->pluginDomain); ?></td>
			<td><input tabindex="2029" type='text' name='EventCost' size='6' value='<?php echo $_EventCost; ?>' /></td>
		</tr>
		<tr>
			<td></td>
			<td><small><?php _e('Leave blank to hide the field. Enter a 0 for events that are free.', $this->pluginDomain); ?></small></td>
		</tr>
		<tr class="eventBritePluginPlug">
			<td colspan="2" class="snp_sectionheader">
				<h4><?php _e('Sell Tickets &amp; Track Registration', $this->pluginDomain); ?></h4>	
			</td>
		</tr>
		<tr class="eventBritePluginPlug">
			<td colspan="2">
				<p><?php _e('Interested in selling tickets and tracking registrations? Now you can do it for free using our <a href="http://wordpress.org/extend/plugins/eventbrite-for-the-events-calendar/">Eventbrite Integration Plugin</a>. Eventbrite is a feature rich easy-to-use event management tool. "Wow, you\'re selling Eventbrite pretty hard. You must get a kickback."  Well, now that you mention it... we do. We get a little something for everyone that registers an event using our referral link. It\'s how we\'re able to keep supporting and building plugins for the open source community. ', $this->pluginDomain); ?> <a href="http://www.eventbrite.com/r/simpleevents"><?php _e('Check it out here.', $this->pluginDomain); ?></a></p>
			</td>
		</tr>
		
		
	</table>
	</div>
	
	<?php do_action( 'sp_events_above_donate', $postId ); ?>
	
	<div id="mainDonateRow" class="eventForm">
			<?php _e('<h4>If You Like This Plugin - Help Support It</h4><p>We spend a lot of time and effort building robust plugins and we love to share them with the community. If you use this plugin consider making a donation to help support its\' continued development. You may remove this message on the <a href="/wp-admin/options-general.php?page=the-events-calendar.php">settings page</a>.</p>', $this->pluginDomain); ?>
				<div id="snp_thanks">
					<?php _e('Thanks', $this->pluginDomain); ?><br/>
					<h5 class="snp_brand">Shane &amp; Peter</h5>
					<a href="http://www.shaneandpeter.com?source=events-plugin" target="_blank">www.shaneandpeter.com</a>		
				</div>
				<div id="snp_donate">
				 	<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		            	<input type="hidden" name="cmd" value="_s-xclick">
		                <input type="hidden" name="hosted_button_id" value="10750983">
		                <input type="hidden" name="item_name" value="Events Post Editor">
			            <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">	
		    	        <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
		        	    <label id="submitLabel" for="submit">
		            </label>
			       </form>
				</div>
		<div style="clear:both;"></div>	
	</div><!-- end maindonaterow -->
	
	<style>
	#eventDetails h4,
		#EventBriteDetailDiv h4 {
		text-transform: uppercase;
		border-bottom: 1px solid #e5e5e5;
		padding-bottom: 6px;
	}

	.eventForm td {
		padding-bottom: 10px !important;
		padding-top: 0 !important;
	}

	.eventForm .snp_sectionheader {
		padding-bottom: 5px !important;
	}

	#snp_thanks {
		float: left;
		width: 200px;
		margin: 5px 0 0 0;
	}

	.snp_brand {
		font-weight: normal;
		margin: 8px 0;
		font-family: Georgia !important;
		font-size: 17px !important;
	}

	.eventForm p {
		margin: 0 0 10px 0!important;
	}

	#eventDetails small,
		#EventBriteDetailDiv small {
		color: #a3a3a3;
		font-size: 10px;
	}

	#eventBriteTicketing,
		#mainDonateRow {
		background: url(<?php echo WP_PLUGIN_URL . '/the-events-calendar/resources/images/bg_fade.png';
		?>) repeat-x top left;
		background-color: #fff;
		padding: 10px 15px;
		border: 1px solid #e2e2e2;
		-moz-border-radius: 3px;
		-khtml-border-radius: 3px;
		-webkit-border-radius: 3px;
		-moz-border-radius-topleft: 0;
		-moz-border-radius-topright: 0;
		-webkit-border-top-left-radius: 0;
		-webkit-border-top-right-radius: 0;
		border-radius: 3px;
		margin: -11px 6px 0;
	}

	#eventBriteTicketing h2 {
		background: url(<?php echo WP_PLUGIN_URL . '/the-events-calendar/resources/images/logo_eventbrite.png';
		?>) no-repeat top right;
		height: 57px;
		margin: 0;
	}

	.eventForm {
		margin-top: -20px;
	}

	.eventForm .description_input {
		border: 1px solid #dfdfdf;
		width: 95%;
		height: 45px;
	}

	#EventInfo,
		table.eventForm {
		width: 100%;
	}

	td.snp_message {
		padding-bottom: 10px !important;
	}
	</style>
</div><!--//eventDetails-->
<?php do_action( 'sp_events_details_bottom', $postId ); ?>