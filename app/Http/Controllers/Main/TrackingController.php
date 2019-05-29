<?php
/**
 * Created by PhpStorm.
 * User: DeVlas
 * Date: 15.08.2017
 * Time: 20:33
 */

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Services\TrackingHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\HttpTrackingRequestService;

class TrackingController extends Controller
{
    public function index(request $request) {
        $content = ['content'=> [], 'headers' => []];

        if ($request->isMethod('post')) {
            $this->validate($request, [
                'tracker' => [
                    'bail',
                    'required',
                    Rule::in(array_keys(TrackingHelper::getTrackers())),
                ],
                'tracking_number' => [
                    'bail',
                    'required',
                ],
            ]);

            $response = HttpTrackingRequestService::get('https://www.trackingmore.com/gettracedetail.php', [
                'Referer' => TrackingHelper::REFERRER_TRACKING_MORE
            ], [
                'tracknumber' => $request->input('tracking_number'),
                'express' =>$request->input('tracker')
            ]);

            if($response->getStatusCode() === HttpTrackingRequestService::STATUS_SUCCESS_CODE) {
                $content = TrackingHelper::prepareInfo(TrackingHelper::getContent($response->getBody()->getContents()));
            }
        }

        return view('tracking.index', [
            'trackers' => TrackingHelper::getTrackers(),
            'content' => $content
        ]);
    }

}