<?php
class qa_donations_payzone_process
{

    const CONNECT_GATEWAY = 'https://paiement.payzone.ma';

    function match_request($request)
    {
        $parts = explode('/', $request);
        return $parts[0] == 'process-donation';
    }

    function process_request($request)
    {
        require_once 'Connect2PayClient.php';
        $connect2pay = 'https://paiement.payzone.ma';
        $merchant = qa_opt('payzone_merchant_id');
        $password = qa_opt('payzone_merchant_password');
        /*
         * Initialize the payment using the class Connect2PayClient.php
         * */
        $c2pClient = new Connect2PayClient($connect2pay, $merchant, $password);
        if ($c2pClient->handleCallbackStatus()) {
            // Get the Error code
            $status = $c2pClient->getStatus();

            // The payment status code
            $errorCode = $status->getErrorCode();
            // Custom data that could have been provided in ctrlCustomData when creating
            // the payment
            $merchantData = $status->getCtrlCustomData();
            // The transaction ID generated for this payment
            $transactionId = $status->getTransactionID();
            // The unique token, known only by the payment page and the merchant
            $merchantToken = $status->getMerchantToken();
            $selectspec=array(
                'columns' => array('code'),
                'source' => '^donators WHERE code=$',
                'arguments' => array($merchantToken),
            );
            $existDonation = qa_db_single_select($selectspec);
            if (!empty($existDonation)){
                if ($errorCode == '000') {
                    qa_db_query_sub(
                        'UPDATE ^donators SET status=1 WHERE code=$',
                         $merchantToken
                    );
                    // If we reach this part of the code the payment succeeded
                    // Do the required stuff to validate the payment in the application

                }
            }
        }
    }
}