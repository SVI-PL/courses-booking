<?php
  if ( ! defined( 'ABSPATH' ) ) exit;
  
add_action('plugins_loaded', 'almosoft_init_booking_class');

function almosoft_init_booking_class(){
	
	require_once (Almosoft_Course_booking_file.'/admin/almosoft_settings_api.php');
	require_once (Almosoft_Course_booking_file.'/admin/settings.php');
	require_once (Almosoft_Course_booking_file.'/includes/template_loader.php');
	require_once (Almosoft_Course_booking_file.'/vendor/autoload.php');


	class Almosoft_Courses_Booking extends Experts_Template_Loader{
		
		public function __construct(){
			
			add_action( 'wp_enqueue_scripts', array( &$this, 'assets_files_load') );
			add_action( 'wp_enqueue_scripts', array( &$this, 'code_95_cursusdata') );
			add_action( 'wp_enqueue_scripts', array( &$this, 'object_almosoft') );

			//add new query vars.
			add_action('init', array($this, 'booking_endpoint'),10);
			
			add_filter('request', array($this, 'set_booking_var_default_val'),1);
			add_filter( 'parse_request', array($this, 'almosoft_save_student'), 0 );
			add_shortcode( 'booking_success_page', array( $this, 'booking_complete_status' ) );
			
			register_activation_hook( __FILE__, array($this,'almosoft_plugin_activate' ));
			
			//shortcode to render booking html
			add_shortcode('booking_steps_form', array($this, 'booking_steps_form'));
			add_shortcode('header_booking_form', array($this, 'header_booking_form'));
			
			$almosoft_booking_options 	= get_option('almosoft_booking');
			$almosoft_payment_config 	= get_option('almosoft_payment');
			$almosoft_niwo_config 		= get_option('almosoft_niwo');
			$almosoft_price 			= get_option('almosoft_price');
			$this->almosoft_nivo_config	= array_merge($almosoft_niwo_config,$almosoft_price);
//echo "<pre>almosoft_price="; print_r($almosoft_price); echo"</pre>";
//echo "<pre>almosoft_payment_config="; print_r($almosoft_payment_config); echo"</pre>";
//echo "<pre>almosoft_nivo_config="; print_r($almosoft_niwo_config); echo"</pre>";
			$this->courses = (isset($almosoft_booking_options['available_courses']))? $almosoft_booking_options['available_courses']: '';
            $this->niwo_courses = (isset($almosoft_booking_options['niwo_courses']))? $almosoft_booking_options['niwo_courses']: '';
			$this->referral_options = (isset($almosoft_booking_options['referral_options']))? $almosoft_booking_options['referral_options']: '';
			$this->locations = (isset($almosoft_booking_options['locations']))? $almosoft_booking_options['locations']: '';
			$this->price = (isset($almosoft_payment_config['price']))? $almosoft_payment_config['price']: 50;
			$this->partial_payment = (isset($almosoft_payment_config['partial_payment']))? $almosoft_payment_config['partial_payment']: 50;
			
			$crm_api_config =  get_option('almosoft_api_setting');
			$this->api_username = (isset($almosoft_payment_config['username']))? $almosoft_payment_config['username']: 'nuvrachtwagen';
			$this->api_pass = (isset($almosoft_payment_config['password']))? $almosoft_payment_config['password']: 'XGTZwkKrPRhv7S3';
			
			$this->currency = 'Eur';
			$this->curr_symbol = '€';
			$this->booking_success = (isset($almosoft_booking_options['booking_success']))? $almosoft_booking_options['booking_success']: 'You have been registered successfully';
			$this->registration_failed = (isset($almosoft_payment_config['registration_failed']))? $almosoft_payment_config['registration_failed']: '
Aanmelding niet geluk. Probeer nog een keer.';
			$this->booking_pending = (isset($almosoft_payment_config['booking_pending']))? $almosoft_payment_config['booking_pending']: '
Betaling niet gelukt. Probeer  nog een keer.';
			
			$this->mollie_test_key = (isset($almosoft_payment_config['mollie_test_key']))? $almosoft_payment_config['mollie_test_key'] : 'test_USJtthD2rqdEfMyH6mUMCBkAEda6A4';
			$this->mollie_live_key = (isset($almosoft_payment_config['mollie_live_key']))? $almosoft_payment_config['mollie_live_key'] : 'live_HvJFz4DyxvkhHpvcqmmnkE3FmEqAdn';
			$this->mollie_key = ($almosoft_payment_config['testmode']== 'on')? $this->mollie_test_key : $this->mollie_live_key;
			$this->success_page = (isset($almosoft_payment_config['success_page']))? $almosoft_payment_config['success_page'] : 745;
			$this->payment_method_title = (isset($almosoft_payment_config['payment_title']))? $almosoft_payment_config['payment_title'] : 'Pay by iDeal(Mollie) payment gateway';
			$this->payment_method_description = (isset($almosoft_payment_config['description']))? $almosoft_payment_config['description'] : 'Pay by iDeal(Mollie) payment gateway';
			
			$this->apiurl      = "https://crm.nuvrachtwagen.nl";
			$this->webhookUrl  = site_url()."/idealpayment/";
			
			
			add_action( 'wp_ajax_course_available_dates', array($this,'get_course_available_dates') );
			add_action( 'wp_ajax_nopriv_course_available_dates', array($this,'get_course_available_dates') );
			
			add_action( 'wp_ajax_booking_payment_process', array($this,'booking_payment_process') );
			add_action( 'wp_ajax_nopriv_booking_payment_process', array($this,'booking_payment_process') );

			add_action( 'wp_ajax_booking_payment_process_paypal', array($this,'booking_payment_process_paypal') );
			add_action( 'wp_ajax_nopriv_booking_payment_process_paypal', array($this,'booking_payment_process_paypal') );

			add_action( 'wp_ajax_payment_process_paypal_success', array($this,'payment_process_paypal_success') );
			add_action( 'wp_ajax_nopriv_payment_process_paypal_success', array($this,'payment_process_paypal_success') );

			$this->mollie = new \Mollie\Api\MollieApiClient();
			$this->mollie->setApiKey($this->mollie_live_key); // CheckBox doesn't work correctly. Set live key
			
			add_action( 'almosoft_admin_booking_list_page', array( $this, 'booking_list' ) );
			
			add_action( 'admin_menu', array( &$this, 'register_booking_menu' ) );
			
			add_action( 'admin_menu', array(&$this,'almosoft_admin_view_booking') );
			add_filter('rewrite_rules_array', array($this,'add_rewrite_rules'));
			add_filter('query_vars', array($this, 'add_query_vars'));
			
		}
        function payment_process_paypal_success(){
		    $payment_id = $_POST['paypal_id'];
		    global $wpdb;
            $this->payment_ststus_change($payment_id, 'Received');
            $booking_info = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM {$wpdb->prefix}courses_bookings WHERE payment_id='%s'",
                    $payment_id
                )
            );

            $deposit = $booking_info->deposit;
            $course_ids = $booking_info->course_num_ids;
            $bookings = explode(',',$course_ids);
            $no_of_course = count($bookings);
            $final_deposit = $deposit;

            if($no_of_course>1){
                $final_deposit = $deposit/$no_of_course;
            }

            $student_registration = array(
                'first_name'=>$booking_info->first_name,
                'last_name'=>$booking_info->last_name,
                'email'=>$booking_info->email,
                'deposit'=>$final_deposit,
                'phone'=>$booking_info->phone,
                'address'=>$booking_info->address,
                'postcode'=>$booking_info->postcode,
                'residence'=>$booking_info->residence,
                'date_of_birth'=>$booking_info->date_of_birth,
                'referral'=>$booking_info->referral,
                'pass_status'=>'UNKNOWN',
                'student_status'=>'NEW',
                'comment'=>'',
                'added_by'=>4,
            );

            if($course_ids){
                $this->register_customer_on_Crm($student_registration, $bookings, $payment_id);
            }else{
                //crm registration missed ststus change
                $this->registration_ststus_change($payment_id,'missed');
            }
        }
		function booking_complete_status(){
		  global $wp;
		  
		  $sucess_message = $this->booking_success;	
		  if ( isset($wp->query_vars['regis']) && !empty($wp->query_vars['regis'])) {
			 
			 $orderId = $wp->query_vars['regis'];
			
			 $payment_id = $this->get_payment_id($orderId);
			 if($payment_id){
				 $payment = $this->mollie->payments->get($payment_id);
				 $sucess_message = $this->booking_success;
				 
				 if($payment->isCanceled()){
					$sucess_message = $this->registration_failed;	
				 }
				 
				 if($payment->isPending()){
					$sucess_message = $this->booking_pending;	
				 }
				 
				 if($payment->isFailed()){
					$sucess_message = $this->registration_failed;	
				 }
			 }else{
				 $sucess_message = 'Order id does not exist.';	
			 }
			 
			 //$data = array('message'=>$sucess_message);
			 //$this->set_template_data($data)->get_template_part( "thank-you" );
			 
		  }
		  return "<p class='thankyoumsg'>".$sucess_message."</p>";	
		}
		function add_rewrite_rules($aRules) {
			$aNewRules = array('thank-you/([^/]+)/?$' => 'index.php?pagename=thank-you&regis=$matches[1]');
			$aRules = $aNewRules + $aRules;
			return $aRules;
		}
		
		function add_query_vars($aVars) {
			$aVars[] = "regis"; // represents the name of the product category as shown in the URL
			return $aVars;
		}

		function register_booking_menu(){
			add_menu_page('Manage Booking', 'Bookings', 'edit_posts', 'bookings', array( $this, 'admin_booking_list_page' ), 'dashicons-groups', 7 ) ;
			
		}
		// all booking list show 
		function admin_booking_list_page() {
			do_action( 'almosoft_admin_booking_list_page' );
		}
		
		function almosoft_admin_view_booking(){
			add_submenu_page(
				'null',
				'Booking Details!',
				'Hidden!',
				'edit_posts',
				'viewbooking',
				array($this, 'view_booking_details')
			);
		}
		
		
		function view_booking_details(){
			$id = $this->get_get('id');
			
			$booking_info = $this->get_booking_details_by_id($id);
			$data = array('booking_info'=>$booking_info);
			$this->set_template_data( $data )->get_template_part( "admin/booking_details" );
			
		}
		
		function get_booking_details_by_id($id){
			global $wpdb;
			$booking_info = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT * FROM {$wpdb->prefix}courses_bookings WHERE id='%s'",
					$id
				)
			);
			return $booking_info;
		}
		
		function get_payment_id($id){
			global $wpdb;
			return $payment_id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT payment_id FROM {$wpdb->prefix}courses_bookings WHERE id='%s'",
					$id
				)
			);
			
		}
		
		function booking_endpoint(){
			
			add_rewrite_endpoint('idealpayment', EP_ALL);  	
		}
		
		function set_booking_var_default_val($vars ){
			
			if (isset($vars['idealpayment'])) {
				$vars['idealpayment'] = true;
			}
			
			return $vars;
		}
		
		function almosoft_save_student($wp){
			global $wpdb;
			if (!array_key_exists( 'idealpayment', $wp->query_vars ) ) {
				return;
			}
			
			$id = $this->get_post('id');
			
			if(!empty($id)){
				
				$payment = $this->mollie->payments->get($id);
				
				if ($payment->isPaid()){
					$this->payment_ststus_change($payment->id, 'Received');
					$booking_info = $wpdb->get_row(
						$wpdb->prepare(
							"SELECT * FROM {$wpdb->prefix}courses_bookings WHERE payment_id='%s'",
							$payment->id
						)
					);
					
					$deposit = $booking_info->deposit;
					$course_ids = $booking_info->course_num_ids;
					$bookings = explode(',',$course_ids);
					$no_of_course = count($bookings);
					$final_deposit = $deposit;
					
					if($no_of_course>1){
						$final_deposit = $deposit/$no_of_course;
					}
					
					$student_registration = array(
						'first_name'=>$booking_info->first_name,
						'last_name'=>$booking_info->last_name,
						'email'=>$booking_info->email,
						'deposit'=>$final_deposit,
						'phone'=>$booking_info->phone,
						'address'=>$booking_info->address,
						'postcode'=>$booking_info->postcode,
						'residence'=>$booking_info->residence,
						'date_of_birth'=>$booking_info->date_of_birth,
						'referral'=>$booking_info->referral,
						'pass_status'=>'UNKNOWN',
						'student_status'=>'NEW',
						'comment'=>'',
						'added_by'=>4,
					);
					
					if($course_ids){
						$this->register_customer_on_Crm($student_registration, $bookings, $payment->id);
					}else{
						//crm registration missed ststus change
						$this->registration_ststus_change($payment->id,'missed');
					}
				}
				
				//payment pending
				if ($payment->isPending()){
					$this->payment_ststus_change($payment->id, 'Pending');
				}
				
				//payment pending
				if ($payment->isCanceled()){
					$this->payment_ststus_change($payment->id, 'Canceled');
				}
				
				//payment pending
				if ($payment->isFailed()){
					$this->payment_ststus_change($payment->id, 'Failed');
				}
				
			}
			
			die();
		}
		
		function payment_ststus_change($payment_id, $status){
			global $wpdb;
				
			$table = "{$wpdb->prefix}courses_bookings";
			return $wpdb->update($table,array('payment_status'=>$status), array('payment_id'=>$payment_id));
		}
		
		/** update crm registration status in wp */
		function registration_ststus_change($payment_id, $status){
			global $wpdb;
				
			$table = "{$wpdb->prefix}courses_bookings";
			return $wpdb->update($table,array('crm_status'=>$status), array('payment_id'=>$payment_id));
		}
		
		function register_customer_on_Crm($student_registration, $bookings, $payment_id){
			
			
			$action_url = $this->apiurl."/api/students/";
			
			$json_data = json_encode($student_registration);
			
			$token = $this->get_api_token();
			
			foreach($bookings as $booking){
				
				$response_student = $this->post_api_request($action_url, $json_data,$token);
				
				$student_id = $response_student['response']->id;
				if(!$student_id){
					//crm registration missed ststus change
					$this->registration_ststus_change($payment_id,'missed');
				}else{
					$this->registration_ststus_change($payment_id,'created');
				}
				// save course id
				$action_regis_url = $this->apiurl."/api/students/{$student_id}/make_booking/";
				
				$booking_data = array('course'=>$booking);
				$json_booking = json_encode($booking_data);
				$response_booking = $this->post_api_request($action_regis_url, $json_booking, $token);
				
			}
			
		}
		
		function almosoft_plugin_activate() {
			global $wp_rewrite;
			
			$wp_rewrite->flush_rules();
		}
		
		function stringconvertto_options($comma_separated_string, $args=array()){
			
			
			$arr_options = explode(',', $comma_separated_string);
			$option_html = '';
			
			if(!empty($args) && isset($args['blank_option'])){
				
				if($args['blank_option']=='yes'){
					
					$option_html .= "<option value=''>".$args['blank_option_label']."</option>\n\n";
				}
			}
			foreach($arr_options as $optionval){
				$optionval = trim($optionval);
				$selected = '';
				if(!empty($args) && isset($args['current_val'])){
					$selected = ($args['current_val']==$optionval)?'selected="selected"':'';
				}
				$option_html .= "<option {$selected} value='{$optionval}'>{$optionval}</option>\n\n";
			}
			
			return $option_html;
			
		}
		
		function get_api_request($url, $token, $params=''){
				
			$args = array("Authorization: Token {$token}");
			
			$curl = curl_init();
			
			if($params){
				$url = sprintf("%s?%s", $url, rawurldecode(http_build_query($params)));
			}
			
			// OPTIONS:
			curl_setopt($curl, CURLOPT_URL, $url);
			//curl_setopt($curl, CURLOPT_HEADER, 1);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $args);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			
			// EXECUTE:
			$result = curl_exec($curl);
			$status_code = curl_getinfo($curl,  CURLINFO_HTTP_CODE );
			
			curl_close($curl);
			
			$response = array();
			if($status_code !=200){
				$response['error'] = 'error to process request';
			}
			
			return $result = json_decode($result);
		
		}

		function post_api_request($url, $json='', $token){
			
			$curl = '';
			$result = '';
			$status_code = '';
			$response = array();
			/**if(isset($access_token['error'])){
				return $access_token;
			}
			
			$data = $access_token['response'];
			$token = $data->token;*/
			
			$args = array(
				'Content-Type: application/json',
				"Authorization: Token {$token}"
			  );
			
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_POST, 1);
			if ($json)
			curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
			
			// OPTIONS:
			curl_setopt($curl, CURLOPT_VERBOSE, 1);
			//curl_setopt($curl, CURLOPT_HEADER, 1);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $args);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			
			
			$result = curl_exec($curl);
			$status_code = curl_getinfo($curl,  CURLINFO_HTTP_CODE );
			
			curl_close($curl);
			
			
			if($status_code !=200 && $status_code !=201){
				$response['error'] = 'error to process request';
			}
			
			$response['response'] = json_decode($result);
			
			return $response;

		}
		
		function get_api_token(){
			$curl = '';
			$json = '';
			$result = '';
			$status_code = '';
			
			$url = $this->apiurl.'/auth/';

			$args = array(
				'Content-Type: application/json',
				);
			
			
			$auth_info = array('username'=>$this->api_username,'password'=>$this->api_pass);
			
			$json = json_encode($auth_info);
			
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_POST, 1);
			if ($json)
			curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
			
			// OPTIONS:
			curl_setopt($curl, CURLOPT_VERBOSE, 1);
			//curl_setopt($curl, CURLOPT_HEADER, 1);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $args);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			
			// EXECUTE:
			$result = curl_exec($curl);
			$status_code = curl_getinfo($curl,  CURLINFO_HTTP_CODE );
		
			curl_close($curl);
			$response = array();
			if($status_code !=200){
				$response['error'] = 'error to process request';
			}
			
			$response['response'] = json_decode($result);
			$token = $response['response']->token;
			return $token;
		}
		
		function get_course_available_dates(){
			$token = $this->get_api_token();
			$from_date = date('Y-m-d');
			$course_type = $this->get_post('course_type');
            $course_label = $this->get_post('course_label');
			$to_date = '';
			$city = str_replace(' ','',$this->get_post('city'));
			$city = strtoupper($city);
			$available='false';
			$args = array(
				'course_type'=>$course_type,
                'course_label'=>$course_label,
				'from_date'=>$from_date,
				'to_date'=>$to_date,
				'city'=>$city,
				'available'=>'true',
				'ordering'=>'date'
			);
			
			$action_url = $this->apiurl."/api/courses/";
			$response = $this->get_api_request($action_url, $token, $args);
			
			$date_options_html = '';
			$response_data = array();
			
			foreach($response as $course){
				$date = date('l j F Y',strtotime($course->date));
				$date =  $this->dutch_strtotime($date);
				//$course_date->date = $date;
				$response_data[] = array(
				'id'=>$course->id,
				'city'=>$course->city,
				'course_type'=>$course->course_type,
                'course_label'=>$course->course_label,
				'date'=>$date,
				'time'=>$course->time,
				);
			}
			
			wp_send_json_success( $response_data );
			
		}
		
		function dutch_strtotime($datetime) {
			$days = array(
				"Monday"   => "maandag",
				"Tuesday"   => "dinsdag",
				"Wednesday"  => "woensdag",
				"Thursday" => "donderdag",
				"Friday"   => "vrijdag",
				"Saturday"  => "zaterdag",
				"Sunday"    => "zondag"
			);

			$months = array(
				"January"   => "jan",
				"February"  => "febr",
				"March"     => "mrt",
				"April"     => "apr",
				"May"       => "mei",
				"June"      => "juni",
				"July"      => "juli",
				"August"  => "aug",
				"September" => "sep",
				"October"   => "okt",
				"November"  => "nov",
				"December"  => "dec"
			);

			$array_wday_mday_m_y = explode(" ", $datetime);
			
			$dutch_date = $days[$array_wday_mday_m_y[0]].', '.$array_wday_mday_m_y[1].' '.$months[$array_wday_mday_m_y[2]].' '. $array_wday_mday_m_y[3];
			return $dutch_date;
		}
		
		function get_course_ids($booking_order_details, $colm='course_type', $return_type='string'){
			$ids = array();
			$bookings = json_decode($booking_order_details);
			foreach($bookings as $booking){
				$ids[$booking->id] = ($colm=='course_type')?$booking->course_type:$booking->id;
			}
			
			if($return_type=='arr'){
				return $ids;
			}
			
			return implode(',',$ids);
		}
		
		function get_course_code_date($booking_order_details){
			$ids = array();
			$bookings = json_decode($booking_order_details);
			foreach($bookings as $booking){
				$ids[$booking->id] = $booking->city .' '.$booking->course_type .' '.$booking->date;
			}
			
			return implode(';',$ids);
		}
		
		function booking_payment_process(){
			
			$payment_option   = $this->get_post('payment_option');
			$customer_data    = stripslashes(str_replace('\\','',$this->get_post('customer_data')));
			$booking_order_details = stripslashes(str_replace('\\','',$this->get_post('booking_order_details')));
			$course_ids_code = $this->get_course_code_date($booking_order_details);
			$course_ids = $this->get_course_ids($booking_order_details,'id');
			
			$customer = json_decode($customer_data);
			switch($payment_option){
				case 'partial':
					$deposit = $customer->partial_total;
				break;
				case 'full':
					$deposit = $customer->grand_total;
				break;
				
				default:
				$deposit = 0;
			}
			
				
			$date_of_birth = date_create($customer->date_of_birth);
			$date_of_birth = date_format($date_of_birth,"Y-m-d");
			
			$student_registration = array(
				'first_name'=>$customer->first_name,
				'last_name'=>$customer->last_name,
				'email'=>$customer->email,
				'deposit'=>$deposit,
				'phone'=>$customer->phone,
				'address'=>$customer->address,
				'postcode'=>$customer->postcode,
				'residence'=>$customer->residence,
				'date_of_birth'=>$date_of_birth,
				'referral'=>$customer->referral,
				'pass_status'=>'UNKNOWN',
				'student_status'=>'NEW',
				'comment'=>'',
				'added_by'=>4,
			);
			
			$json_data = json_encode($student_registration);
			
			if($payment_option !='skip'){
				
				$value = ($payment_option == 'partial')? $customer->partial_total : $customer->grand_total;
				
				$amount = number_format($value, 2, '.', '');
				
				$student_registration['payment_status'] = 'incomplete';
				$student_registration['course_ids'] 	= $course_ids_code;
				$student_registration['course_num_ids'] = $course_ids;
				$orderId = $this->save_customer_details_wp($student_registration);
				
				$payment = $this->mollie->payments->create([
					"amount" => [
						"currency" => strtoupper($this->currency),
						"value" => $amount
					],
					"description" => "Payment for {$student_registration['course_ids']} {$student_registration['phone']}",
					"redirectUrl" => get_permalink($this->success_page).$orderId.'/',
					"webhookUrl"  => $this->webhookUrl,
				]);

				
				$this->update_order_payment_id($orderId, $payment->id);
				
				$response_student = array(
					'pay_method'=>$payment_option, 
					'payment_url'=>$payment->getCheckoutUrl()
				);
				
				$response_data = json_encode($response_student);
				
				wp_send_json_success( $response_student );
				
			}else{
				//save data to crm after payment skipped case
				$crm_status = 'missed';
				$action_url = $this->apiurl."/api/students/";
				$bookings = $this->get_course_ids($booking_order_details,'id', 'arr');
				$token = $this->get_api_token();
				
				foreach($bookings as $booking){
					
					$response_student = array();
					$booking_data = array();
					$response_booking = array();
					$crm_status = 'created';
					$response_student = $this->post_api_request($action_url, $json_data, $token);
					
					$student_id = $response_student['response']->id;
					
					if(!$student_id){
						$crm_status = 'missed';
					}
					// save course id
					$make_booking_url = $this->apiurl."/api/students/{$student_id}/make_booking/";
					
					$booking_data = array('course'=>$booking);
					$json_booking = json_encode($booking_data);
					$response_booking = $this->post_api_request($make_booking_url, $json_booking, $token);
					
				}
				
				$student_registration['course_ids'] = $course_ids_code;
				$student_registration['course_num_ids'] = $course_ids;
				$student_registration['crm_status'] = $crm_status;
				$student_registration['payment_status'] = 'on spot';
				
				$this->save_customer_details_wp($student_registration);
				$response_booking['pay_method'] = $payment_option;
				
				wp_send_json_success( $response_booking );
				
			}
		}

		function booking_payment_process_paypal(){
			$payment_option   = $this->get_post('payment_option');
			$customer_data    = stripslashes(str_replace('\\','',$this->get_post('customer_data')));
			$booking_order_details = stripslashes(str_replace('\\','',$this->get_post('booking_order_details')));
			$course_ids_code = $this->get_course_code_date($booking_order_details);
			$course_ids = $this->get_course_ids($booking_order_details,'id');

			$customer = json_decode($customer_data);
            $deposit = $customer->grand_total;


			$date_of_birth = date_create($customer->date_of_birth);
			$date_of_birth = date_format($date_of_birth,"Y-m-d");

			$student_registration = array(
				'first_name'=>$customer->first_name,
				'last_name'=>$customer->last_name,
				'email'=>$customer->email,
				'deposit'=>$deposit,
				'phone'=>$customer->phone,
				'address'=>$customer->address,
				'postcode'=>$customer->postcode,
				'residence'=>$customer->residence,
				'date_of_birth'=>$date_of_birth,
				'referral'=>$customer->referral,
				'pass_status'=>'UNKNOWN',
				'student_status'=>'NEW',
				'comment'=>'',
				'added_by'=>4,
			);

			$json_data = json_encode($student_registration);

            $value = $customer->grand_total;
            $amount = number_format($value, 2, '.', '');
            $student_registration['payment_status'] = 'incomplete';
            $student_registration['course_ids'] 	= $course_ids_code;
            $student_registration['course_num_ids'] = $course_ids;
            $orderId = $this->save_customer_details_wp($student_registration);
            $payment_id = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1,10);
            $this->update_order_payment_id($orderId, $payment_id);
            wp_send_json_success($payment_id);
		}

		function save_customer_details_wp($booking_data){
			global $wpdb;
			
			unset($booking_data['pass_status']);
			unset($booking_data['student_status']);
			unset($booking_data['comment']);
			unset($booking_data['added_by']);			
			
			$table = "{$wpdb->prefix}courses_bookings";
			
			$wpdb->insert($table,$booking_data);
			return $wpdb->insert_id;
		}
		
		function update_order_payment_id($id, $payment_id){
			global $wpdb;
				
			$table = "{$wpdb->prefix}courses_bookings";
			return $wpdb->update($table,array('payment_id'=>$payment_id), array('id'=>$id));
		}
		
		public function api_header_response($result){
			$headers = [];
			$result = rtrim($result);
			$data = explode("\n",$result);
			$headers['status'] = $data[0];
			array_shift($data);
			foreach($data as $part){

				//some headers will contain ":" character (Location for example), and the part after ":" will be lost, Thanks to @Emanuele
				$middle = explode(":",$part,2);

				//Supress warning message if $middle[1] does not exist, Thanks to @crayons
				if ( !isset($middle[1]) ) { $middle[1] = null; }

				$headers[trim($middle[0])] = trim($middle[1]);
			}
			
			return $headers;
		}
		
		function header_booking_form(){
			$steps_html = '';
			ob_start();
			$data = array('booking'=>$this);
			$this->set_template_data( $data )->get_template_part( "header_booking_form" );
			$steps_html = ob_get_clean();
			return $steps_html;
		}
		  
		
		function booking_steps_form(){
			
			ob_start();
			
			$course_code 	= $this->get_get('code');
			$location_city  = $this->get_get('city');
			
			$landing_active_1 = "active_step";
			$landing_active_2 = "hide_booking_step";
			
			if($course_code){
				$landing_active_1 = "hide_booking_step";
				$landing_active_2 = "active_step";
			}
			
			?>
				
				<div class="booking_wrapper" id="booking_wrapper">
					<!-- booking header start -->
					<?php 
					
					$this->set_template_data(array())->get_template_part( "booking_steps/header" ); ?>
					<!-- end -->
					<div id="booking_steps">
						
							<!-- step one start -->
							<?php 
							$data = array('booking'=>$this,'active_tab'=>$landing_active_1);
							$this->set_template_data($data)->get_template_part( "booking_steps/step_one" ); 
							?>
							
							<!-- End -->
							<!-- step two start -->
							<?php 
							$data = array('booking'=>$this,'active_tab'=>$landing_active_2);
							$this->set_template_data($data)->get_template_part( "booking_steps/step_two" ); 
							?>
							
							<!-- end -->
							<!-- step three start -->
							<?php 
							$data = array('booking'=>$this);
							$this->set_template_data($data)->get_template_part( "booking_steps/step_three" ); 
							?>
							
							<!-- end -->
							<!-- step four start -->
							<?php 
							$data = array('booking'=>$this);
							$this->set_template_data($data)->get_template_part( "booking_steps/step_four" ); 
							?>
							<!-- end -->

					</div>

                    <div class="booking_action clearfix">
						<div class="book_col_6">
							<span id="previous_step" class="previous backbtn">Vorige</span>
						</div>
						<div class="book_col_6">
							<span id='next_step' class="button main_form_btn">Volgende</span><span id='pay_now' class="button main_form_btn">Volgende</span>
						</div>
					</div>
					<div id="booking_completed"><p><?php echo $this->booking_success; ?></p></div>
				</div>
			<?php
			$steps_html = ob_get_clean();
			return $steps_html;
		}
		
		function assets_files_load(){
			
			wp_register_style('booking_styles', untrailingslashit( plugin_dir_url( __FILE__ ) )."/assets/css/style.css", array(),  date("h:i:s"));
			wp_enqueue_style('booking_styles');
			
			// Load the datepicker script (pre-registered in WordPress).
			//wp_enqueue_script( 'jquery-ui-datepicker' );

			wp_register_style('booking_select2_styles', untrailingslashit( plugin_dir_url( __FILE__ ) )."/assets/css/select2.min.css", array(), '');
			wp_enqueue_style('booking_select2_styles');
			
			wp_register_style('fontawesome', untrailingslashit( plugin_dir_url( __FILE__ ) )."/assets/css/font-awesome.min.css", array(), '');
			wp_enqueue_style('fontawesome');

		}
		function code_95_cursusdata(){

            if (!is_page('code-95') && !is_page('code_95_cursusdata')){
                wp_register_script( 'almosoft_select2_bookings', untrailingslashit( plugin_dir_url( __FILE__ ) )."/assets/js/select2.min.js", array('jquery') );
                wp_enqueue_script('almosoft_select2_bookings');

                wp_register_script( 'almosoft_bookings', untrailingslashit( plugin_dir_url( __FILE__ ) )."/assets/js/almosoftbookings.js", array('jquery','almosoft_select2_bookings'), date("h:i:s"),true );
                wp_enqueue_script('almosoft_bookings');
            }


		}

		function object_almosoft(){
			$course_code 	= $this->get_get('code');
			$location_city  = $this->get_get('city');

			$almosoft_data_array = array(
				'plugin_base_url' 		=> untrailingslashit( plugin_dir_url( __FILE__ ) ).'/assets/',
				'location_options'		=> $this->stringconvertto_options($this->locations, array('current_val'=>'','blank_option'=>true, 'blank_option_label'=>'Kies uw locatie')),
				'ajaxurl'				=> admin_url( 'admin-ajax.php' ),
				'price'					=> $this->price,
				'part_price'			=> $this->partial_payment,
				'prices'				=> $this->almosoft_nivo_config,
				'course_code'			=> htmlspecialchars($course_code),
				'location_city'			=> $location_city,
				'bookingurl'			=> site_url().'/vrachtwagen-theorie/',
                'bookingurl_niwo'		=> site_url().'/niwo/wat-is-niwo/',
				'registration_failed'	=> $this->registration_failed,
				'currency'				=> $this->currency,
				'curr_symbol'			=> $this->curr_symbol
			);

			//after wp_enqueue_script
			wp_localize_script( 'almosoft_bookings', 'object_almosoft', $almosoft_data_array );


		}

		protected function get_post( $name ) {
			if ( isset( $_POST[ $name ] ) ) {
				return $_POST[ $name ];
			}
			return null;
		}
		protected function get_get( $name ) {
			if ( isset( $_REQUEST[ $name ] ) ) {
				return $_REQUEST[ $name ];
			}
			return null;
		}
		
		function logPaymentSteps($message){
			ini_set("log_errors", 1);
			ini_set("error_log", "./paymenttracking.log");
			error_log( $message );
		}
		
		public function booking_list(){
			require_once Almosoft_Course_booking_file.'/admin/bookings.php';
			$class= new Bookings_Table();
			$class->prepare_items();

			$message = '';
			if ('delete' === $class->current_action()) {
				$message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Booking deleted: %d', 'almosoft'), count($_REQUEST['id'])) . '</p></div>';
			}
			
			?>
			<style>.wp-heading-inline{display:inline-block;margin-right:20px;padding-right: 20px !important;}#review_id{width:90px;}#comment_text{width:300px;}#post_id{width:50px;}</style>
			<div class="wrap">
			<h2 class="wp-heading-inline">Manage Booking</h2>
			<?php echo $message; ?>
			<form id="cities-table" method="GET">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
			<p class="search-box">
			<label class="screen-reader-text" for="post-search-input">Search:</label>
			<input type="search" id="post-search-input" name="s" value="">
			<input type="submit" id="search-submit" class="button" value="Search customer">
			</p>
			<?php $class->display(); ?>
			</form>
			</div>
			<?php 
		}
		
	}
	
	$bookings = new Almosoft_Courses_Booking();
	new Almosoft_Settings_API_Booking();
}