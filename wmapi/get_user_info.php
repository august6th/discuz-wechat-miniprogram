<?php
require 'inc.php';

$token = $_POST['token'];
$result = WmApiLib::check_token($token);
if ($result['uid'] == 0) {
    WmApiError::display_result('user_no_login');
    exit();
}
$uid = $result['uid'];

//用户信息
$member = DB::fetch_first("SELECT * FROM " . DB::table('common_member') . " WHERE uid='" . $result['uid'] . "'");
if (!$member) {
    WmApiError::display_result('user_login_failed');
    exit();
}

$author = $member['username'];

$wmapi_user = DB::fetch_first("SELECT * FROM " . DB::table('wmapi_user') . " WHERE uid=" . $uid);
if ($wmapi_user && !empty($wmapi_user['nickname'])) {
    $author = $wmapi_user['nickname'];
}


//用户详细信息
$member_profile = DB::fetch_first("SELECT * FROM " . DB::table('common_member_profile') . " WHERE uid='{$result['uid']}'");
//获取勋章
$member_field_forum = DB::fetch_first("SELECT * FROM " . DB::table('common_member_field_forum') . " WHERE uid='{$result['uid']}'");

$medalids = $member_field_forum['medals'];
$usermedals = $medal_detail = $usermedalmenus = array();
if ($medalids) {
    foreach ($medalids = explode("\t", $medalids) as $key => $medalid) {
        list($medalid, $medalexpiration) = explode("|", $medalid);
        if (empty($medalid)) {
            continue;
        }
        if (!$medalexpiration || $medalexpiration > TIMESTAMP) {
            $usermedals[] = $medalid;
        }
    }
    if (!empty($usermedals)) {
        $medal_detail = DB::fetch_all("SELECT * FROM " . DB::table('forum_medal') . " WHERE medalid in(" . implode(',', $usermedals) . ")");
        foreach ($medal_detail as $val) {
            if ($val['expiration'] == 0 || $val['expiration'] > TIMESTAMP) {
                $usermedalmenus[] = array('medalid' => $val['medalid'], 'name' => $val['name'], 'image' => STATICURL . 'image/common/' . $val['image']);
            }
        }
    }
}

//统计数据
$member_count = DB::fetch_first("SELECT * FROM " . DB::table('common_member_count') . " WHERE uid='{$result['uid']}'");

//头像

$data = array();
$data['uid'] = $member['uid'];
$data['avatar'] = WmApiLib::get_user_avatar($member['uid']);
$data['username'] = $author;

/*
$data['email']    = $member['email'];
$data['password'] = $member['password'];
$data['groupid']  = $member['groupid'];//等级
$data['extcredits3'] = $member_count['extcredits3'];//金钱
$data['extcredits6'] = $member_count['extcredits6'];//模币
$data['extcredits8'] = $member_count['extcredits6'];//应助分
$data['follower']    = $member_count['follower'];//分数数
$data['following']   = $member_count['following'];//关注数
$data['gender']      = lang('space','gender_'.$member_profile['gender']);//$member_profile['gender'] ? ($member_profile['gender']==1 ? '男' : '女') : '保密';//关注数
$data['department']   = $member_profile['field2'];//部门
$data['constellation']   = $member_profile['constellation'];//星座
$data['medals']   = $usermedalmenus;//勋章
$data['bloodtype'] = $member_profile['bloodtype'];//血型
$data['sightml'] = strip_tags($member_profile_forum['sightml']);//签名
*/

//dd($data);

WmApiError::display_result('ok', $data);

