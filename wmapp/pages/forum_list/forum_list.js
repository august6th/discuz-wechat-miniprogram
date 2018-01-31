var app = getApp()
Page({
  data: {
    fid: 0,
    articleList: [],
    page_size: 5,
    page_index: 0,
    recent: 1,
    digest: 0,
    order_by_views: 0,
    has_top: 0,
    reload_index: 1,
    no_data:false,
    have_data: false,
    nomore_data : false,
    lite_switch: app.globalData.lite_switch,
  },

  onLoad: function (options) {
    console.log(options);
    var fid = options.fid;
    console.log(fid);
    this.setData({
      fid: fid,
    })
    this.reloadIndex();
    this.get_forum_info();
  },

  onShow: function () {
    if (this.data.reload_index == 1) {
      this.reloadIndex();
      this.get_forum_info();
      this.setData({
        reload_index: 0
      });
    }
  },

  toDetail: function (e) {
    console.log(e);
    var pid = e.currentTarget.dataset.pid;
    var tid = e.currentTarget.dataset.tid;
    wx.navigateTo({
      url: '../detail/detail?pid=' + pid + '&tid=' + tid,
    })
  },

  reloadIndex: function () {
    var that = this;
    var tmpArticleList = [];
    var page_size = that.data.page_size;
    var page_index = 0;
    wx.request({
      url: getApp().globalData.svr_url + "get_thread.php",
      method: "post",
      header: { "content-type": "application/x-www-form-urlencoded" },
      data: {
        token: wx.getStorageSync("token"),
        fid: that.data.fid,
        page_size: page_size,
        page_index: page_index,
        digest: that.data.digest,
        order_by_views: that.data.order_by_views,
      },
      success: function (resp) {
        console.log(resp);
        var resp_dict = resp.data;
        if (resp_dict.err_code == 0) {
          if (resp_dict.data.forum_thread_data.length != 0) {
            that.setData({
              articleList: resp_dict.data.forum_thread_data,
              page_index: page_index,
              no_data: false,
              have_data: false,
              nomore_data: false,
            })
          } else {
            that.setData({
              articleList: resp_dict.data.forum_thread_data,
              page_index: page_index,
              no_data: true,
              nomore_data:false,
              have_data:false,
            })
          }
        } else {
          getApp().showSvrErrModal(resp);
        }
      }
    })
  },

  onReachBottom: function () {
    var that = this;
    that.setData({
      have_data: true,
    })
    var page_size = that.data.page_size;
    var page_index = that.data.page_index + 1;
    wx.request({
      url: getApp().globalData.svr_url + "get_thread.php",
      method: "post",
      header: { "content-type": "application/x-www-form-urlencoded" },
      data: {
        token: wx.getStorageSync("token"),
        fid: that.data.fid,
        page_size: page_size,
        page_index: page_index,
        digest: that.data.digest,
        order_by_views: that.data.order_by_views,
      },
      success: function (resp) {
        console.log(resp);
        var resp_dict = resp.data;
        if (resp_dict.err_code == 0) {
          var tmpArticleList = that.data.articleList;
          var respArticleList = resp_dict.data.forum_thread_data;
          var has_append = 0;
          // console.log(respArticleList)
          if (respArticleList.length > 0) {
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
                page_index: page_index
              })
            }
          } else {
            that.setData({
              have_data: false,
              nomore_data: true,
            })
          }
        }
      }
    })
  },

  onPullDownRefresh: function () {
    console.log('onPullDownRefresh');
    this.reloadIndex();
    wx.stopPullDownRefresh();
  },

  get_forum_info: function () {
    var that = this;
    wx.request({
      url: getApp().globalData.svr_url + "get_forum_info.php",
      method: "post",
      header: { "content-type": "application/x-www-form-urlencoded" },
      data: {
        token: wx.getStorageSync("token"),
        fid: that.data.fid,
      },
      success: function (resp) {
        console.log(resp);
        var resp_dict = resp.data;
        if (resp_dict.err_code == 0) {
          // console.log(resp_dict)
          var data = {};
          that.setData({
            forum_data: resp_dict.data.forum_data,
            few_top_thread_data: resp_dict.data.few_top_thread_data,
            top_thread_data: resp_dict.data.top_thread_data,
            show_more: resp_dict.data.show_more,
            has_top: resp_dict.data.has_top,
          });
        }
      }
    })
  },

  show_more_top: function () {
    console.log(this.data)
    this.setData({
      few_top_thread_data: this.data.top_thread_data,
      show_more: 0,
    });
  },

  get_recent_thread: function () {
    this.setData({
      recent: 1,
      digest: 0,
      order_by_views: 0,
    })
    this.reloadIndex();
  },

  get_hot_thread: function () {
    this.setData({
      recent: 0,
      digest: 0,
      order_by_views: 1,
    })
    this.reloadIndex();
  },

  get_digest_thread: function () {
    this.setData({
      recent: 0,
      digest: 1,
      order_by_views: 0,
    })
    this.reloadIndex();
  },

  toAddArticle: function (e) {
    wx.navigateTo({
      url: '../add_forum_article/add_forum_article?fid=' + this.data.fid + "&fname=" + this.data.forum_data.name,
    })
  }
})