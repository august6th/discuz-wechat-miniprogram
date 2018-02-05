<?php

require '../source/class/class_core.php';
include_once '../source/function/function_home.php';

$discuz = C::app();
$cachelist = array('plugin', 'setting', 'heats', 'globalstick', 'magic', 'userapp', 'usergroups', 'diytemplatenamehome', 'medals');

$discuz->cachelist = $cachelist;
$discuz->init();

//dd($_SERVER['HTTPS']);
$http_type = $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';

include_once 'wmapi_config.php';
include_once 'error.php';
include_once 'lib.php';

function getDataForCharset($data)
{
    return (CHARSET != 'UTF-8') ? dhtmlspecialchars(WmApiError::array_iconv($data)) : dhtmlspecialchars($data);
}

function get_url_content($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    # curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

    if (!curl_exec($ch)) {
        error_log(curl_error($ch));
        $data = '';
    } else {
        $data = curl_multi_getcontent($ch);
    }
    curl_close($ch);
    return $data;
}

function mini_pro_messagesafeclear($message) {
	if(strpos($message, '[/password]') !== FALSE) {
		$message = '';
	}
	if(strpos($message, '[/postbg]') !== FALSE) {
		$message = preg_replace("/\s?\[postbg\]\s*([^\[\<\r\n;'\"\?\(\)]+?)\s*\[\/postbg\]\s?/is", '', $message);
	}
	if(strpos($message, '[/begin]') !== FALSE) {
		$message = preg_replace("/\[begin(=\s*([^\[\<\r\n]*?)\s*,(\d*),(\d*),(\d*),(\d*))?\]\s*([^\[\<\r\n]+?)\s*\[\/begin\]/is", '', $message);
	}
	if(strpos($message, '[page]') !== FALSE) {
		$message = preg_replace("/\s?\[page\]\s?/is", '', $message);
	}
	if(strpos($message, '[/index]') !== FALSE) {
		$message = preg_replace("/\s?\[index\](.+?)\[\/index\]\s?/is", '', $message);
	}
	if(strpos($message, '[/begin]') !== FALSE) {
		$message = preg_replace("/\[begin(=\s*([^\[\<\r\n]*?)\s*,(\d*),(\d*),(\d*),(\d*))?\]\s*([^\[\<\r\n]+?)\s*\[\/begin\]/is", '', $message);
	}
	if(strpos($message, '[/groupid]') !== FALSE) {
		$message = preg_replace("/\[groupid=\d+\].*\[\/groupid\]/i", '', $message);
	}
	$language = lang('forum/misc');
	$message = preg_replace(array($language['post_edithtml_regexp'],$language['post_editnobbcode_regexp'],$language['post_edit_regexp']), '', $message);
	$language = lang('forum/misc');
	loadcache(array('bbcodes_display', 'bbcodes', 'smileycodes', 'smilies', 'smileytypes', 'domainwhitelist'));
	$bbcodesclear = 'attach'.($_G['cache']['bbcodes_display'][$_G['groupid']] ? '|'.implode('|', array_keys($_G['cache']['bbcodes_display'][$_G['groupid']])) : '');
	$message = strip_tags(preg_replace(array(
			"/\[hide=?\d*\](.*?)\[\/hide\]/is",
			"/\[($bbcodesclear)=?.*?\].+?\[\/\\1\]/si",
		), array(
			"[b]$language[post_hidden][/b]",
			'',
		), $message));
	return trim($message);
}