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
    imageList: [],
    aidList: [],
    loading_hidden: true,
    loading_msg: '加载中...',
  },

  chooseImage: function () {
    var that = this
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
            url: getApp().globalData.svr_url + 'add_image.php',
            filePath: localFilePath,
            name: 'myfile',
            method: 'POST',
            formData: {
              token: wx.getStorageSync("token"),
            },
            success: function(resp) {
              console.log(resp);
              var resp_dict = JSON.parse(resp.data)
              console.log(resp_dict)
              if (resp_dict.err_code == 0)
              {
                console.log(resp_dict.data.file_url)
                tmpImageList.push(resp_dict.data.file_url);
                tmpAidList.push(resp_dict.data.aid);
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
            articleContent: '',
            imageList: [],
            aidList: [],
          })

          var pages = getCurrentPages();
          var prevPage = pages[pages.length - 2];
          prevPage.setData({
            reload_index: 1,
          });
          wx.navigateBack({
            delta: 1,
          })
        } else {
          getApp().showSvrErrModal(resp);
        }
      }
    })
  },

    /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    app.checkLogin()
    console.log(options);
    var fid = options.fid;
    var fname = options.fname;
    this.setData({
      fid: fid,
      fname: fname,
    })
  },

  onShow: function() {
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