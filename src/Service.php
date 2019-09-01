<?php
namespace Osen\Coop;

class Service
{
    public static $config;
    public static $host;

    public static function init($configs = array())
    {
        $defaults = array(
            "Env"                 => "sandbox",
            "ConsumerKey"         => "ss0sD2ANhjvhx_rHU0a6Xf8ROdYa",
            "ConsumerSecret"      => "zOfReXCIwn1TfnEYJJJGNP6l3Tka",
            "AccountNumber"       => "54321987654321",
            "BankCode"            => "011",
            "BranchCode"          => "00011001",
            "CallbackURL"         => "/coop/callback",
            "TransactionCurrency" => "KES",
        );

        $parsed       = array_merge($defaults, $configs);
        self::$config = (object) $parsed;
        
        self::$host = ($parsed['Env'] == 'sandbox') 
            ? 'https://developer.co-opbank.co.ke:8243' 
            : 'https://developer.co-opbank.co.ke:8280';
    }

    public static function token()
    {
        $url           = self::$host . '/token';
        $authorization = base64_encode(self::$config->ConsumerKey . ':' . self::$config->ConsumerSecret);
        $header        = array("Authorization: Basic {$authorization}");
        $content       = "grant_type=client_credentials";
        $curl          = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL            => $url,
            CURLOPT_HTTPHEADER     => $header,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $content,
        ));

        $response = curl_exec($curl);
        if ($response === false) {
            return curl_error($curl);
        }

        return json_decode($response)->access_token;
    }

    public static function reconcile($callback = null)
    {
        $input    = file_get_contents('php://input');
        $response = json_decode($input, true);
        $response = !is_array($response) ? array() : $response;

        return is_null($callback)
        ? $response
        : \call_user_func_array(array($callback), array($response));
    }
}
