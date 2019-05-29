<?php
namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Excel;
use Countries;

use App\Components\Exceptions\DbException;

class LabelGeneratorController extends Controller
{
    public function index()
    {
		return view('generate-label.index');
    }
	
    public function upload(request $request)
    {
        $this->validate($request, [
            'file' => [
            'bail',
            'required',
            ],
        ]);
        
        if ($request->hasFile('file')){
            $file_path = $request->file('file')->getRealPath();
            
            $labelHtml = "";
            $Html = Excel::load($file_path, function($reader) use ($request, &$labelHtml) {
                foreach ($reader->toArray() as $row) {
                    $labelHtml .= '<table class="labelContainer">';
                    $labelHtml .= '<tr><td class="first-row" style="font-size: 18px;font-weight:bold"><span style="font-size:13px">Order ID:</span> <br />' . $row['客户单号'] . '</td><td class="first-row"><h3>' . $row['目的国家'] . '</h3></td><td class="first-row"><h3>' . $row['运输方式'] . '</h3></td></tr>';
                    
                    $labelHtml .= '<tr><td colspan="3" style="padding-left:20px;font:bold 16px Arial">To: ' . $row['收件人姓名'] . ' <br /><br /> ' . $row['联系地址'] . ' <br /> ' . $row['城市'] . ", " . $row['州省'] . ' <br /> ' . $row['收件人邮编'] . ' <br /> ' . Countries::where('iso_3166_2', str_replace("DHL-", "", $row['目的国家']))->first()->name . ' <br /><br /> Tel: ' . $row['收件人电话'] . ' </td></tr>';
                    
                    $labelHtml .= '<tr><td colspan="3" style="padding-left:10px;font:bold 14px Arial">' . $row['配货信息1'] . '</td></tr>';
                    $labelHtml .= '</table><div class="page-break"></div>';
                }
            })->get();
            
            $labelHeaderHtml = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><style>body{font-family: "微软雅黑","Microsoft Yahei",Arial,Helvetica,sans-serif,"宋体"} .page-break {page-break-after: always} .labelContainer {border-collapse: collapse;width:9.5cm;height:14.5cm} table, td {border:2px solid #000} .first-row {width:33%;height:15%;text-align:center}</style></head><body>';
            $labelFooterHtml = '</body></html>';
            
            echo $labelHeaderHtml . $labelHtml . $labelFooterHtml;
        }
    }
}