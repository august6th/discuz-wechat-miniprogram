<?php
require 'inc.php';

$token = $_POST['token'];
$result = WmApiLib::check_token($token);
$openid = $result['openid'];
$uid = 0;

$username = getDataForCharset($_POST['username']);
$avatar_url = $_POST['avatar_url'];
if (empty($username) || empty($avatar_url)) {
    WmApiError::display_result('param_error', '');
    exit();
}

/* 注释掉，关闭微信登陆功能
// 检查用户是否已经存在
$wmapi_user = DB::fetch_first("SELECT * FROM " . DB::table('wmapi_user') . " WHERE openid='" . $openid . "'");
if ($wmapi_user && $wmapi_user['uid'] > 0) {
    $uid = $wmapi_user['uid'];
    if (time() > $wmapi_user['logintime'] + 7200) {
        $wmapi_user['nickname'] = $username;
        $wmapi_user['logintime'] = time();
        DB::insert('wmapi_user', $wmapi_user, 1, 1);
        WmApiLib::set_user_avatar($uid, $avatar_url);
    }
} else {
    $wx_username = $username . '_wx' . substr($openid, -4) . rand(10, 99);
    $member = DB::fetch_first("SELECT * FROM " . DB::table('common_member') . " WHERE username='" . $wx_username . "'");
    if ($member) {
        WmApiError::display_result('sys_error');
        exit();
    }

    $insert_common_member = array(
        'username' => $wx_username,
        'password' => md5(random(10))
    );

    $uid = DB::insert('common_member', $insert_common_member, 1);
    if ($uid <= 0) {
        WmApiError::display_result('sys_error');
        exit();
    }

    $insert_wmapi_user = array(
        'uid' => $uid,
        'openid' => $openid,
        'nickname' => $username,
        'logintime' => time(),
        'dateline' => time()
    );

    $wm_uid = DB::insert('wmapi_user', $insert_wmapi_user, 1);
    if ($wm_uid <= 0) {
        WmApiError::display_result('sys_error');
        exit();
    }
    WmApiLib::set_user_avatar($uid, $avatar_url);
}
*/

$data['token'] = WmApiLib::get_token($uid, $openid);
$data['token_expire'] = 7200;

WmApiError::display_result('ok', $data);

