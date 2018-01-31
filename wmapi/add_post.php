<?php
require 'inc.php';

$token = $_POST['token'];
$result = WmApiLib::check_token($token);
if ($result['uid'] == 0)
{
    WmApiError::display_result('user_no_login');
    exit();
}
$uid = $result['uid'];

if (empty($_POST['message']) || empty($_POST['tid']))
{
    WmApiError::display_result('param_error');
    exit();
}

// 检查参数
$tid = $_POST['tid'];
$message = getDataForCharset(urldecode($_POST['message']));

//用户信息
$member = DB::fetch_first("SELECT * FROM ".DB::table('common_member')." WHERE uid='".$result['uid']."'");
if(!$member) {
	WmApiError::display_result('user_login_failed');
    exit();
}

$author = $member['username'];

$wmapi_user = DB::fetch_first("SELECT * FROM ".DB::table('wmapi_user')." WHERE uid=".$uid);
if ($wmapi_user && !empty($wmapi_user['nickname']))
{
	$author = $wmapi_user['nickname'];
}

$thread_data = DB::fetch_first("select * from ".DB::table('forum_thread')." where tid=".$tid);

$fid = $thread_data['fid'];
$authorid = $result['uid'];
$dateline = time();

// 获取tid
$pid = DB::insert('forum_post_tableid', array('pid'=>null), 1);
if ($pid < 0)
{
    WmApiError::display_result('sys_error', '');
    exit();
}

// 添加贴子
$insert_post = array(
		'pid' => $pid,
		'fid' => $fid,
		'tid' => $tid,
		'first' => 0,
		'author' => $author,
		'authorid' => $authorid,
		'dateline' => $dateline,
		'message' => $message,
		'status' => 0
		);

$post_id = DB::insert('forum_post', $insert_post, 1);

// 更新版块
DB::query("update ".DB::table('forum_forum').' set posts=posts+1 where fid='.$fid);

// 更新主题
DB::query("update ".DB::table('forum_thread').' set replies=replies+1 where tid='.$tid);

$data = array();
$data['pid'] = $pid;
WmApiError::display_result('ok',$data);
