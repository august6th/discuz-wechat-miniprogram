<?php
require 'inc.php';
require_once libfile('function/discuzcode');

$token = $_POST['token'];
$result = WmApiLib::check_token($token);
//$result['uid'] = 1;
if ($result['uid'] == 0) {
    WmApiError::display_result('user_no_login');
    exit();
}
$uid = $result['uid'];

$sql_where = ' where invisible>=0 and first=0 and authorid=' . $uid;

$page_size = $_POST['page_size'];
if (!empty($page_size)) {
    if (!is_numeric($page_size)) {
        WmApiError::display_result('param_error', '');
        exit;
    }
} else {
    $page_size = 100;
}

$page_index = $_POST['page_index'];
if (!empty($page_index)) {
    if (!is_numeric($page_index)) {
        WmApiError::display_result('param_error', '');
        exit;
    }
} else {
    $page_index = 0;
}

$sql_limit = ' order by dateline desc limit ' . ($page_index * $page_size) . ', ' . $page_size;

$resp_data = array();

$forum_post_data = DB::fetch_all("SELECT * FROM " . DB::table('forum_post') . $sql_where . $sql_limit);


foreach ($forum_post_data as $key => &$value) {

    $thread_data = DB::fetch_first("SELECT * FROM " . DB::table('forum_thread') . " where tid=" . $value['tid']);
    if ($thread_data['closed']) {
        unset($forum_post_data[$key]);
    } else {
        // 修改字段
//    dd($value);
//    dd($thread_data['tid']);
//    dd($value);
        $value['create_time'] = date('Y-m-d', $value['dateline']);
        $value['thread_subject'] = $thread_data['subject'];
        $value['message'] = discuzcode($value['message'], 0, 0, 0, 1, 1, 0, 0, 0, 0, 0);
    }
}
//dd($forum_post_data);

$resp_data['self_post_list'] = $forum_post_data;

//dd($resp_data);

WmApiError::display_result('ok', $resp_data);
