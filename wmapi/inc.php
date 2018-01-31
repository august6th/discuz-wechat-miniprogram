<?php

require '../source/class/class_core.php';
include_once '../source/function/function_home.php';

$discuz = C::app();
$cachelist = array('plugin', 'setting', 'heats', 'globalstick', 'magic', 'userapp', 'usergroups', 'diytemplatenamehome', 'medals');

$discuz->cachelist = $cachelist;
$discuz->init();

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
