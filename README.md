## 快速创建属于你自己的Discuz论坛
### 环境
本环境是自己在公司的生产环境，不同的环境，可能需要部分修改，才能实现。
1. PHP 5.2
2. IIS 7
3. Discuz X3.2
4. Windows Server 2008



### 安装方式

1. 将本仓库下的 wmapi 放到 Discuz 论坛的根目录下 ，访问 https://你的网址/wmapi/wmapi_install.php
2. 填写你的小程序 appid 以及 appsecret 进行安装（至此，论坛接口已安装完成）
3. wmapp 即为小程序源码，微信提供的开发者工具打开
4. 修改 app.js 下的 base_url 以及 svr_url 为你的网址

（其他细节部分，例如小程序的申请，请自行谷歌、或者百度）