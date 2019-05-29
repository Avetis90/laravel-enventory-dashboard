<?php
require 'vendor/autoload.php';

if(!empty($_POST['data'])){
$search_array = array('<link rel="shortcut icon" href="//cbu01.alicdn.com/favicon.ico"/>', '//cbu01.alicdn.com/cms/upload/2011/202/160/61202_1446014988.png', '<a class="btn btn-print" href="#" title="打印">打&nbsp;印</a>', '<div class="mod-print-page">', '(location>"https"?"//g":"//g")+".alicdn.com/alilog/mlog/aplus_b2b2.js")', '<script src="//astyle-src.alicdn.com/app/trade/js/move/trade/print-merge.js"></script>', '<link href="//astyle-src.alicdn.com/app/trade/css/move/trade/print-merge.css" rel="stylesheet" type="text/css" />', 'with(document)', 'id="tb-beacon-aplus",src=', 'mlog/aplus_v2.js")');

$replace_array = array('<meta http-equiv="content-type" content="text/html; charset=utf-8" />', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABEAAAARCAMAAAAMs7fIAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAwBQTFRFNDQ0AAAAy8vLmZmZ1tbWpsPw9PT05eXlAIz/WFhY///+IlOe+r5ac5XJ7+TV7JgE0tLSp8TwyHAA+sEh/v7+/vHa/eO7+rUC9aQO/vHd/u3P9aQB8qc1/LIz9qEGenp6+dWh+tuk5H8AkFAA7YkAq31E97Yh97Mgp2kb5p4p/OK1/Nyu1ngA85cA2cOe+bhA1MW0+7JK7JcPt2wO/NaVRm6r8ePRjXVb+MyMcXFx1ax1Ef8A+bo1+rcC/Pfu9KonwqB097UN9akF+qwm9qwrd0AA3n8A+b0s8u7n8qIicEkc7uLOyncOyXEAx4Ao2LSH+bkV2sas4s+26YUAmVQA+KcA7uTWsWwR+aUA3YIA/tmndEAA75oFbDoA/OG1/uvM/OG3ql8Af39/iIiIpn5L3osC39/fxLKb3dTJtIpN+9uu77lgtWUA5ocA8p0J75cZ+Lsi+bZdzb6u238A+q8Q+bIHiFwnZT0O+rYr248hm1QA5owAhqrj34QBhmpHwW8A8JYA/Nyw/eS4xm0A09PT/Nuou2gA6o4A5ZAZ6ooA/PLg+bw5wmwA544T7Y0A//7+z3MAx3ADwZxl+b0b+rcxvIA5/uCz7t3D/wAApY928q8iyYk2znMA04QJjmxD4YIEkJCQ960Ow3AI////////paWlpqamp6enqKioqampqqqqq6urrKysra2trq6ur6+vsLCwsbGxsrKys7OztLS0tbW1tra2t7e3uLi4ubm5urq6u7u7vLy8vb29vr6+v7+/wMDAwcHBwsLCw8PDxMTExcXFxsbGx8fHyMjIycnJysrKy8vLzMzMzc3Nzs7Oz8/P0NDQ0dHR0tLS09PT1NTU1dXV1tbW19fX2NjY2dnZ2tra29vb3Nzc3d3d3t7e39/f4ODg4eHh4uLi4+Pj5OTk5eXl5ubm5+fn6Ojo6enp6urq6+vr7Ozs7e3t7u7u7+/v8PDw8fHx8vLy8/Pz9PT09fX19vb29/f3+Pj4+fn5+vr6+/v7/Pz8/f39/v7+////KXwEZgAAAKV0Uk5T//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////8AIHvOBAAAAN5JREFUeNpiWAIBYooQ+v8SBijDsJLrP7LIfynZudNRRUwqUq1gIkD6////POrVfv9BDKBI/DStuIRWfbWY+RaN2llRTUsYsjucp9h0uxc4Sdi7ePYaZy5hsJtU3ykXIDy5dGFebY6ruQfQnP964cURtuKhDToh5elAsxmAxnlrljhK6/YJlWX8B4sAhXhm8UfmtrtNBdsPEhGVMeqpag5WWWQGU6OQ7xs9T7VNaY4/VISrcGLszP5/DokTZgMdCDJZ0sfLAOTyIA1lPohIV0oRSPX//4F1YWARdAAQYAATm55lnQFXxgAAAABJRU5ErkJggg==', '', '<div class="mod-print-page" style="border:0px">', '', '', '<style>html{color:#000;overflow-y:scroll;background:#fff}body,h1,h2,h3,h4,h5,h6,hr,p,blockquote,dl,dt,dd,ul,ol,li,pre,fieldset,lengend,button,input,textarea,form,th,td{margin:0;padding:0}body,button,input,select,textarea{font:12px/1.5 Tahoma,Arial,"\5b8b\4f53",sans-serif}h1,h2,h3,h4,h5,h6{font-size:100%;font-weight:400}address,cite,dfn,em,var{font-style:normal}code,kbd,pre,samp,tt{font-family:"Courier New",Courier,monospace}small{font-size:12px}ul,ol{list-style:none}a{text-decoration:none}a:hover{text-decoration:underline;color:#ff7300}abbr[title],acronym[title]{border-bottom:1px dotted;cursor:help}q:before,q:after{content:\'\'}:focus{outline:0}legend{color:#000}fieldset,img{border:none}button,input,select,textarea{font-size:100%}table{border-collapse:collapse;border-spacing:0}hr{border:none;height:1px;*color:#fff}img{-ms-interpolation-mode:bicubic}.fd-left{float:left}.fd-right{float:right}.fd-clear{clear:both}.fd-clr{zoom:1}.fd-clr:after{display:block;clear:both;height:0;content:"\0020"}.fd-hide{display:none}.fd-show{display:block}.fd-inline{display:inline}.fd-lump{display:inline-block;display:-moz-inline-stack;zoom:1;*display:inline}.fd-visible{visibility:visible}.fd-hidden{visibility:hidden}.fd-locate{position:relative}.fd-cny{font-family:Helvetica,Arial}.fd-gray{color:#666}.fd-bold{font-weight:700}.w952{width:952px;margin:0 auto;padding:0 4px}.w968{width:968px;margin:0 auto}.w968 .layout{padding-left:8px}#header,#content,#footer,.layout{zoom:1}#header:after,#content:after,#footer:after,.layout:after{content:"\0020";display:block;height:0;clear:both}.grid-fixed{margin-right:0!important}.layout .grid-1{float:left;width:32px;margin-right:8px}.layout .grid-2{float:left;width:72px;margin-right:8px}.layout .grid-3{float:left;width:112px;margin-right:8px}.layout .grid-4{float:left;width:152px;margin-right:8px}.layout .grid-5{float:left;width:192px;margin-right:8px}.layout .grid-6{float:left;width:232px;margin-right:8px}.layout .grid-7{float:left;width:272px;margin-right:8px}.layout .grid-8{float:left;width:312px;margin-right:8px}.layout .grid-9{float:left;width:352px;margin-right:8px}.layout .grid-10{float:left;width:392px;margin-right:8px}.layout .grid-11{float:left;width:432px;margin-right:8px}.layout .grid-12{float:left;width:472px;margin-right:8px}.layout .grid-13{float:left;width:512px;margin-right:8px}.layout .grid-14{float:left;width:552px;margin-right:8px}.layout .grid-15{float:left;width:592px;margin-right:8px}.layout .grid-16{float:left;width:632px;margin-right:8px}.layout .grid-17{float:left;width:672px;margin-right:8px}.layout .grid-18{float:left;width:712px;margin-right:8px}.layout .grid-19{float:left;width:752px;margin-right:8px}.layout .grid-20{float:left;width:792px;margin-right:8px}.layout .grid-21{float:left;width:832px;margin-right:8px}.layout .grid-22{float:left;width:872px;margin-right:8px}.layout .grid-23{float:left;width:912px;margin-right:8px}.layout .grid-24{float:left;width:952px}.w952{width:700px;text-align:right}#content{padding:8px}.back-top{display:none;position:fixed;_position:absolute;right:10px;bottom:100px;_bottom:150px;width:16px;height:56px;background:url(//cbu01.alicdn.com/cms/upload/2011/188/652/256881_1070828466.png);cursor:pointer}.mod-title-info{margin:8px 0;color:#333;line-height:24px}.mod-title-info strong{font-weight:700}.mod-title-info span,.mod-title-info a{display:inline-block}.mod-title-info a.btn{background:url(//cbu01.alicdn.com/images/myalibaba/trade/logistics/imgs_v2.gif) no-repeat -9999px -9999px}.mod-title-info a:link,.mod-title-info a:visited{background-position:0 0!important;width:58px;height:22px;color:#06C;text-decoration:none;margin:0 8px;text-align:center;line-height:20px}.mod-title-info a:hover{background-position:0 -36px!important}.mod-print-page{padding:8px;border:1px dotted #666;text-align:left;color:#333}.mod-print-page .page-info{color:#999;margin:4px 0}.mod-print-page .wpr{word-break:break-all;word-wrap:break-word}.mod-print-page h2{line-height:50px;font-size:16px;display:block;text-align:center;font-weight:700;border-bottom:1px dashed #ccc}.mod-print-page h3{line-height:36px;height:36px;font-weight:700;font-size:14px;display:block;clear:both;overflow:hidden}.mod-print-page h3 img{float:left;overflow:hidden;margin:7px 3px 0 0}.mod-print-page .cell-bor-2{margin:0!important}.mod-print-page .cell-bor-ct{border:1px solid #DBDBDB;margin:0 0 16px 0}.mod-print-page .cell-info-box{border:1px solid #DBDBDB;border-collapse:collapse;table-layout:fixed}.mod-print-page .cell-info-box{width:100%;margin:0 0 16px 0}.mod-print-page .cell-info-box .col-title{width:100px}.mod-print-page .cell-info-box .col-con2{width:190px}.mod-print-page .cell-info-box td,.mod-print-page .cell-info-box th{border:1px solid #DBDBDB;padding:4px 0;vertical-align:middle}.mod-print-page .cell-info-box td{text-align:left;word-break:break-all;word-wrap:break-word}.mod-print-page .cell-info-box th{text-align:center;height:32px;line-height:32px}.mod-print-page .cell-info-box .td-ct{padding:0 4px}.mod-print-page .cell-info-box .title{font-weight:700}.mod-print-page .cell-info-box .col-order{width:40px}.mod-print-page .cell-info-box .col-name{width:140px}.mod-print-page .cell-info-box .col-spec{width:80px;text-align:center}.mod-print-page .cell-info-box .col-num{width:60px}.mod-print-page .cell-info-box .col-price{width:82px}.mod-print-page .cell-info-box .col-productNum{width:95px}.mod-print-page .cell-info-box .col-promotion{width:70px;text-align:center}.mod-print-page .cell-info-box .col-money{width:86px}.mod-print-page .cell-info-box .ali-right{text-align:right;padding-right:4px}.mod-print-page .cell-info-box .ali-center{text-align:center}.mod-print-page .cell-info-box .ali-left{text-align:left}.mod-print-page .cell-info-box .money-b{font-weight:700;color:#666}.mod-print-page .cell-info-box .money{display:inline-block}.mod-print-page .cell-info-box .num{display:inline-block}.mod-print-page .cell-info-box .name{display:block}.mod-print-page .cell-info-box .price{display:inline-block}.mod-print-page .cell-info-box .productNum{width:80px;display:inline-block}.mod-print-page .cell-info-box .company,.mod-print-page .cell-info-box .buyer-name,.mod-print-page .cell-info-box .addr,.mod-print-page .cell-info-box .mobile{display:inline-block}.mod-print-page .cell-info-box .saler-name,.mod-print-page .cell-info-box .alipay,.mod-print-page .cell-info-box .tel{width:182px;display:inline-block}.mod-print-page .cell-info-box tfoot td{border:none 0;background:#e4e4e4}.mod-print-page .cell-message{color:#666;display:block;overflow:hidden}.mod-print-page .cell-message pre{white-space:normal;word-wrap:break-word;word-break:normal}.mod-print-page .cell-order-info{line-height:20px;color:#666;margin:0 0 8px 0;float:left;display:block}.mod-print-page .cell-order-info li{float:left;margin:0 10px 0 0;width:326px}.mod-print-page .cell-order-info .last{margin:0}.mod-print-page .cell-order-info strong{font-weight:700;color:#333}.mod-print-page .total-order-info{width:100%;line-height:24px;border:0 none}.mod-print-page .total-order-info td{text-align:left;border:0 none}.mod-print-page .total-order-info .right-cell{text-align:right}.mod-print-page .cell-logistics-info li{display:block;line-height:20px;overflow:hidden;white-space:normal;word-break:break-all;word-wrap:break-word}.mod-print-page .cell-logistics-info li strong,.mod-print-page .cell-logistics-info li span{float:left}.mod-print-page .cell-logistics-info li span{width:600px;color:#666}.mod-print-page .rk-info{clear:both;height:36px}.mod-print-page .rk-info ul{width:360px;float:right}.mod-print-page .rk-info li{float:left;width:180px}.mod-print-page .line-box{border-bottom:1px dotted #ccc;margin-bottom:16px}.mod-print-page .total-info{background:#e4e4e4;border:1px solid #999;margin-bottom:16px}.mod-print-page h3.msg-tit{background:0 0;padding:0;font-size:12px}.mod-print-page .buyer td,.mod-print-page .buyer th{vertical-align:top}.mod-print-page .total-info .summary{padding:0 10px;height:32px;line-height:32px;text-align:right}.mod-print-page .total-info .summary li{float:right;margin:0 0 0 16px;display:inline-block}.mod-print-page h2 .sub-tit{font-size:12px;font-weight:400;margin-left:10px}.print-view-box{float:left;width:150px;text-align:left}.print-view-box span{float:left}.print-view-list span{float:left;width:86px;padding:0 20px 0 5px}.print-view-list{position:relative;float:left;width:111px;cursor:pointer;background:url(//cbu01.alicdn.com/cms/upload/2011/400/260/62004_1446014988.png) no-repeat}.print-view-list .view-list{position:absolute;left:0;top:23px;border:1px solid #ccc;z-index:1000}.print-view-list .view-list li{float:left;border-bottom:1px solid #ccc;background:#fff}.view-list li a:link,.view-list li a:visited,.view-list li a:active,.view-list li a:hover{width:auto;float:left;display:block;margin:0;padding:0 8px;text-align:left;color:#666}.view-list li a:hover{background:#e4e4e4}.print-view-box .d-n{display:none}.bor-n{border-color:#fff;border-bottom:1px dotted #666}@media print{.w952{width:99%}#content{padding:0}.mod-print-page{padding:0;border:none;page-break-inside:auto}.pager-bar{page-break-before:always}.mod-print-page .order-list,.mod-print-page .cell-message,.mod-print-page .cell-info-box,.mod-print-page .total-info{page-break-before:auto}.mod-print-page .cell-info-box tr .mod-print-page .cell-info-box td,.mod-print-page .total-info{page-break-inside:avoid}.mod-print-page .cell-logistics-info li strong,.mod-print-page .cell-logistics-info li span{float:none}#head,#footer,.mod-title-info,.back-top{display:none}}.trade-spec{color:#666}.trade-spec .spec-item{padding-right:14px}.trade-spec .spec-item-last{padding-right:0!important}</style>', '/*', '*/', '*/');

$content = str_replace($search_array, $replace_array, $_POST['data']);

//echo $content;
$path = "";
if (isset($_POST['month'])) {
	if (!file_exists("./" . $_POST['month'])) {
		mkdir("./" . $_POST['month'], 0777, true);
	}
	$path = $_POST['month'] . "/";
}

//file_put_contents( "./" . $path . $_POST['filename'], $content );
file_put_contents("./" . $path . $_POST['filename'], "\xEF\xBB\xBF".  $content); 
echo $path . $_POST['filename'] . " Saved!";
} else {
echo "No Data Sent";
}

exit();