<?php

namespace Imamuseum\Harvester2\Clients;

use Illuminate\Support\Facades\Cache;
use Exception;

/**
 * Proficio Source provides an interface for accessing a proficio database
 */
class PictionClient
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }


    /**
     * Send curl request to Piction
     * @param $url Url minus piction.api.endpoint/
     * @author Daniel Keller
     */
    public function request($params, $type = 'query')
    {
        $surl = $this->getSurl();
        $endpoint = $this->config['endpoint'];

        switch ($type) {
            case 'query':
                $type = 'IQ';
                break;

            case 'image':
                $type = '';
                $endpoint = $this->config['img_endpoint'];
                break;

            case 'collection':
                $type = 'CONTACT_COLLECTIONS';
                break;

            default:
                new Exception("No request type provided when trying to call Piction: $url");
                break;
        }

        $url = $this->generateUrlParams($params);
        $full_url = $endpoint.$type."/SURL/".$surl."/".$url;

        // Piction API doesn't always return valid JSON
        // So we requist xml and convert it to JSON
        $json = $this->curlCall($full_url);
        $json = $this->fixJSON($json);
        $response = json_decode($json);

        if (is_null($response)) {
            throw new Exception("Invalid response when querying Piction source - index:".$this->config['index'].", request: $full_url, response: $json");
        }

        return $response;

    }


    /**
     * Takes $key => $value request parameters and generates url string
     * @param $params   request parameters
     * @author Daniel Keller
     */
    private function generateUrlParams($params)
    {
        $url = '';

        // Use JSON format
        $params['JSON'] = 'TRUE';

        // Append to url
        foreach ($params as $key => $param) {
            $url .= "$key/".rawurlencode($param)."/";
        }
        return $url;
    }

    /**
     * Sometimes Piction returns bad json strings
     * @param $json   json to fix
     * @author Daniel Keller
     */
    private function fixJSON($json)
    {
        $json = preg_replace('/[\r\n]/', '', $json);
        return str_replace('"r":[,', '"r":[', $json);
    }


    /**
     * Return SURL key if set else request new SURL key from Piction
     * @author Daniel Keller
     */
    private function getSurl()
    {
        // Check cache
        if (Cache::has('surl')) {
            return Cache::get('surl');
        }

        // Request new SURL key
        $endpoint = $this->config['endpoint'];
        $user = $this->config['username'];
        $pswd = $this->config['password'];

        $response = $this->curlCall("$endpoint/piction_login/USERNAME/$user/PASSWORD/$pswd/JSON/TRUE");
        $response = json_decode($response);

        if (isset($response->SURL)) {
            Cache::put('surl', $response->SURL, 600);
            return $response->SURL;
        }

        if (isset($response->m)) {
            throw new Exception($response->m);
        }

        throw new Exception("Failed to get SURL from Piction. No error message returned.");
    }

    /**
     * Make curl request for url
     * @param $url   url to request
     * @author Daniel Keller
     */
    private function curlCall($url)
    {
        $request = curl_init($url);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($request);
        curl_close($request);

        return $response;
    }
}
