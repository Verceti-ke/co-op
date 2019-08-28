<?php
namespace Osen\Coop;

class TransactionStatus extends Service
{
    public static function send($MessageReference, $callback = null)
    {
        $url   = 'https://developer.co-opbank.co.ke:' . parent::$port . '/QueryStatus/v1.0.0/query';
        $token = parent::token();

        $requestPayload = array(
            "MessageReference" => $MessageReference,
        );

        $headers = array('Content-Type: application/json', "Authorization: Bearer {$token}");

        $process = curl_init();
        curl_setopt($process, CURLOPT_URL, $url);
        curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($process, CURLOPT_POSTFIELDS, $requestPayload);
        curl_setopt($process, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, true);

        $return = curl_exec($process);

        $response = json_decode($return, true);

        return is_null($callback)
        ? $response
        : \call_user_func_array(array($callback), array($response));
    }
}
