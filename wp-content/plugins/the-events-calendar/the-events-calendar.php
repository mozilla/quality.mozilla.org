<?php
/*
 Plugin Name:  The Events Calendar
 Plugin URI: http://wordpress.org/extend/plugins/the-events-calendar/
 Description:  The Events Calendar plugin enables you to rapidly create and manage events using the post editor.  Features include optional Eventbrite integration, Google Maps integration as well as default calendar grid and list templates for streamlined one click installation.
 Version: 1.5.6
 Author: Shane & Peter, Inc.
 Author URI: http://www.shaneandpeter.com/
 Text Domain: the-events-calendar
 */

if ( !class_exists( 'The_Events_Calendar' ) ) {

	class The_Events_Calendar {
		const CATEGORYNAME	 		= 'Events';
		const OPTIONNAME 			= 'sp_events_calendar_options';
		// default formats, they are overridden by WP options or by arguments to date methods
		const DATEONLYFORMAT 		= 'F j, Y';
		const TIMEFORMAT			= 'g:i A';
		
		const DBDATEFORMAT	 		= 'Y-m-d';
		const DBDATETIMEFORMAT 		= 'Y-m-d g:i A';
	
		private $defaultOptions = '';
		public $latestOptions;
		        
		public $displaying;
		public $pluginDir;
		public $pluginUrl;
		public $pluginDomain = 'the-events-calendar';

		public $metaTags = array(
					'_isEvent',
					'_EventAllDay',
					'_EventStartDate',
					'_EventEndDate',
					'_EventVenue',
					'_EventCountry',
					'_EventAddress',
					'_EventCity',
					'_EventState',
					'_EventProvince',
					'_EventZip',
					'_EventCost',
					'_EventPhone',
				);
				
		public $currentPostTimestamp;
		
		public $daysOfWeekShort;
		public $daysOfWeek;
		private function constructDaysOfWeek() {
			$this->daysOfWeekShort = array( __( 'Sun', $this->pluginDomain ), __( 'Mon', $this->pluginDomain ), __( 'Tue', $this->pluginDomain ), __( 'Wed', $this->pluginDomain ), __( 'Thu', $this->pluginDomain ), __( 'Fri', $this->pluginDomain ), __( 'Sat', $this->pluginDomain ) );
			$this->daysOfWeek = array( __( 'Sunday', $this->pluginDomain ), __( 'Monday', $this->pluginDomain ), __( 'Tuesday', $this->pluginDomain ), __( 'Wednesday', $this->pluginDomain ), __( 'Thursday', $this->pluginDomain ), __( 'Friday', $this->pluginDomain ), __( 'Saturday', $this->pluginDomain ) );
		}
		
		private $countries;
		private function constructCountries( $useDefault = true ) {
				$countries = array(
					"US" => __("United States", $this->pluginDomain),
					"AF" => __("Afghanistan", $this->pluginDomain),
					"AL" => __("Albania", $this->pluginDomain),
					"DZ" => __("Algeria", $this->pluginDomain),
					"AS" => __("American Samoa", $this->pluginDomain),
					"AD" => __("Andorra", $this->pluginDomain),
					"AO" => __("Angola", $this->pluginDomain),
					"AI" => __("Anguilla", $this->pluginDomain),
					"AQ" => __("Antarctica", $this->pluginDomain),
					"AG" => __("Antigua And Barbuda", $this->pluginDomain),
					"AR" => __("Argentina", $this->pluginDomain),
					"AM" => __("Armenia", $this->pluginDomain),
					"AW" => __("Aruba", $this->pluginDomain),
					"AU" => __("Australia", $this->pluginDomain),
					"AT" => __("Austria", $this->pluginDomain),
					"AZ" => __("Azerbaijan", $this->pluginDomain),
					"BS" => __("Bahamas", $this->pluginDomain),
					"BH" => __("Bahrain", $this->pluginDomain),
					"BD" => __("Bangladesh", $this->pluginDomain),
					"BB" => __("Barbados", $this->pluginDomain),
					"BY" => __("Belarus", $this->pluginDomain),
					"BE" => __("Belgium", $this->pluginDomain),
					"BZ" => __("Belize", $this->pluginDomain),
					"BJ" => __("Benin", $this->pluginDomain),
					"BM" => __("Bermuda", $this->pluginDomain),
					"BT" => __("Bhutan", $this->pluginDomain),
					"BO" => __("Bolivia", $this->pluginDomain),
					"BA" => __("Bosnia And Herzegowina", $this->pluginDomain),
					"BW" => __("Botswana", $this->pluginDomain),
					"BV" => __("Bouvet Island", $this->pluginDomain),
					"BR" => __("Brazil", $this->pluginDomain),
					"IO" => __("British Indian Ocean Territory", $this->pluginDomain),
					"BN" => __("Brunei Darussalam", $this->pluginDomain),
					"BG" => __("Bulgaria", $this->pluginDomain),
					"BF" => __("Burkina Faso", $this->pluginDomain),
					"BI" => __("Burundi", $this->pluginDomain),
					"KH" => __("Cambodia", $this->pluginDomain),
					"CM" => __("Cameroon", $this->pluginDomain),
					"CA" => __("Canada", $this->pluginDomain),
					"CV" => __("Cape Verde", $this->pluginDomain),
					"KY" => __("Cayman Islands", $this->pluginDomain),
					"CF" => __("Central African Republic", $this->pluginDomain),
					"TD" => __("Chad", $this->pluginDomain),
					"CL" => __("Chile", $this->pluginDomain),
					"CN" => __("China", $this->pluginDomain),
					"CX" => __("Christmas Island", $this->pluginDomain),
					"CC" => __("Cocos (Keeling) Islands", $this->pluginDomain),
					"CO" => __("Colombia", $this->pluginDomain),
					"KM" => __("Comoros", $this->pluginDomain),
					"CG" => __("Congo", $this->pluginDomain),
					"CD" => __("Congo, The Democratic Republic Of The", $this->pluginDomain),
					"CK" => __("Cook Islands", $this->pluginDomain),
					"CR" => __("Costa Rica", $this->pluginDomain),
					"CI" => __("Cote D'Ivoire", $this->pluginDomain),
					"HR" => __("Croatia (Local Name: Hrvatska)", $this->pluginDomain),
					"CU" => __("Cuba", $this->pluginDomain),
					"CY" => __("Cyprus", $this->pluginDomain),
					"CZ" => __("Czech Republic", $this->pluginDomain),
					"DK" => __("Denmark", $this->pluginDomain),
					"DJ" => __("Djibouti", $this->pluginDomain),
					"DM" => __("Dominica", $this->pluginDomain),
					"DO" => __("Dominican Republic", $this->pluginDomain),
					"TP" => __("East Timor", $this->pluginDomain),
					"EC" => __("Ecuador", $this->pluginDomain),
					"EG" => __("Egypt", $this->pluginDomain),
					"SV" => __("El Salvador", $this->pluginDomain),
					"GQ" => __("Equatorial Guinea", $this->pluginDomain),
					"ER" => __("Eritrea", $this->pluginDomain),
					"EE" => __("Estonia", $this->pluginDomain),
					"ET" => __("Ethiopia", $this->pluginDomain),
					"FK" => __("Falkland Islands (Malvinas)", $this->pluginDomain),
					"FO" => __("Faroe Islands", $this->pluginDomain),
					"FJ" => __("Fiji", $this->pluginDomain),
					"FI" => __("Finland", $this->pluginDomain),
					"FR" => __("France", $this->pluginDomain),
					"FX" => __("France, Metropolitan", $this->pluginDomain),
					"GF" => __("French Guiana", $this->pluginDomain),
					"PF" => __("French Polynesia", $this->pluginDomain),
					"TF" => __("French Southern Territories", $this->pluginDomain),
					"GA" => __("Gabon", $this->pluginDomain),
					"GM" => __("Gambia", $this->pluginDomain),
					"GE" => __("Georgia", $this->pluginDomain),
					"DE" => __("Germany", $this->pluginDomain),
					"GH" => __("Ghana", $this->pluginDomain),
					"GI" => __("Gibraltar", $this->pluginDomain),
					"GR" => __("Greece", $this->pluginDomain),
					"GL" => __("Greenland", $this->pluginDomain),
					"GD" => __("Grenada", $this->pluginDomain),
					"GP" => __("Guadeloupe", $this->pluginDomain),
					"GU" => __("Guam", $this->pluginDomain),
					"GT" => __("Guatemala", $this->pluginDomain),
					"GN" => __("Guinea", $this->pluginDomain),
					"GW" => __("Guinea-Bissau", $this->pluginDomain),
					"GY" => __("Guyana", $this->pluginDomain),
					"HT" => __("Haiti", $this->pluginDomain),
					"HM" => __("Heard And Mc Donald Islands", $this->pluginDomain),
					"VA" => __("Holy See (Vatican City State)", $this->pluginDomain),
					"HN" => __("Honduras", $this->pluginDomain),
					"HK" => __("Hong Kong", $this->pluginDomain),
					"HU" => __("Hungary", $this->pluginDomain),
					"IS" => __("Iceland", $this->pluginDomain),
					"IN" => __("India", $this->pluginDomain),
					"ID" => __("Indonesia", $this->pluginDomain),
					"IR" => __("Iran (Islamic Republic Of)", $this->pluginDomain),
					"IQ" => __("Iraq", $this->pluginDomain),
					"IE" => __("Ireland", $this->pluginDomain),
					"IL" => __("Israel", $this->pluginDomain),
					"IT" => __("Italy", $this->pluginDomain),
					"JM" => __("Jamaica", $this->pluginDomain),
					"JP" => __("Japan", $this->pluginDomain),
					"JO" => __("Jordan", $this->pluginDomain),
					"KZ" => __("Kazakhstan", $this->pluginDomain),
					"KE" => __("Kenya", $this->pluginDomain),
					"KI" => __("Kiribati", $this->pluginDomain),
					"KP" => __("Korea, Democratic People's Republic Of", $this->pluginDomain),
					"KR" => __("Korea, Republic Of", $this->pluginDomain),
					"KW" => __("Kuwait", $this->pluginDomain),
					"KG" => __("Kyrgyzstan", $this->pluginDomain),
					"LA" => __("Lao People's Democratic Republic", $this->pluginDomain),
					"LV" => __("Latvia", $this->pluginDomain),
					"LB" => __("Lebanon", $this->pluginDomain),
					"LS" => __("Lesotho", $this->pluginDomain),
					"LR" => __("Liberia", $this->pluginDomain),
					"LY" => __("Libyan Arab Jamahiriya", $this->pluginDomain),
					"LI" => __("Liechtenstein", $this->pluginDomain),
					"LT" => __("Lithuania", $this->pluginDomain),
					"LU" => __("Luxembourg", $this->pluginDomain),
					"MO" => __("Macau", $this->pluginDomain),
					"MK" => __("Macedonia, Former Yugoslav Republic Of", $this->pluginDomain),
					"MG" => __("Madagascar", $this->pluginDomain),
					"MW" => __("Malawi", $this->pluginDomain),
					"MY" => __("Malaysia", $this->pluginDomain),
					"MV" => __("Maldives", $this->pluginDomain),
					"ML" => __("Mali", $this->pluginDomain),
					"MT" => __("Malta", $this->pluginDomain),
					"MH" => __("Marshall Islands", $this->pluginDomain),
					"MQ" => __("Martinique", $this->pluginDomain),
					"MR" => __("Mauritania", $this->pluginDomain),
					"MU" => __("Mauritius", $this->pluginDomain),
					"YT" => __("Mayotte", $this->pluginDomain),
					"MX" => __("Mexico", $this->pluginDomain),
					"FM" => __("Micronesia, Federated States Of", $this->pluginDomain),
					"MD" => __("Moldova, Republic Of", $this->pluginDomain),
					"MC" => __("Monaco", $this->pluginDomain),
					"MN" => __("Mongolia", $this->pluginDomain),
					"MS" => __("Montserrat", $this->pluginDomain),
					"MA" => __("Morocco", $this->pluginDomain),
					"MZ" => __("Mozambique", $this->pluginDomain),
					"MM" => __("Myanmar", $this->pluginDomain),
					"NA" => __("Namibia", $this->pluginDomain),
					"NR" => __("Nauru", $this->pluginDomain),
					"NP" => __("Nepal", $this->pluginDomain),
					"NL" => __("Netherlands", $this->pluginDomain),
					"AN" => __("Netherlands Antilles", $this->pluginDomain),
					"NC" => __("New Caledonia", $this->pluginDomain),
					"NZ" => __("New Zealand", $this->pluginDomain),
					"NI" => __("Nicaragua", $this->pluginDomain),
					"NE" => __("Niger", $this->pluginDomain),
					"NG" => __("Nigeria", $this->pluginDomain),
					"NU" => __("Niue", $this->pluginDomain),
					"NF" => __("Norfolk Island", $this->pluginDomain),
					"MP" => __("Northern Mariana Islands", $this->pluginDomain),
					"NO" => __("Norway", $this->pluginDomain),
					"OM" => __("Oman", $this->pluginDomain),
					"PK" => __("Pakistan", $this->pluginDomain),
					"PW" => __("Palau", $this->pluginDomain),
					"PA" => __("Panama", $this->pluginDomain),
					"PG" => __("Papua New Guinea", $this->pluginDomain),
					"PY" => __("Paraguay", $this->pluginDomain),
					"PE" => __("Peru", $this->pluginDomain),
					"PH" => __("Philippines", $this->pluginDomain),
					"PN" => __("Pitcairn", $this->pluginDomain),
					"PL" => __("Poland", $this->pluginDomain),
					"PT" => __("Portugal", $this->pluginDomain),
					"PR" => __("Puerto Rico", $this->pluginDomain),
					"QA" => __("Qatar", $this->pluginDomain),
					"RE" => __("Reunion", $this->pluginDomain),
					"RO" => __("Romania", $this->pluginDomain),
					"RU" => __("Russian Federation", $this->pluginDomain),
					"RW" => __("Rwanda", $this->pluginDomain),
					"KN" => __("Saint Kitts And Nevis", $this->pluginDomain),
					"LC" => __("Saint Lucia", $this->pluginDomain),
					"VC" => __("Saint Vincent And The Grenadines", $this->pluginDomain),
					"WS" => __("Samoa", $this->pluginDomain),
					"SM" => __("San Marino", $this->pluginDomain),
					"ST" => __("Sao Tome And Principe", $this->pluginDomain),
					"SA" => __("Saudi Arabia", $this->pluginDomain),
					"SN" => __("Senegal", $this->pluginDomain),
					"SC" => __("Seychelles", $this->pluginDomain),
					"SL" => __("Sierra Leone", $this->pluginDomain),
					"SG" => __("Singapore", $this->pluginDomain),
					"SK" => __("Slovakia (Slovak Republic)", $this->pluginDomain),
					"SI" => __("Slovenia", $this->pluginDomain),
					"SB" => __("Solomon Islands", $this->pluginDomain),
					"SO" => __("Somalia", $this->pluginDomain),
					"ZA" => __("South Africa", $this->pluginDomain),
					"GS" => __("South Georgia, South Sandwich Islands", $this->pluginDomain),
					"ES" => __("Spain", $this->pluginDomain),
					"LK" => __("Sri Lanka", $this->pluginDomain),
					"SH" => __("St. Helena", $this->pluginDomain),
					"PM" => __("St. Pierre And Miquelon", $this->pluginDomain),
					"SD" => __("Sudan", $this->pluginDomain),
					"SR" => __("Suriname", $this->pluginDomain),
					"SJ" => __("Svalbard And Jan Mayen Islands", $this->pluginDomain),
					"SZ" => __("Swaziland", $this->pluginDomain),
					"SE" => __("Sweden", $this->pluginDomain),
					"CH" => __("Switzerland", $this->pluginDomain),
					"SY" => __("Syrian Arab Republic", $this->pluginDomain),
					"TW" => __("Taiwan", $this->pluginDomain),
					"TJ" => __("Tajikistan", $this->pluginDomain),
					"TZ" => __("Tanzania, United Republic Of", $this->pluginDomain),
					"TH" => __("Thailand", $this->pluginDomain),
					"TG" => __("Togo", $this->pluginDomain),
					"TK" => __("Tokelau", $this->pluginDomain),
					"TO" => __("Tonga", $this->pluginDomain),
					"TT" => __("Trinidad And Tobago", $this->pluginDomain),
					"TN" => __("Tunisia", $this->pluginDomain),
					"TR" => __("Turkey", $this->pluginDomain),
					"TM" => __("Turkmenistan", $this->pluginDomain),
					"TC" => __("Turks And Caicos Islands", $this->pluginDomain),
					"TV" => __("Tuvalu", $this->pluginDomain),
					"UG" => __("Uganda", $this->pluginDomain),
					"UA" => __("Ukraine", $this->pluginDomain),
					"AE" => __("United Arab Emirates", $this->pluginDomain),
					"GB" => __("United Kingdom", $this->pluginDomain),
					"UM" => __("United States Minor Outlying Islands", $this->pluginDomain),
					"UY" => __("Uruguay", $this->pluginDomain),
					"UZ" => __("Uzbekistan", $this->pluginDomain),
					"VU" => __("Vanuatu", $this->pluginDomain),
					"VE" => __("Venezuela", $this->pluginDomain),
					"VN" => __("Viet Nam", $this->pluginDomain),
					"VG" => __("Virgin Islands (British)", $this->pluginDomain),
					"VI" => __("Virgin Islands (U.S.)", $this->pluginDomain),
					"WF" => __("Wallis And Futuna Islands", $this->pluginDomain),
					"EH" => __("Western Sahara", $this->pluginDomain),
					"YE" => __("Yemen", $this->pluginDomain),
					"YU" => __("Yugoslavia", $this->pluginDomain),
					"ZM" => __("Zambia", $this->pluginDomain),
					"ZW" => __("Zimbabwe", $this->pluginDomain)
					);
					if ($useDefault) {
						$defaultCountry = eventsGetOptionValue('defaultCountry');
						if($defaultCountry) {
							asort($countries);
							$countries = array($defaultCountry[0] => __($defaultCountry[1], $this->pluginDomain)) + $countries;
							array_unique($countries);
						}
						$this->countries = $countries;
					} else {
						$this->countries = $countries;
					}
		}

		/**
		 * Initializes plugin variables and sets up wordpress hooks/actions.
		 *
		 * @return void
		 */
		function __construct( ) {
			$this->currentDay		= '';
			$this->pluginDir		= basename(dirname(__FILE__));
			$this->pluginUrl = WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__));
			$this->errors			= '';
			register_activation_hook( __FILE__, 	array( &$this, 'on_activate' ) );
			register_deactivation_hook( __FILE__, 	array( &$this, 'on_deactivate' ) );
			add_action( 'reschedule_event_post', array( $this, 'reschedule') );
			add_action( 'init',				array( $this, 'loadPluginTextDomain' ) );
			add_action( 'init', 			array( $this, 'flushRewriteRules' ) );
			add_action( 'pre_get_posts',	array( $this, 'setOptions' ) );
			add_action( 'admin_menu', 		array( $this, 'addOptionsPage' ) );
			add_action( 'admin_init', 		array( $this, 'checkForOptionsChanges' ) );
			add_action( 'wp_ajax_hideDonate', array( $this, 'storeHideDonate'));
			add_action( 'admin_menu', 		array( $this, 'addEventBox' ) );
			add_action( 'save_post',		array( $this, 'addEventMeta' ), 15 );
			add_action( 'publish_post',		array( $this, 'addEventMeta' ), 15 );
			add_filter( 'generate_rewrite_rules', array( $this, 'filterRewriteRules' ) );
			add_filter( 'query_vars',		array( $this, 'eventQueryVars' ) );			
			add_filter( 'posts_join',		array( $this, 'events_search_join' ) );
			add_filter( 'posts_where',		array( $this, 'events_search_where' ) );
			add_filter( 'posts_orderby',	array( $this, 'events_search_orderby' ) );
			add_filter( 'posts_fields',		array( $this, 'events_search_fields' ) );
			add_filter( 'post_limits',		array( $this, 'events_search_limits' ) );
			add_action( 'template_redirect',		array($this, 'templateChooser' ) );
		}
		
		public function addOptionsPage() {
    		add_options_page('The Events Calendar', 'The Events Calendar', 'administrator', basename(__FILE__), array($this,'optionsPageView'));		
		}
		
		public function optionsPageView() {
			include( dirname( __FILE__ ) . '/views/events-options.php' );
		}
		
		public function checkForOptionsChanges() {
			if (isset($_POST['saveEventsCalendarOptions']) && check_admin_referer('saveEventsCalendarOptions')) {
                $options = $this->getOptions();
				$options['viewOption'] = $_POST['viewOption'];
				if($_POST['defaultCountry']) {
					$this->constructCountries();
					$defaultCountryKey = array_search($_POST['defaultCountry'],$this->countries);
					$options['defaultCountry'] = array($defaultCountryKey,$_POST['defaultCountry']);					
				}
				
				$options['embedGoogleMaps'] = $_POST['embedGoogleMaps'];
				if($_POST['embedGoogleMapsHeight']) {
					$options['embedGoogleMapsHeight'] = $_POST['embedGoogleMapsHeight'];
					$options['embedGoogleMapsWidth'] = $_POST['embedGoogleMapsWidth'];
				}
				
				$options['showComments'] = $_POST['showComments'];
				$options['resetEventPostDate'] = $_POST['resetEventPostDate'];
				
				do_action( 'sp-events-save-more-options' );
				
				$this->saveOptions($options);
				$this->latestOptions = $options;
			
			} // end if
		}
		
		public function storeHideDonate() {
			if ( $_POST['donateHidden'] ) {
                $options = $this->getOptions();
				$options['donateHidden'] = true;
				
				$this->saveOptions($options);
			
			} // end if
		}
		
		/// OPTIONS DATA
        
        public function getOptions() {
            if ('' === $this->defaultOptions) {
                $this->defaultOptions = get_option(The_Events_Calendar::OPTIONNAME, array());
            }
            return $this->defaultOptions;
        }
		        
        private function saveOptions($options) {
            if (!is_array($options)) {
                return;
            }
            if ( update_option(The_Events_Calendar::OPTIONNAME, $options) ) {
				$this->latestOptions = $options;
			} else {
				$this->latestOptions = $this->getOptions();
			}
        }
        
        public function deleteOptions() {
            delete_option(The_Events_Calendar::OPTIONNAME);
        }

		public function templateChooser() {
			$this->constructDaysOfWeek();
			if( !is_feed() ) {
				// list view
				if ( $this->in_event_category() && ( events_displaying_upcoming() || events_displaying_past() ) ) {
					if (file_exists(TEMPLATEPATH.'/events/list.php') ) {
						include (TEMPLATEPATH.'/events/list.php');
					}
					else {
						include dirname( __FILE__ ) . '/views/list.php';
					}
					exit;
				}
			
				// grid view
				if ( $this->in_event_category() ) {
					if (file_exists(TEMPLATEPATH.'/events/gridview.php') ) {
						include (TEMPLATEPATH.'/events/gridview.php');
					}
					else {
						include dirname( __FILE__ ) . '/views/gridview.php';
					}
					exit;
				}

				// single event
				if (is_single() && in_category( $this->eventCategory() ) ) {
					if (file_exists(TEMPLATEPATH.'/events/single.php') ) {
						include (TEMPLATEPATH.'/events/single.php');
					}
					else {
						include trailingslashit( WP_PLUGIN_DIR ) . trailingslashit( plugin_basename( dirname( __FILE__ ) ) ) . 'views/single.php';
					}
					exit;
				}
			} // if is_feed()
		}
		
		public function loadStylesAndScripts( ) {
			$eventsURL = trailingslashit( WP_PLUGIN_URL ) . trailingslashit( plugin_basename( dirname( __FILE__ ) ) ) . 'resources/';
			
			wp_enqueue_script('sp-events-calendar-script', $eventsURL.'events.js', array('jquery') );
			wp_enqueue_style('sp-events-calendar-style', $eventsURL.'events.css');
			
		}
		
		// works around a bug where setting category base to null doesn't allow get_option to return a default value
		public function getCategoryBase() {
			$category_base = get_option('category_base', 'category');
			return ( empty( $category_base ) ) ? 'category' : $category_base;
		}
		
		public function truncate($text, $excerpt_length = 44) {

			$text = strip_shortcodes( $text );

			$text = apply_filters('the_content', $text);
			$text = str_replace(']]>', ']]&gt;', $text);
			$text = strip_tags($text);

			$words = explode(' ', $text, $excerpt_length + 1);
			if (count($words) > $excerpt_length) {
				array_pop($words);
				$text = implode(' ', $words);
				$text = rtrim($text);
				$text .= '&hellip;';
				}

			return $text;
		}
		
		public function loadPluginTextDomain() {
			load_plugin_textdomain( $this->pluginDomain, false, basename(dirname(__FILE__)) . '/lang/');
		}
	
		/**
		 * Helper method to return an array of 1-12 for months
		 */
		public function months( ) {
			$months = array();
			foreach( range( 1, 12 ) as $month ) {
				$months[ $month ] = $month;
			}
			return $months;
		}

		/**
		 * Helper method to return an array of translated month names or short month names
		 * @return Array translated month names
		 */
		public function monthNames( $short = false ) {
			if($short) {
				$months = array( 'Jan'	=> __('Jan', $this->pluginDomain), 
							  	 'Feb' 	=> __('Feb', $this->pluginDomain), 
							     'Mar' 	=> __('Mar', $this->pluginDomain), 
							     'Apr' 	=> __('Apr', $this->pluginDomain), 
							     'May'  => __('May', $this->pluginDomain), 
							     'Jun' 	=> __('Jun', $this->pluginDomain), 
							     'Jul'	=> __('Jul', $this->pluginDomain), 
							     'Aug' 	=> __('Aug', $this->pluginDomain), 
							     'Sep' 	=> __('Sep', $this->pluginDomain), 
							     'Oct' 	=> __('Oct', $this->pluginDomain), 
							     'Nov' 	=> __('Nov', $this->pluginDomain), 
							     'Dec' 	=> __('Dec', $this->pluginDomain) 
						     );
			} else {
				$months = array( 'January' 	    => __('January', $this->pluginDomain), 
							  	 'February' 	=> __('February', $this->pluginDomain), 
							     'March' 		=> __('March', $this->pluginDomain), 
							     'April' 		=> __('April', $this->pluginDomain), 
							     'May' 		    => __('May', $this->pluginDomain), 
							     'June' 		=> __('June', $this->pluginDomain), 
							     'July'	        => __('July', $this->pluginDomain), 
							     'August' 		=> __('August', $this->pluginDomain), 
							     'September' 	=> __('September', $this->pluginDomain), 
							     'October' 	    => __('October', $this->pluginDomain), 
							     'November' 	=> __('November', $this->pluginDomain), 
							     'December' 	=> __('December', $this->pluginDomain) 
						     );
			}
			return $months;
		}

		/**
		 * Helper method to return an array of 1-31 for days
		 */
		public function days( $totalDays ) {
			$days = array();
			foreach( range( 1, $totalDays ) as $day ) {
				$days[ $day ] = $day;
			}
			return $days;
		}

		/**
		 * Helper method to return an array of years, back 2 and forward 5
		 */
		public function years( ) {
			$year = ( int )date_i18n( 'Y' );
			// Back two years, forward 5
			$year_list = array( $year - 5, $year - 4, $year - 3, $year - 2, $year - 1, $year, $year + 1, $year + 2, $year + 3, $year + 4, $year + 5 );
			$years = array();
			foreach( $year_list as $single_year ) {
				$years[ $single_year ] = $single_year;
			}

			return $years;
		}
		
		/**
		 * Creates the category and sets up the theme resource folder with sample config files.
		 * 
		 * @return void
		 */
		public function on_activate( ) {
			$now = time();
			$firstTime = $now - ($now % 66400);
			wp_schedule_event( $firstTime, 'daily', 'reschedule_event_post'); // schedule this for midnight, daily
			$this->create_category_if_not_exists( );	
		}
		/**
		* This function is scheduled to run at midnight.  If any posts are set with EventStartDate
		* to today, update the post so that it was posted today.   This will force the event to be
		* displayed in the main loop on the homepage.
		* 
		* @return void
		*/	
		public function reschedule( ) {
			$resetEventPostDate = eventsGetOptionValue('resetEventPostDate', 'off');
			if( $resetEventPostDate == 'off' ){
				return;
			}
			global $wpdb;
			$query = "
				SELECT * FROM $wpdb->posts
				LEFT JOIN $wpdb->postmeta ON($wpdb->posts.ID = $wpdb->postmeta.post_id)
				WHERE 
				$wpdb->postmeta.meta_key = '_EventStartDate' 
				AND $wpdb->postmeta.meta_value = CURRENT_DATE()";
			$return = $wpdb->get_results($query, OBJECT);
			if ( is_array( $return ) && count( $return ) ) {
				foreach ( $return as $row ) {
					$updateQuery = "UPDATE $wpdb->posts SET post_date = NOW() WHERE $wpdb->posts.ID = " . $row->ID;
					$wpdb->query( $updateQuery );
				}
			}
		}
		/**
		 * fields filter for standard wordpress templates.  Adds the start and end date to queries in the
		 * events category
		 *
		 * @param string fields
		 * @param string modified fields for events queries
		 */
		public function events_search_fields( $fields ) {
			if( !$this->in_event_category() ) { 
				return $fields;
			}
			global $wpdb;
			$fields .= ', eventStart.meta_value as EventStartDate, eventEnd.meta_value as EventEndDate ';
			return $fields;

		}
		/**
		 * join filter for standard wordpress templates.  Adds the postmeta tables for start and end queries
		 *
		 * @param string join clause
		 * @return string modified join clause 
		 */
		public function events_search_join( $join ) {
			if( !$this->in_event_category() ) { 
				return $join;
			}
			global $wpdb;
			$join .= "LEFT JOIN {$wpdb->postmeta} as eventStart ON( {$wpdb->posts}.ID = eventStart.post_id ) ";
			$join .= "LEFT JOIN {$wpdb->postmeta} as eventEnd ON( {$wpdb->posts}.ID = eventEnd.post_id ) ";
			return $join;
		}
		/**
		 * where filter for standard wordpress templates.  Inspects the event options and filters
		 * event posts for upcoming or past event loops
		 *
		 * @param string where clause
		 * @return string modified where clause
		 */
		public function events_search_where( $where ) {
			if( !$this->in_event_category() ) { 
				return $where;
			}
			$where .= ' AND ( eventStart.meta_key = "_EventStartDate" AND eventEnd.meta_key = "_EventEndDate" ) ';
			if( events_displaying_month( ) ) {}
			if( events_displaying_upcoming( ) ) {	
				// Is the start date in the future?
				$where .= ' AND ( eventStart.meta_value > "'.$this->date.'" ';
				// Or is the start date in the past but the end date in the future? (meaning the event is currently ongoing)
				$where .= ' OR ( eventStart.meta_value < "'.$this->date.'" AND eventEnd.meta_value > "'.$this->date.'" ) ) ';
			}
			if( events_displaying_past( ) ) {
				// Is the start date in the past?
				$where .= ' AND  eventStart.meta_value < "'.$this->date.'" ';
			}
			return $where;
		}
		/**
		 * @return bool true if is_category() is on a child of the events category
		 */
		public function in_event_category( ) {
			if( is_category( The_Events_Calendar::CATEGORYNAME ) ) {
				return true;
			}
			$cat_id = get_query_var( 'cat' );
			if( $cat_id == $this->eventCategory() ) {
				return true;
			}
			$cats = get_categories('child_of=' . $this->eventCategory());
			$is_child = false;
			foreach( $cats as $cat ) {
				if( is_category( $cat->name ) ) {
					$is_child = true;
				}
			}
			return $is_child;
		}
		/**
		 * orderby filter for standard wordpress templates.  Adds event ordering for queries that are
		 * in the events category and filtered according to the search parameters
		 *
		 * @param string orderby
		 * @return string modified orderby clause
		 */
		public function events_search_orderby( $orderby ) {
			if( !$this->in_event_category() ) { 
				return $orderby;
			}
			global $wpdb;
			$orderby = ' eventStart.meta_value '.$this->order;
			return $orderby;

		}
		/**
		 * limit filter for standard wordpress templates.  Adds limit clauses for pagination 
		 * for queries in the events category
		 *
		 * @param string limits clause
		 * @return string modified limits clause
		 */
		public function events_search_limits( $limits ) { 
			if( !$this->in_event_category() ) { 
				return $limits;
			}
			global $wpdb, $wp_query, $paged;
			if (empty($paged)) {
					$paged = 1;
			}
			$posts_per_page = intval( get_option('posts_per_page') );
			$paged = get_query_var('paged') ? intval( get_query_var('paged') ) : 1;
			$pgstrt = ( ( $paged - 1 ) * $posts_per_page ) . ', ';
			$limits = 'LIMIT ' . $pgstrt . $posts_per_page;
			return $limits;
		}
		/**
	     * Gets the Category id to use for an Event
	     * @return int|false Category id to use or false is none is set
	     */
	    static function eventCategory() {
			return get_cat_id( The_Events_Calendar::CATEGORYNAME );
	    }
		/**
		 * undocumented
		 */
		public function flushRewriteRules() 
		{
		   global $wp_rewrite;
		   $wp_rewrite->flush_rules();
		}		
		/**
		 * Adds the event specific query vars to Wordpress
		 *
		 * @return mixed array of query variables that this plugin understands
		 */
		public function eventQueryVars( $qvars ) {
			$qvars[] = 'eventDisplay';
			$qvars[] = 'eventDate';
			return $qvars;		  
		}
		/**
		 * Adds Event specific rewrite rules.
		 *
		 *	events/				=>	/?cat=27
		 *  events/month		=>  /?cat=27&eventDisplay=month
		 *	events/upcoming		=>	/?cat=27&eventDisplay=upcoming
		 *	events/past			=>	/?cat=27&eventDisplay=past
		 *	events/2008-01/#15	=>	/?cat=27&eventDisplay=bydate&eventDate=2008-01-01
		 *
		 * @return void
		 */
		public function filterRewriteRules( $wp_rewrite ) {
			$categoryId = get_cat_id( The_Events_Calendar::CATEGORYNAME );
			$category_base = $this->getCategoryBase();
			$newRules = array(

				$category_base . '/' . strtolower( The_Events_Calendar::CATEGORYNAME ) . '/month' 	=> 'index.php?cat=' . $categoryId . '&eventDisplay=month',
				$category_base . '/' . strtolower( The_Events_Calendar::CATEGORYNAME ) . '/upcoming/page/(\d+)' => 'index.php?cat=' . $categoryId . '&eventDisplay=upcoming&paged=' . $wp_rewrite->preg_index(1),
				$category_base . '/' . strtolower( The_Events_Calendar::CATEGORYNAME ) . '/upcoming' => 'index.php?cat=' . $categoryId . '&eventDisplay=upcoming',
				$category_base . '/' . strtolower( The_Events_Calendar::CATEGORYNAME ) . '/past/page/(\d+)' => 'index.php?cat=' . $categoryId . '&eventDisplay=past&paged=' . $wp_rewrite->preg_index(1),
				$category_base . '/' . strtolower( The_Events_Calendar::CATEGORYNAME ) . '/past' 	=> 'index.php?cat=' . $categoryId . '&eventDisplay=past',
				$category_base . '/' . strtolower( The_Events_Calendar::CATEGORYNAME ) . '/(\d{4}-\d{2})$'
				 										=> 'index.php?cat=' . $categoryId . '&eventDisplay=month' .
														  	'&eventDate=' . $wp_rewrite->preg_index(1),
				$category_base . '/' . strtolower( The_Events_Calendar::CATEGORYNAME ) . '/?$'=> 'index.php?cat=' . $categoryId . '&eventDisplay=' . eventsGetOptionValue('viewOption','month')
													
			);
		  $wp_rewrite->rules = $newRules + $wp_rewrite->rules;
		}
		/**
		 * Creates the events category and updates the  core options (if not already done)
		 * @return int cat_ID
		 */
		public function create_category_if_not_exists( ) {
			if ( !category_exists( The_Events_Calendar::CATEGORYNAME ) ) {
				$category_id = wp_create_category( The_Events_Calendar::CATEGORYNAME );
				return $category_id;
			} else {
				return $this->eventCategory();
			}
		}

		/**
		 * This plugin does not have any deactivation functionality. Any events, categories, options and metadata are
		 * left behind.
		 * 
		 * @return void
		 */
		public function on_deactivate( ) { 
		  	wp_clear_scheduled_hook('reschedule_event_post');
		}
		/**
		 * Converts a set of inputs to YYYY-MM-DD HH:MM:SS format for MySQL
		 */
		public function dateToTimeStamp( $year, $month, $day, $hour, $minute, $meridian ) {
			if ( preg_match( '/(PM|pm)/', $meridian ) && $hour < 12 ) $hour += "12";
			if ( preg_match( '/(AM|am)/', $meridian ) && $hour == 12 ) $hour = "00";
			return "$year-$month-$day $hour:$minute:00";
		}
		public function getTimeFormat( $dateFormat = self::DATEONLYFORMAT ) {
			return $dateFormat . ' ' . get_option( 'time_format', self::TIMEFORMAT );
		}
		/**
		 * Adds / removes the event details as meta tags to the post.
		 *
		 * @param string $postId 
		 * @return void
		 */
		public function addEventMeta( $postId ) {
			if ($_POST['isEvent'] == 'yes') {
				$category_id = $this->create_category_if_not_exists();
				// add a function below to remove all existing categories - wp_set_post_categories(int ,  array )
				if( $_POST['EventAllDay'] == 'yes' ) {
					$_POST['EventStartDate'] = $this->dateToTimeStamp( $_POST['EventStartYear'], $_POST['EventStartMonth'], $_POST['EventStartDay'], "12", "00", "AM" );
					$_POST['EventEndDate'] = $this->dateToTimeStamp( $_POST['EventEndYear'], $_POST['EventEndMonth'], $_POST['EventEndDay'], "11", "59", "PM" );
				} else {
					delete_post_meta( $postId, '_EventAllDay' );
					$_POST['EventStartDate'] = $this->dateToTimeStamp( $_POST['EventStartYear'], $_POST['EventStartMonth'], $_POST['EventStartDay'], $_POST['EventStartHour'], $_POST['EventStartMinute'], $_POST['EventStartMeridian'] );
					$_POST['EventEndDate'] = $this->dateToTimeStamp( $_POST['EventEndYear'], $_POST['EventEndMonth'], $_POST['EventEndDay'], $_POST['EventEndHour'], $_POST['EventEndMinute'], $_POST['EventEndMeridian'] );
				}
				// sanity check that start date < end date
				$startTimestamp = strtotime( $_POST['EventStartDate'] );
				$endTimestamp 	= strtotime( $_POST['EventEndDate'] );
				if ( $startTimestamp > $endTimestamp ) {
					$_POST['EventEndDate'] = $_POST['EventStartDate'];
				}
				// give add-on plugins a chance to cancel this meta update
				try {
					do_action( 'sp_events_event_save', $postId );
				} catch ( Exception $e) {
					// there was an error with a sub-plugin saving the post details
					// make sure the error is saved somehow and displayed
					update_post_meta( $postId, Eventbrite_for_The_Events_Calendar::EVENTBRITEERROPT, trim( $e->getMessage() ) );
				}
				//update meta fields		
				foreach ( $this->metaTags as $tag ) {
					$htmlElement = ltrim( $tag, '_' );
					if ( isset( $_POST[$htmlElement] ) ) {
						update_post_meta( $postId, $tag, $_POST[$htmlElement] );
					}
				}
				do_action( 'sp_events_update_meta', $postId );
				// merge event category into this post
				update_post_meta( $postId, '_EventCost', the_event_cost( $postId ) ); // XXX eventbrite cost field
				$cats = wp_get_object_terms($postId, 'category', array('fields' => 'ids'));				
				wp_set_post_categories( $postId, array_merge(array( get_category( $category_id )->cat_ID ), $cats ));
			}
			if ($_POST['isEvent'] == 'no' && is_event( $postId ) ) {
				// remove event meta tags if they exist...this post is no longer an event
				foreach ( $this->metaTags as $tag ) {
					delete_post_meta( $postId, $tag );
				}
				$event_cats[] = $this->eventCategory();
				$cats = get_categories('child_of=' . $this->eventCategory());
				foreach( $cats as $cat ) {
					$event_cats[] = $cat->term_id;
				}
				// remove the event categories from this post but keep any non-event categories
				$terms =  wp_get_object_terms($postId, 'category'); 
				$non_event_cats = array();
				foreach ( $terms as $term ) {
					if( !in_array( $term->term_id, $event_cats ) ) {
						$non_event_cats[] = $term->term_id;
					}
				}
				wp_set_post_categories( $postId, $non_event_cats );
				do_action( 'sp_events_event_clear', $postId );
			}
		}
		
		/**
		 * Adds a style chooser to the write post page
		 *
		 * @return void
		 */
		public function EventsChooserBox( ) {
			global $post;
			$options = '';
			$style = '';
			$postId = $post->ID;

			foreach ( $this->metaTags as $tag ) {
				if ( $postId ) {
					$$tag = get_post_meta( $postId, $tag, true );
				} else {
					$$tag = '';
				}
			}
			$isEventChecked			= ( $_isEvent == 'yes' ) ? 'checked' : '';
			$isNotEventChecked		= ( $_isEvent == 'no' || $_isEvent == '' ) ? 'checked' : '';
			$isEventAllDay			= ( $_EventAllDay == 'yes' ) ? 'checked' : '';
			$startDayOptions       	= array(
										31 => $this->getDayOptions( $_EventStartDate, 31 ),
										30 => $this->getDayOptions( $_EventStartDate, 30 ),
										29 => $this->getDayOptions( $_EventStartDate, 29 ),
										28 => $this->getDayOptions( $_EventStartDate, 28 )
									  );
			$endDayOptions			= array(
										31 => $this->getDayOptions( $_EventEndDate, 31 ),
										30 => $this->getDayOptions( $_EventEndDate, 30 ),
										29 => $this->getDayOptions( $_EventEndDate, 29 ),
										28 => $this->getDayOptions( $_EventEndDate, 28 )
									  );
			$startMonthOptions 		= $this->getMonthOptions( $_EventStartDate );
			$endMonthOptions 		= $this->getMonthOptions( $_EventEndDate );
			$startYearOptions 		= $this->getYearOptions( $_EventStartDate );
			$endYearOptions		 	= $this->getYearOptions( $_EventEndDate );
			$startMinuteOptions 	= $this->getMinuteOptions( $_EventStartDate );
			$endMinuteOptions 		= $this->getMinuteOptions( $_EventEndDate );
			$startHourOptions	 	= $this->getHourOptions( $_EventStartDate );
			$endHourOptions		 	= $this->getHourOptions( $_EventEndDate );
			$startMeridianOptions	= $this->getMeridianOptions( $_EventStartDate );
			$endMeridianOptions		= $this->getMeridianOptions( $_EventEndDate );		
			include( dirname( __FILE__ ) . '/views/events-meta-box.php' );
		}
		/**
		 * Given a date (YYYY-MM-DD), returns the first of the next month
		 *
		 * @param date
		 * @return date
		 */
		public function nextMonth( $date ) {
			$dateParts = split( '-', $date );
			if ( $dateParts[1] == 12 ) {
				$dateParts[0]++;
				$dateParts[1] = "01";
				$dateParts[2] = "01";
			} else {
				$dateParts[1]++;
				$dateParts[2] = "01";
			}
			if ( $dateParts[1] < 10 && strlen( $dateParts[1] ) == 1 ) {
				$dateParts[1] = "0" . $dateParts[1];
			}
			$return =  $dateParts[0] . '-' . $dateParts[1];
			return $return;
		}
		/**
		 * Given a date (YYYY-MM-DD), return the first of the previous month
		 *
		 * @param date
		 * @return date
		 */
		public function previousMonth( $date ) {
			$dateParts = split( '-', $date );

			if ( $dateParts[1] == 1 ) {
				$dateParts[0]--;
				$dateParts[1] = "12";
				$dateParts[2] = "01";
			} else {
				$dateParts[1]--;
				$dateParts[2] = "01";
			}
			if ( $dateParts[1] < 10 ) {
				$dateParts[1] = "0" . $dateParts[1];
			}
			$return =  $dateParts[0] . '-' . $dateParts[1];

			return $return;
		}

		/**
		 * Callback for adding the Meta box to the admin page
		 * @return void
		 */
		public function addEventBox( ) {
			add_meta_box( 'Event Details', __( 'The Events Calendar', 'Events_textdomain' ), 
		                array( $this, 'EventsChooserBox' ), 'post', 'normal', 'high' );
		}
		/** 
		 * Builds a set of options for diplaying a meridian chooser
		 *
		 * @param string YYYY-MM-DD HH:MM:SS to select (optional)
		 * @return string a set of HTML options with all meridians 
		 */
		public function getMeridianOptions( $date = "" ) {
			if( strstr( get_option( 'time_format', self::TIMEFORMAT ), 'A' ) ) {
				$a = 'A';
				$meridians = array( "AM", "PM" );
			} else {
				$a = 'a';
				$meridians = array( "am", "pm" );
			}
			if ( empty( $date ) ) {
				$meridian = date_i18n($a);
			} else {
				$meridian = date($a, strtotime( $date ) );
			}
			$return = '';
			foreach ( $meridians as $m ) {
				$return .= "<option value='$m'";
				if ( $m == $meridian ) {
					$return .= ' selected="selected"';
				}
				$return .= ">$m</option>\n";
			}
			return $return;
		}
		/**
		 * Builds a set of options for displaying a month chooser
		 * @param string the current date to select  (optional)
		 * @return string a set of HTML options with all months (current month selected)
		 */
		public function getMonthOptions( $date = "" ) {
			$months = $this->monthNames();
			$options = '';
			if ( empty( $date ) ) {
				$month = date_i18n( 'F' );
			} else {
				$month = date( 'F', strtotime( $date ) );
			}
			$monthIndex = 1;
			foreach ( $months as $englishMonth => $monthText ) {
				if ( $monthIndex < 10 ) { 
					$monthIndex = "0" . $monthIndex;  // need a leading zero in the month
				}
				if ( $month == $englishMonth ) {
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				}
				$options .= "<option value='$monthIndex' $selected>$monthText</option>\n";
				$monthIndex++;
			}
			return $options;
		}
		/**
		 * Builds a set of options for displaying a day chooser
		 * @param int number of days in the month
		 * @param string the current date (optional)
		 * @return string a set of HTML options with all days (current day selected)
		 */
		public function getDayOptions( $date = "", $totalDays = 31 ) {
			$days = $this->days( $totalDays );
			$options = '';
			if ( empty ( $date ) ) {
				$day = date_i18n( 'd' );
			} else {
				$day = date( 'd', strtotime( $date) );
			}
			foreach ( $days as $dayText ) {
				if ( $dayText < 10 ) { 
					$dayText = "0" . $dayText;  // need a leading zero in the day
				}
				if ( $day == $dayText ) {
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				}
				$options .= "<option value='$dayText' $selected>$dayText</option>\n";
			}
			return $options;
		}
		/**
		 * Builds a set of options for displaying a year chooser
		 * @param string the current date (optional)
		 * @return string a set of HTML options with adjacent years (current year selected)
		 */
		public function getYearOptions( $date = "" ) {
			$years = $this->years();
			$options = '';
			if ( empty ( $date ) ) {
				$year = date_i18n( 'Y' );
			} else {
				$year = date( 'Y', strtotime( $date ) );
			}
			foreach ( $years as $yearText ) {
				if ( $year == $yearText ) {
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				}
				$options .= "<option value='$yearText' $selected>$yearText</option>\n";
			}
			return $options;
		}
		/**
		 * Builds a set of options for displaying an hour chooser
		 * @param string the current date (optional)
		 * @return string a set of HTML options with hours (current hour selected)
		 */
		public function getHourOptions( $date = "" ) {
			$hours = $this->hours();
			if( count($hours) == 12 ) $h = 'h';
			else $h = 'H';
			$options = '';
			if ( empty ( $date ) ) {
				$hour = date_i18n( $h );
			} else {
				$timestamp = strtotime( $date );
				$hour = date( $h, $timestamp );
				// fix hours if time_format has changed from what is saved
				if( preg_match('(pm|PM)', $timestamp) && $h == 'H') $hour = $hour + 12;
				if( $hour > 12 && $h == 'h' ) $hour = $hour - 12;
				
			}
			foreach ( $hours as $hourText ) {
				if ( $hour == $hourText ) {
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				}
				$options .= "<option value='$hourText' $selected>$hourText</option>\n";
			}
			return $options;
		}
		/**
		 * Builds a set of options for displaying a minute chooser
		 * @param string the current date (optional)
		 * @return string a set of HTML options with minutes (current minute selected)
		 */
		public function getMinuteOptions( $date = "" ) {
			$minutes = $this->minutes();
			$options = '';
			if ( empty ( $date ) ) {
				$minute = '00';
			} else {
				$minute = date( 'i', strtotime( $date ) ); 
			}
			foreach ( $minutes as $minuteText ) {
				if ( $minute == $minuteText ) {
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				}
				$options .= "<option value='$minuteText' $selected>$minuteText</option>\n";
			}
			return $options;
		}
		/**
	     * Helper method to return an array of 1-12 for hours
	     */
	    public function hours() {
	      $hours = array();
		  $rangeMax = ( strstr( get_option( 'time_format', self::TIMEFORMAT ), 'H' ) ) ? 23 : 12;
	      foreach(range(1,$rangeMax) as $hour) {
			if ( $hour < 10 ) {
				$hour = "0".$hour;
			}
	        $hours[$hour] = $hour;
	      }
	      return $hours;
	    }
		/**
	     * Helper method to return an array of 00-59 for minutes
	     */
	    public static function minutes( ) {
	      $minutes = array();
	      for($minute=0; $minute < 60; $minute+=5) {
					if ($minute < 10) {
						$minute = "0" . $minute;
					}
	        $minutes[$minute] = $minute;
	      }
	      return $minutes;
	    }
		/**
		 * Sets event options based on the current query string
		 *
		 * @return void
		 */
		public function setOptions( ) {
			global $wp_query;
			$display = ( isset( $wp_query->query_vars['eventDisplay'] ) ) ? $wp_query->query_vars['eventDisplay'] : eventsGetOptionValue('viewOption','month');
			switch ( $display ) {
				case "past":
					$this->displaying		= "past";
					$this->startOperator	= "<=";
					$this->order			= "DESC";
					$this->date				= date_i18n( The_Events_Calendar::DBDATETIMEFORMAT );
					break;
				case "upcoming":
					$this->displaying		= "upcoming";					
					$this->startOperator	= ">=";
					$this->order			= "ASC";
					$this->date				= date_i18n( The_Events_Calendar::DBDATETIMEFORMAT );
					break;					
				case "month":
				case "default":
					$this->displaying		= "month";
					$this->startOperator	= ">=";
					$this->order			= "ASC";
					// TODO date set to YYYY-MM
					// TODO store DD as an anchor to the URL
					if ( isset ( $wp_query->query_vars['eventDate'] ) ) { 
						$this->date = $wp_query->query_vars['eventDate'] . "-01";
					} else {
						$date = date_i18n( The_Events_Calendar::DBDATEFORMAT );
						$this->date = substr_replace( $date, '01', -2 );
					}
					break;
			}
		}
		public function getDateString( $date ) {
			$dateParts = split( '-', $date );
		    $timestamp = mktime( 0, 0, 0, $dateParts[1], 1, $dateParts[0] );
		    return date( "F Y", $timestamp );
		}
	} // end The_Events_Calendar class
} // end if !class_exists The_Events_Calendar

if( class_exists( 'The_Events_Calendar' ) && !function_exists( 'get_event_style' ) ) {
	global $spEvents;
	$spEvents = new The_Events_Calendar();
	
	/**
	 * retrieve specific key from options array, optionally provide a default return value
	 *
	 * @param string option key
	 * @param string default return value (optional)
	 * @return string option value or default
	 */
	function eventsGetOptionValue($optionName, $default = '') {
		global $spEvents;
		if($optionName) {
			if( $spEvents->latestOptions ) {
				return $spEvents->latestOptions[$optionName];
			}
			$options = $spEvents->getOptions();
			return ( $options[$optionName] ) ? $options[$optionName] : $default;
		}
	}
	
	/**
	 * Output function: Prints the events calendar 'grid view'
	 *
	 * @return void
	 */
	function event_grid_view( ) {
		global $spEvents;
		global $wp_query;
		$wp_query->set( 'eventDisplay', 'bydate' );
		$eventPosts = get_events();
		$monthView = events_by_month( $eventPosts, $spEvents->date );
		list( $year, $month ) = split( '-', $spEvents->date );
		$date = mktime(12, 0, 0, $month, 1, $year); // 1st day of month as unix stamp
		$daysInMonth = date("t", $date);
		$startOfWeek = get_option( 'start_of_week', 0 );
		$rawOffset = date("w", $date) - $startOfWeek;
		$offset = ( $rawOffset < 0 ) ? $rawOffset + 7 : $rawOffset; // month begins on day x
		$rows = 1;
		require( dirname( __FILE__ ) . '/views/table.php' );
	}
	/**
	 * Maps events to days
	 *
	 * @param array of events from get_events()
	 * @param string date of the 
	 * @return array days of the month with events as values
	 */
	function events_by_month( $results, $date ) {
		if( preg_match( '/(\d{4})-(\d{2})/', $date, $matches ) ) {
			$queryYear	= $matches[1];
			$queryMonth = $matches[2];
		} else {
			return false; // second argument not a date we recognize
		}
		$monthView = array();
		for( $i = 1; $i <= 31; $i++ ) {
			$monthView[$i] = array();
		}
		foreach ( $results as $event ) {
			$started = false;
			list( $startYear, $startMonth, $startDay, $garbage ) = explode( '-', $event->EventStartDate );
			list( $endYear, $endMonth, $endDay, $garbage ) = explode( '-', $event->EventEndDate );
			list( $startDay, $garbage ) = explode( ' ', $startDay );
			list( $endDay, $garbage ) = explode( ' ', $endDay );
			for( $i = 1; $i <= 31 ; $i++ ) {
				if ( ( $i == $startDay && $startMonth == $queryMonth ) ||  strtotime( $startYear.'-'.$startMonth ) < strtotime( $queryYear.'-'.$queryMonth ) ) {
					$started = true;
				}
				if ( $started ) {
					$monthView[$i][] = $event;
				}
				if( $i == $endDay && $endMonth == $queryMonth ) {
					continue 2;
				}
			}
		}
		return $monthView;
	}
	/**
	 * Output function: Prints the selected event style
	 *
	 * @param string $post_id 
	 * @return void
	 */
	function event_style( $postId = null ) {	
		echo get_event_style( $postId );
	}

	/**
	 * Template function: 
	 * @return boolean
	 */
	function is_event( $postId = null ) {
		if ( $postId === null || !is_numeric( $postId ) ) {
			global $post;
			$postId = $post->ID;
		}
		if (get_post_meta( $postId, '_isEvent', true )) {
			return true;
		}
		return false;
	}
	/**
	 * Returns a link to google maps for the given event
	 *
	 * @param string $postId 
	 * @return string a fully qualified link to http://maps.google.com/ for this event
	 */
	function get_event_google_map_link( $postId = null ) {
		if ( $postId === null || !is_numeric( $postId ) ) {
			global $post;
			$postId = $post->ID;
		}
		if ( !is_event( $postId ) ) {
			return false;
		}
		$address = get_post_meta( $postId, '_EventAddress', true );
		$city = get_post_meta( $postId, '_EventCity', true );
		$state = get_post_meta( $postId, '_EventState', true );
		$province = get_post_meta($postId, '_EventProvince', true );
		$zip = get_post_meta( $postId, '_EventZip', true );
		$country = get_post_meta($postId, '_EventCountry', true );
		$google_url = "http://maps.google.com/maps?f=q&source=s_q&hl=en&geocode=&q=";
		if ( $country == "United States" && !empty( $address ) && !empty( $city ) && !empty( $state) && !empty( $zip ) ) {
			return $google_url . urlencode( $address . " " . $city . " " . $state . " " . $zip . " " . $country);
		} elseif ( !empty( $country ) && !empty( $address ) && !empty( $city ) && !empty( $province ) && !empty( $zip ) ) {
			return $google_url . urlencode( $address . " " . $city . " " . $province . " " . $zip . " " . $country);
		}
		return "";
	}
	/**
	 * Displays a link to google maps for the given event
	 *
	 * @param string $postId 
	 * @return void
	 */
	function event_google_map_link( $postId = null ) {
		echo get_event_google_map_link( $postId );
	}
	/**
	 * Returns an embeded google maps for the given event
	 *
	 * @param string $postId 
	 * @param int $width 
	 * @param int $height
	 * @return string - an iframe pulling http://maps.google.com/ for this event
	 */
	function get_event_google_map_embed( $postId = null, $width = '100%', $height = '350' ) {
		if ( $postId === null || !is_numeric( $postId ) ) {
			global $post;
			$postId = $post->ID;
		}
		if ( !is_event( $postId ) ) {
			return false;
		}
		$address = get_post_meta( $postId, '_EventAddress', true );
		$city = get_post_meta( $postId, '_EventCity', true );
		$state = get_post_meta( $postId, '_EventState', true );
		$province = get_post_meta($postId, '_EventProvince', true );
		$zip = get_post_meta( $postId, '_EventZip', true );
		$country = get_post_meta($postId, '_EventCountry', true );
		if (!$height){
		$height = eventsGetOptionValue('embedGoogleMapsHeight','350');}
		if (!$width){
		$width = eventsGetOptionValue('embedGoogleMapsWidth','100%');}
		
			if ( $country == "United States" && !empty( $address ) && !empty( $city ) && !empty( $state) && !empty( $zip ) ) {
				$googleaddress = urlencode( $address . " " . $city . " " . $state . " " . $zip . " " . $country);
			} elseif ( !empty( $country ) && !empty( $address ) && !empty( $city ) && !empty( $province ) && !empty( $zip ) ) {
				$googleaddress = urlencode( $address . " " . $city . " " . $province . " " . $zip . " " . $country);
			};
		
		if ($googleaddress) {
		
			$google_iframe = '<div id="googlemaps"><iframe width="'.$width.'" height="'.$height.'" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://www.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q='.$googleaddress.'?>&amp;output=embed"></iframe><br /><small><a href="http://www.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q='.$googleaddress.'" style="color:#0000FF;text-align:left">View Larger Map</a></small></div>';
			return $google_iframe;
		}
		else {
			return '';
		};
		
	}
	/**
	 * Displays an embeded google map for the given event
	 *
	 * @param string $postId 
	 * @param int $width 
	 * @param int $height
	 * @return void
	 */
	function event_google_map_embed( $postId = null, $width = null, $height = null ) {
		if (eventsGetOptionValue('embedGoogleMaps') == 'on'){ echo get_event_google_map_embed( $postId, $width, $height );};
	}
	/**
	 * Prints out the javascript required to control the datepicker (onChange of the id='datepicker')
	 *
	 * @param string a prefix to add to the ID of the calendar elements.  This allows you to reuse the calendar on the same page.
	 * @return void
	 */
	function get_jump_to_date_calendar( $prefix = '' ) {
		global $spEvents, $wp_query;
		if ( isset ( $wp_query->query_vars['eventDate'] ) ) { 
			$date = $wp_query->query_vars['eventDate'] . "-01";
		} else {
			$date = date_i18n( The_Events_Calendar::DBDATEFORMAT );
		}
		$monthOptions = $spEvents->getMonthOptions( $date );
		$yearOptions = $spEvents->getYearOptions( $date );
		include('views/datepicker.php');
	}
	/**
	 * Returns the event start date
	 *
	 * @param int post id
	 * @param bool display time?
	 * @param string date format
	 * @return string date
	 */
	function the_event_start_date( $postId = null, $showtime = 'true', $dateFormat = '' ) {
		global $spEvents, $post;
		if ( $postId === null || !is_numeric( $postId ) ) {
			global $post;
			$postId = $post->ID;
		}
		if( $dateFormat ) $format = $dateFormat;
		else $format = get_option( 'date_format', The_Events_Calendar::DATEONLYFORMAT );
		if( the_event_all_day( $postId ) ) {
		    $showtime = false;
		}
		if ( $showtime ) {
			$format = $spEvents->getTimeFormat( $format );
		}
		$shortMonthNames = ( strstr( $format, 'M' ) ) ? true : false;
		$date = date ( $format, strtotime( get_post_meta( $postId, '_EventStartDate', true ) ) );
		return str_replace( array_keys($spEvents->monthNames( $shortMonthNames )), $spEvents->monthNames( $shortMonthNames ), $date);
	}
	/**
	 * Returns the event end date
	 *
	 * @param int post id
	 * @param bool display time?
	 * @param string date format
	 * @return string date
	 */
	function the_event_end_date( $postId = null, $showtime = 'true', $dateFormat = '' ) {
		global $spEvents, $post;
		if ( $postId === null || !is_numeric( $postId ) ) {
			$postId = $post->ID;
		}
		if( $dateFormat ) $format = $dateFormat;
		else $format = get_option( 'date_format', The_Events_Calendar::DATEONLYFORMAT );
		if( the_event_all_day( $postId ) ) {
		    $showtime = false;
		}
		if ( $showtime ) {
			$format = $spEvents->getTimeFormat( $format );
		}
		$date = date ( $format, strtotime( get_post_meta( $postId, '_EventEndDate', true ) ) );
		return str_replace( array_keys($spEvents->monthNames()), $spEvents->monthNames(), $date);
	}
	/**
	* If EventBrite plugin is active
	* 	If the event is registered in eventbrite, and has one ticket.  Return the cost of that ticket.
	* 	If the event is registered in eventbrite, and there are many tickets, return "Varies"
	* If the event is not registered in eventbrite, and there is meta, return that.
	* If the event is not registered in eventbrite, and there is no meta, return ""
	*
	* @param mixed post id or null if used in the loop
	* @return string
	*/
	function the_event_cost( $postId = null) {
		global $spEvents;
		if ( $postId === null || !is_numeric( $postId ) ) {
			global $post;
			$postId = $post->ID;
		}
		if( class_exists( 'Eventbrite_for_The_Events_Calendar' ) ) {
			global $spEventBrite;
			$returned = $spEventBrite->the_event_cost($postId);
			if($returned) {
				return $returned;
			}
		}
		if ( $cost = get_post_meta( $postId, '_EventCost', true ) ) {
			return $cost;
		} else {
			return "";
		}
	}
	/**
	 * Returns the event venue
	 *
	 * @return string venue
	 */
	function the_event_venue( $postId = null) {
		if ( $postId === null || !is_numeric( $postId ) ) {
			global $post;
			$postId = $post->ID;
		}
		return get_post_meta( $postId, '_EventVenue', true );
	}
	/**
	 * Returns the event country
	 *
	 * @return string country
	 */
	function the_event_country( $postId = null) {
		if ( $postId === null || !is_numeric( $postId ) ) {
			global $post;
			$postId = $post->ID;
		}
		return get_post_meta( $postId, '_EventCountry', true );
	}
	/**
	 * Returns the event address
	 *
	 * @return string address
	 */
	function the_event_address( $postId = null) {
		if ( $postId === null || !is_numeric( $postId ) ) {
			global $post;
			$postId = $post->ID;
		}
		return get_post_meta( $postId, '_EventAddress', true );
	}
	/**
	 * Returns the event city
	 *
	 * @return string city
	 */
	function the_event_city( $postId = null) {
		if ( $postId === null || !is_numeric( $postId ) ) {
			global $post;
			$postId = $post->ID;
		}
		return get_post_meta( $postId, '_EventCity', true );
	}
	/**
	 * Returns the event state
	 *
	 * @return string state
	 */
	function the_event_state( $postId = null) {
		if ( $postId === null || !is_numeric( $postId ) ) {
			global $post;
			$postId = $post->ID;
		}
		return get_post_meta( $postId, '_EventState', true );
	}
	/**
	 * Returns the event province
	 *
	 * @return string province
	 */
	function the_event_province( $postId = null) {
		if ( $postId === null || !is_numeric( $postId ) ) {
			global $post;
			$postId = $post->ID;
		}
		return get_post_meta( $postId, '_EventProvince', true );
	}
	/**
	 * Returns the event zip code
	 *
	 * @return string zip code 
	 */
	function the_event_zip( $postId = null) {
		if ( $postId === null || !is_numeric( $postId ) ) {
			global $post;
			$postId = $post->ID;
		}
		return get_post_meta( $postId, '_EventZip', true );
	}
	/**
	 * Returns the event phone number
	 *
	 * @return string phone number
	 */
	function the_event_phone( $postId = null) {
		if ( $postId === null || !is_numeric( $postId ) ) {
			global $post;
			$postId = $post->ID;
		}
		return get_post_meta( $postId, '_EventPhone', true );
	}
	/**
	 * Returns a list of lectures that are associated with this event
	 *
	 * @param int optional post id
	 * @return mixed array of posts or false
	 */
	function the_event_lectures( $postId = null ) {
		if ( $postId === null || !is_numeric( $postId ) ) {
			global $post;
			$postId = $post->ID;
		}
		if( !is_event( $postId ) ) { 
			return false;
		}
		global $wpdb;
		$query = "SELECT * FROM {$wpdb->postmeta} WHERE meta_key = '_lectureEvent' AND meta_value = '{$postId}'";
		$results = $wpdb->get_results( $query );
		if( empty( $results ) ) { 
			return $results;
		}
		$lectures = array();
		foreach ( $results as $lecture ) {
			$lectures[] = $lecture->post_id;
		}
		$lectures = array_unique( $lectures );
		$results = array();
		foreach ( $lectures as $lectureId ) {
			$results[] = get_post( $lectureId ); 
		}
		return $results;
		
	}

	/**
	 * Helper function to load XML using cURL
	 *
	 * @return array with xml data
	 */
	function load_xml($url) {
    
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $data = simplexml_load_string(curl_exec($ch));

        curl_close($ch);

        return $data;
    }
		
	/**
	 * Called inside of the loop, returns true if the current post's meta_value (EventStartDate)
	 * is different than the previous post.   Will always return true for the first event in the loop.
	 *
	 * @return bool
	 */
	function is_new_event_day( ) {
		global $spEvents, $post;
		$retval = false;
		$now = time();
		$postTimestamp = strtotime( $post->EventStartDate, $now );
		$postTimestamp = strtotime( date( The_Events_Calendar::DBDATEFORMAT, $postTimestamp ), $now); // strip the time
		if ( $postTimestamp != $spEvents->currentPostTimestamp ) { 
			$retval = true;
		}
		$spEvents->currentPostTimestamp = $postTimestamp; 
		return $retval;
	}
	/**
	 * Call this function in a template to query the events and start the loop. Do not
	 * subsequently call the_post() in your template, as this will start the loop twice and then
	 * you're in trouble.
	 * 
	 * http://codex.wordpress.org/Displaying_Posts_Using_a_Custom_Select_Query#Query_based_on_Custom_Field_and_Category
	 *
	 * @param int number of results to display for upcoming or past modes (default 10)
	 * @uses $wpdb
	 * @uses $wp_query
	 * @return array results
	 */
	function get_events( $numResults = null ) {
		if( !$numResults ) $numResults = get_option( 'posts_per_page', 10 );
		global $wpdb, $wp_query, $spEvents;
		$spEvents->setOptions();
		$categoryId = get_cat_id( The_Events_Calendar::CATEGORYNAME );
		
		$extraSelectClause ='';
		$extraJoinEndDate ='';
		if ( events_displaying_month() ) {
			$extraSelectClause	= ", d2.meta_value as EventEndDate ";
			$extraJoinEndDate	 = " LEFT JOIN $wpdb->postmeta  as d2 ON($wpdb->posts.ID = d2.post_id) ";
			$whereClause = " AND d1.meta_key = '_EventStartDate' AND d2.meta_key = '_EventEndDate' ";
			// does this event start in this month?
			$whereClause .= " AND ((d1.meta_value >= '".$spEvents->date."'  AND  d1.meta_value < '".$spEvents->nextMonth( $spEvents->date )."')  ";
			// Or does it end in this month?
			$whereClause .= " OR (d2.meta_value  >= '".$spEvents->date."' AND d2.meta_value < '".$spEvents->nextMonth( $spEvents->date )."' ) ";
			// Or does the event start sometime in the past and end sometime in the distant future?
			$whereClause .= " OR (d1.meta_value  <= '".$spEvents->date."' AND d2.meta_value > '".$spEvents->nextMonth( $spEvents->date )."' ) ) ";
			$numResults = 999999999;
		}
		if ( events_displaying_upcoming() ) {
			$extraSelectClause	= ", d2.meta_value as EventEndDate ";
			$extraJoinEndDate	 = " LEFT JOIN $wpdb->postmeta  as d2 ON($wpdb->posts.ID = d2.post_id) ";
			$whereClause = " AND d1.meta_key = '_EventStartDate' AND d2.meta_key = '_EventEndDate' ";
			// Is the start date in the future?
			$whereClause .= ' AND ( d1.meta_value > "'.$spEvents->date.'" ';
			// Or is the start date in the past but the end date in the future? (meaning the event is currently ongoing)
			$whereClause .= ' OR ( d1.meta_value < "'.$spEvents->date.'" AND d2.meta_value > "'.$spEvents->date.'" ) ) ';
		}
		$eventsQuery = "
			SELECT $wpdb->posts.*, d1.meta_value as EventStartDate
				$extraSelectClause
			 	FROM $wpdb->posts 
			LEFT JOIN $wpdb->postmeta as d1 ON($wpdb->posts.ID = d1.post_id)
			$extraJoinEndDate
			LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
			LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
			WHERE $wpdb->term_taxonomy.term_id = $categoryId
			AND $wpdb->term_taxonomy.taxonomy = 'category'
			AND $wpdb->posts.post_status = 'publish'
			$whereClause
			ORDER BY d1.meta_value ".$spEvents->order."
			LIMIT $numResults";
		$return = $wpdb->get_results($eventsQuery, OBJECT);
		if ( $return ) {
			$wp_query->in_the_loop = true;
			do_action('loop_start');
		}
		return $return;
	}
	/**
	 * template tag to get an array of all events, regardless of the current query terms
	 *
	 * @param string field to order by (default is start date);
	 * @param string sort order (default is ASC)
	 * @return array of events
	 */
	function get_all_events( $orderby = 'd1.meta_value', $sort = 'DESC', $limit = 100 ) {
		global $wpdb;
		$categoryId = get_cat_id( The_Events_Calendar::CATEGORYNAME );
		$eventsQuery = "
			SELECT $wpdb->posts.*, d1.meta_value as EventStartDate
		 	FROM $wpdb->posts 
			LEFT JOIN $wpdb->postmeta as d1 ON($wpdb->posts.ID = d1.post_id)
			LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
			LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
			WHERE $wpdb->term_taxonomy.term_id = $categoryId
			AND $wpdb->term_taxonomy.taxonomy = 'category'
			AND $wpdb->posts.post_status = 'publish'
			AND d1.meta_key = '_EventStartDate'
			ORDER BY $orderby $sort
			LIMIT $limit
			";
		return $wpdb->get_results($eventsQuery, OBJECT);
	}
	/**
	 * Returns true if the query is set for past events, false otherwise
	 * 
	 * @return bool
	 */
	function events_displaying_past() {
		global $spEvents;
		return ($spEvents->displaying == "past") ? true : false;
	}
	/**
	 * Returns true if the query is set for upcoming events, false otherwise
	 * 
	 * @return bool
	 */
	function events_displaying_upcoming() {
		global $spEvents;
		return ($spEvents->displaying == "upcoming") ? true : false;
	}
	/**
	 * Returns true if the query is set for month display (as opposed to Upcoming / Past)
	 *
	 * @return bool
	 */
	function events_displaying_month() {
		global $spEvents;
		return ( $spEvents->displaying == "month" ) ? true : false;
	}
	/**
	 * Returns a link to the previous events in list view
	 *
	 * @return string 
	 */
	function events_get_past_link() {
		global $spEvents;
		if( '' == get_option('permalink_structure') ) {
			return trailingslashit( get_bloginfo('url') ) . '?cat=' . $spEvents->eventCategory() . '&eventDisplay=past';
		} else {
			return get_bloginfo( 'url' ) . '/'. $spEvents->getCategoryBase() . '/' . strtolower( The_Events_Calendar::CATEGORYNAME ) . '/past/';
		}
	}
	/**
	 * Returns a link to the upcoming events in list view
	 *
	 * @return string 
	 */
	function events_get_upcoming_link() {
		global $spEvents;
		if( '' == get_option('permalink_structure') ) {
			return trailingslashit( get_bloginfo('url') ) . '?cat=' . $spEvents->eventCategory() . '&eventDisplay=upcoming';
		} else {
			return get_bloginfo( 'url' ) . '/'. $spEvents->getCategoryBase() . '/' . strtolower( The_Events_Calendar::CATEGORYNAME ) . '/upcoming/';
		}
	}
	/**
	 * Returns a link to the next month's events page
	 *
	 * @return string 
	 */
	function events_get_next_month_link() {
		global $spEvents;
		if( '' == get_option('permalink_structure') ) {
			return trailingslashit( get_bloginfo('url') ) . '?cat=' . $spEvents->eventCategory() . '&eventDate=' . $spEvents->nextMonth( $spEvents->date );
		} else {
			return get_bloginfo( 'url' ) . '/'. $spEvents->getCategoryBase() . '/' . strtolower( The_Events_Calendar::CATEGORYNAME ) . '/' . $spEvents->nextMonth( $spEvents->date );
		}
	}
	/**
	 * Returns a link to the previous month's events page
	 *
	 * @return string
	 */
	function events_get_previous_month_link() {
		global $spEvents;
		if( '' == get_option('permalink_structure') ) {
			return trailingslashit( get_bloginfo('url') ) . '?cat=' . $spEvents->eventCategory() . '&eventDate=' . $spEvents->previousMonth( $spEvents->date );
		} else {
			return get_bloginfo( 'url' ) . '/' . $spEvents->getCategoryBase() . '/' . strtolower( The_Events_Calendar::CATEGORYNAME ) . '/' . $spEvents->previousMonth( $spEvents->date );
		}
	}
	/**
	 * Returns a link to the events category
	 *
	 * @return string
	 */
	function events_get_events_link() {
		global $spEvents;
		if( '' == get_option('permalink_structure') ) {
			return trailingslashit( get_bloginfo('url') ) . '?cat=' . $spEvents->eventCategory(); 
		} else {
			return get_bloginfo( 'url' ) . '/' . $spEvents->getCategoryBase() . '/' . strtolower( The_Events_Calendar::CATEGORYNAME ) . '/'; 
		}
	}
	
	function events_get_gridview_link( ) {
		global $spEvents;
		if( '' == get_option('permalink_structure') ) {
			return trailingslashit( get_bloginfo('url') ) . '?cat=' . $spEvents->eventCategory() . '&eventDisplay=month';
		} else {
			return trailingslashit( get_bloginfo('url') ) . $spEvents->getCategoryBase() . '/' . strtolower( The_Events_Calendar::CATEGORYNAME ) . '/month';
		}
	}
		
	function events_get_listview_link( ) {
		global $spEvents;
		if( '' == get_option('permalink_structure') ) {
			return trailingslashit( get_bloginfo('url') ) . '?cat=' . $spEvents->eventCategory() . '&eventDisplay=upcoming';
		} else {
			return trailingslashit( get_bloginfo('url') ) . $spEvents->getCategoryBase() . '/' . strtolower( The_Events_Calendar::CATEGORYNAME ) . '/upcoming';
		}
	}
	function events_get_listview_past_link( ) {
		global $spEvents;
		if( '' == get_option('permalink_structure') ) {
			return trailingslashit( get_bloginfo('url') ) . '?cat=' . $spEvents->eventCategory() . '&eventDisplay=past';
		} else {
			return trailingslashit( get_bloginfo('url') ) . $spEvents->getCategoryBase() . '/' . strtolower( The_Events_Calendar::CATEGORYNAME ) . '/past';
		}
	}

	/**
	 * Returns a textual description of the previous month
	 *
	 * @return string
	 */
	function events_get_previous_month_text() {
		global $spEvents;
		return $spEvents->getDateString( $spEvents->previousMonth( $spEvents->date ) );
	}
	/**
	 * Returns a texual description of the current month
	 *
	 * @return string
	 */
	function events_get_current_month_text( ){
		global $spEvents; 
		return date( 'F', strtotime( $spEvents->date ) );
	}
	/**
	 * Returns a textual description of the next month
	 *
	 * @return string
	 */
	function events_get_next_month_text() {
		global $spEvents;
		return $spEvents->getDateString( $spEvents->nextMonth( $spEvents->date ) );
	}
	/**
	 * Returns a formatted date string of the currently displayed month (in "jump to month" mode)
	 *
	 * @return string
	 */
	function events_get_displayed_month() {
		global $spEvents;
		if ( $spEvents->displaying == "month" ) {
			return $spEvents->getDateString( $spEvents->date );
		}
		return " ";
	}
	/**
	 * Returns a link to the currently displayed month (if in "jump to month" mode)
	 *
	 * @return string
	 */
	function events_get_this_month_link() {
		global $spEvents;
		if ( $spEvents->displaying == "month" ) {
			return get_bloginfo( 'url' )  . '/' . strtolower( The_Events_Calendar::CATEGORYNAME ) . '/' . $spEvents->date;
		}
		return false;
	}
	/**
	 * Returns the state or province for US or non-US addresses
	 *
	 * @return string
	 */
	function the_event_region() {
		if (get_post_meta($postId, '_EventCountry', true ) == 'United States') {
			return the_event_state();
		} else {
			return the_event_province(); 
		}
	}
	/**
	 * Returns true if the event is an all day event
	 *
	 * @return bool
	 */
	function the_event_all_day( $postId = null ) {
		if ( $postId === null || !is_numeric( $postId ) ) {
			global $post;
			$postId = $post->ID;
		}
		return get_post_meta( $postId, '_EventAllDay', true );
	}
} // end if class_exists('The-Events-Calendar')

if( !class_exists( 'Events_List_Widget' ) ) {
	/**
	 * Event List Widget
	 *
	 * Creates a widget that displays the next upcoming x events
	 */

	class Events_List_Widget extends WP_Widget {
		
		public $pluginDomain = 'the-events-calendar';
		
		function Events_List_Widget() {
				/* Widget settings. */
				$widget_ops = array( 'classname' => 'eventsListWidget', 'description' => __( 'A widget that displays the next upcoming x events.', $this->pluginDomain) );

				/* Widget control settings. */
				$control_ops = array( 'id_base' => 'events-list-widget' );

				/* Create the widget. */
				$this->WP_Widget( 'events-list-widget', 'Events List Widget', $widget_ops, $control_ops );
			}
		
			function widget( $args, $instance ) {
				global $wp_query;
				extract( $args );

				/* User-selected settings. */
				$title = apply_filters('widget_title', $instance['title'] );
				$limit = $instance['limit'];
				$start = $instance['start'];
				$end = $instance['end'];
				$venue = $instance['venue'];
				$address = $instance['address'];
				$city = $instance['city'];
				$state = $instance['state'];
				$province = $instance['province'];
				$zip = $instance['zip'];
				$country = $instance['country'];
				$phone = $instance['phone'];
				$cost = $instance['cost'];
				
				if ( eventsGetOptionValue('viewOption') == 'upcoming') {
					$event_url = events_get_listview_link();
				} else {
					$event_url = events_get_gridview_link();
				}

				/* Before widget (defined by themes). */
				echo $before_widget;

				/* Title of widget (before and after defined by themes). */
				if ( $title )
					echo $before_title . $title . $after_title;
				
				/* Display link to all events */
				echo '<div class="dig-in"><a href="' . $event_url . '">' . __('View All Events', $this->pluginDomain ) . '</a></div>';

				/* Display list of events. */
					if( function_exists( 'get_events' ) ) {
						$old_display = $wp_query->get('eventDisplay');
						$wp_query->set('eventDisplay', 'upcoming');
						$posts = get_events($limit);
						//print_r($posts);
						if ($posts) : 
						
							echo "<ul class='upcoming'>";
							foreach( $posts as $post ) : 
								setup_postdata($post);
								if (file_exists(TEMPLATEPATH.'/events/events-list-load-widget-display.php') ) {
									include (TEMPLATEPATH.'/events/events-list-load-widget-display.php');
								} else {
									include( dirname( __FILE__ ) . '/views/events-list-load-widget-display.php' );						
								}
							endforeach;
						echo "</ul>";

						else:
							echo "no events";
						endif;
						$wp_query->set('eventDisplay', $old_display);
					}
				

				/* After widget (defined by themes). */
				echo $after_widget;
			}	
		
			function update( $new_instance, $old_instance ) {
					$instance = $old_instance;

					/* Strip tags (if needed) and update the widget settings. */
					$instance['title'] = strip_tags( $new_instance['title'] );
					$instance['limit'] = strip_tags( $new_instance['limit'] );
					$instance['start'] = strip_tags( $new_instance['start'] );
					$instance['end'] = strip_tags( $new_instance['end'] );
					$instance['venue'] = strip_tags( $new_instance['venue'] );
					$instance['country'] = strip_tags( $new_instance['country'] );
					$instance['address'] = strip_tags( $new_instance['address'] );
					$instance['city'] = strip_tags( $new_instance['city'] );
					$instance['state'] = strip_tags( $new_instance['state'] );
					$instance['province'] = strip_tags( $new_instance['province'] );
					$instance['zip'] = strip_tags( $new_instance['zip'] );
					$instance['phone'] = strip_tags( $new_instance['phone'] );
					$instance['cost'] = strip_tags( $new_instance['cost'] );

					return $instance;
			}
		
			function form( $instance ) {
				/* Set up default widget settings. */
				$defaults = array( 'title' => 'Upcoming Events', 'limit' => '5', 'start' => true, 'end' => false, 'venue' => false, 'country' => true, 'address' => false, 'city' => true, 'state' => true, 'province' => true, 'zip' => false, 'phone' => false, 'cost' => false);
				$instance = wp_parse_args( (array) $instance, $defaults );			
				include( dirname( __FILE__ ) . '/views/events-list-load-widget-admin.php' );
			}
	}

	/* Add function to the widgets_ hook. */
	add_action( 'widgets_init', 'events_list_load_widgets' );

	/* Function that registers widget. */
	function events_list_load_widgets() {
		global $pluginDomain;
		register_widget( 'Events_List_Widget' );
		// load text domain after class registration
		load_plugin_textdomain( $pluginDomain, false, basename(dirname(__FILE__)) . '/lang/');
	}
}

if( !class_exists( 'Events_Calendar_Widget') ) {
	
	/**
	* Calendar widget class
	*
	* 
	*/
	class Events_Calendar_Widget extends WP_Widget {
		
			public $pluginDomain = 'the-events-calendar';

			function Events_Calendar_Widget() {
				$widget_ops = array('classname' => 'events_calendar_widget', 'description' => __( 'A calendar of your events') );
				$this->WP_Widget('calendar', __('Events Calendar'), $widget_ops);
			}

			function widget( $args, $instance ) {
				extract($args);
				$title = apply_filters('widget_title', empty($instance['title']) ? '&nbsp;' : $instance['title']);
				echo $before_widget;
				if ( $title )
					echo $before_title . $title . $after_title;
				echo '<div id="calendar_wrap">';
				//echo get_calendar_custom(); /* 5 is the category id I have for event */
				echo '</div>';
				echo $after_widget;
			}
		
			function update( $new_instance, $old_instance ) {
				$instance = $old_instance;
				$instance['title'] = strip_tags($new_instance['title']);

				return $instance;
			}

			function form( $instance ) {
				$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
				$title = strip_tags($instance['title']);
		?>
				<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<?php
			}
		
		}
	
		/* Add function to the widgets_ hook. */
		add_action( 'widgets_init', 'events_calendar_load_widgets' );
		//add_action( 'widgets_init', 'get_calendar_custom' );
	
		//function get_calendar_custom(){echo "hi";}

		/* Function that registers widget. */
		function events_calendar_load_widgets() {
			global $pluginDomain;
			register_widget( 'Events_Calendar_Widget' );
			// load text domain after class registration
			load_plugin_textdomain( $pluginDomain, false, basename(dirname(__FILE__)) . '/lang/');
		}
}
?>