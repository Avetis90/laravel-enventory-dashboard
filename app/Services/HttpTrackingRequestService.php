<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class HttpTrackingRequestService
{
    const STATUS_SUCCESS_CODE = 200;

    private static function send($type, $url, $options = [], $sysOptions = [])
    {
        $client = new Client($options);
        $options = array_merge($options, $sysOptions);
        try {
            return $response = $client->request($type, $url, $options);
        } catch (RequestException $e) {
            //        TODO make exceptions handler
        } catch (ClientException $e) {
            //        TODO make exceptions handler
        }
    }

    public static function post($url, $headers, $payload = [], $isMultipart = false, $sysOptions = [])
    {
        $options = [
            'headers' => $headers,
        ];
        if ($isMultipart) {
            $options['multipart'] = $payload;
        } else {
            $options['form_params'] = $payload;
        }
        return HttpTrackingRequestService::send('POST', $url, $options, $sysOptions);
    }

    public static function get($url, $headers, $query, $sysOptions = [])
    {
        $options = [
            'headers' => $headers,
            'query' => $query
        ];
        return HttpTrackingRequestService::send('GET', $url, $options, $sysOptions);
    }
}