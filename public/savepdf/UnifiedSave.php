<?php
//require 'vendor/autoload.php';

//use Sunra\PhpSimple\HtmlDomParser;
function get_inner_html( $node ) { 
    $innerHTML= ''; 
    $children = $node->childNodes; 
    foreach ($children as $child) { 
        $innerHTML .= $child->ownerDocument->saveXML( $child ); 
    } 
    
    return $innerHTML; 
} 

if(!empty($_POST['data'])){
        /*
    libxml_use_internal_errors(true);
    $dom = new DOMDocument;
    $dom->loadHTML($_POST['data']);
    libxml_use_internal_errors(false);
    
    $body = $dom->getElementsByTagName('body')->item(0);
    
    echo $body->textContent; // print all the text content in the body

    
    echo "failed1";
    $xpath = new DOMXpath($_POST['data']);
    $result = $xpath->query('//div[@class="layout"]');
    if ($result->length > 0) {
        echo "failed1";
        print_r($result->item(0)->nodeValue);
    } else {
        echo "failed2";
    }
    */
    libxml_use_internal_errors( true);
    $dom = new DOMDocument();
    $dom->loadHTML(mb_convert_encoding($_POST['data'], 'HTML-ENTITIES', 'UTF-8'));    
    $xpath = new DOMXPath($dom);
    $div = $xpath->query('//div[@id="bd"]');
    //print_r($div->item(0));
    $div0 = $div->item(0);
    $div1 = $div->item(1);
    $images = $dom->getElementsByTagName('img');
    $imgs = array();
    foreach($images as $img) {
        $imgs[] = $img;
    }
    foreach($imgs as $img) {
        $img->parentNode->removeChild($img);
    }
    $result .= $dom->saveHTML($div0);
    //$result .= $dom->saveHTML($div1);
    $result = str_replace('<div class="unit-stage make-stage-crumb" data-stage=\'{"stageIndex":4}\'>', '<div class="unit-stage make-stage-crumb" data-stage="{&quot;stageIndex&quot;:4 }" style="width: 620px; visibility: visible;">', $result);
    $result = str_replace('-then', '-pass', $result);
    
    $html_template = file_get_contents('./unified_1688_layout.html');
    $content = str_replace("{replace_layout_content}", $result, $html_template);
    
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