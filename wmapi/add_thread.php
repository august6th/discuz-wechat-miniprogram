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

// 检查参数
$fid = $_POST['fid'];
$authorid = $result['uid'];
$subject = getDataForCharset($_POST['subject']);
$message = getDataForCharset($_POST['message']);
$dateline = time();
$lastpost = time();
$lastposter = $member['username'];
$status = 32;
$aid_list = $_POST['aid_list'];

if (empty($fid) || empty($subject) || empty($message))
{
    WmApiError::display_result('param_error');
    exit();
}

$attachment = 0;
if (!empty($aid_list))
{
	$attachment = 2;
}

// 添加主题
$insert_thread = array(
		'fid' => $fid,
		'author' => $author,
		'authorid' => $authorid,
		'subject' => $subject,
		'dateline' => $dateline,
		'lastpost' => $lastpost,
		'lastposter' => $lastposter,
		'status' => $status,
		'attachment' => $attachment
		);

$tid = DB::insert('forum_thread', $insert_thread, 1);
if ($tid < 0)
{
    WmApiError::display_result('sys_error', '');
    exit();
}

// 获取pid
$pid = DB::insert('forum_post_tableid', array('pid'=>null), 1);
if ($pid < 0)
{
    WmApiError::display_result('sys_error', '');
    exit();
}

// 添加主贴
$insert_post = array(
		'pid' => $pid,
		'fid' => $fid,
		'tid' => $tid,
		'first' => 1,
		'author' => $author,
		'authorid' => $authorid,
		'subject' => $subject,
		'dateline' => $dateline,
		'message' => $message,
		'status' => 0,
		'attachment' => $attachment
		);

$post_id = DB::insert('forum_post', $insert_post, 1);
if ($post_id < 0)
{
    WmApiError::display_result('sys_error', '');
    exit();
}


// 保持图片
if (!empty($aid_list))
{
	$array_aid_list = explode(',', $aid_list);
	foreach ($array_aid_list as &$value) 
	{
		$aid = $value;
		$forum_attachment_unused = DB::fetch_first("SELECT * FROM ".DB::table('forum_attachment_unused').' where aid='.$aid);

		$array_forum_attachment = array(
				'aid' => $aid, 
				'uid' => $uid, 
				'tid' => $tid,
				'pid' => $pid,
				'tableid' => $pid%10,
				);
		DB::insert('forum_attachment', $array_forum_attachment, 1, 1);

		$array_threadimage = array(
				'tid' => $tid,
				'attachment' => $forum_attachment_unused['attachment'],
				);
		DB::insert('forum_threadimage', $array_threadimage);

		$array_forum_attachment = array(
				'aid' => $aid,
				'tid' => $tid,
				'pid' => $pid,
				'uid' => $uid,
				'dateline' => time(),
				'filename' => $forum_attachment_unused['filename'],
				'filesize' => $forum_attachment_unused['filesize'],
				'attachment' => $forum_attachment_unused['attachment'],
				'isimage' => 1,
				'width' => 500,
				);
		DB::insert('forum_attachment_'.$tid%10, $array_forum_attachment);
	}
}

// 更新版块
DB::query("update ".DB::table('forum_forum').' set posts=posts+1, threads=threads+1 where fid='.$fid);

$data = array();
$data['tid'] = $tid;
$data['pid'] = $pid;
WmApiError::display_result('ok',$data);
