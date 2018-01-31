<!--add_article.wxml-->
<view class="container">

  <loading hidden="{{loading_hidden}}">
    {{loading_msg}}
  </loading>

<view class="page-body">
  <view class="page__bd">
    <view class="weui-cells-title">
      发布到版块：{{fname}}
    </view>
    <view class="weui-cells cells-no-border">

        <view class="weui-cell weui-cell_input">
          <view class="weui-cell__hd">
            <view class="weui-label">标题</view>
          </view>
          <view class="weui-cell__bd">
            <input class="weui-input" type="text" placeholder="请输入标题" name="key" value="{{articleTitle}}" bindinput="inputTitle"></input>
          </view>
        </view>

        <view class="weui-cell">
            <view class="weui-cell__bd">
                <textarea class="weui-textarea" placeholder="请输入内容" bindinput="inputContent" style="height: 5em" value="{{articleContent}}"/>
                <!-- <view class="weui-textarea-counter">0/200</view> -->
            </view>
        </view>

      <view class="weui-cell">
        <view class="weui-cell__bd">
          <view class="weui-uploader">
            <view class="weui-uploader__hd">
              <view class="weui-uploader__title">选择图片</view>
              <!-- <view class="weui-uploader__info">{{imageList.length}}/{{count[countIndex]}}</view> -->
            </view>
            <view class="weui-uploader__bd">
              <view class="weui-uploader__files">
                <block wx:for="{{imageList}}" wx:for-item="image" wx:key="imageId" >
                  <view class="weui-uploader__file">
                    <view  class="image-cell">
                      <view bindtap="delImg" data-index="{{index}}">
                        <image class='del-img-btn' src="../../resources/image/del-img.png"/>
                      </view>
                    <image class="weui-uploader__img" src="{{image}}" data-src="{{image}}" bindtap="previewImage"></image>
                    </view>
                  </view>
                </block>
              </view>
              <view class="weui-uploader__input-box image-cell">
                <view class="weui-uploader__input" bindtap="chooseImage"></view>
              </view>
            </view>
          </view>
        </view>
      </view>
    </view>
    
    <view class="btn-area">
      <button class="weui-btn" type="primary" bindtap="articleSubmit">提交</button>
    </view>

  </view>
</view>
</view>