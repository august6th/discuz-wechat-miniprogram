<?php
require 'inc.php';

$code = $_POST['code'];
if (empty($code)) {
    WmApiError::display_result('param_code_error', '');
    exit();
}

$mp_url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . WMAPI_APPID . '&secret=' . WMAPI_APPSECRET . '&js_code=' . $code . '&grant_type=authorization_code';
$content = get_url_content($mp_url);
WmApiError::runlog('mp_resp:' . $content);

$user_data = json_decode($content);
if (empty($user_data->{'openid'})) {
    WmApiError::display_result('param_code_error', '');
    exit();
}

$openid = $user_data->{'openid'};

$uid = 0;

$token = $_POST['token'];
if (!empty($token)) {
    $result = WmApiLib::decode_token($token);
    if ($result['uid'] != 0) {
        $uid = $result['uid'];
    }
}

/* 注释掉，关闭微信登陆功能
if ($uid == 0) {
    // 检查用户是否已经存在
    $wmapi_user = DB::fetch_first("SELECT * FROM " . DB::table('wmapi_user') . " WHERE openid='" . $openid . "'");
    if ($wmapi_user && $wmapi_user['uid'] > 0) {
        $uid = $wmapi_user['uid'];
    }
}
*/

$data = array();
if ($uid == 0) {
    $data['has_login'] = 0;
} else {
    $data['has_login'] = 1;
}
$data['token'] = WmApiLib::get_token($uid, $openid);
$data['token_expire'] = 7200;

WmApiError::display_result('ok', $data);

