<?php
include_once './wmapi_config.php';
include_once './error.php';

class WmApiLib
{

    static function get_token($uid, $openid)
    {
        $time = time();
        $token = authcode("{$uid}\t{$time}\t{$openid}", 'ENCODE', WMAPI_KEY, WMAPI_KEY_EXPIRY);
        return $token;
    }

    static function decode_token($token)
    {
        if (empty($token)) {
            return 'token_empty';
        }
        $authstr = authcode($token, 'DECODE', WMAPI_KEY, WMAPI_KEY_EXPIRY);

        $result = array();
        list($result['uid'], $result['time'], $result['openid']) = daddslashes(explode("\t", $authstr));
        return $result;
    }

    static function check_token($token)
    {
        $result = self::decode_token($token);
        if (!is_array($result) || empty($result['openid'])) {
            WmApiError::display_result($result);
            exit();
        }
        return $result;
    }

    static function check_user($token)
    {
        global $_G;
        $user = self::check_token($token);
        if (!is_array($user) || empty($user['uid']) || empty($user['password']) || empty($user['time'])) {
            return 'token_error';
        }
        $uid = intval($user['uid']);
        $userinfo = DB::fetch_first("SELECT * FROM " . DB::table('common_member') . " WHERE uid={$uid}");
        if (empty($userinfo) || $userinfo['password'] !== $user['password']) {
            return 'token_error';
        }
        if ((time() - $user['time']) > WMAPI_KEY_EXPIRY) {
            return 'token_has_expired';
        }
        if ($userinfo['status'] != 0) {
            return 'user_status_excption';
        }
        if ($userinfo['groupid'] == 5) {
            return 'user_visit_been_banned';
        }
        $_G['uid'] = $uid;
        self::init_user();
        return 'token_check_successed';
    }

    static function init_user()
    {
        global $_G;
        $discuz_uid = $_G['uid'];

        if ($discuz_uid) {
            $user = getuserbyuid($discuz_uid, 1);
            if (isset($user['_inarchive'])) {
                C::t('common_member_archive')->move_to_master($discuz_uid);
            }
            $_G['member'] = $user;
            if ($user && $user['groupexpiry'] > 0 && $user['groupexpiry'] < TIMESTAMP) {
                '脱离了小组';
            }
        } else {
            $username = '';
            $groupid = 7;
            setglobal('member', array('uid' => 0, 'username' => $username, 'adminid' => 0, 'groupid' => $groupid, 'credits' => 0, 'timeoffset' => 9999));
        }

        $cachelist[] = 'usergroup_' . $_G['member']['groupid'];
        if ($user && $user['adminid'] > 0 && $user['groupid'] != $user['adminid']) {
            $cachelist[] = 'admingroup_' . $_G['member']['adminid'];
        }

        setglobal('groupid', getglobal('groupid', 'member'));
        !empty($cachelist) && loadcache($cachelist);

        setglobal('uid', getglobal('uid', 'member'));
        setglobal('username', getglobal('username', 'member'));
        setglobal('adminid', getglobal('adminid', 'member'));
        setglobal('groupid', getglobal('groupid', 'member'));

        if ($_G['member'] && $_G['group']['radminid'] == 0 && $_G['member']['adminid'] > 0 && $_G['member']['groupid'] != $_G['member']['adminid'] && !empty($_G['cache']['admingroup_' . $_G['member']['adminid']])) {
            $_G['group'] = array_merge($_G['group'], $_G['cache']['admingroup_' . $_G['member']['adminid']]);
        }
    }

    static function get_user_avatar($uid)
    {
        $size = 'middle';
        $uid = abs(intval($uid));
        $uid = sprintf("%09d", $uid);
        $dir1 = substr($uid, 0, 3);
        $dir2 = substr($uid, 3, 2);
        $dir3 = substr($uid, 5, 2);
        $avatar = 'data/avatar/' . $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . substr($uid, -2) . "_avatar_$size.jpg";

        if (!file_exists('../uc_server/' . $avatar)) {
            $avatar = 'images/noavatar_middle.gif';
        }
        $avatar_url = ($_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/get_image.php?type=avatar&file_url=' . $avatar;
        return $avatar_url;
    }

    static function set_user_avatar($uid, $avatar_url)
    {
        $uid = abs(intval($uid));
        $uid = sprintf("%09d", $uid);
        $dir1 = substr($uid, 0, 3);
        $dir2 = substr($uid, 3, 2);
        $dir3 = substr($uid, 5, 2);

        $tmp_dir = '../uc_server/data/avatar/' . $dir1;
        if (!file_exists($tmp_dir)) {
            mkdir($tmp_dir);
        }

        $tmp_dir = '../uc_server/data/avatar/' . $dir1 . '/' . $dir2;
        if (!file_exists($tmp_dir)) {
            mkdir($tmp_dir);
        }

        $tmp_dir = '../uc_server/data/avatar/' . $dir1 . '/' . $dir2 . '/' . $dir3;
        if (!file_exists($tmp_dir)) {
            mkdir($tmp_dir);
        }

        $content = file_get_contents($avatar_url);

        $size = 'big';
        $avatar = '../uc_server/data/avatar/' . $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . substr($uid, -2) . "_avatar_$size.jpg";
        file_put_contents($avatar, $content);

        {
            list($width, $height) = getimagesize($avatar);
            // 缩略图比例
            $percent = 1.0 * 300 / $width;

            // 缩略图尺寸
            $newwidth = $width * $percent;
            $newheight = $height * $percent;

            // 加载图像
            $src_im = @imagecreatefromjpeg($avatar);
            $dst_im = imagecreatetruecolor($newwidth, $newheight);

            // 调整大小
            imagecopyresized($dst_im, $src_im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

            $size = 'middle';
            $avatar = '../uc_server/data/avatar/' . $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . substr($uid, -2) . "_avatar_$size.jpg";
            //输出缩小后的图像
            imagejpeg($dst_im, $avatar);

            imagedestroy($dst_im);
            imagedestroy($src_im);
        }


        {
            list($width, $height) = getimagesize($avatar);
            // 缩略图比例
            $percent = 1.0 * 150 / $width;

            // 缩略图尺寸
            $newwidth = $width * $percent;
            $newheight = $height * $percent;

            // 加载图像
            $src_im = @imagecreatefromjpeg($avatar);
            $dst_im = imagecreatetruecolor($newwidth, $newheight);

            // 调整大小
            imagecopyresized($dst_im, $src_im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

            $size = 'small';
            $avatar = '../uc_server/data/avatar/' . $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . substr($uid, -2) . "_avatar_$size.jpg";
            //输出缩小后的图像
            imagejpeg($dst_im, $avatar);

            imagedestroy($dst_im);
            imagedestroy($src_im);
        }
    }

}
