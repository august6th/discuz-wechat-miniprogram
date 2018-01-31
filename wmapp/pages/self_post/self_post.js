// pages/self_post/self_post.js
var WxParse = require('../../wxParse/wxParse.js');
Page({

  /**
   * 页面的初始数据
   */
  data: {
    page_size: 8,
    page_index: 0,
  },

  onLoad: function (options) {
    this.reloadIndex();
  },

  reloadIndex: function() {
    var that = this;
    var tmpPostList = [];  
    var page_size = that.data.page_size;
    var page_index = 0;
    wx.request({
      url: getApp().globalData.svr_url + "get_self_post.php",
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
            postList: resp_dict.data.self_post_list,
            page_index: page_index
          })
          console.log(resp_dict.data.self_post_list)

          // 我的帖子 parse
          var postArr = []

          for (var i = 0; i < resp_dict.data.self_post_list.length; i++) {
            // console.log(post_list[i].message)
            postArr.push(resp_dict.data.self_post_list[i].message)
          }

          // console.log(postArr);

          for (let j = 0; j < postArr.length; j++) {
            WxParse.wxParse('reply' + j, 'html', postArr[j], that);
            if (j === postArr.length - 1) {
              WxParse.wxParseTemArray("replyTemArray", 'reply', postArr.length, that)
            }
          }

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
      url: getApp().globalData.svr_url + "get_self_post.php",
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
          var tmpPostList = that.data.postList;
          var respPostList = resp_dict.data.self_post_list;
          var has_append = 0;
          for (var i = 0; i < respPostList.length; ++i) {
            var has_in = 0;
            for (var j = 0; j < tmpPostList.length; ++j) {
              if (respPostList[i].pid == tmpPostList[j].pid) {
                has_in = 1;
              } 
            }
            if (has_in == 0) {
              tmpPostList.push(respPostList[i]);
              has_append = 1;
            }
          }

          if (has_append == 1)
          {
            that.setData({
              postList: tmpPostList,
              page_index: page_index  
            })
            // 我的帖子 parse
            var postArr = []

            for (var i = 0; i < tmpPostList.length; i++) {
              // console.log(post_list[i].message)
              postArr.push(tmpPostList[i].message)
            }

            // console.log(postArr);

            for (let j = 0; j < postArr.length; j++) {
              WxParse.wxParse('reply' + j, 'html', postArr[j], that);
              if (j === postArr.length - 1) {
                WxParse.wxParseTemArray("replyTemArray", 'reply', postArr.length, that)
              }
            }
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
