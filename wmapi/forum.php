<?php
require 'inc.php';

$token = $_POST['token'];
$result = WmApiLib::check_token($token);

$resp_data = array();
$forum_forum_data = DB::fetch_all("SELECT * FROM ".DB::table('forum_forum')." WHERE status=1");

$forum_group = array();
foreach ($forum_forum_data as &$value) 
{
	if ($value['type'] == 'group')
	{
		array_push($forum_group, $value);
	}
}

foreach ($forum_group as &$value) 
{
	$sub_group = array();
	foreach ($forum_forum_data as &$sub_value) 
	{
		if ($sub_value['fup'] == $value['fid'])
		{
			array_push($sub_group, $sub_value);
		}
	}
	$value['sub_group'] = $sub_group;
}

WmApiError::display_result('ok',$forum_group);


