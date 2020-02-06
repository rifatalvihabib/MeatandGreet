<?php

/*
Plugin Name: MoceanSMS WooCommerce
Plugin URI:  https://dev.moceansms.com
Description: MoceanSMS Order SMS Notification for WooCommerce
Version:     1.0.7
Author:      Micro Ocean Technologies
Author URI:  https://dev.moceansms.com
License:     GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Domain Path: /languages
Text Domain: moceansms-woocommerce
*/


if ( ! defined( 'WPINC' ) ) {
    die;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-moceansms-woocommerce-hook.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-moceansms-woocommerce-logger.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-moceansms-woocommerce-notification.php';
require_once plugin_dir_path( __FILE__ ) . 'admin/class-moceansms-woocommerce-setting.php';
require_once plugin_dir_path( __FILE__ ) . 'lib/class.settings-api.php';


$hook_actions = array();
$hook_actions[] = array('hook' => 'woocommerce_order_status_pending', 'function_to_be_called' => 'Moceansms_WooCoommerce_Notification::send_sms_woocommerce_order_status_pending', 'priority' => 10, 'accepted_args' => 1);
$hook_actions[] = array('hook' => 'woocommerce_order_status_failed', 'function_to_be_called' => 'Moceansms_WooCoommerce_Notification::send_sms_woocommerce_order_status_failed', 'priority' => 10, 'accepted_args' => 1);
$hook_actions[] = array('hook' => 'woocommerce_order_status_on-hold', 'function_to_be_called' => 'Moceansms_WooCoommerce_Notification::send_sms_woocommerce_order_status_on_hold', 'priority' => 10, 'accepted_args' => 1);
$hook_actions[] = array('hook' => 'woocommerce_order_status_processing', 'function_to_be_called' => 'Moceansms_WooCoommerce_Notification::send_sms_woocommerce_order_status_processing', 'priority' => 10, 'accepted_args' => 1);
$hook_actions[] = array('hook' => 'woocommerce_order_status_completed', 'function_to_be_called' => 'Moceansms_WooCoommerce_Notification::send_sms_woocommerce_order_status_completed', 'priority' => 10, 'accepted_args' => 1);
$hook_actions[] = array('hook' => 'woocommerce_order_status_refunded', 'function_to_be_called' => 'Moceansms_WooCoommerce_Notification::send_sms_woocommerce_order_status_refunded', 'priority' => 10, 'accepted_args' => 1);
$hook_actions[] = array('hook' => 'woocommerce_order_status_cancelled', 'function_to_be_called' => 'Moceansms_WooCoommerce_Notification::send_sms_woocommerce_order_status_cancelled', 'priority' => 10, 'accepted_args' => 1);
$hook_actions[] = array('hook' => 'woocommerce_order_status_pending_to_on-hold', 'function_to_be_called' => 'Moceansms_WooCoommerce_Notification::send_admin_notification', 'priority' => 10, 'accepted_args' => 1);
$hook_actions[] = array('hook' => 'woocommerce_order_status_pending_to_processing', 'function_to_be_called' => 'Moceansms_WooCoommerce_Notification::send_admin_notification', 'priority' => 10, 'accepted_args' => 1);
$hook_actions[] = array('hook' => 'woocommerce_order_status_pending_to_completed', 'function_to_be_called' => 'Moceansms_WooCoommerce_Notification::send_admin_notification', 'priority' => 10, 'accepted_args' => 1);
$hook_actions[] = array('hook' => 'woocommerce_order_status_pending_to_failed', 'function_to_be_called' => 'Moceansms_WooCoommerce_Notification::send_admin_notification', 'priority' => 10, 'accepted_args' => 1);
$hook_actions[] = array('hook' => 'woocommerce_order_status_pending_to_cancelled', 'function_to_be_called' => 'Moceansms_WooCoommerce_Notification::send_admin_notification', 'priority' => 10, 'accepted_args' => 1);
$hook_actions[] = array('hook' => 'woocommerce_order_status_failed_to_on-hold', 'function_to_be_called' => 'Moceansms_WooCoommerce_Notification::send_admin_notification', 'priority' => 10, 'accepted_args' => 1);
$hook_actions[] = array('hook' => 'woocommerce_order_status_failed_to_processing', 'function_to_be_called' => 'Moceansms_WooCoommerce_Notification::send_admin_notification', 'priority' => 10, 'accepted_args' => 1);
$hook_actions[] = array('hook' => 'woocommerce_order_status_failed_to_completed', 'function_to_be_called' => 'Moceansms_WooCoommerce_Notification::send_admin_notification', 'priority' => 10, 'accepted_args' => 1);


$hook = new Moceansms_WooCoommerce_Hook();
$hook->add_action($hook_actions);

new Moceansms_WooCoommerce_Setting();

// Loads frontend scripts and styles
add_action( 'admin_enqueue_scripts', 'admin_enqueue_scripts');

function admin_enqueue_scripts() {
    wp_enqueue_script( 'admin-moceansms-scripts', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), false, true );
}

?>