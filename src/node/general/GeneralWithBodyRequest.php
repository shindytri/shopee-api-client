<?php


namespace Haistar\ShopeePhpSdk\node\general;


use Haistar\ShopeePhpSdk\client\ShopeeApiConfig;
use Exception;
use GuzzleHttp\Client;
use Haistar\ShopeePhpSdk\client\SignGenerator;

class GeneralWithBodyRequest
{
    /**
     * @param $httpMethod
     * @param $apiPath
     * @param $params
     * @param $body
     * @param ShopeeApiConfig $apiConfig
     */
    public static function makeMethod($httpMethod, $baseUrl, $apiPath, $params, $body, ShopeeApiConfig $apiConfig){
        // Validate Input
        /** @var ShopeeApiConfig $apiConfig */
        if ($apiConfig->getPartnerId() == "") throw new Exception("Input of [partner_id] is empty");
        if ($apiConfig->getSecretKey() == "") throw new Exception("Input of [secret_key] is empty");

        //Timestamp
        $timeStamp = time();
        // Concatenate Base String
        $baseString = $apiConfig->getPartnerId()."".$apiPath."".$timeStamp;
        $signedKey = SignGenerator::generateSign($baseString, $apiConfig->getSecretKey());

        $apiPath .= "?";

        if ($params != null){
            foreach ($params as $key => $value){
                $apiPath .= "&". $key . "=" . urlencode($value);
            }
        }

        $requestUrl = $baseUrl.$apiPath."&"."partner_id=".urlencode($apiConfig->getPartnerId())."&"."timestamp=".urlencode($timeStamp)."&"."sign=".urlencode($signedKey);

        $guzzleClient = new Client([
            'base_uri' => $baseUrl,
            'timeout' => 3.0
        ]);

        return json_decode($guzzleClient->request('POST', $requestUrl, ['json' => $body])->getBody()->getContents());
    }
}