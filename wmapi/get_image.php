<?php
require 'inc.php';

$file_url = $_GET['file_url'];

$file_type = pathinfo($file_url, PATHINFO_EXTENSION);

if ($file_type == "jpeg" or $file_type == 'jpg') {
    header('Content-Type: image/jpeg');
} else if ($file_type == 'png') {
    header('Content-Type: image/png');
} else if ($file_type == 'gif') {
    header('Content-Type:image/gif');
} else {
    WmApiError::display_result('file_type_error', '');
    exit();
}

$base_file_path = getglobal('setting/attachdir') . './forum/';

$type = $_GET['type'];
if (!empty($type) && $type == 'avatar') {
    $base_file_path = dirname(__FILE__) . '/../uc_server/';
}

$move_to_file = $base_file_path . '/' . $file_url;
$file_content = file_get_contents($move_to_file);
echo $file_content;
