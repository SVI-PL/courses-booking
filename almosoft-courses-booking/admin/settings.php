 <?php

if ( !class_exists('Almosoft_Settings_API_Booking' ) ):
class Almosoft_Settings_API_Booking{

    private $settings_api;

    function __construct() {
        $this->settings_api = new Almosoft_Settings_API;

        add_action( 'admin_init', [$this, 'admin_init'] );
        add_action( 'admin_menu', [$this, 'admin_menu'] );
    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    function admin_menu() {
		
        add_options_page( 'Booking Setting', 'Booking Setting', 'delete_posts', 'settings_api_booking', [$this, 'plugin_page'] );
    }

    function get_settings_sections() {
        $sections = [
            [
                'id'    => 'almosoft_booking',
                'title' => __( 'Booking Setting', 'almosoft' )
            ],
            [
                'id'    => 'almosoft_niwo',
                'title' => __( 'NIWO Prices', 'almosoft' )
            ],
            [
                'id'    => 'almosoft_price',
                'title' => __( 'Prices', 'almosoft' )
            ],
            [
                'id'    => 'almosoft_payment',
                'title' => __( 'Payment Settings', 'almosoft' )
            ],
			[
                'id'    => 'almosoft_api_setting',
                'title' => __( 'CRM API Settings', 'almosoft' )
            ]
        ];
        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $settings_fields = [
            'almosoft_booking' => [
                [
                    'name'              => 'niwo_courses',
                    'label'             => __( 'NIWO Courses', 'almosoft' ),
                    'desc'              => __( 'Enter course code with comma separated', 'almosoft' ),
                    'placeholder'       => __( 'Link label', 'almosoft' ),
                    'type'              => 'textarea',
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                [
                    'name'              => 'available_courses',
                    'label'             => __( 'Courses', 'almosoft' ),
                    'desc'              => __( 'Enter course code with comma separated', 'almosoft' ),
                    'placeholder'       => __( 'Link label', 'almosoft' ),
                    'type'              => 'textarea',
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                [
                    'name'              => 'referral_options',
                    'label'             => __( 'Hoe heeft u ons gevonden?', 'almosoft' ),
                    'desc'              => __( 'Enter referrale with comma separated', 'almosoft' ),
                    'placeholder'       => __( '', 'almosoft' ),
                    'type'              => 'textarea',
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
				[
                    'name'              => 'locations',
                    'label'             => __( 'Locations', 'almosoft' ),
                    'desc'              => __( 'Please enter each city in new line', 'almosoft' ),
                    'placeholder'       => __( '', 'almosoft' ),
                    'type'              => 'textarea',
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field'
//                ]
			],
				[
                    'name'              => 'booking_success',
                    'label'             => __( 'Registration Success', 'almosoft' ),
                    'desc'              => __( 'Registration Success message', 'almosoft' ),
                    'type'              => 'textarea',
					'placeholder'       => __( '', 'almosoft' ),
                    'default'           => ''                    
                ],
				[
                    'name'              => 'booking_failed',
                    'label'             => __( 'Registration Failed', 'almosoft' ),
                    'desc'              => __( 'Registration Failed message', 'almosoft' ),
                    'type'              => 'textarea',
					'placeholder'       => __( '', 'almosoft' ),
                    'default'           => 'Registration process failed. Please try again.'                    
                ],
				[
                    'name'              => 'booking_pending',
                    'label'             => __( 'Payment Pending', 'almosoft' ),
                    'desc'              => __( 'Payment pending message', 'almosoft' ),
                    'type'              => 'textarea',
					'placeholder'       => __( '', 'almosoft' ),
                    'default'           => 'Payment is not processed. Please try again.'                    
                ],
            ],

            'almosoft_niwo' => [
				[
                    'name'              => 'ONWG-1',
                    'label'             => __( 'ONWG-1', 'almosoft' ),
//                    'desc'              => __( 'Price per course ONWG-1.', 'almosoft' ),
                    'placeholder'       => __( '', 'almosoft' ),
                    'type'              => 'text',
                    'default'           => '',
					'size'				=> '100px',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
				[
                    'name'              => 'ONWG-2',
                    'label'             => __( 'ONWG-2', 'almosoft' ),
//                    'desc'              => __( 'Price per course ONWG-2.', 'almosoft' ),
                    'placeholder'       => __( '', 'almosoft' ),
                    'type'              => 'text',
                    'default'           => '',
					'size'				=> '100px',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
				[
                    'name'              => 'ONPG',
                    'label'             => __( 'ONPG', 'almosoft' ),
//                    'desc'              => __( 'Price per course ONPG.', 'almosoft' ),
                    'placeholder'       => __( '', 'almosoft' ),
                    'type'              => 'text',
                    'default'           => '',
					'size'				=> '100px',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
				[
                    'name'              => 'ONBG',
                    'label'             => __( 'ONBG', 'almosoft' ),
//                    'desc'              => __( 'Price per course ONBG.', 'almosoft' ),
                    'placeholder'       => __( '', 'almosoft' ),
                    'type'              => 'text',
                    'default'           => '',
					'size'				=> '100px',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
				[
                    'name'              => 'ONCG',
                    'label'             => __( 'ONCG', 'almosoft' ),
//                    'desc'              => __( 'Price per course ONCG.', 'almosoft' ),
                    'placeholder'       => __( '', 'almosoft' ),
                    'type'              => 'text',
                    'default'           => '',
					'size'				=> '100px',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
				[
                    'name'              => 'ONFM',
                    'label'             => __( 'ONFM', 'almosoft' ),
//                    'desc'              => __( 'Price per course ONFM.', 'almosoft' ),
                    'placeholder'       => __( '', 'almosoft' ),
                    'type'              => 'text',
                    'default'           => '',
					'size'				=> '100px',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
            ],
            'almosoft_price' => [
				[
                    'name'              => 'RV1',
                    'label'             => __( 'RV1', 'almosoft' ),
                    'placeholder'       => __( '', 'almosoft' ),
                    'type'              => 'text',
                    'default'           => '',
					'size'				=> '100px',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
				[
                    'name'              => 'V2C',
                    'label'             => __( 'V2C', 'almosoft' ),
                    'placeholder'       => __( '', 'almosoft' ),
                    'type'              => 'text',
                    'default'           => '',
					'size'				=> '100px',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
				[
                    'name'              => 'V3C',
                    'label'             => __( 'V3C', 'almosoft' ),
                    'placeholder'       => __( '', 'almosoft' ),
                    'type'              => 'text',
                    'default'           => '',
					'size'				=> '100px',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
            ],
            'almosoft_payment' => [
                [
                    'name'              => 'payment_title',
                    'label'             => __( 'Payment title', 'almosoft' ),
                    'desc'              => __( 'This controls the title which the user sees during checkout.', 'almosoft' ),
                    'placeholder'       => __( '', 'almosoft' ),
                    'type'              => 'text',
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
				[
                    'name'              => 'price',
                    'label'             => __( 'Price', 'almosoft' ),
                    'desc'              => __( 'Price per course.', 'almosoft' ),
                    'placeholder'       => __( '', 'almosoft' ),
                    'type'              => 'text',
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
				[
                    'name'              => 'partial_payment',
                    'label'             => __( 'Partial Payment', 'almosoft' ),
                    'desc'              => __( 'Partial payment.', 'almosoft' ),
                    'placeholder'       => __( '', 'almosoft' ),
                    'type'              => 'text',
                    'default'           => '',
					'size'				=> '100px',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                [
                    'name'              => 'description',
                    'label'             => __( 'description', 'almosoft' ),
                    'desc'              => __( 'This controls the description which the user sees during checkout.', 'almosoft' ),
                    'placeholder'       => __( 'Pay with your credit card', 'almosoft' ),
                    'type'              => 'textarea',
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                [
                    'name'    => 'testmode',
                    'label'   => __( 'Enable Test Mode', 'almosoft' ),
                    'desc'    => __( 'Place the payment gateway in test mode using test API keys.', 'almosoft' ),
                    'type'    => 'checkbox',
                    'default' => ''
                ],
				[
                    'name'              => 'mollie_test_key',
                    'label'             => __( 'Test Key', 'almosoft' ),
                    'desc'              => __( '', 'almosoft' ),
                    'placeholder'       => __( '', 'almosoft' ),
                    'type'              => 'text',
                    'default'           => ''
                ],
				[
                    'name'              => 'mollie_live_key',
                    'label'             => __( 'Live Key', 'almosoft' ),
                    'type'              => 'text',
                    'default'           => ''
                    
                ],
				[
                    'name'              => 'paypal_key',
                    'label'             => __( 'Paypal Key', 'almosoft' ),
                    'type'              => 'text',
                    'default'           => ''

                ],
				[
                    'name'              => 'success_page',
                    'label'             => __( 'Success/thank you page', 'almosoft' ),
                    'type'              => 'select',
					'options'			=>$this->get_pages(),
                    'default' => 'no',
                ]
            ],
			'almosoft_api_setting' => [
                [
                    'name'              => 'username',
                    'label'             => __( 'Username', 'almosoft' ),
                    'desc'              => __( 'CRM API username', 'almosoft' ),
                    'placeholder'       => __( '', 'almosoft' ),
                    'type'              => 'text',
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
				[
                    'name'              => 'password',
                    'label'             => __( 'Password', 'almosoft' ),
                    'desc'              => __( 'CRM API password', 'almosoft' ),
                    'placeholder'       => __( '', 'almosoft' ),
                    'type'              => 'password',
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
			]
        ];
        return $settings_fields;
    }

    function plugin_page() {
        echo '<div class="wrap">';

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
    }
	
	public function get_pages($title = false, $indent = true){
		$wp_pages = get_pages('sort_column=menu_order');
		$page_list = array();

		if ($title)
			$page_list[] = $title;

		foreach ($wp_pages as $page) {
			$prefix = '';
			// show indented child pages?
			if ($indent) {
				$has_parent = $page->post_parent;
				while ($has_parent) {
					$prefix .= ' - ';
					$next_page = get_page($has_parent);
					$has_parent = $next_page->post_parent;
				}
			}
			// add to page list array array
			$page_list[$page->ID] = $prefix . $page->post_title;
		}
		return $page_list;
	}

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
   /** function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }*/
}
endif;