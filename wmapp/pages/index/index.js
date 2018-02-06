//index.js
//获取应用实例
var app = getApp()
var WxParse = require('../../wxParse/wxParse.js');
Page({
  data: {
    articleList: [],
    page_size: 10,
    page_index: 0,
    loading_hidden: true,
    loading_msg: '加载中...',
    scroll_show: false,
    no_data: false,
    have_data: false,
    nomore_data: false,
    lite_switch: app.globalData.lite_switch,
  },

  onLoad: function () {
    var that = this;
    wx.checkSession({
      success: function () {
        console.log('check success');
        var token = app.getSt("token", '');
        console.log(token);
        if (token == null || token == undefined || token == '') {
          wx.login({
            success: function (res) {
              if (res.code) {
                //console.log(res);
                wx.request({
                  url: getApp().globalData.svr_url + 'get_token.php',
                  method: 'POST',
                  header: { "content-type": "application/x-www-form-urlencoded" },
                  data: {
                    code: res.code,
                  },
                  success: function (resp) {
                    console.log(resp);
                    var resp_dict = resp.data;
                    if (resp_dict.err_code == 0) {
                      console.log('Set token...');
                      app.putSt('token', resp.data.data.token, 7100);
                      that.reloadIndex();
                    } else {
                      getApp().showSvrErrModal(resp);
                    }
                  }
                })
              } else {
                console.log('获取用户登录态失败！' + res.errMsg)
              }
            }
          });
        } else {
          that.reloadIndex();
        }
      },
      fail: function () {
        console.log('check fail');
        wx.login({
          success: function (res) {
            if (res.code) {
              //console.log(res);
              wx.request({
                url: getApp().globalData.svr_url + 'get_token.php',
                method: 'POST',
                header: { "content-type": "application/x-www-form-urlencoded" },
                data: {
                  code: res.code,
                },
                success: function (resp) {
                  console.log(resp);
                  var resp_dict = resp.data;
                  if (resp_dict.err_code == 0) {
                    console.log('Set token...');
                    app.putSt('token', resp.data.data.token, 7100);
                    that.reloadIndex();
                  } else {
                    getApp().showSvrErrModal(resp);
                  }
                }
              })
            } else {
              console.log('获取用户登录态失败！' + res.errMsg)
            }
          }
        });
      }
    })
  },

  toDetail: function (e) {
    console.log(e);
    var tid = e.currentTarget.dataset.tid;
    wx.navigateTo({
      url: '../detail/detail?tid=' + tid,
    })
  },

  onReachBottom: function () {
    var that = this;
    var page_size = that.data.page_size;
    var page_index = that.data.page_index + 1;
    wx.request({
      url: getApp().globalData.svr_url + "get_thread.php",
      method: "post",
      header: { "content-type": "application/x-www-form-urlencoded" },
      data: {
        token: wx.getStorageSync("token"),
        page_size: page_size,
        page_index: page_index
      },
      success: function (resp) {
        console.log('Load more threads...');
        console.log(resp);
        var resp_dict = resp.data;
        if (resp_dict.err_code == 0) {
          var tmpArticleList = that.data.articleList;
          var respArticleList = resp_dict.data.forum_thread_data;
          var has_append = 0;
          for (var i = 0; i < respArticleList.length; ++i) {
            var has_in = 0;
            for (var j = 0; j < tmpArticleList.length; ++j) {
              if (respArticleList[i].tid == tmpArticleList[j].tid) {
                has_in = 1;
              }
            }
            if (has_in == 0) {
              tmpArticleList.push(respArticleList[i]);
              has_append = 1;
            }
          }

          if (has_append == 1) {
            that.setData({
              articleList: tmpArticleList,
              page_index: page_index,
              have_data: true,
              nomore_data: false,
            })
          } else {
            that.setData({
              have_data: false,
              nomore_data: true,
            })
          }
        } else {
          getApp().showSvrErrModal(resp);
        }
      }
    })
  },

  reloadIndex: function () {
    var that = this;
    var tmpArticleList = [];
    var page_size = that.data.page_size;
    var page_index = 0;
    this.setData({
      loading_hidden: false,
      loading_msg: '加载中...'
    });
    wx.request({
      url: getApp().globalData.svr_url + "get_thread.php",
      method: "post",
      header: { "content-type": "application/x-www-form-urlencoded" },
      data: {
        token: wx.getStorageSync("token"),
        page_size: page_size,
        page_index: page_index
      },
      success: function (resp) {
        console.log('Load threads...');
        console.log(resp);
        var resp_dict = resp.data;
        if (resp_dict.err_code == 0) {
          that.setData({
            articleList: resp_dict.data.forum_thread_data,
            page_index: page_index,
            have_data: true,
            loading_hidden: true,
            loading_msg: '加载完毕...'
          })
        } else {
          getApp().showSvrErrModal(resp);
        }
      }
    })
  },

  onShow: function () {
    if (wx.getStorageSync("reload_index") == 1) {
      this.reloadIndex();
      wx.setStorage({
        key: 'reload_index',
        data: 0,
      })
    }
  },

  onPullDownRefresh: function () {
    console.log('onPullDownRefresh');
    this.reloadIndex();
    wx.stopPullDownRefresh();
  },

  onShareAppMessage: function (res) {
    return {
      title: "",
      path: '/pages/index/index',
      success: function (res) {
        console.log(res);
      },
    }
  },
  onPageScroll: function (e) {
    if (e.scrollTop >= 600) {
      this.setData({
        scroll_show: true
      })
    } else {
      this.setData({
        scroll_show: false
      })
    }
  },
  scrollToTop: function () {
    if (wx.pageScrollTo) {
      wx.pageScrollTo({
        scrollTop: 0,
        duration: 600
      })
    } else {
      wx.showModal({
        title: '提示',
        content: '当前微信版本过低，无法使用该功能，请升级到最新微信版本后重试。'
      })
    }
    this.setData({
      scroll_show: false
    })
  }
})
