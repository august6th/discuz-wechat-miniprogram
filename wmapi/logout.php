<?php

require 'inc.php';

$token = $_POST['token'];
$result = WmApiLib::check_token($token);
$uid = 0;
$openid = $result['openid'];


$data['token']      =  WmApiLib::get_token($uid, $openid);
$data['token_expire'] = 7200;

WmApiError::runlog(json_encode($data));
WmApiError::display_result('ok',$data);

