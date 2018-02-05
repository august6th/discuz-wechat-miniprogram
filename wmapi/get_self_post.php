<?php
require 'inc.php';
require_once libfile('function/discuzcode');
require_once libfile('function/post');

$token = $_POST['token'];
$result = WmApiLib::check_token($token);
//$result['uid'] = 1;
if ($result['uid'] == 0) {
    WmApiError::display_result('user_no_login');
    exit();
}
$uid = $result['uid'];

$viewperms = C::t('forum_forumfield')->fetch_all_field_perm();
$unselect_forum = array();
foreach ($viewperms as $viewperm) {
	if ($viewperm['viewperm']) {
		array_push($unselect_forum, $viewperm['fid']);
	}
}

if (!empty($unselect_forum)) {
	$sql_not_in = " and fid not in ('" . implode("','", $unselect_forum) . "')"; 
	$sql_where = ' where invisible>=0 and first=0 and authorid=' . $uid . $sql_not_in;
} else {
	$sql_where = ' where invisible>=0 and first=0 and authorid=' . $uid;
}

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

    $thread_data = DB::fetch_first("SELECT * FROM " . DB::table('forum_thread') . " where closed = 0 and tid=" . $value['tid']);
		// 修改字段
//    dd($value);
//    dd($thread_data['tid']);
//    dd($value);
        $value['create_time'] = date('Y-m-d', $value['dateline']);
        $value['thread_subject'] = $thread_data['subject'];
        $value['message'] = discuzcode(mini_pro_messagesafeclear($value['message']), 0, 0, 0, 1, 1, 0, 0, 0, 0, 0);
}
//dd($forum_post_data);

$resp_data['self_post_list'] = $forum_post_data;

//dd($resp_data);

WmApiError::display_result('ok', $resp_data);
