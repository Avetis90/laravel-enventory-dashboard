<?php
namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Excel;
use Countries;

use App\Components\Exceptions\DbException;

class InvoiceGenerateController extends Controller
{
    public function index()
    {
		return view('invoice-generate.index');
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
                    $labelHtml .= '<tr><td style="text-align:center;font-weight:bold;font-size:16px;height:0.75cm;-webkit-box-shadow:inset 0px 0px 0px 3px #000; -moz-box-shadow:inset 0px 0px 0px 3px #000; box-shadow:inset 0px 0px 0px 3px #000;" colspan="4">Commercial Invoice</td></tr>';
					
                    $labelHtml .= '<tr><td style="padding-left:5px;text-align:left;height:0.5cm;font-size:13px;font-weight:bold" colspan="1">Date: ' . date('Y-m-d') . '</td><td style="height:0.5cm;text-align:left;padding-left:5px;font-weight:bold;font-size:13px" colspan="3">Invoice No. ' . $row['客户单号'] . '</td></tr>';
					
                    $labelHtml .= '<tr><td style="text-align:left;font-size:13px;padding-left:5px;height:2.5cm" colspan="1"><strong>Exporter / Shipper:</strong><br />DaiBo<br />
No.3 YUNXIAO Road<br />
GuangZhou, GUANGDONG, CHINA<br /></td><td style="text-align:left;padding-left:5px;font-size:12px;height:2.5cm" colspan="3"><strong>Consignee:</strong><br />' . $row['收件人姓名'] . ' <br /> ' . $row['联系地址'] . ', ' . $row['城市'] . ", " . $row['州省'] . ' ' . $row['收件人邮编'] . ', ' . Countries::where('iso_3166_2', str_replace("DHL-", "", $row['目的国家']))->first()->name . '<br /> Tel: ' . $row['收件人电话'] . '</td></tr>';


                    $labelHtml .= '<tr><td style="padding-left:5px;text-align:left;height:0.5cm;font-size:13px" colspan="1"><strong>Origin of Goods:</strong> CHINA</td><td style="height:0.5cm;text-align:left;padding-left:5px;font-size:12px" colspan="3"><strong>Destination:</strong> ' . strtoupper(Countries::where('iso_3166_2', str_replace("DHL-", "", $row['目的国家']))->first()->name) . '</td></tr>';
					
                    $labelHtml .= '
					<tr>
						<td style="text-align:center;height:0.25cm;font-size:11px;width:50%">DESCRIPTION OF GOODS</td>
						<td style="text-align:center;height:0.25cm;font-size:10px;width:10%">QTY</td>
						<td style="text-align:center;height:0.25cm;font-size:10px;width:19%">UNIT VALUE</td>
						<td style="text-align:center;height:0.25cm;font-size:10px;width:21%">TOTAL VALUE</td>
					</tr>';
					
                    $labelHtml .= '
					<tr>
						<td style="text-align:center;height:0.25cm;font-size:12px;width:50%">' . $row['海关报关品名1'] . '</td>
						<td style="text-align:center;height:0.25cm;font-size:11px;width:10%">' . $row['申报品数量1'] . '</td>
						<td style="text-align:center;height:0.25cm;font-size:11px;width:19%">$69.99 USD</td>
						<td style="text-align:center;height:0.25cm;font-size:11px;width:21%">$69.99 USD</td>
					</tr>';
					
                    $labelHtml .= '</table><div class="page-break"></div>';
                }
            })->get();
            
            $labelHeaderHtml = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><style>body{font-family: "微软雅黑","Microsoft Yahei",Arial,Helvetica,sans-serif,"宋体"} .page-break {page-break-after: always} .labelContainer {border-collapse: collapse;width:9.5cm;height:14.5cm} table, td {border:2px solid #000} .first-row {width:33%;height:15%;text-align:center}</style></head><body>';
            $labelFooterHtml = '</body></html>';
            
            echo $labelHeaderHtml . $labelHtml . $labelFooterHtml;
        }
    }
}