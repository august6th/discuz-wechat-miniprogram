// pages/self_thread/self_thread.js
var app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    articleList: [],
    page_size: 5,
    page_index: 0,
    lite_switch: app.globalData.lite_switch
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.reloadIndex();
  },

  onShow: function () {
  },

  reloadIndex: function() {
    var that = this;
    var tmpArticleList = [];  
    var page_size = that.data.page_size;
    var page_index = 0;
    wx.request({
      url: getApp().globalData.svr_url + "get_self_thread.php",
      method: "post",
      header: { "content-type": "application/x-www-form-urlencoded" },
      data: {
        token: wx.getStorageSync("token"),
        page_size: page_size,
        page_index: page_index
      },
      success: function (resp) {
        console.log(resp);
        var resp_dict = resp.data;
        if (resp_dict.err_code == 0) {
          that.setData({
            articleList: resp_dict.data.forum_thread_data,
            page_index: page_index
          })
        } else {
          getApp().showSvrErrModal(resp);
        }
      }
    })
  },

  onReachBottom: function() {
    var that = this;
    var page_size = that.data.page_size;
    var page_index = that.data.page_index+1;
    wx.request({
      url: getApp().globalData.svr_url + "get_self_thread.php",
      method: "post",
      header: { "content-type": "application/x-www-form-urlencoded" },
      data: {
        token: wx.getStorageSync("token"),
        page_size: page_size,
        page_index: page_index
      },
      success: function (resp) {
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

          if (has_append == 1)
          {
            that.setData({
              articleList: tmpArticleList,
              page_index: page_index  
            })
          }
        } else {
          getApp().showSvrErrModal(resp);
        }
      }
    })
  },

  toDetail: function (e) {
    console.log(e);
    var tid = e.currentTarget.dataset.tid;
    wx.navigateTo({
      url: '../detail/detail?tid='+tid,
    })
  },
})