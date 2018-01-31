<?php

$error_log = $_POST['error_log'];
$svr_url = $_POST['svr_url'];
if (!empty($error_log))
{
	$msg = "\n----- error_log : ".$svr_url." : ".time()." -----\n".$error_log."\n";
	$myfile = fopen("./data/report_error".date("Y-m-d").".log", "a");
	fwrite($myfile, $msg);
	fclose($myfile);
}
exit;

$result = array();
$result['err_code'] = 0;
$result['err_msg'] = 'ok';
header('Content-Type: application/json');
echo json_encode($result);
