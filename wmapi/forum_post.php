<?php
require 'inc.php';

$token = $_POST['token'];
$result = WmApiLib::check_token($token);

$sql_where = ' where 1 = 1 ';

$is_first = $_GET['is_first'];
if ($is_first != 0) {
    $sql_where = $sql_where . ' and first = 1';
}

$fid = $_GET['fid'];
if (!empty($fid)) {
    if (!is_numeric($fid)) {
        WmApiError::display_result('param_error', '');
        exit;
    } else {
        $sql_where = $sql_where . ' and fid=' . $fid;
    }
}

$tid = $_GET['tid'];
if (!empty($tid)) {
    if (!is_numeric($tid)) {
        WmApiError::display_result('param_error', '');
        exit;
    } else {
        $sql_where = $sql_where . ' and fid=' . $tid;
    }
}

$page_size = $_GET['page_size'];
if (!empty($page_size)) {
    if (!is_numeric($page_size)) {
        WmApiError::display_result('param_error', '');
        exit;
    }
} else {
    $page_size = 5;
}

$page_index = $_GET['page_index'];
if (!empty($tid)) {
    if (!is_numeric($tid)) {
        WmApiError::display_result('param_error', '');
        exit;
    }
} else {
    $page_index = 0;
}

$sql_limit = ' limit ' . ($page_index * $page_size) . ', ' . $page_size;

$resp_data = array();

$forum_post_data = DB::fetch_all("SELECT * FROM " . DB::table('forum_post') . $sql_where . $sql_limit);

foreach ($forum_post_data as &$value) {
    // 获取图片
    if ($value['attachment'] != 0) {
        $value['attach_list'] = DB::fetch_all("SELECT * FROM " . DB::table('forum_attachment_' . ($tid % 10)));
    }

    // 获取版块
    $forum_forum_data = DB::fetch_first("SELECT * FROM " . DB::table('forum_forum') . " WHERE status=1 and fid=" . $value['fid']);
    if ($forum_forum_data) {
        $value['fid_name'] = $forum_forum_data['name'];
    }

    // 修改字段
    $value['create_time'] = date('Y-m-d', $value['dateline']);
}

$resp_data['forum_post_data'] = $forum_post_data;

WmApiError::display_result('ok', $resp_data);
