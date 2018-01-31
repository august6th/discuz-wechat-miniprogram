<?php
require 'inc.php';

$token = $_POST['token'];
$result = WmApiLib::check_token($token);

$fid = $_POST['fid'];
if (!empty($fid) || !is_numeric($fid)) {
    WmApiError::display_result('param_error');
    exit();
}

$resp_data = array();
// 获取置顶列表
$sql_where = ' where displayorder=3 or (displayorder>0 and fid=' . $fid . ')';
$sql_limit = ' order by dateline desc';

$forum_thread_data = DB::fetch_all("SELECT * FROM " . DB::table('forum_thread') . $sql_where . $sql_limit);
foreach ($forum_thread_data as &$value) {
    // 修改字段
    $value['create_time'] = date('Y-m-d', $value['dateline']);
}

$resp_data['top_thread_data'] = $forum_thread_data;

// 获取版块信息
$forum_data = DB::fetch_first("SELECT * FROM " . DB::table('forum_forum') . " WHERE fid=" . $fid);
$resp_data['forum_data'] = $forum_data;

WmApiError::display_result('ok', $resp_data);