## 备份描述

> 备份源码在线上测试论坛进行过测试，主要有以下几个功能，考虑到有破坏的可能，特此备份。

### 2018.1.23

1. ```file_get_content()```  函数，在论坛上有可能无法使用，已经在 ***inc.php*** 文件中实现相同功能的

   ```get_url_content()```  函数。

   ```php
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
   ```

2. 由于论坛整体格式为 GBK，而和微信服务端交互，必须为 UTF-8 编码，所以对接口编码进行了一部分的优化。

   输出的情况下，有 ***error.php*** 下的 ```array_iconv()``` 函数，来对输出数组进行编码的转换。

   ```php
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
                   return mb_convert_encoding($str, $out_charset, $in_charset);
               } else {
                   return $str;
               }
           }
   ```

   接受的情况下, 有 ***inc.php*** 下的 ```getDataForCharset()``` 函数来整合接受编码。

   ```php
   function getDataForCharset($data)
   {
       return (CHARSET != 'UTF-8') ? dhtmlspecialchars(WmApiError::array_iconv($data)) : dhtmlspecialchars($data);
   }
   ```

3. 关闭了微信登陆的功能。

4. 获取帖子时新增了帖子的具体内容，message。

5. 优化了其他一些细节部分的内容。

### 2018.1.24

1. 输出帖子时，对 Discuz Code 进行解码，同时，获取图片附件，以及表情图片。
2. 接受时，解析文本的加粗、高亮、斜体、字体等。让帖子呈原样输出。
3. 以上功能，重点在 ***wmapi/get_post_detail.php*** 、 ***wmapi/get_self_post.php*** 两个脚本中重点突出。

### 2018.1.31

> 本次更新十分重要，是根据生产环境测试得来
>
> 本次记录内容页包含了小程序的部分修改

1. 部分获取小程序的数据的方式为 ```get_url_content()``` 部分为 ```file_get_content()``` （生产环境可能由于设置问题，导致如此怪异，请结合自身服务器来看）
2. 首页新增加载状态 （加载中... 没有更多... 暂无数据...）
3. 群组帖子，无法显示群组的名称
4. 修复中文无法登陆 bug （在小程序向服务器传递中文字符串的时候，先 ```encodeURI()``` 再到论坛服务器上 ```urldecode()``` 最后进行 UTF8 -> GBK 的转码，当然，是有必要的情况下。）
5. 首次登陆，无法直接加载（是因为小程序 app.js 中还有 微信登陆的残留代码在执行，关闭即可，否则会报错，参数错误。）


### 2018.2.1

1. 部分非图片附件，加载空白位占位
2. 部分图片附件以及头像图片加载为 http 而非 https

### 2018.2.3

1. 部分手机显示的头像被压扁
2. 个人回复获得的数据有误
3. 过滤带有权限控制的版块的帖子
4. 有非图片附件的帖子进行提示
5. 过滤隐藏贴以及密码贴的内容


### 2018.2.5

个人的失误，老代码覆盖了新代码，今天回复，并新增如下功能

1. 付费贴内容隐藏并提示
2. 回到顶部按钮

