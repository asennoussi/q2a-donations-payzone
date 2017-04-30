<?php
class qa_donations_payzone_page {
    const CURRENCY = 'MAD';
    const ORDER_ID = 1234;
    const PAYMENT_TYPE = 'CreditCard';
    const SHIPPING_TYPE = 'Virtual';
    const PAYMENT_MODE = 'Single';
    const MERCHANT_EMAIL = 'aymane.sennoussi@gmail.com';
    const CONNECT_GATEWAY = 'https://paiement.payzone.ma';


    function match_request($request)
    {
        $parts=explode('/', $request);
        return $parts[0]=='donation' && isset($parts[1]) && intval($parts[1])%100==0 && intval($parts[1])>0 ;
    }

    function process_request($request)
    {
        $parts=explode('/', $request);
        $parts[1] = $amount = intval($parts[1]);
        if (qa_clicked('ok') && qa_is_http_post()) {
            require_once QA_INCLUDE_DIR.'qa-db-metas.php';
            require_once 'Connect2PayClient.php';

            $connect2pay = 'https://paiement.payzone.ma';
            $merchant = qa_opt('payzone_merchant_id');
            $password = qa_opt('payzone_merchant_password');

            /*
             * Initialize the payment using the class Connect2PayClient.php
             * */


            $c2pClient = new Connect2PayClient(self::CONNECT_GATEWAY, $merchant, $password);
            $first_name = qa_post_text('first_name');
            $last_name = qa_post_text('last_name_name');
            $phone = qa_post_text('phone_number');
            $userId = qa_get_logged_in_userid()?qa_get_logged_in_userid():qa_cookie_get();

            $c2pClient->setOrderID(self::ORDER_ID);
            $c2pClient->setCurrency(self::CURRENCY);
            $c2pClient->setAmount($amount*100);
            $c2pClient->setPaymentType(self::PAYMENT_TYPE);
            $c2pClient->setShippingType(self::SHIPPING_TYPE);
            $c2pClient->setPaymentMode(self::PAYMENT_MODE);
            $c2pClient->setMerchantNotificationTo(self::MERCHANT_EMAIL);
            $c2pClient->setShopperFirstName($first_name);
            $c2pClient->setShopperLastName($last_name);
            $c2pClient->setShopperPhone($phone);
            $c2pClient->setCtrlRedirectURL(qa_opt('site_url').'thank-you');
            $c2pClient->setCtrlCallbackURL(qa_opt('site_url').'process-donation');

            if ($c2pClient->validate()) {
                $c2pClient->prepareTransaction();
                // Create the payment transaction on the payment pa
                // We can save in session the token info returned by the payment page (could
                // be used later when the customer will return from the payment page)
            $_SESSION['merchantToken'] = $code = $c2pClient->getMerchantToken();
                qa_db_query_sub(
                    'INSERT INTO ^donators (userid, date, amount,phone, code,status)'.
                    'VALUES ($,NOW(), $, $, $, 0)',
                    $userId, $amount, $phone, $code
                );

            // If setup is correct redirect the customer to the payment page.
            header('Location: ' . $c2pClient->getCustomerRedirectURL().'?lang=fr');
        }
            else {
            echo "error validate: ";
            echo $c2pClient->getClientErrorMessage() . "\n";
        }

        }


        $qa_content=qa_content_prepare();
        $qa_content['title']=str_replace('^1',qa_opt('site_title'),qa_lang_html('plugin_donations_payzone/donation_title'));
        $qa_content['custom'] =str_replace(['^1','^2'],['<b>'.qa_opt('site_title').'</b>','<b>'.$parts[1].'</b>'],qa_lang_html('plugin_donations_payzone/donation_body'));
        $qa_content['form']=array(
            'tags' => 'method="post" action="'.qa_self_html().'"',

            'style' => 'tall',

            'title' => qa_lang_html('plugin_donations_payzone/tell_us_about_you'),

            'fields' => array(
                'first_name' => array(
                    'label' => qa_lang_html('plugin_donations_payzone/first_name'),
                    'tags' => 'name="first_name"',
                    'value' => '',
                    # 'error' => qa_html('Another error'),
                ),
                'last_name' => array(
                    'label' => qa_lang_html('plugin_donations_payzone/last_name'),
                    'tags' => 'name="last_name"',
                    'value' => '',
                ),

                'phone_number' => array(
                    'label' => qa_lang_html('plugin_donations_payzone/phone_number'),
                    'tags' => 'name="phone_number"',
                    'value' => '',
                ),

            ),

            'buttons' => array(
                'ok' => array(
                    'tags' => 'name="ok" type="submit" ',
                    'label' => qa_lang_html('plugin_donations_payzone/proceed'),
                    'value' => '1',
                ),
            ),

        );
        $qa_content['custom_2'] =  'سوف تتجهون لموقع Payzone.ma حيث يمكنكم إكمال إجراءات الدعم بكل أمان ببطاقتكم البنكية الوطنية.';
        return $qa_content;
    }
}