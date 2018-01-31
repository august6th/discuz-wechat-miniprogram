<?php
require 'inc.php';

$can_install = true;
// 判断是否已经安装

function InstallWmApi($appid, $appsecret) {
	// 检查appid和appsecret
	$mp_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
//	echo $mp_url;
    $content = get_url_content($mp_url);
	$content_json =  json_decode($content);
	if (empty($content_json->{'access_token'}))
	{
		echo "appid或者appsecret无效";
	    exit();
	}

	// 修改配置文件
	$wmapi_key = WMAPI_KEY;
	if (empty($wmapi_key)) {
		$pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
		for ($i=0; $i<16; $i++)   
		{   
			$wmapi_key .= $pattern{mt_rand(0,35)};    //生成php随机数   
		}   
	}

	$wmapi_config = fopen("wmapi_config.php", "w")  or die("Unable to open file!");
	$wmapi_config_msg = "<?php
define('WMAPI_KEY', '".$wmapi_key."');
define('WMAPI_KEY_EXPIRY', 7200);
define('WMAPI_APPID', '".$appid."');
define('WMAPI_APPSECRET', '".$appsecret."');
";
	fwrite($wmapi_config, $wmapi_config_msg);
	fclose($wmapi_config);

	// 创建数据表
	$wmapi_user_sql = '
		CREATE TABLE IF NOT EXISTS '.DB::table('wmapi_user').' (
				wm_uid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
				uid mediumint(8) unsigned NOT NULL,
				openid varchar(255) NOT NULL,
				nickname varchar(255) NOT NULL default "",
				logintime int(10) unsigned NOT NULL,
				dateline int(10) unsigned NOT NULL,
				PRIMARY KEY (wm_uid),
				UNIQUE KEY `uid` (`uid`)
				)';
	DB::query($wmapi_user_sql);

	// 写入install.lock
	$install_lock = fopen("./data/install.lock", "w")  or die("Unable to open file!");
	fwrite($install_lock, time());
	fclose($install_lock);
}

if ($_POST['appid'] && $_POST['appsecret'])
{
	InstallWmApi($_POST['appid'], $_POST['appsecret']);
}

$filename = './data/install.lock';
if (is_writable($filename)) {
	echo "wmdz已经安装成功";
	exit;
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Language" content="zh-CN" />
    <title>wmdz安装</title>
	<style type="text/css">
		.green {color: green}
		.red {color: red}
	</style>
</head>

<body style="text-align:center">
	<div style="margin-left:auto; margin-right:auto; margin-top: 100px; width:800px">
	<h3>wmdz安装</h3>
	<table border="1px" width="800px">
		<tr>
			<td>discuz是否已安装</td>
			<td>
				<?php 
					if (file_exists("../data/install.lock")) {
						echo "<div class='green'>yes</div>";
					} else {
						echo "<div class='red'>no</div>";
						$can_install = false;
					}
				?>
			</td>
		</tr>

		<tr>
			<td>wmapi_config.php配置文件是否可写</td>
			<td>
				<?php 
					$filename = 'wmapi_config.php';
					if (is_writable($filename)) {
						echo "<div class='green'>yes</div>";
					} else {
						echo "<div class='red'>no</div>";
						$can_install = false;
					}
				?>
			</td>
		</tr>

		<tr>
			<td>data目录是否可写</td>
			<td>
				<?php 
					$filename = './data';
					if (is_writable($filename)) {
						echo "<div class='green'>yes</div>";
					} else {
						echo "<div class='red'>no</div>";
						$can_install = false;
					}
				?>
			</td>
		</tr>

	</table>
	<br/>

	<form method="post" action="wmapi_install.php">
	<table border="1px" width="800px">
		<tr>
			<td>小程序的appid</td>
			<td><input type="text" name="appid"></td>
		</tr>
		<tr>
			<td>小程序的appsecret</td>
			<td><input type="text" name="appsecret"></td>
		</tr>
	</table>
	<?php
		if ($can_install)  {
			echo '<input type="submit" value="安装">';
		}
	?>
	</form>


	</div>

</body>
</html>
