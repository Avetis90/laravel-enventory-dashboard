<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Services\UploadHelper;

use App\Models\uploadedOrders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

use Excel;
use \Exception;

class UploadedOrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('upload-orders.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function upload(request $request)
    {
        $this->validate($request, [
        'file' => [
        'bail',
        'required',
        ],
        ]);
        
		$userUpload = $request->file('file');
        $format = $request->input('format');
		
		if ($userUpload->getClientOriginalExtension() == "csv"){
			$info = UploadHelper::parseOrderCsv($userUpload, $format);
		}
        
        return view('upload-orders.result', [
			'message' => $info['message'],
			'orders' => $info['orders'],
			'duplicateOrders' => $info['dupOrders'],
        ]);
    }
	
	public function downloadWytOrders(request $request)
	{
		
        $formats = array("mt");
        
        $sites['wyt']['url'] = 'http://www.wyt168.com:8082';
        $sites['wyt']['user'] = 'CSZCZW';
        $sites['wyt']['pass'] = 'a62JdtydHtA1';
        
        $sites['mt']['url'] = 'http://211.159.182.134:8082';
        $sites['mt']['user'] = 'SMXCZW';
        $sites['mt']['pass'] = 'a62JdtydHtA1';
		
        //$sites['invative']['url'] = 'http://118.89.21.251:8082';
        //$sites['invative']['user'] = 'invative';
        //$sites['invative']['pass'] = '123456';
        
        foreach ($formats as $format){
            
            $cookieJar = new \GuzzleHttp\Cookie\CookieJar();
            $client = new Client();
            $client->request('POST', $sites[$format]['url'] . '/signin.htm', [
            'form_params' => [
            'username' => $sites[$format]['user'],
            'password' => $sites[$format]['pass']
            ],
            'cookies' => $cookieJar
            ]);
            
            
            $response = $client->request('GET', $sites[$format]['url'] . '/downloadOrder.htm', [
                'query' => [
                    'tradeshop_id' => '',
                    'consigneeCountry' => '',
                    'startDate' => date("Y-m-d", strtotime("-30 days")) . " 00:00:00",
                    'endDate' => date("Y-m-d") . " 23:59:59",
                    'consigneeName' => '',
                    'product_id' => '',
                    'order_status' => '',
                    'buyerId' => '',
                    'documentCode' => '',
                ],
                'cookies' => $cookieJar
            ]);
            
            $FeeOrdersResponse = $client->request('POST', $sites[$format]['url'] . '/downloadFeeDetails.htm', [
                'query' => [
                    'startDate' => date("Y-m-d", strtotime("-10 days")) . " 00:00:00",
                    'endDate' => date("Y-m-d") . " 23:59:59",
                    'documentCode' => '',
                ],
                'cookies' => $cookieJar
            ]);
            
            Storage::put('tmp/' . $format . 'OrdersDownloaded.xls', $response->getBody());
            Storage::put('tmp/' . $format . 'FeeOrders.xls', $FeeOrdersResponse->getBody());
			
            Excel::load(Storage::path('tmp/' . $format . 'OrdersDownloaded.xls'))->store('csv', Storage::path('tmp/'));
            Excel::load(Storage::path('tmp/' . $format . 'FeeOrders.xls'))->store('csv', Storage::path('tmp/'));
			
            $UploadHelper = new UploadHelper();
			
            $info = UploadHelper::parseOrderCsv(Storage::path('tmp/' . $format . 'OrdersDownloaded.csv'), "wyt");
            $info2 = $UploadHelper->parseInvoiceOrders(Storage::path('tmp/' . $format . 'FeeOrders.csv'), "wyt");
            
            $log_message = date("Y-m-d H:i:s") . ": " . $format . " - " . $info['message'] . "\n" .
							date("Y-m-d H:i:s") . ": " . $format . " - " . $info2['message'];
							
            Storage::append('tmp/WytOrdersDownloaded.log', $log_message);
        }
        /*
        return view('upload-orders.result', [
            'message' => $info['message'],
            'orders' => $info['orders'],
            'duplicateOrders' => $info['dupOrders'],
        ]);
        */
        
		/*
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="testData.xls"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            
            Excel::load(Storage::path('tmp/' . $format . 'OrdersDownloaded.xls'), function($reader) {
            
            })->store('csv', Storage::path('tmp/' . $format . 'OrdersDownloaded.csv'));

            
		$info = parseOrderCsv();

        return view('upload-orders.result', [
			'message' => $info['message'],
			'orders' => $info['orders'],
			'duplicateOrders' => $info['dupOrders'],
        ]);
		*/
	}
	
	public function downloadSongOrders(request $request)
	{
			$format = "song";
			
            Excel::load(Storage::path('tmp/20180807-Content.xls'))->store('csv', Storage::path('tmp/'));
            Excel::load(Storage::path('tmp/20180807-Fee.xls'))->store('csv', Storage::path('tmp/'));
			
            $UploadHelper = new UploadHelper();
			
            $info = UploadHelper::parseOrderCsv(Storage::path('tmp/20180807-Content.csv'), "wyt");
            $info2 = $UploadHelper->parseInvoiceOrders(Storage::path('tmp/20180807-Fee.csv'), "wyt");
            
            $log_message = date("Y-m-d H:i:s") . ": " . $format . " - " . $info['message'] . "\n" .
							date("Y-m-d H:i:s") . ": " . $format . " - " . $info2['message'];
							
            Storage::append('tmp/TonyOrdersDownloaded.log', $log_message);
        
        return view('upload-orders.result', [
            'message' => $info2['message'],
            'orders' => $info2['orders'],
            'duplicateOrders' => $info2['dupOrders'],
        ]);
        
        
		/*
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="testData.xls"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            
            Excel::load(Storage::path('tmp/' . $format . 'OrdersDownloaded.xls'), function($reader) {
            
            })->store('csv', Storage::path('tmp/' . $format . 'OrdersDownloaded.csv'));

            
		$info = parseOrderCsv();

        return view('upload-orders.result', [
			'message' => $info['message'],
			'orders' => $info['orders'],
			'duplicateOrders' => $info['dupOrders'],
        ]);
		*/
	}
	
	public function updateNewInvoiceOrders(request $request)
	{
			$format = "song";
			
            Excel::load(Storage::path('tmp/2018070804-update.xls'))->store('csv', Storage::path('tmp/'));
			
            $UploadHelper = new UploadHelper();
			
            $info = UploadHelper::parseOrderCsv(Storage::path('tmp/2018070804-update.csv'), "wyt");
            
            $log_message = date("Y-m-d H:i:s") . ": " . $format . " - " . $info['message'] . "\n";
							
            Storage::append('tmp/TonyOrdersDownloaded.log', $log_message);
        
        return view('upload-orders.result', [
            'message' => $info['message'],
            'orders' => $info['orders'],
            'duplicateOrders' => $info['dupOrders'],
        ]);
        
        
		/*
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="testData.xls"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            
            Excel::load(Storage::path('tmp/' . $format . 'OrdersDownloaded.xls'), function($reader) {
            
            })->store('csv', Storage::path('tmp/' . $format . 'OrdersDownloaded.csv'));

            
		$info = parseOrderCsv();

        return view('upload-orders.result', [
			'message' => $info['message'],
			'orders' => $info['orders'],
			'duplicateOrders' => $info['dupOrders'],
        ]);
		*/
	}
	
	
	public function uploadedTest (Request $request)
	{
		
        $formats = array("wyt", "mt");
        
        $sites['wyt']['url'] = 'http://www.wyt168.com:8082';
        $sites['wyt']['user'] = 'CSZCZW';
        $sites['wyt']['pass'] = 'a62JdtydHtA1';
        
        $sites['mt']['url'] = 'http://211.159.182.134:8082';
        $sites['mt']['user'] = 'SMXCZW';
        $sites['mt']['pass'] = 'a62JdtydHtA1';
        
        foreach ($formats as $format){
            
            $cookieJar = new \GuzzleHttp\Cookie\CookieJar();
            $client = new Client();
            $client->request('POST', $sites[$format]['url'] . '/signin.htm', [
            'form_params' => [
            'username' => $sites[$format]['user'],
            'password' => $sites[$format]['pass']
            ],
            'cookies' => $cookieJar
            ]);
            
            $FeeOrdersResponse = $client->request('POST', $sites[$format]['url'] . '/downloadFeeDetails.htm', [
                'query' => [
                    'startDate' => date("Y-m-d", strtotime("-10 days")) . " 00:00:00",
                    'endDate' => date("Y-m-d") . " 23:59:59",
                    'documentCode' => '',
                ],
                'cookies' => $cookieJar
            ]);
            
            Storage::put('tmp/' . $format . 'FeeOrders.xls', $FeeOrdersResponse->getBody());
        }
		
	}
    
    public function ubiDownloadOrders (Request $request)
    {
        $cookieJar = new \GuzzleHttp\Cookie\CookieJar();
        $client = new Client();
		
        $response = $client->request('GET', 'https://cn.etowertech.com/IndexServlet?language=zh_CN&credential=eyJicmFuZCI6ImNuLmV0b3dlcnRlY2guY29tIiwic2Vzc2lvbklkIjoiM1RUb0hQVDF0NmZNUWk5ampKdVRmQSJ9', [
            'allow_redirects' => [
            'max'             => 10,        // allow at most 10 redirects.
            'strict'          => true,      // use "strict" RFC compliant redirects.
            'referer'         => true,      // add a Referer header
            'track_redirects' => true
            ],
            'cookies' => $cookieJar
        ]);
        
        $sessionId = $cookieJar->toArray()[0]['Value'];
        
        $response = $client->request('POST', 'https://cn.etowertech.com/parcel/order/data/0?offset=0&limit=300', [
        'form_params' => [
            'queryType' => 'trackingNo',
            'trackingNos' => '',
            'dateType' => 'dateCreated',
            'statusStr' => '0,1,2,3,4,5,9,10,6,8,11,12,13,14',
            'startDate' => date("Y-m-d", strtotime("-4 days")) . " 00:00:00",
            'endDate' => date("Y-m-d") . " 23:59:59"
        ],
        'headers' => [
            'Referer' => 'https://cn.etowertech.com/parcel/order/list',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
            'Accept'     => 'application/json',
            'requestType'      => 'ajax',
            'sessionId'      => $sessionId,
            'X-Requested-With'      => 'XMLHttpRequest',
        ],
        'cookies' => $cookieJar
        ]);
        
        $orders = array();
		$orders['ok'] = [];
		$orders['dup'] = [];
        $ordersInfo = json_decode($response->getBody(), true);
		$repalceStrings = array("塑料产品", "*1;", "美");
        $cleanAddress = array(", ,", ", ");

        foreach ($ordersInfo['rows'] as $order){
            $orderId = $order['referenceNo'];
            $orderExist = uploadedOrders::where('order_id', $orderId)->first();
            $trackNumExist = uploadedOrders::where('track_num', $order['trackingNo'])->first();
            
            $order_key = ($orderExist) ? "dup" : "ok";
            if ($orderExist && !$trackNumExist){
                $orderId = $orderId . "(RMA)";
            }
            
            $orderAddress = $order['address']['addressLine1'] . ", " . $order['address']['addressLine2'] . ", " . $order['address']['addressLine3'];
            $orders[$order_key][] = array(
                'order_id' => $order['referenceNo'],
                'skus' => trim(str_replace($repalceStrings, "", $order['sku'])),
                'shipping_method' => $order['serviceOption'],
                'country' => $order['address']['country'],
                'name' => $order['recipientName'],
                'state' => $order['address']['state'],
                'city' => $order['address']['city'],
                'address' => trim(rtrim(str_replace(", , ", "", $orderAddress), ", ")),
                'zip' => $order['address']['postcode'],
                'phone' => $order['phone'],
                'email' => "",
                'track_num' => $order['trackingNo'],
            );
        }
        
        
        
        $message = "No order inserted. Total duplicate " . count($orders['dup']) . " orders";
        if (count($orders['ok']) > 0){
            if (uploadedOrders::insert($orders['ok'])){
                $message = "Successfully Inserted " . count($orders['ok']) . " orders. Duplicate orders: " . count($orders['dup']);
            } else {
                $message = "Error. Total okay orders: " . count($orders['ok']) . ". Duplicate orders: " . count($orders['dup']);
            }
        }
		
		$log_message = date("Y-m-d H:i:s") . ": " . "UBI " . " - " . $message;
		Storage::append('tmp/ordersDownloaded.log', $log_message);
        
		/*
        return view('upload-orders.result', [
            'message' => $message,
            'orders' => $orders['ok'],
            'duplicateOrders' => $orders['dup'],
        ]);
		*/
		
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    


    /**
     * Display the specified resource.
     *
     * @param  \App\uploadedOrders  $uploadedOrders
     * @return \Illuminate\Http\Response
     */
    public function show(uploadedOrders $uploadedOrders)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\uploadedOrders  $uploadedOrders
     * @return \Illuminate\Http\Response
     */
    public function edit(uploadedOrders $uploadedOrders)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\uploadedOrders  $uploadedOrders
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, uploadedOrders $uploadedOrders)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\uploadedOrders  $uploadedOrders
     * @return \Illuminate\Http\Response
     */
    public function destroy(uploadedOrders $uploadedOrders)
    {
        //
    }
}
