<?php

class Moceansms_WooCoommerce_Notification {
    
    public static function send_sms_woocommerce_order_status_pending($order_id) {
        self::send_customer_notification($order_id, "pending");
    }

    public static function send_sms_woocommerce_order_status_failed($order_id) {
        self::send_customer_notification($order_id, "failed");        
    }

    public static function send_sms_woocommerce_order_status_on_hold($order_id) {
        self::send_customer_notification($order_id, "on-hold");        
    }

    public static function send_sms_woocommerce_order_status_processing($order_id) {
        self::send_customer_notification($order_id, "processing");        
    }

    public static function send_sms_woocommerce_order_status_completed($order_id) {
        self::send_customer_notification($order_id, "completed");        
    }

    public static function send_sms_woocommerce_order_status_refunded($order_id) {
        self::send_customer_notification($order_id, "refunded");        
    }

    public static function send_sms_woocommerce_order_status_cancelled($order_id) {
        self::send_customer_notification($order_id, "cancelled");        
    }
    
    public static function send_sms_woocommerce_order_status_changed($order_id, $old_status, $new_status) {
        $log = new Moceansms_WooCoommerce_Logger();
        $log->add('MoceanSMS', 'Order status changed": old status: '.$old_status.' , new status: '.$new_status);
    }    
    
     public static function woocommerce_payment_complete($order_id) {
        $log = new Moceansms_WooCoommerce_Logger();
        $log->add('MoceanSMS', 'Payment completed');
    }       
    
     public static function woocommerce_payment_complete_order_status($order_id) {
        $log = new Moceansms_WooCoommerce_Logger();
        $log->add('MoceanSMS', 'Completed order status');
    }        

    public static function send_customer_notification($order_id, $status) {
        if( !in_array( $status, self::moceansms_woocommerce_get_option( 'moceansms_woocommerce_send_sms', 'customer_setting', array() ) ) ) return;
        
        $log = new Moceansms_WooCoommerce_Logger();
        
        $order_details = new WC_Order($order_id);

        $message = self::moceansms_woocommerce_get_option('moceansms_woocommerce_sms_template_'.$status, 'customer_setting', '');
        if (empty($message)) {
            $message = self::moceansms_woocommerce_get_option('moceansms_woocommerce_sms_template_default', 'customer_setting', '');
        }
        if (empty($message)) {
            return;
        }
        $message = self::replace_order_keyword($message, $order_details, 'customer', $status);
        
        require_once plugin_dir_path(dirname(__FILE__)). 'lib/autoload.php';   
        
        try{

            $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
            $phoneNumber = $phoneNumberUtil->parse($order_details->get_billing_phone(), $order_details->get_billing_country());
            if($phoneNumberUtil->isValidNumber($phoneNumber) && $phoneNumberUtil->getNumberType($phoneNumber) == 1) {
                $customer_phone_no = $phoneNumberUtil->format($phoneNumber, \libphonenumber\PhoneNumberFormat::E164);
                $customer_phone_no = self::phone_number_processing($customer_phone_no);
                
                $log->add('MoceanSMS', 'Customer\'s billing phone number ('.$order_details->get_billing_phone().') in country ('.$order_details->get_billing_country().') converted to '.$customer_phone_no);

                self::send_sms($customer_phone_no, $message);            
            } else {
                $log->add('MoceanSMS', 'Customer\'s billing phone number ('.$order_details->get_billing_phone().') not a valid mobile number in country ('.$order_details->get_billing_country().'), not sending SMS.');
            }
        }
        catch (Exception $e) {

            $log->add('MoceanSMS', 'Unable to retrieve customer\'s billing phone number ('.$order_details->get_billing_phone().') or country ('.$order_details->get_billing_country().').');
            $log->add('MoceanSMS', 'Error ' . $e->getMessage());

        }
    }
    

    public static function send_admin_notification($order_id) {
        if (self::moceansms_woocommerce_get_option('moceansms_woocommerce_admin_send_sms', 'admin_setting', 'off') == 'off')  return;

        $order_details = new WC_Order($order_id);
        $message = self::moceansms_woocommerce_get_option('moceansms_woocommerce_admin_sms_template', 'admin_setting', '');
        $message = self::replace_order_keyword($message, $order_details, 'admin', '');
        $admin_phone_no = trim(self::moceansms_woocommerce_get_option('moceansms_woocommerce_admin_sms_recipients', 'admin_setting', ''));
        $admin_phone_no = self::phone_number_processing($admin_phone_no);
        $admin_phone_no = str_replace(',', ' ', $admin_phone_no);
        if($admin_phone_no == '' || $message == '') return;
        self::send_sms($admin_phone_no, $message);
    }    

    public static function replace_order_keyword($message, $order_details, $user_type, $order_status) {
        $items = $order_details->get_items();
        $product_name = "";
        foreach ( $items as $item ) {
            $product_name .= ', '.$item['name'];  
        }        
        if($product_name) {
            $product_name = substr($product_name, 2);
        }
        
        $search = array('[shop_name]', '[order_id]', '[order_currency]', '[order_amount]', '[order_status]', '[order_product]', '[billing_first_name]', '[billing_last_name]', '[billing_phone]', '[billing_email]', '[billing_company]', '[billing_address]', '[billing_country]', '[billing_city]', '[billing_state]', '[billing_postcode]', '[payment_method]'); 
        $replace = array(get_bloginfo('name'), $order_details->get_order_number(), $order_details->get_currency(), $order_details->get_total(), ucfirst($order_details->get_status()), $product_name, $order_details->get_billing_first_name(), $order_details->get_billing_last_name(), $order_details->get_billing_phone(), $order_details->get_billing_email(), $order_details->get_billing_company(), $order_details->get_billing_address_1(), $order_details->get_billing_country(), $order_details->get_billing_city(), $order_details->get_billing_state(), $order_details->get_billing_postcode(), $order_details->get_payment_method()); 
        $message = str_replace($search, $replace, $message); 

        $additional_billing_fields_array = self::get_additional_billing_fields();
        foreach ($additional_billing_fields_array as $field) {
            $post_data = get_post_meta( $order_details->get_order_number(), $field, true);
            $message = str_replace('['.$field.']', $post_data, $message); 
        }
        
        $status_for_basc = array('on-hold', 'pending', 'processing');
        if($user_type == 'customer' && in_array($order_status, $status_for_basc) && strpos($message, '[bank_details]') !== false) {
            $bank_message = '';
            $bank_message_template = '[bank_name] - [account_name] (Acc No.: [account_number], Sort code: [sort_code], IBAN: [iban], BIC: [bic])';
            $bank_details = new WC_Gateway_BACS();
            if($order_details->payment_method == 'bacs') {
                foreach($bank_details->account_details as $details) {
                    if($details['bank_name'] != '' && $details['account_name'] != '' && $details['account_number'] != '') {
                        $search = array('[bank_name]', '[account_name]', '[account_number]', '[sort_code]', '[iban]', '[bic]'); 
                        $replace = array($details['bank_name'], $details['account_name'], $details['account_number'], $details['sort_code'], $details['iban'], $details['bic']);
                        $bank_message .= ', '.str_replace($search, $replace, $bank_message_template);  
                    }
                }
                $bank_message = str_replace(' Sort code: ,', '', $bank_message);
                $bank_message = str_replace(' IBAN: ,', '', $bank_message);
                $bank_message = str_replace(', BIC: )', ')', $bank_message);

                if($bank_message) {
                    $bank_message = 'Bank details: '.substr($bank_message, 2);
                }
            } 
            $message = TRIM(str_replace('[bank_details]', $bank_message, $message));
        }
        return $message;
    }

    public static function send_sms($phone_no, $message) {
        require_once plugin_dir_path(dirname(__FILE__)) . 'lib/MoceanSMS.php';
        
        $log = new Moceansms_WooCoommerce_Logger();
        
        $api_key = self::moceansms_woocommerce_get_option("moceansms_woocommerce_api_key", 'moceansms_setting', '');
        $api_secret = self::moceansms_woocommerce_get_option("moceansms_woocommerce_api_secret", 'moceansms_setting', '');
        $sms_from = self::moceansms_woocommerce_get_option("moceansms_woocommerce_sms_from", 'moceansms_setting', '');
        
        if($api_key == '' || $api_key == '') return;
        if($sms_from == '') $sms_from = 'SMS';
        
        $log->add('MoceanSMS', 'Sending SMS to '.$phone_no.', message: '.$message);
        
        try {
            $moceansms_rest = new MoceanSMS($api_key, $api_secret); 
            $rest_response = $moceansms_rest->sendSMS($sms_from, $phone_no, $message);          
            
            $log->add('MoceanSMS', 'SMS response from SMS gateway: ' .$rest_response);
        } catch (Exception $e) {
            $log->add('MoceanSMS', 'Failed sent SMS: ' . $e->getMessage());
        }
    }

    public static function moceansms_woocommerce_get_option($option, $section, $default = '') {

        $options = get_option( $section );

        if ( isset( $options[$option] ) ) {
            return $options[$option];
        }

        return $default;
    }
    
    private static function phone_number_processing($phone_no)
    {
        $updated_phone_no = '';
        if($phone_no != '') {
            $phone_no_array = explode(",", $phone_no);
            foreach($phone_no_array as $number) {
                if($number != '') {
                    $number = preg_replace("/[^0-9,.]/", "", $number);
                    $updated_phone_no .= ','.$number;
                }
            }
            $updated_phone_no = substr($updated_phone_no, 1);
        }
        return $updated_phone_no;
    }
    
    public static function get_additional_billing_fields() {
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