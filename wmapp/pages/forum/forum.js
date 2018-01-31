// pages/forum/forum.js
Page({
  /**
   * 页面的初始数据
   */
  data: {
    group_list: []
  },

  onShow: function() {
    var that = this;
    wx.request({
      url: getApp().globalData.svr_url + "get_forum.php",
      method: "post",
      header: { "content-type": "application/x-www-form-urlencoded" },
      data: {
        token: wx.getStorageSync("token"),
      },
      success: function (resp) {
        console.log(resp);
        var resp_dict = resp.data;
        if (resp_dict.err_code == 0) {
          // console.log(resp_dict.data)
          that.setData({
            group_list: resp_dict.data
          })
        } else {
          getApp().showSvrErrModal(resp);
        }
      }
    })
  },

  toForumList: function(e) {
    // console.log(e);
    var fid = e.currentTarget.dataset.fid;
    // console.log(fid);
    wx.navigateTo({
      url: '../forum_list/forum_list?fid='+fid,
    })
  },

  clickGroup: function(e) {
    // console.log(e)
    var fid = e.currentTarget.id;
    // console.log(fid)
    var group_list = this.data.group_list;
    for (var i = 0; i < group_list.length; i++) {
      if (group_list[i].fid == fid) {
        if (group_list[i].open) {
          group_list[i].open = 0;
        } else {
          group_list[i].open = 1;
        }
      }
    }
    this.setData({
      group_list: group_list
    })
    // console.log(this.data.group_list)
  }
})