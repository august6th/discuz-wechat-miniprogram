<?php
include_once '../source/function/function_home.php';

class WmApiError
{
    static $_wm_global_error_msg = array(
        'ok' => 'ok',
        'sys_error' => '系统错误',
        'param_error' => '参数错误',
    	'user_no_login' => '用户没有登录',
        'user_login_failed' => '登录失败',
		'token_error' => 'token错误',
		'token_has_expired' => 'token过期',
		'user_status_excption' => '用户状态异常',
		'user_visit_been_banned' => '用户被禁止访问',
		'token_check_successed' => 'token检查成功',
		'file_too_big' => '文件太大',
		'file_type_error' => '文件类型不支持',
		'file_upload_error' => '文件上传失败',
		'param_code_error' => '参数code错误',
		'token_empty' => 'token不能为空',
		'profile_username_illegal' => '用户名不合法',
		'profile_username_protect' => '用户名不合法',
		'profile_username_duplicate' => '用户名已存在',
		'profile_email_domain_illegal' => '邮箱不合法',
		'profile_email_duplicate' => '邮箱已存在',
    ); 
    
    static $_wm_global_error_code = array(
        'ok' => 0,
        'sys_error' => -1,
        'param_error' => 1,
    	'user_no_login' => 10001,
        'user_login_failed' => 10002,
		'token_error' => 10003,
		'token_has_expired' => 10004,
		'user_status_excption' => 10005,
		'user_visit_been_banned' => 10006,
		'token_check_successed' => 10007,
		'file_too_big' => 10008,
		'file_type_error' => 10009,
		'file_upload_error' => 10010,
		'param_code_error' => 10011,
		'token_empty' => 10012,
		'profile_username_illegal' => 10013,
		'profile_username_protect' => 10014,
		'profile_username_duplicate' => 10015,
		'profile_email_domain_illegal' => 10016,
		'profile_email_duplicate' => 10017,
    );

	static function display_result($error, $data=array())
	{
		
	    if (CHARSET != 'UTF-8') {
            $data = WmApiError::array_iconv($data, CHARSET, 'UTF-8');
        }
        $result = array();
        $result['err_code'] = -1;
        $result['err_msg'] = '数据正在初始化...';

        $result['data'] = array();
        if(isset(self::$_wm_global_error_code[$error]))
        {
            $result['err_code'] = self::$_wm_global_error_code[$error];
            $result['err_msg'] = self::$_wm_global_error_msg[$error];
        }

        if($error == 'ok' and !empty($data))
        {
            $result['data'] = $data;
        }
        header('Content-Type: application/json');
        //$req_data = get_url_content("php://input");
		$req_data = file_get_contents("php://input");

        $resp_data = json_encode($result);
		self::runlog("req_data: ".$req_data." resp_data: ".$resp_data);
		echo $resp_data;
	}

	static function runlog($log_str)
	{
		runlog('wmapi', $log_str);
	}

    static function convertToUtf($str) {
        return urlencode(diconv($str, CHARSET, 'UTF-8'));
    }

    static function array_iconv($str, $in_charset = "UTF-8", $out_charset = CHARSET)
    {
        if (is_array($str)) {
            foreach ($str as $k => $v) {
                $str[$k] = WmApiError::array_iconv($v, $in_charset, $out_charset);
            }
            return $str;
        } else {
            if (is_string($str)) {
                // return iconv('UTF-8', 'GBK//IGNORE', $str);
				//return diconv($str, $out_charset, $in_charset);
                return mb_convert_encoding($str, $out_charset, $in_charset);

            } else {
                return $str;
            }
        }
    }

};
