<?php

namespace App\Library;

use Exception;

class ZiniPay
{

   /**
     * Start a new payment request.
     * Sends data to the ZiniPay checkout API.
     *
     * @param array $requestData  Data required to create the payment.
     * @return string             Payment URL.
     * @throws Exception
     */

    public static function init_payment($requestData)
    {
        $apiUrl = "https://api.zinipay.com/api/checkout-v2";
        
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($requestData),
            CURLOPT_HTTPHEADER => [
                "ZINIPAY-API-KEY: " . env("ZINIPAY_API_KEY"),
                "accept: application/json",
                "content-type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            throw new Exception("cURL Error #:" . $err);
        } else {
            $result = json_decode($response, true);
            if (isset($result['status']) && isset($result['payment_url'])) {
                return $result['payment_url'];
            } else {
                throw new Exception($result['message']);
            }
        }
        throw new Exception("Something went wrong. Please check your API response.");
    }

    /**
     * Verify payment
     *
     * Confirm a payment using an invoice ID.
     * Sends the invoice ID to the ZiniPay verification API.
     *
     * @param string $invoice_id  The invoice ID to verify.
     * @return array              Verification result.
     * @throws Exception
     */

    public static function verify_payment($invoice_id)
    {
        $verifyUrl = "https://api.zinipay.com/api/verify-payment";

        $invoice_data = [
            'invoice_id'    => $invoice_id
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $verifyUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($invoice_data),
            CURLOPT_HTTPHEADER => [
                "ZINIPAY_API_KEY: " . env("ZINIPAY_API_KEY"),
                "accept: application/json",
                "content-type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            throw new Exception("cURL request failed #:" . $err);
        } else {
            return json_decode($response, true);
        }
        throw new Exception("Environment configuration is missing or incorrect.");
    }
}
