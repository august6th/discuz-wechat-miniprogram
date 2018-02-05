<?php
require 'inc.php';
require_once libfile('function/discuzcode');


$token = $_POST['token'];
$result = WmApiLib::check_token($token);
if ($result['uid'] == 0)
{
    WmApiError::display_result('user_no_login');
    exit();
}
$uid = $result['uid'];

$sql_where = ' where displayorder>=0 and closed = 0 and authorid='.$uid;

$order_field = 'dateline';

$page_size = $_POST['page_size'];
if (!empty($page_size))
{
    if(!is_numeric($page_size))
    {
        WmApiError::display_result('param_error', '');
        exit;
    }
}
else
{
    $page_size = 5;
}

$page_index = $_POST['page_index'];
if (!empty($page_index))
{
    if(!is_numeric($page_index))
    {
        WmApiError::display_result('param_error', '');
        exit;
    }
}
else
{
    $page_index = 0;
}

$sql_limit = ' order by '.$order_field.' desc limit '.($page_index*$page_size).', '.$page_size;

$resp_data = array();

$forum_thread_data = DB::fetch_all("SELECT * FROM ".DB::table('forum_thread').$sql_where.$sql_limit);

foreach ($forum_thread_data as &$value) 
{
	$forum_post_data = DB::fetch_first("SELECT * FROM ".DB::table('forum_post')." where first=1 and tid=".$value['tid']);
	$pid = $forum_post_data['pid'];	

	$image_list = array();
	// 获取图片
	/*
    if ($value['attachment'] == 2) {
        $forum_attachment = DB::fetch_all("SELECT * FROM ".DB::table('forum_attachment_'.($value['tid']%10))." where pid=".$pid);
		foreach ($forum_attachment as &$image_item) 
		{
			$image_url = 'http://'.$_SERVER['HTTP_HOST'].'/'.dirname($_SERVER['PHP_SELF']).'/get_image.php?file_url='.$image_item['attachment'];
			array_push($image_list, $image_url);
		}
		$value['image_list'] = $image_list;
    }
	*/

	// 获取版块
	$forum_forum_data = DB::fetch_first("SELECT * FROM ".DB::table('forum_forum')." WHERE status=1 and fid=".$value['fid']);
	if ($forum_forum_data)
	{
		$value['fid_name'] = $forum_forum_data['name'];
	}

	// 修改字段
	$value['create_time'] = date('Y-m-d', $value['dateline']);
}

$resp_data['forum_thread_data'] = $forum_thread_data;

WmApiError::display_result('ok', $resp_data);
