<?php
require 'inc.php';

$token = $_POST['token'];
$result = WmApiLib::check_token($token);

$resp_data = array();
$forum_forum_data = DB::fetch_all("SELECT * FROM " . DB::table('forum_forum') . " WHERE status=1");

/* 过滤有阅读权限的版块 */
$filter = DB::fetch_all("SELECT fid FROM " . DB::table('forum_forumfield') . " WHERE viewperm > 0");
foreach ($filter as $forum) {
    for ($i = 0; $i < count($forum_forum_data); $i++) {
        if ($forum_forum_data[$i]['fid'] == $forum['fid']) {
            unset($forum_forum_data[$i]);
        }
    }
}

$forum_group = array();
foreach ($forum_forum_data as &$value) {
    if ($value['type'] == 'group') {
        array_push($forum_group, $value);
    }
}

foreach ($forum_group as &$value) {
    $sub_group = array();
    foreach ($forum_forum_data as &$sub_value) {
        if ($sub_value['fup'] == $value['fid']) {
            array_push($sub_group, $sub_value);
        }
    }
    $value['sub_group'] = $sub_group;
    if (!empty($sub_group)) {
        array_push($resp_data, $value);
    }
}

//dd($resp_data);
WmApiError::display_result('ok', $resp_data);


