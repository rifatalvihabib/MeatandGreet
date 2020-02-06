<?php

class Moceansms_WooCoommerce_Setting {

    private $settings_api;

    function __construct() {
        $this->settings_api = new WeDevs_Settings_API;

        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'admin_menu') );
    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    function admin_menu() {
        add_options_page( 'MoceanSMS WooCommerce', 'MoceanSMS WooCommerce', 'manage_options', 'moceansms-woocoommerce-setting', array($this, 'plugin_page') );
    }

    function get_settings_sections() {
        $sections = array(
            array(
                'id' => 'moceansms_setting',
                'title' => __( 'MoceanSMS Settings', 'moceansms-woocoommerce' )
            ),
            array(
                'id' => 'admin_setting',
                'title' => __( 'Admin Settings', 'moceansms-woocoommerce' )
            ),
            array(
                'id' => 'customer_setting',
                'title' => __( 'Customer Settings', 'moceansms-woocoommerce' )
            )
        );
        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $additional_billing_fields = '';
        $additional_billing_fields_desc = '';
        $additional_billing_fields_array = $this->get_additional_billing_fields();
        foreach ($additional_billing_fields_array as $field) {
            $additional_billing_fields .= ', ['.$field.']';
        }
        if($additional_billing_fields) {
            $additional_billing_fields_desc = '<br />Custom tags: '.substr($additional_billing_fields, 2);
        }
        $settings_fields = array(
            'moceansms_setting' => array(    
                array(
                    'name'              => 'moceansms_woocommerce_api_key',
                    'label'             => __( 'API Key', 'moceansms-woocoommerce' ),
                    'desc'              => __( 'Your MoceanSMS account API key. Account can be registered <a href="https://dev.moceansms.com/register?fr=wp" target="blank">here</a>', 'moceansms-woocoommerce' ),
                    'type'              => 'text',
                ),
                array(
                    'name'              => 'moceansms_woocommerce_api_secret',
                    'label'             => __( 'API Secret', 'moceansms-woocoommerce' ),
                    'desc'              => __( 'Your MoceanSMS account API secret', 'moceansms-woocoommerce' ),
                    'type'              => 'text',
                ),
                array(
                    'name'              => 'moceansms_woocommerce_sms_from',
                    'label'             => __( 'Message From', 'moceansms-woocoommerce' ),
                    'desc'              => __( 'Sender of the SMS when a message is received at a mobile phone', 'moceansms-woocoommerce' ),
                    'type'              => 'text',
                ),
                array(
                    'name'              => 'moceansms_woocommerce_log_folder',
                    'label'             => __( '', 'moceansms-woocoommerce' ),
                    'desc'              => __( '** All SMS transaction will be logged into "uploads" directory. Check this if you are having issues sending SMS.', 'moceansms-woocoommerce' ),
                    'type'              => 'html',
                ),                
            ),
            'admin_setting' => array(    
                array(
                    'name' => 'moceansms_woocommerce_admin_send_sms',
                    'label' => __( 'Enable Admin SMS Notifications', 'moceansms-woocoommerce' ),
                    'desc' => ' '.__( 'If checked then enable your sms notification for new order', 'moceansms-woocoommerce' ),
                    'type' => 'checkbox',
                    'default' => 'on',
                ),     
                array(
                    'name'              => 'moceansms_woocommerce_admin_sms_recipients',
                    'label'             => __( 'Mobile Number', 'moceansms-woocoommerce' ),
                    'desc'              => __( 'Mobile number to receive new order SMS notification. To send to multiple receivers, separate each entry with comma and mobile number must include country code, e.g. 60123456789, 6545214889', 'moceansms-woocoommerce' ),
                    'type'              => 'text',
                ),
                array(
                    'name' => 'moceansms_woocommerce_admin_sms_template',
                    'label' => __( 'Admin SMS Message', 'moceansms-woocoommerce' ),
                    'desc' => __( 'Customize your SMS with these tags: [shop_name], [order_id], [order_currency], [order_amount], [order_status], [order_product], [payment_method], [billing_first_name], [billing_last_name], [billing_phone], [billing_email], [billing_company], [billing_address], [billing_country], [billing_city], [billing_state], [billing_postcode].'.$additional_billing_fields_desc, 'moceansms-woocoommerce' ),
                    'type' => 'textarea',
                    'rows' => '8',
                    'cols' => '500',
                    'css' => 'min-width:350px;',
                    'default' => __('[shop_name] : You have a new order with order ID [order_id] and order amount [order_currency] [order_amount]. The order is now [order_status].', 'moceansms-woocoommerce')
                )  
            ),
            'customer_setting' => array(      
                array(
                    'name'    => 'moceansms_woocommerce_send_sms',
                    'label'   => __( 'Send notification on', 'moceansms-woocoommerce' ),
                    'desc'    => __( 'Choose when to send a status notification message to your customer', 'moceansms-woocoommerce' ),
                    'type'    => 'multicheck',
                    'options' => array(
                        'pending'   => ' Pending',
                        'on-hold'   => ' On-hold',
                        'processing' => ' Processing',
                        'completed'  => ' Completed',
                        'cancelled'  => ' Cancelled',
                        'refunded'  => ' Refunded',
                        'failed'  => ' Failed'
                    )
                ),                
                array(
                    'name' => 'moceansms_woocommerce_sms_template_default',
                    'label' => __( 'Default Customer SMS Message', 'moceansms-woocoommerce' ),
                    'desc' => __( 'Customize your SMS with these tags: [shop_name], [order_id], [order_currency], [order_amount], [order_status], [order_product], [payment_method], [bank_details], [billing_first_name], [billing_last_name], [billing_phone], [billing_email], [billing_company], [billing_address], [billing_country], [billing_city], [billing_state], [billing_postcode].'.$additional_billing_fields_desc.'<br />Bank details will only be included when BACS/bank transfer option is chosen.', 'moceansms-woocoommerce' ),
                    'type' => 'textarea',
                    'rows' => '8',
                    'cols' => '500',
                    'css' => 'min-width:350px;',
                    'default' => __('[shop_name] : Thank you for purchasing. Your order ([order_id]) is now [order_status].', 'moceansms-woocoommerce')
                ),    
                array(
                    'name' => 'moceansms_woocommerce_sms_template_pending',
                    'label' => __( 'Pending SMS Message', 'moceansms-woocoommerce' ),
                    'desc' => __( 'Customize your SMS with these tags: [shop_name], [order_id], [order_currency], [order_amount], [order_status], [order_product], [payment_method], [bank_details], [billing_first_name], [billing_last_name], [billing_phone], [billing_email], [billing_company], [billing_address], [billing_country], [billing_city], [billing_state], [billing_postcode].'.$additional_billing_fields_desc.'<br />Bank details will only be included when BACS/bank transfer option is chosen.', 'moceansms-woocoommerce' ),
                    'type' => 'textarea',
                    'rows' => '8',
                    'cols' => '500',
                    'css' => 'min-width:350px;',                    
                    'default' => __('[shop_name] : Thank you for purchasing. Your order ([order_id]) is now [order_status].', 'moceansms-woocoommerce')
                ),           
                array(
                    'name' => 'moceansms_woocommerce_sms_template_on-hold',
                    'label' => __( 'On-hold SMS Message', 'moceansms-woocoommerce' ),
                    'desc' => __( 'Customize your SMS with these tags: [shop_name], [order_id], [order_currency], [order_amount], [order_status], [order_product], [payment_method], [bank_details], [billing_first_name], [billing_last_name], [billing_phone], [billing_email], [billing_company], [billing_address], [billing_country], [billing_city], [billing_state], [billing_postcode].'.$additional_billing_fields_desc.'<br />Bank details will only be included when BACS/bank transfer option is chosen.', 'moceansms-woocoommerce' ),
                    'type' => 'textarea',
                    'rows' => '8',
                    'cols' => '500',
                    'css' => 'min-width:350px;',                    
                    'default' => __('[shop_name] : Thank you for purchasing. Your order ([order_id]) is now [order_status].', 'moceansms-woocoommerce')
                ),  
                array(
                    'name' => 'moceansms_woocommerce_sms_template_processing',
                    'label' => __( 'Processing SMS Message', 'moceansms-woocoommerce' ),
                    'desc' => __( 'Customize your SMS with these tags: [shop_name], [order_id], [order_currency], [order_amount], [order_status], [order_product], [payment_method], [bank_details], [billing_first_name], [billing_last_name], [billing_phone], [billing_email], [billing_company], [billing_address], [billing_country], [billing_city], [billing_state], [billing_postcode].'.$additional_billing_fields_desc.'<br />Bank details will only be included when BACS/bank transfer option is chosen.', 'moceansms-woocoommerce' ),
                    'type' => 'textarea',
                    'rows' => '8',
                    'cols' => '500',
                    'css' => 'min-width:350px;',                    
                    'default' => __('[shop_name] : Thank you for purchasing. Your order ([order_id]) is now [order_status].', 'moceansms-woocoommerce')
                ),  
                array(
                    'name' => 'moceansms_woocommerce_sms_template_completed',
                    'label' => __( 'Completed SMS Message', 'moceansms-woocoommerce' ),
                    'desc' => __( 'Customize your SMS with these tags: [shop_name], [order_id], [order_currency], [order_amount], [order_status], [order_product], [payment_method], [billing_first_name], [billing_last_name], [billing_phone], [billing_email], [billing_company], [billing_address], [billing_country], [billing_city], [billing_state], [billing_postcode].'.$additional_billing_fields_desc, 'moceansms-woocoommerce' ),
                    'type' => 'textarea',
                    'rows' => '8',
                    'cols' => '500',
                    'css' => 'min-width:350px;',                    
                    'default' => __('[shop_name] : Thank you for purchasing. Your order ([order_id]) is now [order_status].', 'moceansms-woocoommerce')
                ),  
                array(
                    'name' => 'moceansms_woocommerce_sms_template_cancelled',
                    'label' => __( 'Cancelled SMS Message', 'moceansms-woocoommerce' ),
                    'desc' => __( 'Customize your SMS with these tags: [shop_name], [order_id], [order_currency], [order_amount], [order_status], [order_product], [payment_method], [billing_first_name], [billing_last_name], [billing_phone], [billing_email], [billing_company], [billing_address], [billing_country], [billing_city], [billing_state], [billing_postcode].'.$additional_billing_fields_desc, 'moceansms-woocoommerce' ),
                    'type' => 'textarea',
                    'rows' => '8',
                    'cols' => '500',
                    'css' => 'min-width:350px;',                    
                    'default' => __('[shop_name] : Thank you for purchasing. Your order ([order_id]) is now [order_status].', 'moceansms-woocoommerce')
                ),  
                array(
                    'name' => 'moceansms_woocommerce_sms_template_refunded',
                    'label' => __( 'Refunded SMS Message', 'moceansms-woocoommerce' ),
                    'desc' => __( 'Customize your SMS with these tags: [shop_name], [order_id], [order_currency], [order_amount], [order_status], [order_product], [payment_method], [billing_first_name], [billing_last_name], [billing_phone], [billing_email], [billing_company], [billing_address], [billing_country], [billing_city], [billing_state], [billing_postcode].'.$additional_billing_fields_desc, 'moceansms-woocoommerce' ),
                    'type' => 'textarea',
                    'rows' => '8',
                    'cols' => '500',
                    'css' => 'min-width:350px;',                    
                    'default' => __('[shop_name] : Thank you for purchasing. Your order ([order_id]) is now [order_status].', 'moceansms-woocoommerce')
                ),  
                array(
                    'name' => 'moceansms_woocommerce_sms_template_failed',
                    'label' => __( 'Failed SMS Message', 'moceansms-woocoommerce' ),
                    'desc' => __( 'Customize your SMS with these tags: [shop_name], [order_id], [order_currency], [order_amount], [order_status], [order_product], [payment_method], [billing_first_name], [billing_last_name], [billing_phone], [billing_email], [billing_company], [billing_address], [billing_country], [billing_city], [billing_state], [billing_postcode].'.$additional_billing_fields_desc, 'moceansms-woocoommerce' ),
                    'type' => 'textarea',
                    'rows' => '8',
                    'cols' => '500',
                    'css' => 'min-width:350px;',                    
                    'default' => __('[shop_name] : Thank you for purchasing. Your order ([order_id]) is now [order_status].', 'moceansms-woocoommerce')
                )
            )
        );                
        return $settings_fields;
    }

    function plugin_page() {
        echo '<div class="wrap">';

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }
    
    function get_additional_billing_fields() {
        $default_billing_fields = array(
            'billing_first_name', 'billing_last_name', 'billing_company', 'billing_address_1', 'billing_address_2', 'billing_city', 'billing_state', 
            'billing_country', 'billing_postcode', 'billing_phone', 'billing_email'
        );
        $additional_billing_field = array();
        $billing_fields = array_filter(get_option('wc_fields_billing', array()));
        foreach($billing_fields as $field_key => $field_info) {
            if(!in_array($field_key, $default_billing_fields) && $field_info['enabled']) {
                array_push($additional_billing_field, $field_key);
            }
        }
        return $additional_billing_field;
    }    
}

?>
