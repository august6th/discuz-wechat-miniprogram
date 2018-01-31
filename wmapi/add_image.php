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


$file_size = $_FILES['myfile']['size'];
if ($file_size > 4*1024*1024) {  
    WmApiError::display_result('file_too_big', '');
    exit();  
}  

$file_type = $_FILES['myfile']['type'];  
if ($file_type != "image/jpeg" 
        && $file_type != 'image/jpg' 
        && $file_type != 'image/jpeg' 
        && $file_type != 'image/png') {  
    WmApiError::display_result('file_type_error', '');
    exit();  
}  
  
if(!is_uploaded_file($_FILES['myfile']['tmp_name'])) 
{  
    WmApiError::display_result('file_upload_error', '');
    exit();
}

$base_file_path = getglobal('setting/attachdir').'./forum/';
$ym = date("Ym",time());
$file_path = $ym;
$full_file_path = $base_file_path.'/'.$file_path;
if (!file_exists($full_file_path)) 
{  
    mkdir($full_file_path);  
}  

$d = date("d",time());
$file_path = $file_path.'/'.$d;
$full_file_path = $base_file_path.'/'.$file_path;
if (!file_exists($full_file_path)) 
{  
    mkdir($full_file_path);  
}  

// 获取aid
$aid = DB::insert('forum_attachment', array('uid'=>$uid), 1);
if ($aid < 0)
{
    WmApiError::display_result('sys_error', '');
    exit();
}

$uid = getglobal('uid', 'member');
$file_name = $file_path.'/'.$uid.time().$aid.'.'.pathinfo($_FILES['myfile']['name'], PATHINFO_EXTENSION);;

$uploaded_file = $_FILES['myfile']['tmp_name'];  
$move_to_file = $base_file_path.'/'.$file_name;

if(!move_uploaded_file($uploaded_file, $move_to_file))
{
    WmApiError::display_result('file_upload_error', '');
	exit();
}

list($width, $height) = getimagesize($move_to_file);
if ($width > 500)
{
    // 缩略图比例
    $percent =  1.0*500/$width;
	WmApiError::runlog("percent:".$percent);

    // 缩略图尺寸
    $newwidth = $width * $percent;
    $newheight = $height * $percent;

	if ($file_type == "image/jpeg" || $file_type == 'image/jpg' || $file_type == 'image/pjpeg') 
	{
		// 加载图像
		$src_im = @imagecreatefromjpeg($move_to_file);
		$dst_im = imagecreatetruecolor($newwidth, $newheight);

		// 调整大小
		imagecopyresized($dst_im, $src_im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

		//输出缩小后的图像
		imagejpeg($dst_im, $move_to_file);

		imagedestroy($dst_im);
		imagedestroy($src_im);
	}

    if ($file_type == 'image/png') 
	{  
		// 加载图像
		$src_im = @imagecreatefrompng($move_to_file);
		$dst_im = imagecreatetruecolor($newwidth, $newheight);

		// 调整大小
		imagecopyresized($dst_im, $src_im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

		//输出缩小后的图像
		imagepng($dst_im, $move_to_file);

		imagedestroy($dst_im);
		imagedestroy($src_im);
	}

}


$array_attachment_unused = array(
		'aid' => $aid,
		'uid' => $uid,
		'dateline' => time(),
		'filename' => $_FILES['myfile']['name'],
		'filesize' => $_FILES['myfile']['size'],
		'attachment' => $file_name,
		'isimage' => 1,
		'width' => 500,
		);
DB::insert('forum_attachment_unused', $array_attachment_unused);


// 压缩图片


$data = array();
$data['aid'] = $aid;
$data['file_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/'.dirname($_SERVER['PHP_SELF']).'/get_image.php?file_url='.$file_name;
WmApiError::display_result('ok', $data);
