// user.js
var app = getApp()

Page({

  /**
   * 页面的初始数据
   */
  data: {
    hasUserInfo: false,
    userInfo: {
      avatarUrl: "../../resources/image/user_icon.png",
      nickName: "游客"
    },
    loading_hidden: true,
    loading_msg: '加载中...'
  },

  onLoad: function () {

  },

  onShow: function() {
    var that = this;
    wx.request({
      url: getApp().globalData.svr_url + "get_user_info.php",
      method: "post",
      header: { "content-type": "application/x-www-form-urlencoded" },
      data: {
        token: wx.getStorageSync("token"),
      },
      success: function (resp) {
        console.log(resp);
        var resp_dict = resp.data;
        if (resp_dict.err_code == 0) {
          that.setData({
            hasUserInfo: true,
            userInfo: {
              avatarUrl: resp_dict.data.avatar,
              nickName: resp_dict.data.username
            }
          })
        } else {
          that.setData({
            hasUserInfo: false,
            userInfo: {
              avatarUrl: "../../resources/image/user_icon.png",
              nickName: "游客"
            }
          })
        }
      }
    });
  },

  wxlogin: function() {
    var that = this;
    console.log('wxLogin');
    that.setData({
      loading_hidden: false,
      loading_msg: '登录中...'
    })
    wx.getUserInfo({
      success: function (res) {
        console.log(res.userInfo);
        var username = res.userInfo.nickName;
        var avatar_url = res.userInfo.avatarUrl;
        wx.request({
          url: getApp().globalData.svr_url+'wx_login.php',
          method: 'POST',
          header: { "content-type": "application/x-www-form-urlencoded" },
          data: {
            token: wx.getStorageSync("token"),
            username: username,
            avatar_url: avatar_url
          },
          success: function(resp) {
            console.log(resp);
            var resp_dict = resp.data;
            if (resp_dict.err_code == 0) {
              wx.setStorage({
                key: 'token',
                data: resp.data.data.token,
                success: function() {
                  that.setData({
                    loading_hidden: true,
                  })
                  that.onShow();
                }
              })
            } else {
              getApp().showSvrErrModal(resp);
            }
          }
        })
      }
    });

  },

  logout: function() {
    var that = this;
    wx.request({
      url: getApp().globalData.svr_url+'logout.php',
      method: 'POST',
      header: { "content-type": "application/x-www-form-urlencoded" },
      data: {
        token: wx.getStorageSync("token"),
      },
      success: function(resp) {
        console.log(resp);
        var resp_dict = resp.data;
        if (resp_dict.err_code == 0) {
          wx.setStorage({
            key: 'token',
            data: resp_dict.data.token,
            success: function() {
              that.onShow();
            }
          })
          wx.setStorage({
            key: 'login',
            data: 0,
          })
        } else {
          getApp().showSvrErrModal(resp);
        }
      }
    })
  }

})