<?php
/**
 * Plugin Name: Elmosoft courses booking system
 * Description: Elmosoft courses booking plugin support customer to find avaiable dates based on course ID and location
 * Version: 1.0.0
 * Author: ElmoSoft LLC
 * Text Domain: almost_courses_booking
 * Domain Path: /languages/
 * 
 */
if ( ! defined( 'ABSPATH' ) ) exit;
   
define( "Almosoft_Course_booking_file", untrailingslashit( plugin_dir_path(__FILE__)) );
define( "Almosoft_Course_booking_file_assets", untrailingslashit( plugin_dir_url( __FILE__ ) ).'/assets' );

register_activation_hook(__FILE__, 'install');

function install()
{
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    global $wpdb;
    $sql = "CREATE TABLE {$wpdb->prefix}courses_bookings (
        id INT AUTO_INCREMENT,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) DEFAULT NULL,
        phone VARCHAR(20) DEFAULT NULL,
		email VARCHAR(50) DEFAULT NULL,
		address VARCHAR(200) DEFAULT NULL,
		postcode VARCHAR(20) DEFAULT NULL,
		residence VARCHAR(100) DEFAULT NULL,
		date_of_birth date DEFAULT NULL,
		deposit VARCHAR(10) DEFAULT NULL,
		payment_id VARCHAR(100) DEFAULT NULL,
		payment_status VARCHAR(30) DEFAULT NULL,
		course_ids VARCHAR(500) DEFAULT NULL,
		course_num_ids VARCHAR(50) DEFAULT NULL,
		crm_status VARCHAR(10) DEFAULT NULL,
		referral VARCHAR(50) DEFAULT NULL,
		added_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
     )  ENGINE=INNODB; 
	 ALTER TABLE {$wpdb->prefix}courses_bookings ADD course_ids VARCHAR(50) DEFAULT NULL  AFTER payment_id;
	 ";

    dbDelta($sql);
}

include_once('main.php');