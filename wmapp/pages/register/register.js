// pages/login/login.js
Page({

  /**
   * 页面的初始数据
   */
  data: {
    username: '',
    password: '',
    email: '',
  },

  inputUsername: function(e) {
    this.setData({
      username: e.detail.value
    });
  },

  inputPassword: function(e) {
    this.setData({
      password: e.detail.value
    });
  },

  inputEmail: function(e) {
    this.setData({
      email: e.detail.value
    });
  },
  
  clickRegister: function(e) {
    var that = this;

    var username = that.data.username; 
    if (username == null || username == undefined || username == ''){
      getApp().showErrModal('账号不能为空');
      return;
    }

    var password = that.data.password; 
    if (password == null || password == undefined || password == ''){
      getApp().showErrModal('密码不能为空');
      return;
    }

    var email = that.data.email; 
    if (email == null || email == undefined || email == ''){
      getApp().showErrModal('邮箱不能为空');
      return;
    }

    wx.request({
      url: getApp().globalData.svr_url + "register.php",
      method: "post",
      header: { "content-type": "application/x-www-form-urlencoded" },
      data: {
        token: wx.getStorageSync("token"),
        username: that.data.username,
        password: that.data.password,
        email: that.data.email
      },
      success: function (resp) {
        console.log(resp);
        var resp_dict = resp.data;
        if (resp_dict.err_code == 0) {
          wx.showToast({
            title: '注册成功',
          });
          wx.setStorage({
            key: 'token',
            data: resp_dict.data.token,
          });

          wx.switchTab({
            url:"../user/user"
          });  
        } else {
          getApp().showSvrErrModal(resp);
        }
      }
    })
  }

})