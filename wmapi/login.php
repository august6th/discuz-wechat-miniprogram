<?php

require 'inc.php';

//dd(urldecode($_POST['username']));

//exit;
$token = $_POST['token'];
$result = WmApiLib::check_token($token);
$openid = $result['openid'];

if (!function_exists('uc_user_login')) {
    loaducenter();
}

$username = getDataForCharset(urldecode($_POST['username']));
$password = $_POST['password'];

//dd($username);

//exit;
//用户登录验证
$ucresult = uc_user_login($username, $password, 0, 0, '', '');
$tmp = array();
list($tmp['uid'], $tmp['username'], $tmp['password'], $tmp['email'], $duplicate) = daddslashes($ucresult, 1);
$ucresult = $tmp;
$uid = $ucresult['uid'];

if ($uid < 0) {
    WmApiError::display_result('user_login_failed');
    exit();
}

$data = array();
$data['token'] = WmApiLib::get_token($uid, $openid);
$data['token_expire'] = 7200;

WmApiError::display_result('ok', $data);

