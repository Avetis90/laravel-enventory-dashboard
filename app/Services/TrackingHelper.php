<?php

namespace App\Services;

class TrackingHelper
{
    const REFERRER_TRACKING_MORE = 'https://track.trackingmore.com/';

    const URL = 'https://www.trackingmore.com/gettracedetail.php';

    const ONE_WORLD_EXPRESS = 'oneworldexpress';

    public static function getTrackers()
    {
        return [
            TrackingHelper::ONE_WORLD_EXPRESS => 'One world express'
        ];
    }
    public static function prepareInfo($content) {
        $data = [
            'headers' => [],
            'content' => []
        ];

        if (isset($content['originCountryData']) && isset($content['originCountryData']['trackinfo'])) {
            $data['content'] = $content['originCountryData']['trackinfo'];
        }

        return $data;
    }
    public static function getContent($content) {
        $content = str_replace_first('(','', $content);
        $content = str_replace_last(')', '', $content);
        return json_decode($content, true);
    }
}