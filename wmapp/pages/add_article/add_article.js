// add_article.js
var app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    countIndex: 8,
    count: [1, 2, 3, 4, 5, 6, 7, 8, 9],
    articleTitle: "",
    group_index: 0,
    group_list: [],
    sub_group_index: 0,
    sub_group_list: [],
    imageList: [],
    aidList: [],
    loading_hidden: true,
    loading_msg: '加载中...',
  },

  chooseImage: function () {
    var that = this
    console.log(that.data)
    wx.chooseImage({
      count: 1,
      success: function (res) {
        console.log(res)
        var tmpImageList = that.data.imageList;
        var tmpAidList = that.data.aidList;

        that.setData({
          loading_hidden: false,
          loading_msg: '加载中...'
        })

        for (var i = 0; i < res.tempFilePaths.length; i++) {
          var localFilePath = res.tempFilePaths[i]
          wx.uploadFile({
            url: app.globalData.svr_url + 'add_image.php',
            filePath: localFilePath,
            name: 'myfile',
            method: 'POST',
            formData: {
              token: wx.getStorageSync("token"),
            },
            success: function(resp) {
              console.log(resp);
              var resp_dict = JSON.parse(resp.data)
              if (resp_dict.err_code == 0)
              {
                console.log(resp_dict.data.file_url)
                tmpImageList.push(resp_dict.data.file_url);
                tmpAidList.push(resp_dict.data.aid);
                // console.log(that.data)
                that.setData({
                  imageList: tmpImageList,
                  aidList: tmpAidList
                })
              } else {
                getApp().showSvrErrModal(resp);
              }
              that.setData({
                loading_hidden: true,
              })
            }
           })
        }
      }
    })
  },

  previewImage: function (e) {
    var current = e.target.dataset.src
    wx.previewImage({
      current: current,
      urls: this.data.imageList
    })
  },
  
  inputTitle: function(e) {
    this.setData({
      articleTitle: e.detail.value
    });
  },
  
  groupChange: function(e) {
    var group_data = this.data.group_data;
    var group_index = e.detail.value;
    var sub_group_list = [];
    for (var i = 0; i < group_data[group_index].sub_group.length; i++)
    {
      sub_group_list.push(group_data[group_index].sub_group[i].name);
    }
    this.setData({
      group_index: group_index,
      sub_group_list: sub_group_list,
      sub_group_index: 0,
    });
    console.log(this.data)
  },

  subGroupChange: function(e) {
    var group_data = this.data.group_data;
    var group_index = this.data.group_index;
    var sub_group_index = e.detail.value;
    var fid = group_data[group_index].sub_group[sub_group_index].fid;
    this.setData({
      sub_group_index: sub_group_index,
      fid: fid,
    });
    // console.log(fid);
    console.log(this.data)
  },
  
  inputContent: function(e) {
    this.setData({
      articleContent: e.detail.value
    });
  },

  articleSubmit: function() {
    var that = this;
    var articleTitle = that.data.articleTitle; 
    if (articleTitle == null || articleTitle == undefined || articleTitle == ''){
      getApp().showErrModal('标题不能为空');
      return;
    }

    var articleContent = that.data.articleContent; 
    if (articleContent == null || articleContent == undefined || articleContent == ''){
      getApp().showErrModal('内容不能为空');
      return;
    }

    wx.request({
      url: getApp().globalData.svr_url + 'add_thread.php',
      header: { "content-type": "application/x-www-form-urlencoded" },
      method: 'POST',
      data: {
        token: wx.getStorageSync("token"),
        fid: that.data.fid,
        subject: that.data.articleTitle,
        message: that.data.articleContent,
        pic_list: that.data.imageList,
        aid_list: that.data.aidList,
      },
      success: function(resp) {
        console.log(resp);
        var resp_dict = resp.data;
        if (resp_dict.err_code == 10001) {
          wx.showModal({
            content: "请先登录",
            success: function(res) {
              if (res.confirm) {
                wx.switchTab({
                  url:"../user/user"
                });  
              } else if (res.cancel) {
                console.log('用户点击取消')
              }
            }
          });
        } else if (resp_dict.err_code == 0) {
          that.setData({
            articleTitle: '',
            group_index: 0,
            articleContent: '',
            imageList: [],
            aidList: [],
            group_list: [],
            group_id_list: [],
          })

          wx.setStorage({
            key: 'reload_index',
            data: 1,
          })
          wx.switchTab({
            url: "/pages/index/index",
          })
        } else {
          getApp().showSvrErrModal(resp);
        }
      }
    })
  },
  
  onLoad: function() {
    app.checkLogin()
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

        var group_list = [];
        var group_id_list = [];
        if (resp_dict.err_code == 0) {
          for (var i = 0; i < resp_dict.data.length; i++){
            var group = resp_dict.data[i]
            var group_name = group.name;
            group_list.push(group_name);
          };
          
          var sub_group_list = [];
          var fid = 0;
          if (resp_dict.data.length > 0)
          {
            for (var i = 0; i < resp_dict.data[0].sub_group.length; i++)
            {
              sub_group_list.push(resp_dict.data[0].sub_group[i].name);
            }
            if (resp_dict.data[0].sub_group.length > 0)
            {
              fid = resp_dict.data[0].sub_group[0].fid;
            }
          }

          that.setData({
            group_list: group_list,
            group_data: resp_dict.data,
            group_index: 0,
            sub_group_list: sub_group_list,
            sub_group_index: 0,
            fid: fid,
          })
        } else {
          getApp().showSvrErrModal(resp);
        }

      }
    })
  },

  delImg: function(e) {
    var index = e.currentTarget.dataset.index;
    var imageList = this.data.imageList;
    var aidList = this.data.aidList;
    if (index < imageList.length) {
      imageList.splice(index, 1);
      aidList.splice(index, 1);
    }
    this.setData({
      imageList: imageList,
      aidList: aidList,
    })
  }
})