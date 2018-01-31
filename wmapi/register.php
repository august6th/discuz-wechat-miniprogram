<?php
require 'inc.php';

$token = $_POST['token'];
$result = WmApiLib::check_token($token);

$openid = $result['openid'];

if(!function_exists('uc_user_register')) {
    loaducenter();
}

$username = $_POST['username'];
$password = $_POST['password'];
$email = $_POST['email'];

$uid = uc_user_register($username, $password, $email);
if($uid <= 0 and $uid != -6) {
	if($uid == -1) {
		WmApiError::display_result('profile_username_illegal');
	} elseif ($uid == -2) {
		WmApiError::display_result('profile_username_protect');
	} elseif ($uid == -3) {
		WmApiError::display_result('profile_username_duplicate');
	} elseif ($uid == -4) {
		WmApiError::display_result('profile_username_illegal');
	} elseif($uid == -5) {
		WmApiError::display_result('profile_email_domain_illegal');
	} elseif($uid == -6) {
		WmApiError::display_result('profile_email_duplicate');
	} else {
		WmApiError::display_result('sys_error');
	}
	exit();
}

$init_arr = array(0,0,0,0,0,0,0,0,0);
C::t('common_member')->insert($uid, $username, md5(random(10)), $email, '', '', $init_arr);

$data = array();
$data['token']      =  WmApiLib::get_token($uid, $openid);
$data['token_expire'] = 7200;

WmApiError::display_result('ok',$data);

